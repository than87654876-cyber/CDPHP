<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Báo cáo doanh thu & Thống kê kinh doanh món ăn FOODDAILY
     */
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Cho phép chọn năm phân tích (Mặc định năm hiện tại)
        $selectedYear = (int) $request->input('year', $currentYear);
        if ($selectedYear < 2020 || $selectedYear > $currentYear + 2) {
            $selectedYear = $currentYear;
        }

        // 1. KPI CỐT LÕI
        // 1.1 Doanh thu tháng này
        $monthlyRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('final_amount');

        // Doanh thu tháng trước để so sánh tăng trưởng
        $prevMonthDate = Carbon::now()->subMonth();
        $prevMonthRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $prevMonthDate->year)
            ->whereMonth('created_at', $prevMonthDate->month)
            ->sum('final_amount');

        $monthlyGrowth = $prevMonthRevenue > 0 
            ? round((($monthlyRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) 
            : 0;

        // 1.2 Doanh thu năm được chọn
        $yearlyRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $selectedYear)
            ->sum('final_amount');

        // 1.3 Tổng số đơn hàng
        $orderCount = Order::where('order_status', '!=', 'cancelled')->count();
        $yearlyOrderCount = Order::where('order_status', '!=', 'cancelled')
            ->whereYear('created_at', $selectedYear)
            ->count();

        // 1.4 TỔNG SỐ MÓN ĂN ĐÃ ĐẶT (Total dishes ordered)
        // Tổng số suất món đã bán từ trước tới nay
        $totalDishesOrdered = (int) OrderItem::whereHas('order', function ($q) {
            $q->where('order_status', '!=', 'cancelled');
        })->sum('quantity');

        // Tổng số suất món đã bán trong tháng này
        $monthDishesOrdered = (int) OrderItem::whereHas('order', function ($q) use ($currentYear, $currentMonth) {
            $q->where('order_status', '!=', 'cancelled')
              ->whereYear('created_at', $currentYear)
              ->whereMonth('created_at', $currentMonth);
        })->sum('quantity');

        // 2. DOANH THU THEO TỪNG THÁNG CỦA NĂM ĐƯỢC CHỌN (12 Tháng)
        $paidOrdersInYear = Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $selectedYear)
            ->get();

        $orderItemsThisYear = OrderItem::with('order')
            ->whereHas('order', function ($q) use ($selectedYear) {
                $q->where('order_status', '!=', 'cancelled')
                  ->whereYear('created_at', $selectedYear);
            })
            ->get();

        $dishesSoldByMonth = [];
        foreach ($orderItemsThisYear as $item) {
            if ($item->order && $item->order->created_at) {
                $m = $item->order->created_at->month;
                $dishesSoldByMonth[$m] = ($dishesSoldByMonth[$m] ?? 0) + $item->quantity;
            }
        }

        $monthlyStats = [];
        $chartAreaValues = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthOrders = $paidOrdersInYear->filter(function ($order) use ($m) {
                return $order->created_at->month === $m;
            });

            $revenue = (float) $monthOrders->sum('final_amount');
            $ordersQty = $monthOrders->count();
            $dishesQty = (int) ($dishesSoldByMonth[$m] ?? 0);
            $avgOrder = $ordersQty > 0 ? round($revenue / $ordersQty) : 0;
            $sharePercent = $yearlyRevenue > 0 ? round(($revenue / $yearlyRevenue) * 100, 1) : 0;

            $monthlyStats[$m] = [
                'month' => $m,
                'month_name' => 'Tháng ' . $m,
                'revenue' => $revenue,
                'orders_count' => $ordersQty,
                'dishes_count' => $dishesQty,
                'average_order' => $avgOrder,
                'share_percent' => $sharePercent,
            ];

            $chartAreaValues[] = $revenue;
        }

        // 3. DOANH THU THEO PHƯƠNG THỨC THANH TOÁN
        $codRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereIn('payment_method', ['cash', 'cod'])
            ->whereYear('created_at', $selectedYear)
            ->sum('final_amount');

        $bankRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->where('payment_method', 'bank_transfer')
            ->whereYear('created_at', $selectedYear)
            ->sum('final_amount');

        $momoRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->where('payment_method', 'momo')
            ->whereYear('created_at', $selectedYear)
            ->sum('final_amount');

        $totalRev = $codRevenue + $bankRevenue + $momoRevenue;
        $codPercent = $totalRev > 0 ? round(($codRevenue / $totalRev) * 100) : 40;
        $bankPercent = $totalRev > 0 ? round(($bankRevenue / $totalRev) * 100) : 40;
        $momoPercent = $totalRev > 0 ? round(($momoRevenue / $totalRev) * 100) : 20;

        // 4. MÓN ĂN BÁN CHẠY (BEST SELLERS) VÀ MÓN TỒN / BÁN CHẬM (SLOW MOVING)
        $allOrderItems = OrderItem::whereHas('order', function ($q) {
                $q->where('order_status', '!=', 'cancelled');
            })
            ->get();

        $soldByDish = [];
        $revByDish = [];
        $orderCountByDish = [];

        foreach ($allOrderItems as $item) {
            $dId = $item->dish_id;
            $soldByDish[$dId] = ($soldByDish[$dId] ?? 0) + $item->quantity;
            $revByDish[$dId] = ($revByDish[$dId] ?? 0) + ($item->price * $item->quantity);
            if (!isset($orderCountByDish[$dId])) {
                $orderCountByDish[$dId] = [];
            }
            $orderCountByDish[$dId][$item->order_id] = true;
        }

        $allDishes = Dish::with('category')->get();
        foreach ($allDishes as $dish) {
            $dish->total_sold = (int) ($soldByDish[$dish->id] ?? 0);
            $dish->total_revenue = (float) ($revByDish[$dish->id] ?? 0);
            $dish->order_count = isset($orderCountByDish[$dish->id]) ? count($orderCountByDish[$dish->id]) : 0;
        }

        // Bán chạy: Sắp xếp giảm dần theo total_sold (chỉ lấy món có lượt bán > 0)
        $bestSellingDishes = $allDishes->filter(fn($d) => $d->total_sold > 0)->sortByDesc('total_sold')->values()->take(10);

        // Món tồn / bán chậm: Sắp xếp tăng dần theo total_sold
        $slowMovingDishes = $allDishes->sortBy('total_sold')->values()->take(10);
        $unsoldDishesCount = $allDishes->where('total_sold', 0)->count();

        // Danh sách các năm để lọc (tương thích 100% mọi CSDL: MySQL, MariaDB, SQLite, PostgreSQL)
        $minDate = Order::whereNotNull('created_at')->min('created_at');
        $maxDate = Order::whereNotNull('created_at')->max('created_at');
        $minYear = $minDate ? Carbon::parse($minDate)->year : $currentYear;
        $maxYear = $maxDate ? Carbon::parse($maxDate)->year : $currentYear;

        $availableYears = [];
        $startYear = max($maxYear, $currentYear);
        $endYear = min($minYear, $currentYear);
        for ($y = $startYear; $y >= $endYear; $y--) {
            $availableYears[] = $y;
        }

        if (empty($availableYears)) {
            $availableYears = [$currentYear];
        }

        return view('admin.revenue_report', compact(
            'monthlyRevenue',
            'monthlyGrowth',
            'yearlyRevenue',
            'orderCount',
            'yearlyOrderCount',
            'totalDishesOrdered',
            'monthDishesOrdered',
            'selectedYear',
            'availableYears',
            'monthlyStats',
            'chartAreaValues',
            'codRevenue',
            'bankRevenue',
            'momoRevenue',
            'codPercent',
            'bankPercent',
            'momoPercent',
            'bestSellingDishes',
            'slowMovingDishes',
            'unsoldDishesCount'
        ));
    }
}

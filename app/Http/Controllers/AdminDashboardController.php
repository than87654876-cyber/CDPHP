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

        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
        $monthExpr = $isSqlite ? "CAST(strftime('%m', created_at) AS INTEGER)" : "MONTH(created_at)";
        $ordersMonthExpr = $isSqlite ? "CAST(strftime('%m', orders.created_at) AS INTEGER)" : "MONTH(orders.created_at)";

        // 1. DOANH THU THEO TỪNG THÁNG CỦA NĂM ĐƯỢC CHỌN (12 Tháng)
        // Tính toán trực tiếp bằng SQL GROUP BY MONTH thay vì nạp toàn bộ đơn vào bộ nhớ PHP
        $monthOrdersRaw = Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $selectedYear)
            ->selectRaw("{$monthExpr} as month, SUM(final_amount) as revenue, COUNT(id) as orders_count")
            ->groupByRaw($monthExpr)
            ->get()
            ->keyBy('month');

        // 1.1 Doanh thu năm được chọn (tính trực tiếp từ tổng hợp các tháng)
        $yearlyRevenue = (float) $monthOrdersRaw->sum('revenue');

        // 1.2 Doanh thu tháng này
        if ($selectedYear === $currentYear) {
            $monthlyRevenue = (float) ($monthOrdersRaw->get($currentMonth)->revenue ?? 0);
        } else {
            $monthlyRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->sum('final_amount');
        }

        // Doanh thu tháng trước để so sánh tăng trưởng
        $prevMonthDate = Carbon::now()->subMonth();
        $prevMonthRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $prevMonthDate->year)
            ->whereMonth('created_at', $prevMonthDate->month)
            ->sum('final_amount');

        $monthlyGrowth = $prevMonthRevenue > 0 
            ? round((($monthlyRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) 
            : 0;

        // 1.3 Tổng số đơn hàng
        $orderCount = Order::where('order_status', '!=', 'cancelled')->count();
        $yearlyOrderCount = Order::where('order_status', '!=', 'cancelled')
            ->whereYear('created_at', $selectedYear)
            ->count();

        // 1.4 TỔNG SỐ MÓN ĂN ĐÃ ĐẶT (Total dishes ordered)
        // Tổng số suất món đã bán từ trước tới nay
        $totalDishesOrdered = (int) OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_status', '!=', 'cancelled')
            ->sum('order_items.quantity');

        // Tổng số suất món đã bán trong tháng này
        $monthDishesOrdered = (int) OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_status', '!=', 'cancelled')
            ->whereYear('orders.created_at', $currentYear)
            ->whereMonth('orders.created_at', $currentMonth)
            ->sum('order_items.quantity');

        $monthDishesRaw = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_status', '!=', 'cancelled')
            ->whereYear('orders.created_at', $selectedYear)
            ->selectRaw("{$ordersMonthExpr} as month, SUM(order_items.quantity) as total_qty")
            ->groupByRaw($ordersMonthExpr)
            ->pluck('total_qty', 'month');

        $monthlyStats = [];
        $chartAreaValues = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthData = $monthOrdersRaw->get($m);
            $revenue = (float) ($monthData->revenue ?? 0);
            $ordersQty = (int) ($monthData->orders_count ?? 0);
            $dishesQty = (int) ($monthDishesRaw[$m] ?? 0);
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

        // 3. DOANH THU THEO PHƯƠNG THỨC THANH TOÁN (1 câu truy vấn nhóm thay vì 3 câu quét bảng)
        $paymentBreakdown = Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('payment_method, SUM(final_amount) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        $codRevenue = (float) (($paymentBreakdown['cash'] ?? 0) + ($paymentBreakdown['cod'] ?? 0));
        $bankRevenue = (float) ($paymentBreakdown['bank_transfer'] ?? 0);
        $momoRevenue = (float) ($paymentBreakdown['momo'] ?? 0);

        $totalRev = $codRevenue + $bankRevenue + $momoRevenue;
        $codPercent = $totalRev > 0 ? round(($codRevenue / $totalRev) * 100) : 40;
        $bankPercent = $totalRev > 0 ? round(($bankRevenue / $totalRev) * 100) : 40;
        $momoPercent = $totalRev > 0 ? round(($momoRevenue / $totalRev) * 100) : 20;

        // 4. MÓN ĂN BÁN CHẠY (BEST SELLERS) VÀ MÓN TỒN / BÁN CHẬM (SLOW MOVING)
        // Tính toán tổng hợp trực tiếp bằng SQL thay vì nạp toàn bộ OrderItem của mọi thời đại vào PHP
        $dishStatsRaw = OrderItem::join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.order_status', '!=', 'cancelled')
            ->selectRaw('order_items.dish_id, SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as total_revenue, COUNT(DISTINCT orders.id) as order_count')
            ->groupBy('order_items.dish_id')
            ->get()
            ->keyBy('dish_id');

        $allDishes = Dish::with('category')->get();
        foreach ($allDishes as $dish) {
            $stat = $dishStatsRaw->get($dish->id);
            $dish->total_sold = (int) ($stat->total_sold ?? 0);
            $dish->total_revenue = (float) ($stat->total_revenue ?? 0);
            $dish->order_count = (int) ($stat->order_count ?? 0);
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

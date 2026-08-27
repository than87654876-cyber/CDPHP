<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Calculate KPIs
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyRevenue = Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('final_amount');

        $yearlyRevenue = Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $currentYear)
            ->sum('final_amount');

        $orderCount = Order::where('order_status', '!=', 'cancelled')->count();

        // 2. Prepare Area Chart Data (Months 1-12 of the current year)
        $paidOrdersThisYear = Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereYear('created_at', $currentYear)
            ->get();

        $monthlyRevenueData = array_fill(1, 12, 0);
        foreach ($paidOrdersThisYear as $order) {
            $month = $order->created_at->month;
            $monthlyRevenueData[$month] += (float) $order->final_amount;
        }
        $chartAreaValues = array_values($monthlyRevenueData);

        // 3. Revenue distribution by Payment Methods
        $codRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->whereIn('payment_method', ['cash', 'cod'])
            ->sum('final_amount');

        $bankRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->where('payment_method', 'bank_transfer')
            ->sum('final_amount');

        $momoRevenue = (float) Order::whereIn('payment_status', ['paid', 'completed'])
            ->where('payment_method', 'momo')
            ->sum('final_amount');

        $totalRev = $codRevenue + $bankRevenue + $momoRevenue;
        $codPercent = $totalRev > 0 ? round(($codRevenue / $totalRev) * 100) : 40;
        $bankPercent = $totalRev > 0 ? round(($bankRevenue / $totalRev) * 100) : 40;
        $momoPercent = $totalRev > 0 ? round(($momoRevenue / $totalRev) * 100) : 20;

        return view('admin.revenue_report', compact(
            'monthlyRevenue',
            'yearlyRevenue',
            'orderCount',
            'chartAreaValues',
            'codRevenue',
            'bankRevenue',
            'momoRevenue',
            'codPercent',
            'bankPercent',
            'momoPercent'
        ));
    }
}

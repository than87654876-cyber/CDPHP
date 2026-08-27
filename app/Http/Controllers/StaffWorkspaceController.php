<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class StaffWorkspaceController extends Controller
{
    /**
     * Hiển thị giao diện Bàn làm việc Nhân viên với số liệu thống kê nghiệp vụ
     */
    public function index()
    {
        $todayStr = Carbon::today()->format('Y-m-d');

        // 1. Thống kê Nhà bếp (Kitchen)
        $kitchenSingleQty = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('order_status', ['confirmed', 'preparing']);
        })->sum('quantity');

        $kitchenSubQty = DailySchedule::where('delivery_date', $todayStr)
            ->where('delivery_status', 'pending')
            ->count();

        $kitchenTotal = $kitchenSingleQty + $kitchenSubQty;

        // 2. Thống kê Chăm sóc Khách hàng (CSKH)
        $cskhPendingOrders = Order::where('order_status', 'pending')->count();

        $cskhPendingRefunds = Order::where('health_notes', 'like', '%[Yêu cầu hoàn tiền%')
            ->where('payment_status', '!=', 'refunded')
            ->count();

        // 3. Thống kê Vận chuyển (Shipper)
        $shipDeliveringOrders = Order::where('order_status', 'delivering')->count();
        $shipTodaySubscriptions = DailySchedule::where('delivery_date', $todayStr)->count();

        $shipperDeliveringOrders = $shipDeliveringOrders;
        $shipperSubDispatches = $shipTodaySubscriptions;

        return view('admin.staff_workspace', compact(
            'kitchenSingleQty',
            'kitchenSubQty',
            'kitchenTotal',
            'cskhPendingOrders',
            'cskhPendingRefunds',
            'shipDeliveringOrders',
            'shipTodaySubscriptions',
            'shipperDeliveringOrders',
            'shipperSubDispatches',
            'todayStr'
        ));
    }
}

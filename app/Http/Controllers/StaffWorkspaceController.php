<?php

namespace App\Http\Controllers;

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

        // 1. Thống kê Nhà bếp: Món ăn từ các đơn hàng cần chế biến
        $kitchenSingleQty = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('order_status', ['pending', 'confirmed', 'preparing']);
        })->sum('quantity');

        $kitchenTotal = $kitchenSingleQty;

        // 2. Thống kê Chăm sóc Khách hàng (CSKH) & Đơn hàng mới
        $cskhPendingOrders = Order::where('order_status', 'pending')->count();

        // 3. Thống kê Vận chuyển (Shipper)
        $shipDeliveringOrders = Order::where('order_status', 'delivering')->count();
        $shipperDeliveringOrders = $shipDeliveringOrders;

        // 4. Thống kê Kho & Nhập Hàng
        $lowStockCount = \App\Models\InventoryItem::where('current_stock', '<=', \Illuminate\Support\Facades\DB::raw('min_stock'))->count();
        $todayReceiptsCount = \App\Models\GoodsReceipt::whereDate('receipt_date', $todayStr)->count();

        return view('admin.staff_workspace', compact(
            'kitchenSingleQty',
            'kitchenTotal',
            'cskhPendingOrders',
            'shipDeliveringOrders',
            'shipperDeliveringOrders',
            'lowStockCount',
            'todayReceiptsCount',
            'todayStr'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\GroupOrder;
use App\Models\GroupOrderItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupOrderController extends Controller
{
    // Tạo nhóm đặt đơn mới
    public function create(Request $request)
    {
        $hostName = $request->input('host_name');
        if (empty($hostName) && auth()->check()) {
            $hostName = auth()->user()->fullname ?? auth()->user()->name;
        }
        if (empty($hostName)) {
            $hostName = 'Trưởng nhóm';
        }

        $code = 'GRP-' . strtoupper(Str::random(6));

        $groupOrder = GroupOrder::create([
            'code' => $code,
            'host_id' => auth()->id(),
            'host_name' => $hostName,
            'status' => 'active',
        ]);

        return redirect()->route('nhom.show', ['code' => $groupOrder->code]);
    }

    // Hiển thị phòng đặt đơn nhóm
    public function show($code)
    {
        $groupOrder = GroupOrder::where('code', $code)->with(['items.dish'])->firstOrFail();
        $dishes = Dish::where('is_available', true)->get();

        return view('client.group_order', compact('groupOrder', 'dishes'));
    }

    // Thành viên thêm món vào nhóm
    public function addItem(Request $request, $code)
    {
        $request->validate([
            'member_name' => 'required|string|max:100',
            'dish_id' => 'required|exists:dishes,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $groupOrder = GroupOrder::where('code', $code)->firstOrFail();

        if ($groupOrder->status !== 'active') {
            return back()->with('error', 'Nhóm đặt đơn này đã bị đóng hoặc đã chốt đơn!');
        }

        GroupOrderItem::create([
            'group_order_id' => $groupOrder->id,
            'member_name' => trim($request->member_name),
            'dish_id' => $request->dish_id,
            'quantity' => $request->quantity,
            'note' => $request->note,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã thêm món vào nhóm thành công!']);
        }

        return back()->with('success', 'Đã thêm món vào đơn nhóm thành công!');
    }

    // Xóa món khỏi nhóm
    public function removeItem($code, $itemId)
    {
        $groupOrder = GroupOrder::where('code', $code)->firstOrFail();
        
        $item = GroupOrderItem::where('group_order_id', $groupOrder->id)->where('id', $itemId)->first();
        if ($item) {
            $item->delete();
        }

        return back()->with('success', 'Đã xóa món khỏi đơn nhóm!');
    }

    // API lấy dữ liệu món ăn trong nhóm realtime
    public function pollItems($code)
    {
        $groupOrder = GroupOrder::where('code', $code)->with(['items.dish'])->first();
        if (!$groupOrder) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        return response()->json([
            'status' => $groupOrder->status,
            'items' => $groupOrder->items,
            'total_price' => $groupOrder->items->sum(function($item) {
                return ($item->dish->price ?? 0) * $item->quantity;
            })
        ]);
    }

    // Trưởng nhóm chốt đơn
    public function checkout(Request $request, $code)
    {
        $groupOrder = GroupOrder::where('code', $code)->with(['items.dish'])->firstOrFail();

        if ($groupOrder->items->isEmpty()) {
            return back()->with('error', 'Đơn nhóm chưa có món ăn nào! Vui lòng chọn món trước khi chốt đơn.');
        }

        // Tính tổng tiền
        $totalPrice = 0;
        $notesSummary = ["Chốt đơn nhóm #" . $groupOrder->code . " (Trưởng nhóm: " . $groupOrder->host_name . "):"];

        foreach ($groupOrder->items as $gItem) {
            $dishPrice = $gItem->dish->price ?? 0;
            $totalPrice += $dishPrice * $gItem->quantity;
            $notesSummary[] = "- " . $gItem->member_name . ": " . ($gItem->dish->dish_name ?? 'Món') . " x" . $gItem->quantity;
        }

        // Tạo đơn hàng thật trong bảng orders
        $order = Order::create([
            'user_id' => auth()->id() ?? $groupOrder->host_id,
            'total_price' => $totalPrice,
            'final_amount' => $totalPrice,
            'order_status' => 'preparing', // Đặt hàng tự động sang Đang chuẩn bị theo yêu cầu
            'payment_status' => 'pending',
            'payment_method' => $request->input('payment_method', 'cod'),
            'health_notes' => implode("\n", $notesSummary),
        ]);

        // Tạo các items
        foreach ($groupOrder->items as $gItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'dish_id' => $gItem->dish_id,
                'quantity' => $gItem->quantity,
                'price' => $gItem->dish->price ?? 0,
            ]);
        }

        // Đánh dấu nhóm hoàn tất
        $groupOrder->status = 'completed';
        $groupOrder->save();

        return redirect()->route('giohang')->with('success', '🎉 Đã chốt đơn nhóm #' . $groupOrder->code . ' thành công! Đơn hàng đã được tự động chuyển sang trạng thái Đang chuẩn bị.');
    }
}

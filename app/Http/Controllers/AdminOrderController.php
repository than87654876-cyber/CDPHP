<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // Danh sách đơn hàng (hỗ trợ lọc trạng thái)
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = Order::with(['user', 'orderItems.dish'])
            ->where('order_type', '!=', 'subscription')
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('order_status', $status);
        }

        $orders = $query->get();

        return view('admin.orders', compact('orders', 'status'));
    }

    // Bảng chuẩn bị món ăn hôm nay của nhà bếp
    public function kitchenReport()
    {
        $singleDishes = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('order_status', ['confirmed', 'preparing']);
        })
            ->select('dish_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('dish_id')
            ->with('dish')
            ->get();

        $todayStr = Carbon::today()->format('Y-m-d');
        $subscriptionDishes = DailySchedule::where('delivery_date', $todayStr)
            ->where('delivery_status', 'pending')
            ->select('dish_id', \DB::raw('COUNT(*) as total_qty'))
            ->groupBy('dish_id')
            ->with('dish')
            ->get();

        return view('admin.kitchen_report', compact('singleDishes', 'subscriptionDishes', 'todayStr'));
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.dish'])->findOrFail($id);

        return view('admin.orders_detail', compact('order'));
    }

    // Form chỉnh sửa/cập nhật trạng thái đơn hàng
    public function edit($id)
    {
        $order = Order::findOrFail($id);

        return view('admin.orders_edit', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|string|in:pending,confirmed,preparing,delivering,completed,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $order->order_status = $request->order_status;
        $order->payment_status = $request->payment_status;
        if ($order->payment_status === 'paid' && $order->order_status === 'pending') {
            $order->order_status = 'confirmed';
        }
        if ($request->admin_notes) {
            $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').'[Điều phối: '.$request->admin_notes.']';
        }
        $order->save();

        try {
            event(new \App\Events\OrderUpdated($order, 'status_updated'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        try {
            $telegram = app(\App\Services\TelegramService::class);
            $statusLabels = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'preparing' => 'Đang chuẩn bị',
                'delivering' => 'Đang giao hàng',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy'
            ];
            $statusLabel = $statusLabels[$order->order_status] ?? $order->order_status;
            
            $telegram->sendMessage("🔄 <b>Cập nhật đơn hàng #FDL-{$order->id}:</b>\nTrạng thái mới: <b>{$statusLabel}</b>\nThanh toán: " . ($order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'));
        } catch (\Exception $telError) {
            \Illuminate\Support\Facades\Log::warning('Telegram sending warning: ' . $telError->getMessage());
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái đơn hàng FDL-'.$id.' thành công!',
                'order' => $order,
            ]);
        }

        return redirect()->route('quanly_donhang')->with('success', 'Cập nhật trạng thái đơn hàng FDL-'.$id.' thành công!');
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        $subscription = Subscription::where('order_id', $order->id)->first();
        if ($subscription) {
            $subscription->dailySchedules()->delete();
            $subscription->pauses()->delete();
            $subscription->delete();
        }

        $order->orderItems()->delete();
        $order->delete();

        return redirect()->route('quanly_donhang')->with('success', 'Đã xóa đơn hàng FDL-'.$id.' thành công.');
    }

    // Xuất báo cáo đơn hàng (CSV UTF-8 BOM)
    public function exportOrdersCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bao-cao-don-hang.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Mã đơn hàng', 'Khách hàng', 'Email', 'Số điện thoại', 'Loại đơn', 'Tổng tiền', 'Hình thức thanh toán', 'Trạng thái thanh toán', 'Trạng thái đơn hàng', 'Ngày đặt']);

            $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

            foreach ($orders as $order) {
                $paymentMethodText = 'Tiền mặt (COD)';
                if ($order->payment_method === 'bank_transfer') {
                    $paymentMethodText = 'Chuyển khoản';
                } elseif ($order->payment_method === 'momo') {
                    $paymentMethodText = 'Ví MoMo';
                }
                
                $orderTypeText = $order->order_type === 'single' ? 'Món lẻ' : 'Gói dài kỳ';

                fputcsv($file, [
                    'FDL-' . $order->id,
                    $order->user ? $order->user->fullname : 'Khách vãng lai',
                    $order->user ? $order->user->email : 'N/A',
                    $order->user ? $order->user->phone : 'N/A',
                    $orderTypeText,
                    $order->final_amount,
                    $paymentMethodText,
                    $order->payment_status,
                    $order->order_status,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

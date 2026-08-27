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
        // 1. Lấy món ăn từ đơn hàng lẻ ở trạng thái đã xác nhận hoặc đang chuẩn bị
        $singleDishes = OrderItem::whereHas('order', function ($q) {
            $q->whereIn('order_status', ['confirmed', 'preparing']);
        })
            ->select('dish_id', \DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('dish_id')
            ->with('dish')
            ->get();

        // 2. Lấy món ăn từ gói dịch vụ cần giao ngày hôm nay (delivery_date = hôm nay, status = pending)
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

        // Gửi thông báo Telegram khi cập nhật trạng thái đơn hàng
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

    // Danh sách yêu cầu hoàn tiền
    public function refundsList()
    {
        // Lấy các đơn hàng có yêu cầu hoàn tiền trong health_notes
        $orders = Order::with(['user'])
            ->where('health_notes', 'like', '%[Yêu cầu hoàn tiền%')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.refunds', compact('orders'));
    }

    // Xem chi tiết yêu cầu hoàn tiền
    public function refundShow($id)
    {
        $order = Order::with(['user', 'orderItems.dish'])->findOrFail($id);

        return view('admin.refunds_detail', compact('order'));
    }

    // Phê duyệt hoặc Từ chối yêu cầu hoàn tiền
    public function refundApprove(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|string|in:approve,reject',
            'admin_response' => 'required|string',
        ]);

        $order = Order::findOrFail($id);

        if ($request->action === 'approve') {
            $order->payment_status = 'refunded';
            $order->order_status = 'cancelled';
        }

        $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').
            '[Admin Phản hồi: '.$request->admin_response.' ('.($request->action === 'approve' ? 'Đã duyệt hoàn tiền' : 'Từ chối hoàn tiền').')]';
        $order->save();

        try {
            event(new \App\Events\OrderUpdated($order, 'refund_processed'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        return redirect()->route('quanly_yeucauhoan')->with('success', 'Xử lý yêu cầu hoàn tiền cho đơn hàng FDL-'.$id.' thành công!');
    }

    // Xóa đơn hàng
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Xóa gói đăng ký liên kết (nếu có) và các bảng con của nó để tránh lỗi khóa ngoại
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

    // Xuất báo cáo hoàn tiền (CSV UTF-8 BOM)
    public function exportRefundsCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bao-cao-hoan-tien.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Mã đơn hàng', 'Khách hàng', 'Email', 'Số điện thoại', 'Tổng tiền', 'Thông tin hoàn tiền / Chi tiết', 'Ngày cập nhật']);

            $orders = Order::with('user')
                ->where('health_notes', 'like', '%[Yêu cầu hoàn tiền%')
                ->orderBy('updated_at', 'desc')
                ->get();

            foreach ($orders as $order) {
                fputcsv($file, [
                    'FDL-' . $order->id,
                    $order->user ? $order->user->fullname : 'Khách vãng lai',
                    $order->user ? $order->user->email : 'N/A',
                    $order->user ? $order->user->phone : 'N/A',
                    $order->final_amount,
                    $order->health_notes,
                    $order->updated_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Thực hiện hoàn tiền cho đơn hàng từ phía Admin
    public function processRefund(Request $request, $id)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'refund_reason' => 'required|string',
            'refund_method' => 'required|string|in:bank,momo,cash,other',
            'refund_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $order = Order::findOrFail($id);

        // Upload ảnh minh chứng chuyển khoản hoàn tiền
        $imageLink = 'Không có';
        if ($request->hasFile('refund_image')) {
            try {
                $file = $request->file('refund_image');
                $filename = 'admin_refund_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $filename);
                $imageLink = asset('uploads/' . $filename);
            } catch (\Exception $uploadError) {
                \Illuminate\Support\Facades\Log::warning('Admin refund image upload error: ' . $uploadError->getMessage());
            }
        }

        $refundAmount = floatval($request->input('refund_amount', $order->final_amount));

        $refundInfo = '[Yêu cầu hoàn tiền - Số tiền hoàn lại: '.number_format($refundAmount, 0, ',', '.').'đ, Lý do: '.$request->refund_reason.', Hình ảnh minh chứng: '.$imageLink.', Phương thức: '.$request->refund_method.']';

        $order->payment_status = 'refunded';
        $order->order_status = 'cancelled';
        $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').$refundInfo;
        $order->save();

        try {
            event(new \App\Events\OrderUpdated($order, 'refund_processed'));
        } catch (\Exception $broadcastException) {
            \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
        }

        return redirect()->back()->with('success', 'Đã xử lý hoàn tiền thành công cho đơn hàng #FDL-'.$order->id.'!');
    }

    // Danh sách đánh giá của khách hàng
    public function reviewsList()
    {
        $reviews = \App\Models\Review::with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reviews', compact('reviews'));
    }

    // Xóa đánh giá
    public function destroyReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return redirect()->route('quanly_reviews')->with('success', 'Đã xóa đánh giá thành công.');
    }
}


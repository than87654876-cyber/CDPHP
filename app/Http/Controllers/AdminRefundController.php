<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminRefundController extends Controller
{
    // Danh sách yêu cầu hoàn tiền
    public function refundsList()
    {
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
}

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
        $todayStr = Carbon::today()->format('Y-m-d');

        // Lấy tất cả OrderItems của các đơn đang chờ bếp nấu (kể cả vừa đặt 'pending', 'confirmed', 'preparing')
        $kitchenOrderItems = OrderItem::with(['order.user', 'dish.category'])
            ->whereHas('order', function ($q) {
                $q->whereIn('order_status', ['pending', 'confirmed', 'preparing'])
                  ->where('order_type', '!=', 'subscription');
            })
            ->get();

        // Gom nhóm theo món ăn kèm danh sách mã vận đơn chi tiết
        $singleDishes = $kitchenOrderItems->groupBy('dish_id')->map(function ($items) {
            $first = $items->first();
            $dish = $first->dish;

            // Danh sách các mã vận đơn có món này
            $orders = $items->map(function ($item) {
                $order = $item->order;
                return (object) [
                    'order_id' => $item->order_id,
                    'tracking_code' => 'FDL-' . $item->order_id,
                    'quantity' => $item->quantity,
                    'order_status' => $order->order_status ?? 'pending',
                    'payment_status' => $order->payment_status ?? 'pending',
                    'customer_name' => $order->user->fullname ?? $order->user->name ?? 'Khách',
                    'health_notes' => $order->health_notes ?? '',
                    'created_at' => $order->created_at,
                ];
            })->sortByDesc('order_id')->values();

            return (object) [
                'dish_id' => $first->dish_id,
                'dish' => $dish,
                'total_qty' => $items->sum('quantity'),
                'orders' => $orders,
            ];
        })->sortByDesc('total_qty')->values();

        // Lấy lịch giao combo định kỳ hôm nay kèm mã vận đơn
        $subscriptionSchedules = DailySchedule::with(['dish', 'subscription.user', 'subscription.package'])
            ->where('delivery_date', $todayStr)
            ->where('delivery_status', 'pending')
            ->get();

        $subscriptionDishes = $subscriptionSchedules->groupBy('dish_id')->map(function ($items) {
            $first = $items->first();
            $schedules = $items->map(function ($s) {
                return (object) [
                    'schedule_id' => $s->id,
                    'subscription_id' => $s->subscription_id,
                    'tracking_code' => 'SUB-' . $s->subscription_id . '-D' . $s->id,
                    'customer_name' => $s->subscription->user->fullname ?? $s->subscription->user->name ?? 'Hội viên',
                    'package_name' => $s->subscription->package->package_name ?? 'Gói Combo',
                    'delivery_notes' => $s->delivery_notes,
                ];
            })->values();

            return (object) [
                'dish_id' => $first->dish_id,
                'dish' => $first->dish,
                'total_qty' => $items->count(),
                'schedules' => $schedules,
            ];
        })->sortByDesc('total_qty')->values();

        // Danh sách các đơn hàng chi tiết đang chờ nấu (Vé bếp / Order Tickets)
        $activeOrders = Order::with(['user', 'orderItems.dish'])
            ->whereIn('order_status', ['pending', 'confirmed', 'preparing'])
            ->where('order_type', '!=', 'subscription')
            ->orderBy('created_at', 'asc') // Đơn đặt trước làm trước (FIFO)
            ->get();

        // Danh sách các đơn vừa nấu xong gần đây (delivering hoặc completed trong ngày hoặc gần nhất) để hỗ trợ tra cứu xử lý sự cố
        $recentCompletedOrders = Order::with(['user', 'orderItems.dish'])
            ->whereIn('order_status', ['delivering', 'completed'])
            ->where('order_type', '!=', 'subscription')
            ->whereDate('updated_at', Carbon::today())
            ->orderBy('updated_at', 'desc')
            ->take(30)
            ->get();

        if ($recentCompletedOrders->isEmpty()) {
            $recentCompletedOrders = Order::with(['user', 'orderItems.dish'])
                ->whereIn('order_status', ['delivering', 'completed'])
                ->where('order_type', '!=', 'subscription')
                ->orderBy('updated_at', 'desc')
                ->take(15)
                ->get();
        }

        // Format dữ liệu chi tiết cho modal tra cứu và giải quyết khiếu nại của khách
        $initialCompletedOrders = $recentCompletedOrders->map(function ($order) {
            $customerName = $order->user->fullname ?? $order->user->name ?? 'Khách đặt món';
            $phone = $order->user->phone ?? '';
            $address = $order->user->address ?? '';
            if (empty($phone) && !empty($order->health_notes) && preg_match('/SĐT:\s*([0-9\+\.\s]+)/i', $order->health_notes, $m)) {
                $phone = trim($m[1]);
            }
            if (empty($address) && !empty($order->health_notes) && preg_match('/Giao:\s*([^.\n]+)/i', $order->health_notes, $m)) {
                $address = trim($m[1]);
            }

            return [
                'id' => $order->id,
                'customer' => $customerName,
                'phone' => $phone,
                'address' => $address,
                'healthNotes' => $order->health_notes ?? '',
                'totalItems' => $order->orderItems->count(),
                'finalAmount' => number_format($order->final_amount, 0, ',', '.') . 'đ',
                'orderStatus' => $order->order_status,
                'paymentStatus' => $order->payment_status,
                'time' => $order->updated_at ? $order->updated_at->format('H:i') : ($order->created_at ? $order->created_at->format('H:i') : ''),
                'dishes' => $order->orderItems->map(function ($oi) {
                    return [
                        'name' => $oi->dish->dish_name ?? 'Món ăn',
                        'qty' => $oi->quantity,
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        return view('admin.kitchen_report', compact('singleDishes', 'subscriptionDishes', 'activeOrders', 'recentCompletedOrders', 'initialCompletedOrders', 'todayStr'));
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
        $headers = ['Mã đơn hàng', 'Khách hàng', 'Email', 'Số điện thoại', 'Loại đơn', 'Tổng tiền', 'Hình thức thanh toán', 'Trạng thái thanh toán', 'Trạng thái đơn hàng', 'Ngày đặt'];
        $orders = Order::with('user')->orderBy('created_at', 'desc')->get();

        return $this->exportCsvStream('bao-cao-don-hang.csv', $headers, $orders, function ($order) {
            $paymentMethodText = match ($order->payment_method) {
                'bank_transfer' => 'Chuyển khoản',
                'momo' => 'Ví MoMo',
                default => 'Tiền mặt (COD)',
            };
            $orderTypeText = $order->order_type === 'single' ? 'Món lẻ' : 'Gói dài kỳ';

            return [
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
            ];
        });
    }

    // =========================================================================
    // QUẢN LÝ ĐÁNH GIÁ ĐƠN HÀNG (REVIEWS)
    // =========================================================================

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

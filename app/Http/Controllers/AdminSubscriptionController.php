<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    // Danh sách các gói đăng ký
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'package', 'order'])->orderBy('created_at', 'desc')->get();

        return view('admin.sub_packages', compact('subscriptions'));
    }

    // Chi tiết đăng ký và nhật ký thực đơn
    public function show($id)
    {
        $subscription = Subscription::with(['user', 'package', 'order', 'dailySchedules.dish'])->findOrFail($id);

        // Sắp xếp nhật ký thực đơn theo ngày giao thực tế
        $schedules = $subscription->dailySchedules()->orderBy('delivery_date', 'asc')->get();

        return view('admin.sub_packages_detail', compact('subscription', 'schedules'));
    }

    // Giao diện chỉnh sửa trạng thái/phân bổ món
    public function edit($id)
    {
        $subscription = Subscription::with(['user', 'package', 'order'])->findOrFail($id);
        $schedules = $subscription->dailySchedules()->orderBy('delivery_date', 'asc')->get();

        return view('admin.sub_packages_edit', compact('subscription', 'schedules'));
    }

    // Lưu cập nhật trạng thái/phân bổ món
    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:pending,active,paused,expired,cancelled',
            'payment_status' => 'required|string|in:pending,paid,failed,refunded',
            'menu_day' => 'nullable|array',
        ]);

        // Cập nhật trạng thái gói
        $subscription->status = $request->status;
        if ($request->status === 'cancelled' || $request->status === 'expired') {
            $subscription->remaining_days = 0;
        }
        $subscription->save();

        // Cập nhật trạng thái tài chính đơn hàng gốc
        if ($subscription->order) {
            $subscription->order->payment_status = $request->payment_status;
            $subscription->order->save();
        }

        // Cập nhật thực đơn điều phối món ăn theo ngày (chỉ cho các ngày chưa giao và chưa bị khóa)
        if ($request->has('menu_day')) {
            $schedules = $subscription->dailySchedules()->orderBy('delivery_date', 'asc')->get();
            foreach ($request->menu_day as $dayIndex => $dishName) {
                // Chỉ số mảng bắt đầu từ 1, danh sách dailySchedules bắt đầu từ 0
                if (isset($schedules[$dayIndex - 1])) {
                    $schedule = $schedules[$dayIndex - 1];
                    if (! $schedule->is_locked && $schedule->delivery_status === 'pending') {
                        // Tìm hoặc tạo món ăn mới
                        $dish = Dish::firstOrCreate(
                            ['dish_name' => trim($dishName)],
                            [
                                'category_id' => Category::value('id') ?? 1,
                                'price' => 0.00,
                                'is_available' => true,
                                'description' => 'Món ăn tự chọn của gói dịch vụ',
                            ]
                        );
                        $schedule->dish_id = $dish->id;
                        $schedule->save();
                    }
                }
            }
        }

        return redirect()->route('quanly_goidangky')->with('success', 'Cập nhật phân bổ gói dịch vụ thành công!');
    }

    // Tạo vận đơn giao hàng của hôm nay (nếu có lịch)
    public function createDailyOrder(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        // Hỗ trợ giả lập lịch giao hàng hôm nay bằng cách lấy lịch có trạng thái pending đầu tiên nếu không có lịch đúng ngày hôm nay
        $schedule = $subscription->dailySchedules()
            ->where('delivery_status', 'pending')
            ->orderBy('delivery_date', 'asc')
            ->first();

        if (! $schedule) {
            return back()->with('error', 'Không tìm thấy lịch giao hàng chờ xử lý nào cho gói này.');
        }

        // Kích hoạt giao hàng
        $schedule->delivery_status = 'delivered';
        $schedule->is_locked = true;
        $schedule->save();

        // Giảm số ngày còn lại
        if ($subscription->remaining_days > 0) {
            $subscription->remaining_days -= 1;
            if ($subscription->remaining_days == 0) {
                $subscription->status = 'expired';
            }
            $subscription->save();
        }

        if ($subscription->order) {
            try {
                event(new \App\Events\OrderUpdated($subscription->order, 'daily_dispatch'));
            } catch (\Exception $broadcastException) {
                \Illuminate\Support\Facades\Log::warning('Broadcasting failed: ' . $broadcastException->getMessage());
            }
        }

        return back()->with('success', 'Đã khởi tạo vận đơn giao món ăn "'.$schedule->dish->dish_name.'" thành công!');
    }

    // Xóa đăng ký gói
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->dailySchedules()->delete();
        $subscription->pauses()->delete();
        $subscription->delete();

        return redirect()->route('quanly_goidangky')->with('success', 'Đã xóa đăng ký gói dịch vụ thành công!');
    }
}

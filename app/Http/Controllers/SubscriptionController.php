<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Review;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\SubscriptionPause;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    // Hiển thị danh sách gói đăng ký của khách hàng
    public function index()
    {
        $user = Auth::user();

        // Lấy danh sách các subscription của người dùng
        $subscriptions = Subscription::where('user_id', $user->id)
            ->with(['package', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Lấy tất cả món ăn có sẵn để người dùng lựa chọn đổi món
        $dishes = Dish::where('is_available', true)->get();

        // Xử lý thông tin hiển thị động cho từng subscription
        foreach ($subscriptions as $subscription) {
            $schedules = $subscription->dailySchedules()->orderBy('delivery_date', 'asc')->get();

            // Tìm ngày hiện tại dựa trên số ngày đã giao (số record có trạng thái delivered)
            $completedDays = $schedules->where('delivery_status', 'delivered')->count();

            // Tiến độ hiện tại: Ngày hiện tại là (số ngày đã giao + 1)
            $currentDayNumber = min($schedules->count(), $completedDays + 1);

            // Tìm lịch giao hàng của ngày mai (là ngày tiếp theo sau completedDays)
            $tomorrowSchedule = null;
            $tomorrowDayNumber = $currentDayNumber;

            // Tìm ngày giao có trạng thái pending đầu tiên
            $tomorrowSchedule = $schedules->where('delivery_status', 'pending')->first();
            if ($tomorrowSchedule) {
                // Xác định số thứ tự ngày của tomorrowSchedule
                foreach ($schedules as $index => $sch) {
                    if ($sch->id === $tomorrowSchedule->id) {
                        $tomorrowDayNumber = $index + 1;
                        break;
                    }
                }
            }

            // Gắn dữ liệu động vào đối tượng subscription để dùng ngoài view
            $subscription->schedules_list = $schedules;
            $subscription->completed_days = $completedDays;
            $subscription->current_day_number = $currentDayNumber;
            $subscription->tomorrow_schedule = $tomorrowSchedule;
            $subscription->tomorrow_day_number = $tomorrowDayNumber;

            // Tính phần trăm tiến độ
            $totalDays = max(1, $schedules->count());
            $subscription->progress_percent = round(($completedDays / $totalDays) * 100);
        }

        return view('client.sub_packages', compact('subscriptions', 'dishes'));
    }

    // Đổi món ăn ngày tiếp theo (ngày mai)
    public function changeMenu(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer',
            'day_number' => 'required|integer|min:1',
            'new_dish_id' => 'required|integer|exists:dishes,id',
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $schedules = $subscription->dailySchedules()->orderBy('delivery_date', 'asc')->get();
        $dayIndex = $request->day_number - 1;

        if (! isset($schedules[$dayIndex])) {
            return back()->withErrors(['error' => 'Không tìm thấy lịch giao hàng tương ứng với ngày yêu cầu.']);
        }

        $schedule = $schedules[$dayIndex];

        if ($schedule->is_locked || $schedule->delivery_status !== 'pending') {
            return back()->withErrors(['error' => 'Không thể đổi món cho ngày này vì lịch giao hàng đã bị khóa hoặc đã giao thành công.']);
        }

        $schedule->dish_id = $request->new_dish_id;
        $schedule->save();

        return back()->with('success', 'Đổi món ăn cho ngày '.$request->day_number.' thành công!');
    }

    // Ghi chú giao hàng cho ngày tiếp theo
    public function addNote(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer',
            'day_number' => 'required|integer|min:1',
            'day_note' => 'required|string',
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $schedules = $subscription->dailySchedules()->orderBy('delivery_date', 'asc')->get();
        $dayIndex = $request->day_number - 1;

        if (! isset($schedules[$dayIndex])) {
            return back()->withErrors(['error' => 'Không tìm thấy lịch giao hàng tương ứng với ngày yêu cầu.']);
        }

        $schedule = $schedules[$dayIndex];

        if ($schedule->is_locked || $schedule->delivery_status !== 'pending') {
            return back()->withErrors(['error' => 'Không thể cập nhật ghi chú cho ngày này vì lịch giao hàng đã bị khóa hoặc đã giao thành công.']);
        }

        $schedule->delivery_notes = $request->day_note;
        $schedule->save();

        return back()->with('success', 'Đã lưu ghi chú giao hàng cho ngày '.$request->day_number.' thành công!');
    }

    // Tạm ngưng dịch vụ
    public function pause(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer',
            'pause_days' => 'required|string',
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($subscription->status !== 'active') {
            return back()->withErrors(['error' => 'Chỉ có thể tạm ngưng gói dịch vụ đang hoạt động.']);
        }

        // Cập nhật trạng thái
        $subscription->status = 'paused';
        $subscription->save();

        // Ghi lại thông tin pause
        $days = 3;
        if ($request->pause_days === '7') {
            $days = 7;
        } elseif ($request->pause_days === 'unknown') {
            $days = 30; // tạm đóng băng tối đa 30 ngày
        }

        SubscriptionPause::create([
            'subscription_id' => $subscription->id,
            'pause_start_date' => Carbon::now()->addDay(),
            'pause_end_date' => Carbon::now()->addDays($days),
        ]);

        // Cập nhật ghi chú trên đơn hàng gốc
        if ($subscription->order) {
            $order = $subscription->order;
            $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').
                '[Tạm ngưng dịch vụ - '.$days.' ngày kể từ '.Carbon::now()->addDay()->format('d/m/Y').']';
            $order->save();
        }

        return back()->with('success', 'Yêu cầu tạm ngưng gói dịch vụ của bạn đã được tiếp nhận thành công!');
    }

    // Hủy ngang gói dịch vụ dài hạn
    public function cancel(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer',
            'cancel_reason' => 'required|string|min:50',
            'cancel_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'bank_user' => 'nullable|string',
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($subscription->status === 'cancelled' || $subscription->status === 'expired') {
            return back()->withErrors(['error' => 'Gói dịch vụ này đã dừng hoạt động từ trước.']);
        }

        // Hủy gói dịch vụ
        $remainingDays = $subscription->remaining_days;
        $subscription->status = 'cancelled';
        $subscription->remaining_days = 0;
        $subscription->save();

        // Ghi nhận lý do hủy & yêu cầu hoàn tiền vào Order gốc
        if ($subscription->order) {
            $order = $subscription->order;

            // Giả lập tính số tiền hoàn trả: còn bao nhiêu ngày nhân với đơn giá ngày
            $refundAmount = round(($order->total_amount / max(1, $subscription->dailySchedules()->count())) * $remainingDays, 2);

            $imageUrl = null;
            if ($request->hasFile('cancel_image')) {
                $file = $request->file('cancel_image');
                $filename = time().'_'.$file->getClientOriginalName();
                if (!file_exists(public_path('uploads/cancels'))) {
                    mkdir(public_path('uploads/cancels'), 0777, true);
                }
                $file->move(public_path('uploads/cancels'), $filename);
                $imageUrl = 'uploads/cancels/'.$filename;
            }

            $imageLink = $imageUrl ? url($imageUrl) : 'Không có';
            $refundText = '[Yêu cầu hoàn tiền - Lý do: Hủy gói ('.$request->cancel_reason.'), Hình ảnh minh chứng: '.$imageLink.', Phương thức: Chuyển khoản (Số tiền hoàn lại ước tính: '.$refundAmount.')';
            if ($request->filled('bank_name')) {
                $refundText .= ', Ngân hàng: ' . $request->bank_name . ', STK: ' . $request->bank_account . ', Chủ tài khoản: ' . $request->bank_user;
            }
            $refundText .= ']';
            
            $order->health_notes = ($order->health_notes ? $order->health_notes."\n" : '').$refundText;
            $order->save();
        }

        return back()->with('success', 'Đã hủy gói dịch vụ thành công! Hệ thống sẽ xử lý hoàn lại tiền cho các ngày ăn chưa sử dụng.');
    }

    // Viết đánh giá chất lượng dịch vụ
    public function review(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|integer',
            'service_stars' => 'required|integer|between:1,5',
            'service_comment' => 'required|string',
        ]);

        $subscription = Subscription::where('id', $request->subscription_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Tạo review dựa trên order gốc của gói
        $exists = Review::where('order_id', $subscription->order_id)->exists();
        if ($exists) {
            return back()->withErrors(['error' => 'Bạn đã gửi đánh giá cho gói dịch vụ này trước đây.']);
        }

        Review::create([
            'user_id' => Auth::id(),
            'order_id' => $subscription->order_id,
            'rating' => $request->service_stars,
            'comment' => $request->service_comment,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá chất lượng gói dịch vụ ăn uống của chúng tôi!');
    }

    // Đăng ký mua gói dịch vụ từ modal trang chủ
    public function buyPackage(Request $request)
    {
        $request->validate([
            'package_type' => 'required',
            'package_duration' => 'required|integer|in:7,14,30',
            'start_date' => 'required|date|after_or_equal:today',
            'delivery_slot' => 'required|string|in:morning,noon,evening',
            'sub_phone' => 'required|string',
            'sub_email' => 'required|email',
            'sub_address' => 'required|string',
            'sub_payment_method' => 'required|string|in:cash,bank_transfer',
        ]);

        $servicePackage = null;
        if (is_numeric($request->package_type)) {
            $servicePackage = ServicePackage::where('id', $request->package_type)->first();
        } elseif ($request->package_type === 'family') {
            $servicePackage = ServicePackage::where('package_name', 'like', '%Gia Đình%')->first();
        }

        if (!$servicePackage) {
            $servicePackage = ServicePackage::first();
        }

        if (! $servicePackage) {
            return back()->withErrors(['error' => 'Gói dịch vụ được chọn không khả dụng hoặc đã bị ngừng hoạt động.']);
        }

        // Tính giá tiền: giá gốc * (số ngày chọn / số ngày chuẩn của gói)
        $totalAmount = round($servicePackage->price * ($request->package_duration / $servicePackage->duration_days), 2);

        $deliverySlotLabels = [
            'morning' => 'Sáng sớm (07:00 - 08:30)',
            'noon' => 'Trưa văn phòng (11:00 - 12:30)',
            'evening' => 'Chiều tối muộn (17:30 - 19:00)',
        ];
        $slotLabel = $deliverySlotLabels[$request->delivery_slot] ?? $request->delivery_slot;

        DB::beginTransaction();
        try {
            $paymentMethod = $request->sub_payment_method;
            $orderStatus = 'confirmed';
            $paymentStatus = 'paid';
            $subscriptionStatus = 'active';

            if ($paymentMethod === 'bank_transfer') {
                $paymentStatus = 'pending';
                $orderStatus = 'pending';
                $subscriptionStatus = 'pending';
            }

            // Tạo hóa đơn gói dịch vụ
            $order = new Order;
            $order->user_id = Auth::id();
            $order->order_type = 'subscription';
            $order->total_amount = $totalAmount;
            $order->final_amount = $totalAmount;
            $order->payment_method = $paymentMethod;
            $order->payment_status = $paymentStatus;
            $order->order_status = $orderStatus;
            $order->health_notes = "Gói dịch vụ: {$servicePackage->package_name}. Khung giờ: {$slotLabel}. SĐT: {$request->sub_phone}. Địa chỉ: {$request->sub_address}";
            $order->save();

            // Tạo đăng ký subscription
            $subscription = new Subscription;
            $subscription->order_id = $order->id;
            $subscription->user_id = Auth::id();
            $subscription->package_id = $servicePackage->id;
            $subscription->start_date = $request->start_date;
            $subscription->end_date = Carbon::parse($request->start_date)->addDays($request->package_duration - 1);
            $subscription->remaining_days = $request->package_duration;
            $subscription->status = $subscriptionStatus;
            $subscription->save();

            // Sinh lịch giao hàng hàng ngày và chọn ngẫu nhiên món ăn cho mỗi ngày
            $dishes = Dish::where('is_available', true)->get();
            if ($dishes->isEmpty()) {
                $dishes = Dish::all();
            }

            for ($day = 0; $day < $request->package_duration; $day++) {
                $deliveryDate = Carbon::parse($request->start_date)->addDays($day);

                $schedule = new DailySchedule;
                $schedule->subscription_id = $subscription->id;
                $schedule->delivery_date = $deliveryDate->format('Y-m-d');
                $schedule->dish_id = $dishes->isNotEmpty() ? $dishes->random()->id : 1; // dự phòng món ăn đầu tiên
                $schedule->delivery_status = 'pending';
                $schedule->is_locked = false;
                $schedule->save();
            }

            DB::commit();

            return redirect()->route('goidichvu')->with('success', "Đăng ký thành công gói dịch vụ \"{$servicePackage->package_name}\"!");

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Đã xảy ra lỗi trong quá trình đăng ký gói dịch vụ: '.$e->getMessage()]);
        }
    }
}

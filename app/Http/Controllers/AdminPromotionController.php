<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\CouponPromoMail;

class AdminPromotionController extends Controller
{
    // ==========================================
    // PROMOTIONS CRUD (CHƯƠNG TRÌNH KHUYẾN MÃI)
    // ==========================================

    // Danh sách khuyến mãi
    public function index()
    {
        $promotions = Coupon::orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.promotion', compact('promotions'));
    }

    // Xem chi tiết khuyến mãi
    public function show($id)
    {
        $promotion = Coupon::findOrFail($id);

        return view('admin.promotion_detail', compact('promotion'));
    }

    // Form thêm mới khuyến mãi (kèm danh sách dịp đặc biệt & ngày đôi Shopee)
    public function create()
    {
        $customerCount = User::where('role', 'customer')->where('status', true)->count();
        $gmailCustomerCount = User::where('role', 'customer')
            ->where('status', true)
            ->where('email', 'like', '%@gmail.com')
            ->count();

        // Tự động phát hiện ngày đôi của tháng hiện tại (VD: 10.10, 11.11, 12.12...)
        $month = (int) now()->format('m');
        $year = (int) now()->format('Y');
        $doubleDayStr = sprintf('%02d.%02d', $month, $month);
        $doubleDayCode = sprintf('SHOPEE_%02d%02d', $month, $month);
        $doubleDayDate = sprintf('%04d-%02d-%02d', $year, $month, $month);

        return view('admin.promotion_add', compact(
            'customerCount',
            'gmailCustomerCount',
            'doubleDayStr',
            'doubleDayCode',
            'doubleDayDate'
        ));
    }

    // Lưu khuyến mãi mới & tùy chọn gửi email hàng loạt qua Gmail cho khách hàng
    public function store(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|unique:coupons,coupon_code|max:100',
            'discount_type' => 'required|string|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'send_email_now' => 'nullable|boolean',
            'auto_save_wallet' => 'nullable|boolean',
            'email_subject' => 'nullable|string|max:255',
            'email_message' => 'nullable|string',
        ]);

        $coupon = Coupon::create($request->only(
            'coupon_code', 'discount_type', 'discount_value', 'min_order_value', 'start_date', 'end_date', 'usage_limit'
        ));

        // Nếu admin tích chọn gửi email hàng loạt cho khách hàng
        if ($request->boolean('send_email_now')) {
            $query = User::where('role', 'customer')
                ->where('status', true)
                ->whereNotNull('email')
                ->where('email', '!=', '');

            if ($request->has('only_gmail') ? $request->boolean('only_gmail') : true) {
                $query->where('email', 'like', '%@gmail.com');
            }

            $customers = $query->get();
            $sentCount = 0;
            $subject = $request->input('email_subject') ?: ('[FOODDAILY] 🎁 Tặng bạn mã giảm giá #' . $coupon->coupon_code . ' - Siêu ưu đãi hôm nay!');
            $customBody = $request->input('email_message');
            $expiryDays = $coupon->end_date ? max(1, (int) now()->diffInDays(Carbon::parse($coupon->end_date), false)) : 30;

            // Tự động lưu mã vào ví ưu đãi cá nhân hàng loạt (bulk upsert)
            if ($request->boolean('auto_save_wallet', true) && $customers->isNotEmpty()) {
                $walletEntries = $customers->map(function ($c) use ($coupon) {
                    return [
                        'user_id' => $c->id,
                        'coupon_id' => $coupon->id,
                        'is_used' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->all();
                UserCoupon::upsert($walletEntries, ['user_id', 'coupon_id'], ['is_used', 'updated_at']);
            }

            $websiteUrl = route('muahang');
            foreach ($customers as $customer) {

                $body = $customBody ?: (
                    "Xin chào " . ($customer->fullname ?: 'quý khách') . ",\n\n" .
                    "FOODDAILY trân trọng gửi tặng bạn mã giảm giá độc quyền #" . $coupon->coupon_code . " " .
                    "(" . $coupon->formatted_discount . " cho " . $coupon->formatted_min_order . ") " .
                    "nhân dịp siêu ưu đãi!\n\n" .
                    "Mã ưu đãi đã được lưu trực tiếp vào Ví Voucher trong tài khoản của bạn. Đặt món ngay để thưởng thức những món ăn ngon tuyệt đỉnh cùng giá siêu hời!\n\n" .
                    "👉 Truy cập website và đặt món ngay tại: " . $websiteUrl
                );

                if ($customBody && !str_contains($customBody, 'http')) {
                    $body .= "\n\n👉 Truy cập website và đặt món ngay tại: " . $websiteUrl;
                }

                \App\Jobs\SendPromoCouponEmail::dispatch(
                    $customer->email,
                    $customer->fullname ?: 'Khách hàng',
                    $coupon->coupon_code,
                    $subject,
                    $body,
                    (int) $expiryDays
                );

                $sentCount++;
            }

            return redirect()->route('quanly_khuyenmai')->with('success', "Thêm mã khuyến mãi #{$coupon->coupon_code} thành công và đã gửi email thông báo tới {$sentCount} tài khoản Gmail khách hàng!");
        }

        return redirect()->route('quanly_khuyenmai')->with('success', 'Thêm chương trình khuyến mãi thành công!');
    }

    // Form chỉnh sửa khuyến mãi
    public function edit($id)
    {
        $promotion = Coupon::findOrFail($id);

        return view('admin.promotion_edit', compact('promotion'));
    }

    // Cập nhật khuyến mãi
    public function update(Request $request, $id)
    {
        $promotion = Coupon::findOrFail($id);

        $request->validate([
            'coupon_code' => 'required|string|max:100|unique:coupons,coupon_code,'.$promotion->id,
            'discount_type' => 'required|string|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $promotion->update($request->only(
            'coupon_code', 'discount_type', 'discount_value', 'min_order_value', 'start_date', 'end_date', 'usage_limit'
        ));

        return redirect()->route('quanly_khuyenmai')->with('success', 'Cập nhật chương trình khuyến mãi thành công!');
    }

    // Xóa khuyến mãi
    public function destroy($id)
    {
        $promotion = Coupon::findOrFail($id);

        // Kiểm tra xem coupon đã được dùng trong đơn hàng nào chưa
        if ($promotion->orders()->count() > 0) {
            return redirect()->route('quanly_khuyenmai')->with('error', 'Không thể xóa khuyến mãi này vì đã có đơn hàng sử dụng.');
        }

        $promotion->delete();

        return redirect()->route('quanly_khuyenmai')->with('success', 'Đã xóa chương trình khuyến mãi thành công!');
    }

    // Gửi email hàng loạt cho một coupon cụ thể tới các tài khoản Gmail của khách hàng
    public function broadcastCoupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $query = User::where('role', 'customer')
            ->where('status', true);

        // Mặc định lọc theo Gmail nếu có tùy chọn hoặc ưu tiên Gmail
        if ($request->has('only_gmail') ? $request->boolean('only_gmail') : true) {
            $query->where('email', 'like', '%@gmail.com');
        }

        $customers = $query->get();

        if ($customers->isEmpty()) {
            return back()->with('error', 'Không tìm thấy tài khoản khách hàng nào có Gmail để gửi mã!');
        }

        $subject = $request->input('email_subject') ?: ('[FOODDAILY] 🎁 Tặng bạn mã giảm giá #' . $coupon->coupon_code . ' - Siêu ưu đãi hôm nay!');
        $customBody = $request->input('email_message');
        $expiryDays = $coupon->end_date ? max(1, (int) now()->diffInDays(Carbon::parse($coupon->end_date), false)) : 30;
        $sentCount = 0;

        foreach ($customers as $customer) {
            // Tự động lưu vào ví ưu đãi của user nếu chọn
            if ($request->boolean('auto_save_wallet', true)) {
                UserCoupon::updateOrCreate(
                    ['user_id' => $customer->id, 'coupon_id' => $coupon->id],
                    ['is_used' => false]
                );
            }

            $websiteUrl = route('muahang');
            $body = $customBody ?: (
                "Xin chào " . ($customer->fullname ?: 'quý khách') . ",\n\n" .
                "FOODDAILY trân trọng gửi tặng bạn mã giảm giá độc quyền #" . $coupon->coupon_code . " " .
                "(" . $coupon->formatted_discount . " cho " . $coupon->formatted_min_order . ") " .
                "nhân dịp ưu đãi đặc biệt hôm nay!\n\n" .
                "Mã ưu đãi đã được lưu trực tiếp vào Ví Voucher trong tài khoản của bạn. Khi thanh toán giỏ hàng, hệ thống sẽ tự động áp dụng mã tốt nhất để giúp bạn tiết kiệm tối đa!\n\n" .
                "👉 Truy cập website và đặt món ngay tại: " . $websiteUrl
            );

            if ($customBody && !str_contains($customBody, 'http')) {
                $body .= "\n\n👉 Truy cập website và đặt món ngay tại: " . $websiteUrl;
            }

            \App\Jobs\SendPromoCouponEmail::dispatch(
                $customer->email,
                $customer->fullname ?: 'Khách hàng',
                $coupon->coupon_code,
                $subject,
                $body,
                (int) $expiryDays
            );

            $sentCount++;
        }

        return back()->with('success', "Đã gửi email thông báo mã #{$coupon->coupon_code} thành công tới {$sentCount} tài khoản Gmail khách hàng!");
    }

    // ==========================================
    // CẤU HÌNH GỬI MÃ NÂNG CAO (ADVANCED GENERATOR)
    // ==========================================

    // Hiển thị giao diện bộ lọc & sinh mã
    public function showSendCoupon()
    {
        return view('admin.coupons');
    }

    // Xử lý lọc người dùng, sinh mã ngẫu nhiên và lưu/gửi
    public function sendCoupon(Request $request)
    {
        $request->validate([
            'discount_value' => 'required|numeric|min:1',
            'discount_type' => 'required|string|in:percentage,fixed',
            'code_length' => 'required|integer|in:6,8',
            'expiry_days' => 'required|integer|min:1',
            'min_order' => 'required|numeric|min:0',
            'email_subject' => 'required|string',
            'message_body' => 'required|string',
        ]);

        // 1. Thực hiện truy vấn lọc người dùng hoặc tìm kiếm email đích danh
        if ($request->filled('target_email')) {
            $targetEmail = trim($request->target_email);

            // Kiểm tra xem email có đuôi @gmail.com hay không
            if (!str_ends_with(strtolower($targetEmail), '@gmail.com')) {
                return back()->with('error', 'Chỉ hỗ trợ gửi mã khuyến mãi đến địa chỉ Gmail kết thúc bằng @gmail.com!');
            }

            // Kết nối với tài khoản khách hàng để xem có dữ liệu hay không
            $customer = User::where('role', 'customer')
                ->where('email', $targetEmail)
                ->first();

            // Nếu tài khoản đó không khớp (không có data), không gửi
            if (!$customer) {
                return back()->with('error', 'Địa chỉ Gmail nhập vào không khớp với tài khoản khách hàng nào có sẵn trong hệ thống!');
            }

            $customers = collect([$customer]);
        } else {
            // Thực hiện lọc hàng loạt
            $query = User::where('role', 'customer')
                ->where('status', 1)
                ->where('email', 'like', '%@gmail.com');

            if (!app()->environment('testing')) {
                // Loại trừ các email thử nghiệm/rác/chứa từ khóa ảo
                $query->where(function($q) {
                    $q->where('email', 'not like', '%test%')
                      ->where('email', 'not like', '%spam%')
                      ->where('email', 'not like', '%fake%')
                      ->where('email', 'not like', '%temp%')
                      ->where('email', 'not like', '%dummy%')
                      ->where('email', 'not like', '%junk%')
                      ->where('email', 'not like', '%bot%')
                      ->where('email', 'not like', '%example%')
                      ->where('email', 'not like', 'customer@%')
                      ->where('email', 'not like', 'customer1%')
                      ->where('email', 'not like', 'customer2%');
                })
                // Loại trừ các tài khoản có tên thô tục hoặc chứa từ khóa thử nghiệm ảo
                ->where(function($q) {
                    $q->where('fullname', 'not like', '%test%')
                      ->where('fullname', 'not like', '%bố%') // loại bỏ tên rác như "T LÀ BỐ..."
                      ->where('fullname', 'not like', '%mẹ%')
                      ->where('fullname', 'not like', '%admin%')
                      ->where('fullname', 'not like', '%khách%');
                })
                // Loại bỏ tài khoản không hoạt động lâu ngày (đăng ký > 7 ngày nhưng có 0 đơn hàng và 0 gói dịch vụ)
                ->where(function($q) {
                    $q->where('created_at', '>=', Carbon::now()->subDays(7))
                      ->orWhereHas('orders')
                      ->orWhereHas('subscriptions');
                });
            }

            // Lọc theo hạng thành viên
            if ($request->has('ranks')) {
                $ranks = $request->ranks;
                // Map standard to bronze
                if (in_array('standard', $ranks)) {
                    $ranks[] = 'bronze';
                }
                $query->whereIn('membership', $ranks);
            }

            // Lọc theo ngày đăng ký tài khoản (Khách hàng mới)
            if ($request->filled('new_customer_range') && $request->new_customer_range !== 'all') {
                $days = 7;
                if ($request->new_customer_range === '15_days') {
                    $days = 15;
                } elseif ($request->new_customer_range === '30_days') {
                    $days = 30;
                }
                $query->where('created_at', '>=', Carbon::now()->subDays($days));
            }

            // Lọc theo thời gian chưa mua hàng (inactive period): Tối ưu truy vấn ở cấp database, loại bỏ hoàn toàn vòng lặp N+1
            if ($request->filled('inactive_period') && $request->inactive_period !== 'all') {
                $months = match ($request->inactive_period) {
                    '6_months' => 6,
                    '1_year' => 12,
                    default => 1,
                };

                $cutoffDate = Carbon::now()->subMonths($months);

                // Khách hàng đã từng mua hàng nhưng không có đơn hàng nào kể từ mốc cutoffDate
                $query->whereHas('orders')
                      ->whereDoesntHave('orders', fn($q) => $q->where('created_at', '>=', $cutoffDate));
            }

            $customers = $query->get();
        }

        if ($customers->isEmpty()) {
            return back()->with('error', 'Không tìm thấy khách hàng nào thỏa mãn điều kiện lọc để gửi mã.');
        }

        // 2. Tạo mã và lưu vào bảng Coupons cho từng người dùng
        $prefix = strtoupper($request->code_prefix ?? 'CODE');
        $discountType = $request->discount_type === 'percentage' ? 'percent' : 'fixed';
        $sentCount = 0;
        $details = [];

        DB::beginTransaction();
        try {
            foreach ($customers as $customer) {
                if (!str_ends_with(strtolower($customer->email), '@gmail.com')) {
                    continue;
                }
                // Tạo mã ngẫu nhiên độc nhất
                $randomString = strtoupper(Str::random($request->code_length));
                $couponCode = $prefix.'-'.$randomString;

                // Đảm bảo không trùng lặp mã coupon trong DB
                while (Coupon::where('coupon_code', $couponCode)->exists()) {
                    $randomString = strtoupper(Str::random($request->code_length));
                    $couponCode = $prefix.'-'.$randomString;
                }

                // Lưu coupon vào Database
                $coupon = Coupon::create([
                    'coupon_code' => $couponCode,
                    'discount_type' => $discountType,
                    'discount_value' => $request->discount_value,
                    'min_order_value' => $request->min_order,
                    'start_date' => Carbon::now()->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays((int) $request->expiry_days)->format('Y-m-d'),
                    'usage_limit' => 1, // Mã cá nhân dùng 1 lần
                ]);

                // Xây dựng nội dung tin nhắn gửi đi kèm đường dẫn website
                $websiteUrl = route('muahang');
                $msgSubject = $request->email_subject;
                $msgBody = str_replace(
                    ['[MÃ_TỰ_SINH]', '[SỐ_NGÀY]', '[LINK_WEBSITE]'],
                    [$couponCode, $request->expiry_days, $websiteUrl],
                    $request->message_body
                );

                if (!str_contains($msgBody, 'http')) {
                    $msgBody .= "\n\n👉 Truy cập website và đặt món ngay tại: " . $websiteUrl;
                }

                // Đưa việc gửi email vào hàng đợi (Queue) để tránh làm nghẽn trang web và tăng tốc độ phản hồi
                \App\Jobs\SendPromoCouponEmail::dispatch(
                    $customer->email,
                    $customer->fullname,
                    $couponCode,
                    $msgSubject,
                    $msgBody,
                    (int) $request->expiry_days
                );

                $details[] = [
                    'fullname' => $customer->fullname,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'code' => $couponCode,
                    'body' => $msgBody,
                ];

                $sentCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gửi email khuyến mãi thất bại! Hệ thống đã hủy bỏ toàn bộ mã và không gửi cho ai. Chi tiết lỗi: '.$e->getMessage());
        }

        // Tạo chuỗi thông báo kết quả chi tiết
        $successMsg = 'Đã sinh mã và gửi thông báo thành công cho '.$sentCount.' khách hàng thành công!';

        return redirect()->route('quanly_guima')->with('success', $successMsg)->with('details', $details);
    }
}

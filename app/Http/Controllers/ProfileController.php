<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang Hồ Sơ Cá Nhân
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'info'); // info, address, account, member

        // Lấy danh sách voucher người dùng đã lưu và voucher hệ thống còn hiệu lực
        $savedCoupons = $user->savedCoupons()->wherePivot('is_used', false)->get();
        $allCoupons = Coupon::active()->orderByDesc('discount_value')->get();

        // Thống kê đơn hàng và chi tiêu
        $ordersCount = Order::where('user_id', $user->id)->count();
        $completedOrdersCount = Order::where('user_id', $user->id)->where('order_status', 'completed')->count();
        $totalSpent = Order::where('user_id', $user->id)->where('order_status', 'completed')->sum('final_amount');

        // Chi tiết hạng thành viên
        $membershipDetails = $user->membership_details;

        return view('client.profile', compact(
            'user',
            'tab',
            'savedCoupons',
            'allCoupons',
            'ordersCount',
            'completedOrdersCount',
            'totalSpent',
            'membershipDetails'
        ));
    }

    /**
     * Cập nhật Thông Tin Cá Nhân (Ảnh đại diện, Họ tên, SĐT, Ngày sinh, Giới tính)
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fullname' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'avatar_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'avatar_url' => 'nullable|string|max:500',
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên của bạn.',
            'avatar_file.image' => 'Tệp đại diện phải là hình ảnh hợp lệ.',
            'avatar_file.max' => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $data = [
            'fullname' => $request->fullname,
            'phone' => $request->phone,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
        ];

        // Xử lý upload ảnh đại diện
        if ($request->hasFile('avatar_file')) {
            $avatarUrl = null;

            // Thử upload lên Cloudinary nếu có cấu hình
            try {
                if (class_exists(\App\Services\CloudinaryService::class)) {
                    $cloudinary = app(\App\Services\CloudinaryService::class);
                    $avatarUrl = $cloudinary->upload($request->file('avatar_file'));
                }
            } catch (\Exception $e) {
                Log::warning('Cloudinary avatar upload failed: ' . $e->getMessage());
            }

            // Fallback lưu local trong thư mục public/uploads/avatars/
            if (!$avatarUrl) {
                $dir = public_path('uploads/avatars');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                $file = $request->file('avatar_file');
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $filename);
                $avatarUrl = '/uploads/avatars/' . $filename;
            }

            $data['avatar'] = $avatarUrl;
        } elseif ($request->filled('avatar_url')) {
            $data['avatar'] = $request->avatar_url;
        }

        $user->update($data);

        return redirect()->route('hoso', ['tab' => 'info'])
            ->with('success', 'Cập nhật thông tin cá nhân thành công!');
    }

    /**
     * Cập nhật Địa Chỉ Giao Hàng (Nhà, Công ty, Khác và Địa chỉ mặc định)
     */
    public function updateAddress(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'home_address' => 'nullable|string|max:500',
            'company_address' => 'nullable|string|max:500',
            'other_address' => 'nullable|string|max:500',
            'default_address_type' => 'required|in:home,company,other',
        ]);

        $user->address = $request->home_address;
        $user->company_address = $request->company_address;
        $user->other_address = $request->other_address;
        $user->default_address_type = $request->default_address_type;
        $user->save();

        return redirect()->route('hoso', ['tab' => 'address'])
            ->with('success', 'Đã lưu cấu hình sổ địa chỉ giao hàng thành công!');
    }

    /**
     * Đổi Mật Khẩu
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có tối thiểu 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withInput()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không chính xác.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('hoso', ['tab' => 'account'])
            ->with('success', 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới của bạn.');
    }

    /**
     * Cập nhật Bảo Mật Tài Khoản (Bật / Tắt 2FA)
     */
    public function updateSecurity(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = $request->boolean('two_factor_enabled');
        $user->save();

        $msg = $user->two_factor_enabled 
            ? 'Đã kích hoạt chế độ bảo vệ tài khoản nâng cao!' 
            : 'Đã cập nhật cài đặt bảo mật tài khoản.';

        return redirect()->route('hoso', ['tab' => 'account'])
            ->with('success', $msg);
    }
}

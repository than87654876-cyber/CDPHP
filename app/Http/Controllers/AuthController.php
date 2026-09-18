<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{
    // Hiển thị trang đăng nhập dùng chung
    public function showLogin()
    {
        return view('client.login'); // Sử dụng giao diện đẹp của FOODELICIOUS làm trang đăng nhập duy nhất
    }

    // Xử lý đăng nhập dùng chung (Email hoặc SĐT)
    public function login(Request $request)
    {
        $request->validate([
            'login_input' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('login_input');
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Xác định loại đăng nhập (Email hay SĐT)
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        // Tìm kiếm người dùng
        $user = User::where($field, $loginInput)->first();

        if ($user && Hash::check($password, $user->password)) {
            if (!$user->status) {
                return back()->withErrors(['login_input' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.']);
            }

            Auth::login($user, $remember);
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors(['login_input' => 'Thông tin đăng nhập hoặc mật khẩu không chính xác.'])->withInput();
    }

    // Chuyển hướng người dùng sang trang xác thực Google (Laravel Socialite)
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // Nhận dữ liệu phản hồi từ Google sau khi xác thực (Laravel Socialite Callback)
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $email = $googleUser->getEmail();
            $fullname = $googleUser->getName() ?? 'Google User';

            if (!$email) {
                return view('client.auth_callback', [
                    'error' => 'Không thể lấy được địa chỉ email từ tài khoản Google của bạn.',
                ]);
            }

            // Tìm hoặc tự động tạo tài khoản thành viên mới
            $user = User::where('email', $email)->first();

            if ($user && $user->role === 'guest') {
                $user->update([
                    'fullname' => $fullname,
                    'role' => 'customer',
                    'status' => true,
                ]);
            } elseif (!$user) {
                $user = User::create([
                    'fullname' => $fullname,
                    'email' => $email,
                    'phone' => null,
                    'password' => Hash::make(Str::random(16)),
                    'role' => 'customer',
                    'status' => true,
                ]);
            }

            if (!$user->status) {
                return view('client.auth_callback', [
                    'error' => 'Tài khoản Google này hiện đang bị khóa.',
                ]);
            }

            Auth::login($user, true);

            $targetUrl = in_array($user->role, ['admin', 'staff']) 
                ? ($user->role === 'admin' ? route('quanly') : route('quanly_banlamviec'))
                : route('trangchu');

            return view('client.auth_callback', [
                'targetUrl' => $targetUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('Google Socialite callback login error: ' . $e->getMessage(), ['exception' => $e]);
            return view('client.auth_callback', [
                'error' => 'Có lỗi xảy ra trong quá trình xác thực đăng nhập Google.',
            ]);
        }
    }


    // Hiển thị đăng ký (chỉ cho client/khách hàng)
    public function showClientRegister()
    {
        if (Auth::check()) {
            return redirect()->route('trangchu');
        }

        return view('client.register');
    }

    // Xử lý đăng ký client/khách hàng
    public function clientRegister(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'accepted',
        ]);

        // Kiểm tra xem Email/SĐT có trùng với các vai trò đã đăng ký chính thức không
        $emailExists = User::where('email', $request->email)->where('role', '!=', 'guest')->exists();
        $phoneExists = User::where('phone', $request->phone)->where('role', '!=', 'guest')->exists();

        if ($emailExists) {
            return back()->withErrors(['email' => 'Email này đã được đăng ký tài khoản thành viên.'])->withInput();
        }
        if ($phoneExists) {
            return back()->withErrors(['phone' => 'Số điện thoại này đã được đăng ký tài khoản thành viên.'])->withInput();
        }

        // Tìm tài khoản guest ẩn (nếu có)
        $guestUser = User::where('role', 'guest')
            ->where(function($q) use ($request) {
                $q->where('email', $request->email)->orWhere('phone', $request->phone);
            })->first();

        if ($guestUser) {
            // Nâng cấp tài khoản guest thành customer để gộp lịch sử đơn hàng
            $guestUser->update([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'status' => true,
                'notes' => 'Địa chỉ: ' . $request->address,
            ]);
            $user = $guestUser;
        } else {
            $user = User::create([
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'status' => true,
                'notes' => 'Địa chỉ: ' . $request->address,
            ]);
        }

        Auth::login($user);

        try {
            Mail::to($user->email)->send(new WelcomeMail($user->fullname));
        } catch (\Exception $e) {
            Log::warning('Welcome email sending warning: ' . $e->getMessage());
        }

        return redirect()->route('trangchu')->with('success', 'Đăng ký tài khoản thành công! Lịch sử đơn hàng trước đây của bạn đã được liên kết.');
    }


    // Đăng xuất dùng chung
    public function logout()
    {
        Auth::logout();
        return redirect()->route('trangchu')->with('clear_cart', true);
    }

    // Hàm phụ chuyển hướng dựa trên vai trò
    protected function redirectBasedOnRole($user)
    {
        if (in_array($user->role, ['admin', 'staff'])) {
            if ($user->role === 'admin') {
                return redirect()->route('quanly');
            }
            return redirect()->route('quanly_banlamviec');
        }

        return redirect()->route('trangchu');
    }

    // Gửi OTP reset mật khẩu cho khách hàng
    public function sendClientResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->where('role', 'customer')->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email này không tồn tại hoặc không thuộc tài khoản thành viên.']);
        }
        
        // Sinh OTP gồm 6 chữ số
        $otp = sprintf('%06d', mt_rand(100000, 999999));
        
        $user->update([
            'password_reset_token' => $otp,
            'password_reset_expires_at' => now()->addMinutes(10), // Có hiệu lực trong 10 phút
        ]);
        
        try {
            $phpMailer = app(\App\Services\PHPMailerService::class);
            $emailBody = view('emails.reset_otp', [
                'fullname' => $user->fullname,
                'otp' => $otp
            ])->render();
            
            $phpMailer->send($user->email, 'Mã OTP khôi phục mật khẩu - FOODELICIOUS 🔑', $emailBody);
        } catch (\Exception $e) {
            Log::error('Send reset password OTP email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi email lúc này. Vui lòng thử lại sau.']);
        }
        
        return redirect()->route('trangchu/quenmatkhau/xacnhan', ['email' => $user->email])
            ->with('success', 'Mã OTP khôi phục mật khẩu đã được gửi vào email của bạn!');
    }

    // Hiển thị trang xác nhận mã OTP của khách hàng
    public function showClientOtpVerify(Request $request)
    {
        return view('client.otp_verify');
    }

    // Xử lý xác nhận OTP và đổi mật khẩu mới cho khách hàng
    public function verifyClientOtpAndResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        $user = User::where('email', $request->email)->where('role', 'customer')->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản thành viên.'])->withInput();
        }
        
        if ($user->password_reset_token !== $request->otp) {
            return back()->withErrors(['otp' => 'Mã OTP xác thực không chính xác.'])->withInput();
        }
        
        if ($user->password_reset_expires_at && now()->isAfter($user->password_reset_expires_at)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn sử dụng. Vui lòng yêu cầu lại.'])->withInput();
        }
        
        // Cập nhật mật khẩu mới và xóa thông tin OTP
        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
        
        return redirect()->route('dangnhap')->with('success', 'Đổi mật khẩu thành công! Hãy đăng nhập bằng mật khẩu mới.');
    }

    // Gửi link reset mật khẩu cho Admin/Staff
    public function sendAdminResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->whereIn('role', ['admin', 'staff'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email này không tồn tại hoặc không thuộc quyền quản trị.']);
        }
        
        $token = Str::random(60);
        $user->update([
            'password_reset_token' => $token,
            'password_reset_expires_at' => now()->addMinutes(60),
        ]);
        
        $resetLink = route('doimatkhau', ['token' => $token]);
        
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user->fullname, $resetLink));
        } catch (\Exception $e) {
            Log::error('Send reset password email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi email lúc này. Vui lòng thử lại sau.']);
        }
        
        return back()->with('success', 'Chúng tôi đã gửi liên kết khôi phục mật khẩu vào email của bạn!');
    }

    // Hiển thị trang nhập mật khẩu mới cho khách hàng
    public function showClientResetPassword($token)
    {
        $user = User::where('password_reset_token', $token)
            ->where('password_reset_expires_at', '>', now())
            ->first();
            
        if (!$user) {
            return redirect()->route('dangnhap')->withErrors(['login_input' => 'Liên kết khôi phục mật khẩu đã hết hạn hoặc không hợp lệ. Vui lòng thử lại.']);
        }
        
        return view('client.password_reset', compact('token'));
    }


    // Xử lý đổi mật khẩu mới cho khách hàng
    public function resetClientPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        $user = User::where('password_reset_token', $request->token)
            ->where('password_reset_expires_at', '>', now())
            ->first();
            
        if (!$user) {
            return redirect()->route('dangnhap')->withErrors(['login_input' => 'Token không hợp lệ hoặc đã hết hạn.']);
        }
        
        $user->update([
            'password' => Hash::make($request->password),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);
        
        return redirect()->route('dangnhap')->with('success', 'Đổi mật khẩu thành công! Hãy đăng nhập bằng mật khẩu mới.');
    }

}


@extends('layouts.app')

@section('title', 'Đăng nhập - FOODDAILY')

@section('content')
<div class="py-20 bg-[#f5f5f5] min-h-[calc(100vh-160px)] flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Clean Card Wrapper -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50 border border-slate-200/80 space-y-6">
            
            <!-- Header Card -->
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-xl font-bold mx-auto border border-rose-100 shadow-xs">
                    <i class="fas fa-user-lock"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Chào mừng quay lại!</h2>
                <p class="text-xs text-slate-500 font-medium">Nhập thông tin để truy cập tài khoản FOODDAILY</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('dangnhap.post') }}" method="POST" autocomplete="off" class="space-y-4">
                @csrf
                <div>
                    <label for="login_input" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Email hoặc Số điện thoại <span class="text-emerald-600">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm pointer-events-none">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="text" id="login_input" name="login_input" value="{{ old('login_input') }}" required placeholder="nhapemail@example.com hoặc 090..." class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-sm font-semibold transition-all outline-none">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Mật khẩu <span class="text-emerald-600">*</span>
                        </label>
                        <a href="{{ route('trangchu/quenmatkhau') }}" class="text-xs font-bold text-emerald-600 hover:underline">Quên mật khẩu?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-3 rounded-2xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-sm font-semibold transition-all outline-none">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 text-sm">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500/20 border-slate-300">
                        <span class="text-xs font-medium text-slate-600">Ghi nhớ phiên đăng nhập</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all">
                    ĐĂNG NHẬP
                </button>
            </form>

            <div class="relative flex py-1 items-center">
                <div class="flex-grow border-t border-slate-200"></div>
                <span class="flex-shrink mx-4 text-[11px] font-extrabold text-slate-400 uppercase tracking-widest">Hoặc</span>
                <div class="flex-grow border-t border-slate-200"></div>
            </div>

            <!-- Google Login Button -->
            <a href="{{ route('auth.google') }}" class="w-full py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold flex items-center justify-center gap-2 shadow-xs transition-all">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 48 48">
                    <g>
                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                        <path fill="#4285F4" d="M46.5 24c0-1.55-.15-3.24-.47-4.77H24v9.03h12.75c-.53 2.85-2.14 5.27-4.56 6.88l7.11 5.51C43.46 36.56 46.5 30.93 46.5 24z"></path>
                        <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24s.92 7.54 2.56 10.78l7.98-6.19z"></path>
                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.11-5.51c-1.97 1.32-4.5 2.12-8.78 2.12-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                    </g>
                </svg>
                Đăng nhập nhanh với Google
            </a>

            <p class="text-center text-xs text-slate-500 font-medium">
                Bạn chưa có tài khoản? 
                <a href="{{ route('trangchu/dangky') }}" class="font-extrabold text-emerald-600 hover:underline">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('togglePassword')?.addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - FOODDAILY')

@section('content')
<div class="py-20 bg-[#f5f5f5] min-h-[calc(100vh-160px)] flex items-center justify-center px-4 sm:px-6">
    <div class="max-w-xl w-full">
        <!-- Clean Card Wrapper -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50 border border-slate-200/80 space-y-6">
            
            <!-- Form Header -->
            <div class="text-center space-y-2 border-b border-slate-100 pb-6">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-xl font-bold mx-auto border border-rose-100 shadow-xs">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Tạo Tài Khoản Mới</h2>
                <p class="text-xs text-slate-500 font-medium">Đăng ký thành viên để bắt đầu trải nghiệm món ăn tươi ngon</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Registration Form -->
            <form action="{{ route('trangchu/dangky.post') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="fullname" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Họ và Tên <span class="text-emerald-600">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm pointer-events-none">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="fullname" name="fullname" value="{{ old('fullname') }}" placeholder="Ví dụ: Nguyễn Văn A" class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Số điện thoại <span class="text-emerald-600">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm pointer-events-none">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="0912345678" class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa chỉ Email <span class="text-emerald-600">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm pointer-events-none">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa chỉ giao hàng mặc định <span class="text-emerald-600">*</span></label>
                    <textarea id="address" name="address" rows="2" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..." class="w-full p-3.5 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mật khẩu <span class="text-emerald-600">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Xác nhận mật khẩu <span class="text-emerald-600">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 select-none">
                    <input type="checkbox" id="terms" name="terms" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/20" required>
                    <label for="terms" class="text-xs text-slate-600 font-medium">Tôi đồng ý với các <a href="#" class="text-emerald-600 font-bold hover:underline">Điều khoản dịch vụ & Chính sách bảo mật</a></label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all">
                    TẠO TÀI KHOẢN NGAY
                </button>
            </form>

            <div class="text-center text-xs text-slate-500 font-medium pt-2 border-t border-slate-100">
                <span>Đã có tài khoản?</span>
                <a href="{{ route('dangnhap') }}" class="font-extrabold text-emerald-600 hover:underline ml-1">Đăng nhập tại đây</a>
            </div>
        </div>
    </div>
</div>
@endsection

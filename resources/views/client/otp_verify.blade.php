@extends('layouts.app')

@section('title', 'Xác thực OTP - FOODDAILY')

@section('content')
<div class="py-20 bg-slate-50 min-h-[calc(100vh-160px)] flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <!-- Clean Card Wrapper -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-200/50 border border-slate-200/80 space-y-6">
            
            <!-- Header Card -->
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mx-auto border border-emerald-100 shadow-xs">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Xác Thực Mã OTP</h2>
                <p class="text-xs text-slate-500 font-medium">Nhập mã OTP 6 số để tạo mật khẩu mới</p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                    <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                    <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('trangchu/quenmatkhau/xacnhan.post') }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email khôi phục</label>
                    <input type="email" id="email" name="email" value="{{ old('email', request('email')) }}" class="w-full px-4 py-3 rounded-2xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-600 cursor-not-allowed outline-none" readonly required>
                </div>

                <div>
                    <label for="otp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã xác thực OTP (6 số) <span class="text-emerald-600">*</span></label>
                    <input type="text" id="otp" name="otp" placeholder="123456" maxlength="6" pattern="\d{6}" class="w-full text-center tracking-[0.5em] font-mono text-2xl py-3 rounded-2xl border border-slate-200 text-slate-900 font-extrabold focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required autocomplete="one-time-code">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mật khẩu mới <span class="text-emerald-600">*</span></label>
                        <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Xác nhận <span class="text-emerald-600">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all">
                    XÁC NHẬN ĐỔI MẬT KHẨU
                </button>
            </form>

            <div class="text-center text-xs text-slate-500 font-medium pt-2 border-t border-slate-100">
                <a href="{{ route('dangnhap') }}" class="font-extrabold text-emerald-600 hover:underline inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Quay lại Đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

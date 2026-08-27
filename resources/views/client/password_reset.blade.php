@extends('layouts.app')

@section('title', 'Đổi lại mật khẩu - FOODDAILY')

@section('content')
<div class="min-h-screen py-16 bg-slate-50 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-100 space-y-6">
        
        <!-- Form Header -->
        <div class="text-center space-y-2 border-b border-slate-100 pb-6">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl food-gradient text-white text-xl shadow-md shadow-rose-500/20 mb-1">
                <i class="fas fa-lock"></i>
            </span>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thiết Lập Mật Khẩu Mới</h2>
            <p class="text-xs text-slate-500">Nhập mật khẩu mới bảo mật cho tài khoản của bạn</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('trangchu/doimatkhau.post') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Mật khẩu mới <span class="text-rose-500">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20" required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Xác nhận mật khẩu <span class="text-rose-500">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20" required>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 rounded-2xl food-gradient text-white font-extrabold text-xs shadow-xl shadow-rose-500/20 hover:scale-[1.01] active:scale-[0.99] transition-all">
                CẬP NHẬT MẬT KHẨU
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 pt-2 border-t border-slate-100">
            <a href="{{ route('trangchu/dangnhap') }}" class="font-extrabold text-rose-500 hover:text-rose-600 inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Quay lại Đăng nhập
            </a>
        </div>
    </div>
</div>
@endsection

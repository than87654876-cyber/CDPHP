@extends('layouts.admin')

@section('title', 'Chỉnh sửa Khách hàng - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Cập Nhật Khách Hàng</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] font-extrabold border border-rose-200">
                    KH-{{ sprintf('%03d', $customer->id) }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Điều chỉnh thông tin tài khoản, điểm thưởng và hạng thành viên</p>
        </div>
        <a href="{{ route('khachhang_xem', $customer->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại chi tiết
        </a>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                <i class="fas fa-user-pen"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Biểu mẫu điều chỉnh thông tin thành viên</h3>
                <p class="text-[11px] text-slate-400 font-medium">Mã thành viên cố định: <strong class="text-slate-800">KH-{{ sprintf('%03d', $customer->id) }}</strong></p>
            </div>
        </div>

        <form action="{{ route('khachhang_chinhsua.post', $customer->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Họ và tên -->
                <div class="space-y-2 sm:col-span-2">
                    <label for="fullname" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Họ và Tên khách hàng <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="fullname" name="fullname" value="{{ old('fullname', $customer->fullname) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Số điện thoại -->
                <div class="space-y-2">
                    <label for="phone" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Số điện thoại liên lạc <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Địa chỉ Email <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Điểm tích lũy -->
                <div class="space-y-2">
                    <label for="points" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Điểm thưởng tích lũy
                    </label>
                    <input type="number" id="points" name="points" value="{{ old('points', $customer->points) }}" min="0" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black text-emerald-600 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Phân hạng thành viên -->
                <div class="space-y-2">
                    <label for="membership" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Phân hạng thành viên
                    </label>
                    <select id="membership" name="membership" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="bronze" {{ old('membership', $customer->membership) === 'bronze' ? 'selected' : '' }}>🥉 Đồng (Bronze)</option>
                        <option value="silver" {{ old('membership', $customer->membership) === 'silver' ? 'selected' : '' }}>🥈 Bạc (Silver)</option>
                        <option value="gold" {{ old('membership', $customer->membership) === 'gold' ? 'selected' : '' }}>👑 Vàng (Gold)</option>
                        <option value="diamond" {{ old('membership', $customer->membership) === 'diamond' ? 'selected' : '' }}>💎 Kim Cương (Diamond)</option>
                    </select>
                </div>

                <!-- Ghi chú -->
                <div class="space-y-2 sm:col-span-2">
                    <label for="notes" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Địa chỉ nhận hàng / Ghi chú nội bộ
                    </label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Nhập địa chỉ giao hàng mặc định hoặc ghi chú về khách hàng..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-medium text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('khachhang_xem', $customer->id) }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                    <i class="fas fa-save mr-1.5"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

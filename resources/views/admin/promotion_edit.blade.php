@extends('layouts.admin')

@section('title', 'Chỉnh sửa Mã khuyến mãi - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Chỉnh Sửa Mã Khuyến Mãi</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Cập nhật thông số mã ưu đãi: <strong class="text-[#ee4d2d] uppercase">{{ $promotion->coupon_code }}</strong></p>
        </div>
        <a href="{{ route('quanly_khuyenmai') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại
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
                <i class="fas fa-ticket"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Biểu mẫu cập nhật mã khuyến mãi</h3>
                <p class="text-[11px] text-slate-400 font-medium">Mã ID hệ thống: #{{ $promotion->id }}</p>
            </div>
        </div>

        <form action="{{ route('khuyenmai_chinhsua.post', $promotion->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Mã khuyến mãi -->
                <div class="space-y-2">
                    <label for="coupon_code" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Mã khuyến mãi (Coupon Code) <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="coupon_code" name="coupon_code" value="{{ old('coupon_code', $promotion->coupon_code) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black uppercase text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Loại giảm giá -->
                <div class="space-y-2">
                    <label for="discount_type" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Loại chiết khấu <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="discount_type" name="discount_type" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="percent" {{ old('discount_type', $promotion->discount_type) === 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type', $promotion->discount_type) === 'fixed' ? 'selected' : '' }}>Giảm tiền cố định (đ)</option>
                    </select>
                </div>

                <!-- Mức giảm giá -->
                <div class="space-y-2">
                    <label for="discount_value" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Mức giảm giá <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="number" step="1" min="0" id="discount_value" name="discount_value" value="{{ old('discount_value', (int)$promotion->discount_value) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black text-rose-600 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Đơn tối thiểu -->
                <div class="space-y-2">
                    <label for="min_order_value" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Đơn tối thiểu áp dụng (đ) <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="number" step="1" min="0" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', (int)$promotion->min_order_value) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Ngày bắt đầu -->
                <div class="space-y-2">
                    <label for="start_date" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Ngày bắt đầu áp dụng <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $promotion->start_date ? $promotion->start_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                </div>

                <!-- Ngày kết thúc -->
                <div class="space-y-2">
                    <label for="end_date" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Ngày kết thúc áp dụng <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $promotion->end_date ? $promotion->end_date->format('Y-m-d') : '') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                </div>

                <!-- Giới hạn lượt dùng -->
                <div class="space-y-2 sm:col-span-2">
                    <label for="usage_limit" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Giới hạn số lần sử dụng (Bỏ trống nếu không giới hạn)
                    </label>
                    <input type="number" min="1" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" placeholder="Ví dụ: 100 (Để trống = Vô hạn)" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('quanly_khuyenmai') }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
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

@extends('layouts.admin')

@section('title', 'Chi tiết Mã khuyến mãi - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Chi Tiết Mã Khuyến Mãi</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] font-extrabold border border-rose-200 uppercase">
                    {{ $promotion->coupon_code }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thông số chiết khấu, thời hạn áp dụng và điều kiện sử dụng</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_khuyenmai') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('khuyenmai_chinhsua', $promotion->id) }}" class="px-4 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Chỉnh sửa mã
            </a>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                <i class="fas fa-ticket"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Thông tin chương trình ưu đãi</h3>
                <p class="text-[11px] text-slate-400 font-medium">Mã ID hệ thống: #{{ $promotion->id }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Mã ưu đãi (Coupon Code)</p>
                <p class="text-lg font-black text-[#ee4d2d] mt-1 tracking-wider uppercase">{{ $promotion->coupon_code }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Mức giảm giá</p>
                <p class="text-lg font-black text-emerald-700 mt-1">
                    @if($promotion->discount_type === 'percent')
                        {{ (int)$promotion->discount_value }}% <span class="text-xs font-semibold text-slate-400">(Giảm theo %)</span>
                    @else
                        {{ number_format($promotion->discount_value, 0, ',', '.') }}đ <span class="text-xs font-semibold text-slate-400">(Giảm tiền cố định)</span>
                    @endif
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Giá trị đơn hàng tối thiểu</p>
                <p class="text-base font-black text-slate-800 mt-1">{{ number_format($promotion->min_order_value, 0, ',', '.') }}đ</p>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Giới hạn số lần dùng</p>
                <p class="text-base font-black text-slate-800 mt-1">{{ $promotion->usage_limit ? number_format($promotion->usage_limit) . ' lần' : 'Vô hạn' }}</p>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Thời hạn áp dụng</p>
                <div class="flex items-center gap-2 mt-1.5 text-xs font-bold text-slate-700">
                    <i class="far fa-calendar-alt text-[#ee4d2d]"></i>
                    <span>Từ ngày {{ $promotion->start_date ? $promotion->start_date->format('d/m/Y') : 'N/A' }}</span>
                    <i class="fas fa-arrow-right text-[10px] text-slate-400 mx-1"></i>
                    <span>Đến hết ngày {{ $promotion->end_date ? $promotion->end_date->format('d/m/Y') : 'N/A' }}</span>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ngày tạo lập hệ thống</p>
                <p class="text-xs font-bold text-slate-600 mt-1">{{ $promotion->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

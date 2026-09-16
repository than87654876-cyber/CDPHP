@extends('layouts.admin')

@section('title', 'Chi tiết Khách hàng - FOODDAILY Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Hồ Sơ Khách Hàng</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] font-extrabold border border-rose-200">
                    KH-{{ sprintf('%03d', $customer->id) }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thông tin tài khoản hội viên, điểm tích lũy và lịch sử mua sắm</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_khachhang') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('khachhang_chinhsua', $customer->id) }}" class="px-4 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Sửa thông tin
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Profile Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6 text-center">
            <div class="w-20 h-20 rounded-full bg-gradient-to-tr from-rose-500 to-amber-400 text-white flex items-center justify-center text-2xl font-black mx-auto shadow-md shadow-rose-500/20">
                {{ mb_substr($customer->fullname, 0, 1) }}
            </div>
            
            <div class="space-y-1">
                <h3 class="font-extrabold text-slate-900 text-base">{{ $customer->fullname }}</h3>
                <p class="text-xs text-slate-400 font-semibold">KH-{{ sprintf('%03d', $customer->id) }}</p>
                <div class="pt-2">
                    @if($customer->membership === 'diamond')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-xs font-black border border-cyan-200">
                            💎 Kim Cương
                        </span>
                    @elseif($customer->membership === 'gold')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-black border border-amber-200">
                            👑 Vàng (Gold)
                        </span>
                    @elseif($customer->membership === 'silver')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-black border border-slate-200">
                            🥈 Bạc (Silver)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50/60 text-amber-800 text-xs font-extrabold border border-amber-100">
                            🥉 Đồng (Bronze)
                        </span>
                    @endif
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 grid grid-cols-2 gap-2 text-center">
                <div class="p-3 bg-slate-50 rounded-2xl">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase">Điểm thưởng</p>
                    <p class="text-base font-black text-emerald-600 mt-0.5">{{ number_format($customer->points) }}</p>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase">Đơn hàng</p>
                    <p class="text-base font-black text-slate-800 mt-0.5">{{ $customer->orders()->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Detail Info Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Thông tin liên hệ & Hồ sơ chi tiết</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Họ và tên</p>
                    <p class="text-sm font-black text-slate-900 mt-1">{{ $customer->fullname }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Số điện thoại</p>
                    <p class="text-sm font-black text-slate-800 mt-1">{{ $customer->phone ?? 'Chưa cập nhật' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Địa chỉ Email</p>
                    <p class="text-sm font-extrabold text-slate-800 mt-1">{{ $customer->email }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Địa chỉ nhận hàng / Ghi chú</p>
                    <p class="text-xs font-medium text-slate-700 mt-1 leading-relaxed">{{ $customer->notes ?? 'Chưa có ghi chú địa chỉ' }}</p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ngày tham gia hệ thống</p>
                    <p class="text-xs font-semibold text-slate-600 mt-1">{{ $customer->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

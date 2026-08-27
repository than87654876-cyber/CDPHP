@extends('layouts.admin')

@section('title', 'Quản lý Chương trình Khuyến Mãi - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Chương Trình Khuyến Mãi & Voucher</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quản lý mã giảm giá, mức chiết khấu và thời hạn áp dụng voucher</p>
        </div>

        <a href="{{ route('khuyenmai_them') }}" class="px-5 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Thêm khuyến mãi mới
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-ticket text-[#ee4d2d]"></i> Danh sách mã giảm giá & voucher
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6">STT</th>
                        <th class="py-4 px-6">Mã Voucher</th>
                        <th class="py-4 px-6">Điều kiện áp dụng</th>
                        <th class="py-4 px-6">Mức giảm</th>
                        <th class="py-4 px-6">Thời hạn</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($promotions as $index => $promo)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-400">#{{ $index + 1 }}</td>
                            <td class="py-4 px-6 font-extrabold text-[#ee4d2d]">
                                {{ $promo->coupon_code }}
                            </td>
                            <td class="py-4 px-6 text-slate-700 font-medium">
                                Đơn tối thiểu: {{ number_format($promo->min_order_value) }}đ
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-[#ee4d2d] text-[11px] font-extrabold border border-rose-200">
                                    {{ $promo->discount_type === 'percent' ? 'Giảm ' . number_format($promo->discount_value) . '%' : 'Giảm ' . number_format($promo->discount_value) . 'đ' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium">
                                {{ $promo->start_date->format('d/m/Y') }} - {{ $promo->end_date->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('khuyenmai_xem', $promo->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('khuyenmai_chinhsua', $promo->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('khuyenmai_xoa', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-colors">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-medium">Chưa có mã khuyến mãi nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

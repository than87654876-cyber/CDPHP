@extends('layouts.admin')

@section('title', 'Quản lý Khách hàng - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Danh Sách Khách Hàng</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quản lý tài khoản, lịch sử mua hàng và xuất báo cáo khách hàng</p>
        </div>

        <a href="{{ route('baocao_xuat_customers', request()->query()) }}" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
            <i class="fas fa-file-excel"></i> Xuất Báo Cáo Excel
        </a>
    </div>

    <!-- Filter Buttons Pill Bar -->
    <div class="flex flex-wrap items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
        <a href="{{ route('quanly_khachhang') }}" class="px-4 py-2 rounded-xl border transition-all {{ !isset($filter) || !$filter ? 'bg-[#ee4d2d] text-white border-[#ee4d2d]' : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' }}">
            Tất cả khách hàng
        </a>
        <a href="{{ route('quanly_khachhang', ['filter' => 'first_order']) }}" class="px-4 py-2 rounded-xl border transition-all {{ isset($filter) && $filter === 'first_order' ? 'bg-[#ee4d2d] text-white border-[#ee4d2d]' : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' }}">
            Đã từng đặt hàng
        </a>
        <a href="{{ route('quanly_khachhang', ['filter' => 'active_package']) }}" class="px-4 py-2 rounded-xl border transition-all {{ isset($filter) && $filter === 'active_package' ? 'bg-[#ee4d2d] text-white border-[#ee4d2d]' : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' }}">
            Đang dùng gói combo
        </a>
        <a href="{{ route('quanly_khachhang', ['filter' => 'refunded']) }}" class="px-4 py-2 rounded-xl border transition-all {{ isset($filter) && $filter === 'refunded' ? 'bg-[#ee4d2d] text-white border-[#ee4d2d]' : 'bg-white text-slate-700 hover:bg-slate-100 border-slate-200' }}">
            Đã từng hoàn tiền
        </a>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-users text-[#ee4d2d]"></i> Khách hàng đã đăng ký tài khoản
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6">Mã KH</th>
                        <th class="py-4 px-6">Họ và Tên</th>
                        <th class="py-4 px-6">Số điện thoại</th>
                        <th class="py-4 px-6">Email</th>
                        <th class="py-4 px-6">Hạng thành viên</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-400">KH-{{ sprintf('%03d', $customer->id) }}</td>
                            <td class="py-4 px-6 font-extrabold text-slate-900">
                                {{ $customer->fullname ?? $customer->name }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-700">
                                {{ $customer->phone ?? 'Chưa cập nhật' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $customer->email }}
                            </td>
                            <td class="py-4 px-6">
                                @if($customer->membership === 'diamond')
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 text-[10px] font-black border border-rose-200">💎 Kim cương</span>
                                @elseif($customer->membership === 'gold')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-black border border-amber-200">🥇 Vàng</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">Thường</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('khachhang_xem', $customer->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('khachhang_chinhsua', $customer->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('khachhang_xoa', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa tài khoản khách hàng này?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-colors">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 font-medium">Chưa có khách hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

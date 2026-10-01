@extends('layouts.admin')

@section('title', 'Lịch Sử Phiếu Nhập Kho - FOODDAILY Admin')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black">
                    <i class="fas fa-file-invoice"></i> Quy Trình Nhập Hàng
                </span>
                <span>•</span>
                <span>Lịch sử các đợt nhập hàng từ đối tác</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fas fa-file-invoice-dollar text-emerald-600 text-3xl"></i> Lịch Sử Phiếu Nhập Hàng
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Quản lý các đợt nhập hàng từ đơn vị cung cấp, ngày giờ nhận hàng, chi phí và nghiệm thu kho bãi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('nhaphang_them') }}" class="px-5 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-[#d73211] text-white text-xs font-black transition-all shadow-md shadow-rose-500/20 flex items-center gap-2">
                <i class="fas fa-plus"></i> Tạo Phiếu Nhập Mới
            </a>
            <a href="{{ route('quanly_kho') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-blue-600"></i> Xem Hàng Tồn Kho
            </a>
            <a href="{{ route('quanly_nhacungcap') }}" class="px-4 py-2.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold transition-all shadow-2xs flex items-center gap-2">
                <i class="fas fa-truck-field text-amber-500"></i> Nhà Cung Cấp
            </a>
        </div>
    </div>

    <!-- Alert thông báo -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- 2 Thẻ Thống Kê Chi Phí Nhập Hàng -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tổng tiền nhập hàng trong tháng {{ Carbon\Carbon::now()->format('m/Y') }}</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ number_format($monthReceiptsCost, 0, ',', '.') }} <span class="text-xs font-bold text-slate-400">VNĐ</span></h3>
                <p class="text-[11px] font-semibold text-emerald-600 mt-1">Đã tính vào chi phí giá vốn vận hành</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tổng chi phí nhập hàng toàn hệ thống</p>
                <h3 class="text-2xl font-black text-[#ee4d2d] mt-1">{{ number_format($totalReceiptsCost, 0, ',', '.') }} <span class="text-xs font-bold text-rose-400">VNĐ</span></h3>
                <p class="text-[11px] font-semibold text-slate-500 mt-1">Lịch sử toàn bộ các phiếu nhập</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-[#ee4d2d] flex items-center justify-center text-xl font-bold">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('quanly_nhaphang') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            
            <!-- Mã phiếu -->
            <div class="md:col-span-4 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm theo mã phiếu nhập (PNK-...)" 
                       class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
            </div>

            <!-- Nhà cung cấp -->
            <div class="md:col-span-3">
                <select name="supplier_id" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30">
                    <option value="">Tất cả Nhà Cung Cấp</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Từ ngày -->
            <div class="md:col-span-2">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:outline-hidden">
            </div>

            <!-- Đến ngày -->
            <div class="md:col-span-2">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-3 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700 focus:outline-hidden">
            </div>

            <!-- Nút Lọc -->
            <div class="md:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-bold transition-all shadow-xs flex items-center justify-center">
                    <i class="fas fa-filter"></i>
                </button>
                @if(request()->anyFilled(['q', 'supplier_id', 'from_date', 'to_date']))
                    <a href="{{ route('quanly_nhaphang') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs transition-colors flex items-center justify-center" title="Xóa lọc">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bảng Danh Sách Phiếu Nhập Kho -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-5">Mã Phiếu Nhập</th>
                        <th class="py-4 px-5">Đơn Vị Cung Cấp</th>
                        <th class="py-4 px-4">Ngày Nhập Hàng</th>
                        <th class="py-4 px-4">Người Nhận Kho</th>
                        <th class="py-4 px-4 text-center">Số Mặt Hàng</th>
                        <th class="py-4 px-5 text-right">Tổng Giá Trị</th>
                        <th class="py-4 px-4 text-center">Thanh Toán</th>
                        <th class="py-4 px-5 text-center">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($receipts as $receipt)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        
                        <!-- Mã phiếu -->
                        <td class="py-4 px-5">
                            <a href="{{ route('nhaphang_xem', $receipt->id) }}" class="font-mono font-extrabold text-blue-600 hover:underline text-[13px] block">
                                {{ $receipt->receipt_code }}
                            </a>
                            <span class="text-[10px] text-slate-400 block font-sans">Phiếu nhập kho</span>
                        </td>

                        <!-- Nhà cung cấp -->
                        <td class="py-4 px-5">
                            <span class="font-extrabold text-slate-900 block leading-tight">
                                {{ $receipt->supplier->name ?? 'Đơn vị không xác định' }}
                            </span>
                            @if($receipt->supplier && $receipt->supplier->phone)
                                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-phone-alt text-[9px] text-[#ee4d2d]"></i> {{ $receipt->supplier->phone }}
                                </span>
                            @endif
                        </td>

                        <!-- Ngày nhập -->
                        <td class="py-4 px-4 text-slate-700 font-semibold">
                            {{ $receipt->receipt_date->format('d/m/Y H:i') }}
                        </td>

                        <!-- Người nhận -->
                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-800">
                                {{ $receipt->creator->name ?? 'Admin' }}
                            </span>
                            <span class="text-[10px] text-slate-400 block capitalize">
                                {{ $receipt->creator->role ?? 'Nhân viên' }}
                            </span>
                        </td>

                        <!-- Số lượng mặt hàng -->
                        <td class="py-4 px-4 text-center">
                            <span class="px-2.5 py-1 rounded-xl bg-slate-100 font-black text-slate-700 text-xs">
                                {{ $receipt->items->count() }} mặt hàng
                            </span>
                        </td>

                        <!-- Tổng tiền -->
                        <td class="py-4 px-5 text-right">
                            <span class="text-sm font-black text-emerald-600 block">
                                {{ number_format($receipt->total_amount, 0, ',', '.') }}đ
                            </span>
                        </td>

                        <!-- Trạng thái thanh toán -->
                        <td class="py-4 px-4 text-center">
                            @if($receipt->payment_status === 'paid')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-black uppercase">
                                    Đã chi tiền
                                </span>
                            @elseif($receipt->payment_status === 'debt')
                                <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[10px] font-black uppercase">
                                    Ghi nợ NCC
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase">
                                    Chưa thanh toán
                                </span>
                            @endif
                        </td>

                        <!-- Thao tác -->
                        <td class="py-4 px-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('nhaphang_xem', $receipt->id) }}" 
                                   class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 text-xs font-bold transition-colors flex items-center gap-1">
                                    <i class="fas fa-eye text-blue-500"></i> Xem
                                </a>
                                <a href="{{ route('nhaphang_in', $receipt->id) }}" target="_blank"
                                   class="p-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition-colors" title="In phiếu nhập">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <i class="fas fa-file-circle-xmark text-4xl mb-3 text-slate-300 block"></i>
                            <span class="font-bold text-sm">Chưa có phiếu nhập hàng nào!</span>
                            <p class="text-xs text-slate-400 mt-1">Bấm nút "Tạo Phiếu Nhập Mới" để bắt đầu ghi nhận đợt nhập hàng từ đối tác.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($receipts->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $receipts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

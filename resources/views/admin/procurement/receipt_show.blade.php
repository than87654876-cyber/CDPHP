@extends('layouts.admin')

@section('title', 'Chi Tiết Phiếu Nhập Kho #' . $receipt->receipt_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_nhaphang') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Phiếu Nhập Kho: {{ $receipt->receipt_code }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 text-[10px] font-black uppercase">
                        Đã nhập kho thành công
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Thời gian nhận hàng: {{ $receipt->receipt_date->format('d/m/Y H:i') }} • Người nhận: {{ $receipt->creator->name ?? 'Admin' }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('nhaphang_in', $receipt->id) }}" target="_blank" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-xs flex items-center gap-2">
                <i class="fas fa-print"></i> In Phiếu Nhập Kho
            </a>
            <a href="{{ route('nhaphang_them') }}" class="px-4 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-[#d73211] text-white text-xs font-bold transition-all shadow-md shadow-rose-500/20 flex items-center gap-2">
                <i class="fas fa-plus"></i> Tạo Phiếu Mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">&times;</button>
        </div>
    @endif

    <!-- Khung Hóa Đơn / Phiếu Nhập Kho Chuẩn -->
    <div class="bg-white p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-8 print:border-none print:shadow-none print:p-0">

        <!-- Header Phiếu -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#ee4d2d] to-[#ff6b4a] flex items-center justify-center text-white font-bold text-sm shadow-xs">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <span class="font-black text-lg text-slate-900 tracking-wider">FOODDAILY</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">Hệ Thống Suất Ăn & Cơm Văn Phòng Trực Tuyến</p>
                <p class="text-xs text-slate-500">Địa chỉ: Ký Túc Xá Khu B, ĐHQG-HCM, Dĩ An, Bình Dương</p>
                <p class="text-xs text-slate-500">Hotline Bếp: 1900 6868 • Email: contact@fooddaily.vn</p>
            </div>

            <div class="sm:text-right space-y-1">
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">PHIẾU NHẬP KHO HÀNG HÓA</h2>
                <p class="font-mono text-xs font-extrabold text-[#ee4d2d]">Mã phiếu: {{ $receipt->receipt_code }}</p>
                <p class="text-xs text-slate-500">Ngày lập phiếu: {{ $receipt->created_at->format('d/m/Y H:i:s') }}</p>
                <div class="mt-2 inline-block">
                    @if($receipt->payment_status === 'paid')
                        <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-black uppercase">
                            🟢 Đã thanh toán cho NCC
                        </span>
                    @elseif($receipt->payment_status === 'debt')
                        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[11px] font-black uppercase">
                            🟡 Ghi nợ đối tác
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-black uppercase">
                            Chưa thanh toán
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2 Khối Thông Tin Đối Tác & Người Nhận -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 p-5 rounded-2xl border border-slate-100 text-xs">
            <!-- Đơn vị cung cấp (Bên giao hàng) -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Đơn Vị Cung Cấp (Bên Giao Hàng)</span>
                <h4 class="text-sm font-black text-slate-900">{{ $receipt->supplier->name ?? 'Đơn vị không xác định' }}</h4>
                <p class="text-slate-600"><span class="text-slate-400 font-medium">Mã NCC:</span> {{ $receipt->supplier->supplier_code ?? '-' }}</p>
                <p class="text-slate-600"><span class="text-slate-400 font-medium">Hotline / Zalo:</span> <strong class="text-[#ee4d2d]">{{ $receipt->supplier->phone ?? '-' }}</strong></p>
                @if($receipt->supplier && $receipt->supplier->contact_person)
                    <p class="text-slate-600"><span class="text-slate-400 font-medium">Đại diện giao:</span> {{ $receipt->supplier->contact_person }}</p>
                @endif
                @if($receipt->supplier && $receipt->supplier->address)
                    <p class="text-slate-600"><span class="text-slate-400 font-medium">Địa chỉ:</span> {{ $receipt->supplier->address }}</p>
                @endif
            </div>

            <!-- Bên nhận (Kho cửa hàng) -->
            <div class="space-y-1.5 sm:border-l sm:border-slate-200 sm:pl-6">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">Bên Nhận Hàng (Bếp / Kho FOODDAILY)</span>
                <h4 class="text-sm font-black text-slate-900">Kho Bếp Trung Tâm FOODDAILY</h4>
                <p class="text-slate-600"><span class="text-slate-400 font-medium">Người kiểm nhận:</span> <strong>{{ $receipt->creator->name ?? 'Admin' }}</strong> ({{ $receipt->creator->email ?? '' }})</p>
                <p class="text-slate-600"><span class="text-slate-400 font-medium">Thời điểm giao nhận:</span> {{ $receipt->receipt_date->format('d/m/Y - H:i') }}</p>
                @if($receipt->notes)
                    <p class="text-slate-700 bg-white p-2.5 rounded-xl border border-slate-200/60 mt-1">
                        <strong class="text-slate-500 font-semibold">Ghi chú / HĐ đỏ:</strong> {{ $receipt->notes }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Bảng Chi Tiết Mặt Hàng Nhập -->
        <div class="space-y-3">
            <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Danh Mục Mặt Hàng Nghiệm Thu & Nhập Kho</h4>
            
            <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase">
                            <th class="py-3 px-4 text-center w-12">STT</th>
                            <th class="py-3 px-4">Tên Mặt Hàng / Nguyên Liệu</th>
                            <th class="py-3 px-4 text-center">Đơn Vị</th>
                            <th class="py-3 px-4 text-center">Số Lượng Thực Nhận</th>
                            <th class="py-3 px-4 text-right">Đơn Giá Nhập</th>
                            <th class="py-3 px-4 text-right">Thành Tiền</th>
                            <th class="py-3 px-4 text-center">Hạn Sử Dụng</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($receipt->items as $index => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-slate-900 block">{{ $item->item->name ?? 'Mặt hàng đã xóa' }}</span>
                                <span class="text-[10px] font-mono text-slate-400 uppercase">Mã: {{ $item->item->item_code ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-semibold text-slate-600">
                                {{ $item->item->unit ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-black text-blue-600 text-sm">
                                {{ rtrim(rtrim(number_format($item->quantity, 2, '.', ','), '0'), '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-slate-700">
                                {{ number_format($item->unit_price, 0, ',', '.') }}đ
                            </td>
                            <td class="py-3.5 px-4 text-right font-black text-slate-900">
                                {{ number_format($item->subtotal, 0, ',', '.') }}đ
                            </td>
                            <td class="py-3.5 px-4 text-center text-slate-500 font-medium">
                                {{ $item->expiry_date ? $item->expiry_date->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50/80 border-t-2 border-slate-200">
                            <td colspan="5" class="py-4 px-4 text-right font-extrabold text-slate-700 uppercase tracking-wider text-xs">
                                Tổng Giá Trị Nhập Kho:
                            </td>
                            <td class="py-4 px-4 text-right font-black text-base text-[#ee4d2d]">
                                {{ number_format($receipt->total_amount, 0, ',', '.') }} VNĐ
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Chữ ký xác nhận nghiệm thu giữa 2 bên -->
        <div class="grid grid-cols-3 gap-4 text-center pt-8 border-t border-slate-100 text-xs">
            <div class="space-y-16">
                <div>
                    <span class="font-extrabold text-slate-800 uppercase block">Người Giao Hàng</span>
                    <span class="text-[11px] text-slate-400 italic block">(Ký và ghi rõ họ tên)</span>
                </div>
                <div class="text-slate-400 italic font-medium">
                    (Bên Đơn Vị Cung Cấp)
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <span class="font-extrabold text-slate-800 uppercase block">Thủ Kho / Người Nhận</span>
                    <span class="text-[11px] text-slate-400 italic block">(Ký và xác nhận đủ hàng)</span>
                </div>
                <div class="text-slate-800 font-bold">
                    {{ $receipt->creator->name ?? 'Admin' }}
                </div>
            </div>

            <div class="space-y-16">
                <div>
                    <span class="font-extrabold text-slate-800 uppercase block">Kế Toán / Quản Lý Bếp</span>
                    <span class="text-[11px] text-slate-400 italic block">(Ký duyệt nhập kho)</span>
                </div>
                <div class="text-slate-400 italic font-medium">
                    (Đã duyệt hệ thống)
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

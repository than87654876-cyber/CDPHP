@extends('layouts.admin')

@section('title', 'Thẩm định đơn hoàn tiền - FOODDAILY Admin')

@section('content')
@php
    $notes = $order->health_notes;
    
    $reason = '';
    if (preg_match('/Lý do: ([^,\]]+)/', $notes, $matches)) {
        $reason = $matches[1];
    }
    
    if ($reason === 'wrong_dish') {
        $reasonText = 'Giao sai món ăn / Nhầm lẫn thực đơn';
    } elseif ($reason === 'damaged_food') {
        $reasonText = 'Thực phẩm biến chất, rơi đổ do vận chuyển';
    } elseif ($reason === 'not_delivered') {
        $reasonText = 'Tài xế không giao hàng nhưng bấm hoàn thành';
    } else {
        $reasonText = $reason ?: 'Khác';
    }

    $reqAmount = null;
    if (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $notes, $matches)) {
        $reqAmount = $matches[1];
    }

    $isRefunded = ($order->payment_status === 'refunded');
    $isRejected = (strpos($notes, '[Admin Phản hồi:') !== false && !$isRefunded);
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Hồ Sơ Thẩm Định Hoàn Tiền</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600 font-extrabold border border-rose-200">
                    #FDL-{{ $order->id }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Kiểm tra thông tin khiếu nại, đối soát và ra quyết định xử lý hoàn trả</p>
        </div>
        <a href="{{ route('quanly_yeucauhoan') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer & Order Info Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Thông tin đơn hàng gốc</h3>
            </div>

            <div class="space-y-3.5 text-xs">
                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Mã đơn liên kết</p>
                    <a href="{{ route('donhang_xem', $order->id) }}" class="inline-flex items-center gap-1 font-extrabold text-[#ee4d2d] hover:underline bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100 mt-1">
                        #FDL-{{ $order->id }} <i class="fas fa-external-link-alt text-[9px]"></i>
                    </a>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Khách hàng khiếu nại</p>
                    <p class="font-black text-slate-900 text-sm mt-0.5">{{ $order->user->fullname ?? 'Khách vãng lai' }}</p>
                    <p class="text-[11px] text-slate-400 font-medium">SĐT: {{ $order->user->phone ?? 'Chưa có' }}</p>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tổng giá trị đơn gốc</p>
                    <p class="font-black text-slate-900 text-base mt-0.5">{{ number_format($order->final_amount, 0, ',', '.') }}đ</p>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Số tiền yêu cầu hoàn</p>
                    <p class="font-black text-rose-600 text-xl mt-0.5">{{ $reqAmount ?: number_format($order->final_amount, 0, ',', '.') . 'đ' }}</p>
                </div>

                <div>
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Trạng thái hồ sơ</p>
                    <div class="mt-1">
                        @if($isRefunded)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200">
                                ✔ Đã duyệt hoàn tiền
                            </span>
                        @elseif($isRejected)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-extrabold border border-rose-200">
                                ✕ Đã từ chối khiếu nại
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-extrabold border border-amber-200 animate-pulse">
                                ⏳ Đang chờ thẩm định
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaint Details & Decision Card -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Complaint Reason Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Nội dung khiếu nại từ khách hàng</h3>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Lý do khiếu nại</p>
                        <p class="font-extrabold text-rose-600 text-sm mt-1">{{ $reasonText }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Ghi chú & Lịch sử trao đổi</p>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-700 font-medium leading-relaxed mt-1 whitespace-pre-line">
                            {{ $order->health_notes }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Action Form -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm font-bold">
                        <i class="fas fa-gavel"></i>
                    </div>
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Quyết định thẩm định (Admin)</h3>
                </div>

                <form action="{{ route('yeucauhoan_duyet', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="admin_response" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                            Nội dung phản hồi / Ghi chú thẩm định <span class="text-[#ee4d2d]">*</span>
                        </label>
                        <textarea id="admin_response" name="admin_response" rows="3" required placeholder="Nhập lý do duyệt hoàn tiền hoặc lý do từ chối để thông báo cho khách hàng..." class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs"></textarea>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                        <button type="submit" name="action" value="reject" onclick="return confirm('Bạn có chắc chắn muốn TỪ CHỐI yêu cầu hoàn tiền này?');" class="px-6 py-3 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold text-xs transition-all border border-rose-200 cursor-pointer">
                            <i class="fas fa-times-circle mr-1"></i> Từ chối khiếu nại
                        </button>
                        <button type="submit" name="action" value="approve" onclick="return confirm('Bạn có chắc chắn muốn PHÊ DUYỆT hoàn tiền cho đơn hàng này?');" class="px-8 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-500/25 transition-all cursor-pointer">
                            <i class="fas fa-check-circle mr-1"></i> Phê duyệt hoàn tiền
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Quản lý Hoàn tiền - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Yêu Cầu Khiếu Nại & Hoàn Tiền</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-600 font-extrabold border border-rose-200">
                    {{ count($orders) }} yêu cầu
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thẩm định lý do khiếu nại và xử lý hoàn trả dòng tiền cho khách hàng</p>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs text-xs font-semibold">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 text-lg leading-none">&times;</button>
        </div>
    @endif

    <!-- STATS CARDS -->
    @php
        $pendingCount = 0;
        $refundedCount = 0;
        $rejectedCount = 0;
        $totalRefundAmount = 0;

        foreach($orders as $ord) {
            $isRefunded = ($ord->payment_status === 'refunded');
            $isRejected = (strpos($ord->health_notes, '[Admin Phản hồi:') !== false && !$isRefunded);
            if ($isRefunded) {
                $refundedCount++;
            } elseif ($isRejected) {
                $rejectedCount++;
            } else {
                $pendingCount++;
            }
            $totalRefundAmount += $ord->final_amount;
        }
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold border border-amber-100">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Đang chờ duyệt</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ $pendingCount }} đơn</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold border border-emerald-100">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Đã hoàn tiền</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ $refundedCount }} đơn</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold border border-rose-100">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Đã từ chối</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ $rejectedCount }} đơn</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold border border-indigo-100">
                <i class="fas fa-sack-dollar"></i>
            </div>
            <div>
                <p class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">Tổng giá trị</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalRefundAmount) }}đ</h3>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE CARD -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <!-- Card Filter Header -->
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900">Danh sách yêu cầu khiếu nại & hoàn tiền</h3>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs pointer-events-none">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="refundSearch" placeholder="Tìm theo tên, SĐT, mã đơn..." onkeyup="filterRefundsTable()" class="w-full pl-8 pr-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 focus:border-[#ee4d2d] outline-none transition-all">
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="refundTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6 w-16 text-center">STT</th>
                        <th class="py-4 px-6">Khách hàng / SĐT</th>
                        <th class="py-4 px-6">Mã đơn gốc</th>
                        <th class="py-4 px-6">Ngày gửi yêu cầu</th>
                        <th class="py-4 px-6">Số tiền hoàn</th>
                        <th class="py-4 px-6">Trạng thái xác nhận</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $index => $order)
                        @php
                            $reqAmountText = number_format($order->final_amount, 0, ',', '.') . 'đ';
                            if (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $order->health_notes, $matches)) {
                                $reqAmountText = trim($matches[1]);
                            }

                            $isRefunded = ($order->payment_status === 'refunded');
                            $isRejected = (strpos($order->health_notes, '[Admin Phản hồi:') !== false && !$isRefunded);
                        @endphp
                        <tr class="refund-row hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 text-center font-bold text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-extrabold flex items-center justify-center text-xs">
                                        {{ mb_substr($order->user->fullname ?? 'K', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-xs">{{ $order->user->fullname ?? 'Khách vãng lai' }}</p>
                                        <p class="text-[11px] text-slate-400 font-medium">{{ $order->user->phone ?? 'Chưa có SĐT' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('donhang_xem', $order->id) }}" class="inline-flex items-center gap-1 font-extrabold text-[#ee4d2d] hover:underline bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-100">
                                    #FDL-{{ $order->id }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 font-semibold">
                                    <i class="far fa-clock text-slate-400 text-[11px]"></i>
                                    <span>{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-black text-rose-600 text-sm">
                                {{ $reqAmountText }}
                            </td>
                            <td class="py-4 px-6">
                                @if($isRefunded)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đã hoàn tiền
                                    </span>
                                @elseif($isRejected)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Đã từ chối
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Đang chờ duyệt
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('yeucauhoan_xem', $order->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-[#ee4d2d] text-white font-extrabold text-xs shadow-sm transition-all hover:scale-105 active:scale-95">
                                    <i class="fas fa-file-signature text-xs"></i>
                                    <span>Thẩm định đơn</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto">
                                        <i class="fas fa-shield-halved"></i>
                                    </div>
                                    <h4 class="font-extrabold text-slate-800 text-sm">Hiện chưa có khiếu nại hoàn tiền</h4>
                                    <p class="text-xs text-slate-400">Tất cả các đơn hàng đang vận hành ổn định và không phát sinh khiếu nại dòng tiền.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function filterRefundsTable() {
        const input = document.getElementById('refundSearch');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('.refund-row');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection

@extends('layouts.app')

@section('title', 'Danh sách yêu cầu hoàn tiền - FOODDAILY')

@section('content')
<div class="py-10 bg-transparent min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Navigation Breadcrumb -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d] transition-colors">Trang chủ</a>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                <a href="{{ route('tracuu') }}" class="hover:text-[#ee4d2d] transition-colors">Đơn hàng</a>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                <span class="text-[#ee4d2d] font-bold">Yêu cầu hoàn tiền</span>
            </div>
            <a href="{{ route('trangchu') }}" class="text-xs font-bold text-[#ee4d2d] hover:underline flex items-center gap-1.5">
                <i class="fas fa-arrow-left"></i> Quay lại cửa hàng
            </a>
        </div>

        <!-- Header Title Card -->
        <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-xl shadow-xs shrink-0">
                        <i class="fas fa-rotate-left"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900">Danh sách yêu cầu hoàn tiền</h1>
                        <p class="text-xs text-slate-500 mt-0.5">Theo dõi tình trạng thẩm định và xử lý hoàn tiền cho các đơn hàng của bạn</p>
                    </div>
                </div>

                <!-- Action Button -->
                <a href="{{ route('tracuu') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs transition-colors flex items-center justify-center gap-2 shrink-0">
                    <i class="fas fa-file-invoice"></i> Xem đơn hàng đã mua
                </a>
            </div>

            <!-- Search Form -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <form action="{{ route('yeucauhoan') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Tìm kiếm theo mã đơn hàng (VD: 12, FDL-12) hoặc lý do..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] focus:bg-white outline-none transition-all">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-xl text-xs font-extrabold transition-colors shadow-xs">
                            Tìm kiếm
                        </button>
                        @if(!empty($search))
                            <a href="{{ route('yeucauhoan') }}" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors">
                                Xóa lọc
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Refund Orders List -->
        <div class="space-y-4">
            @forelse($orders as $order)
                @php
                    $notes = $order->health_notes ?? '';
                    
                    $reason = '';
                    if (preg_match('/Lý do: ([^,\]]+)/', $notes, $matches)) {
                        $reason = trim($matches[1]);
                    }
                    
                    $reasonMap = [
                        'wrong_dish' => 'Giao sai món ăn / Nhầm lẫn thực đơn',
                        'damaged_food' => 'Thực phẩm biến chất, rơi đổ do vận chuyển',
                        'not_delivered' => 'Tài xế không giao hàng nhưng bấm hoàn thành',
                    ];
                    $reasonText = $reasonMap[$reason] ?? ($reason ?: 'Khác');

                    $method = 'bank';
                    if (preg_match('/Phương thức: ([^, \]]+)/', $notes, $matches)) {
                        $method = trim($matches[1]);
                    }

                    $detail = '';
                    if (preg_match('/Chi tiết: ([^\]]+)/', $notes, $matches)) {
                        $detail = trim($matches[1]);
                    }

                    $methodText = 'Chuyển khoản Ngân hàng';
                    if ($method === 'momo') {
                        $momoPhone = '';
                        if (preg_match('/SĐT MoMo: ([^,]+)/', $notes, $matches)) { $momoPhone = trim($matches[1]); }
                        $methodText = 'Ví điện tử MoMo' . ($momoPhone ? ' (' . $momoPhone . ')' : '');
                    } else {
                        $bankName = '';
                        if (preg_match('/Ngân hàng: ([^,]+)/', $notes, $matches)) { $bankName = trim($matches[1]); }
                        $methodText = 'Ngân hàng ' . ($bankName ?: 'ATM/Napas');
                    }

                    $adminResponse = '';
                    if (preg_match('/\[Admin Phản hồi: ([^\]\)]+)/', $notes, $matches)) {
                        $adminResponse = trim($matches[1]);
                    }

                    $reqAmountText = number_format($order->final_amount, 0, ',', '.') . 'đ';
                    if (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $notes, $matches)) {
                        $reqAmountText = trim($matches[1]);
                        if (!str_contains($reqAmountText, 'đ')) {
                            $reqAmountText .= 'đ';
                        }
                    }
                    
                    $imageLink = '';
                    if (preg_match('/Hình ảnh minh chứng: ([^,\]]+)/', $notes, $matches)) {
                        $imageLink = trim($matches[1]);
                        if ($imageLink === 'Không có') {
                            $imageLink = '';
                        }
                    }
                @endphp

                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-6 shadow-sm border border-slate-200 hover:border-slate-300 transition-all space-y-4">
                    
                    <!-- Card Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-lg bg-rose-100 text-[#ee4d2d] text-xs font-black">#FDL-{{ $order->id }}</span>
                                <span class="text-xs font-bold text-slate-800">{{ $order->orderItems->pluck('dish.dish_name')->filter()->implode(', ') ?: 'Đơn hàng #' . $order->id }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">
                                <i class="far fa-clock mr-1"></i> Ngày gửi yêu cầu: {{ $order->updated_at->format('H:i - d/m/Y') }}
                            </p>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($order->payment_status === 'refunded')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-extrabold border border-emerald-200">
                                    <i class="fas fa-circle-check text-emerald-600"></i> Đã hoàn tiền
                                </span>
                            @elseif(strpos($order->health_notes ?? '', '[Admin Phản hồi:') !== false && strpos($order->health_notes ?? '', 'Từ chối') !== false)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-extrabold border border-rose-200">
                                    <i class="fas fa-circle-xmark text-rose-600"></i> Từ chối duyệt
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-extrabold border border-amber-200">
                                    <i class="fas fa-spinner fa-spin text-amber-600"></i> Đang chờ xử lý
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        
                        <!-- Left Info -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2.5">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                                <span class="text-slate-500 font-bold">Số tiền yêu cầu hoàn:</span>
                                <span class="font-black text-base text-[#ee4d2d]">{{ $reqAmountText }}</span>
                            </div>
                            <div class="flex justify-between items-start pb-2 border-b border-slate-200/60">
                                <span class="text-slate-500 font-bold shrink-0">Lý do khiếu nại:</span>
                                <span class="font-extrabold text-slate-800 text-right">{{ $reasonText }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-slate-500 font-bold shrink-0">Nhận hoàn qua:</span>
                                <span class="font-extrabold text-slate-800 text-right flex items-center gap-1">
                                    <i class="fas {{ $method === 'momo' ? 'fa-wallet text-pink-500' : 'fa-building-columns text-blue-500' }}"></i>
                                    {{ $methodText }}
                                </span>
                            </div>
                        </div>

                        <!-- Right Info -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between space-y-3">
                            <div class="space-y-1.5">
                                <span class="text-slate-500 font-bold block">Mô tả sự cố từ khách:</span>
                                <p class="text-slate-700 italic bg-white p-2.5 rounded-xl border border-slate-200/80 leading-relaxed font-medium">
                                    "{{ $detail ?: 'Không có ghi chú thêm' }}"
                                </p>
                            </div>

                            @if($adminResponse)
                                <div class="pt-2 border-t border-slate-200/60 space-y-1">
                                    <span class="text-blue-600 font-extrabold flex items-center gap-1">
                                        <i class="fas fa-comment-dots"></i> Phản hồi từ Ban Quản Trị:
                                    </span>
                                    <p class="text-slate-800 font-bold bg-blue-50/80 p-2.5 rounded-xl border border-blue-200/60 leading-relaxed">
                                        {{ $adminResponse }}
                                    </p>
                                </div>
                            @endif

                            @if($imageLink)
                                <div class="pt-2 flex items-center gap-2">
                                    <span class="text-slate-500 font-bold">Hình ảnh minh chứng:</span>
                                    <a href="{{ $imageLink }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#ee4d2d] hover:underline bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                        <i class="fas fa-image"></i> Xem ảnh đính kèm
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>

                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white/95 backdrop-blur-sm rounded-3xl p-12 text-center shadow-sm border border-slate-200 space-y-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl font-bold">
                        <i class="fas fa-file-circle-question"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base font-extrabold text-slate-800">Không có yêu cầu hoàn tiền nào</h3>
                        <p class="text-xs text-slate-500">Bạn chưa gửi yêu cầu hoàn tiền nào hoặc không tìm thấy kết quả phù hợp với từ khóa.</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('tracuu') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs transition-colors shadow-sm">
                            <i class="fas fa-receipt"></i> Xem danh sách đơn hàng đã mua
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection

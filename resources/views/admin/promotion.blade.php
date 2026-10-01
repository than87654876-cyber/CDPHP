@extends('layouts.admin')

@section('title', 'Quản lý Chương trình Khuyến Mãi - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Chương Trình Khuyến Mãi & Voucher</span>
                <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] border border-rose-200 text-[10px] font-black uppercase">
                    Shopee Campaigns
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quản lý mã giảm giá, chiến dịch ngày đôi Shopee, dịp lễ đặc biệt và gửi email hàng loạt cho khách hàng</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('quanly_guima') }}" class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fas fa-filter text-slate-500"></i> Sinh mã tự động
            </a>
            <a href="{{ route('khuyenmai_them') }}" class="px-5 py-2.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> Thêm khuyến mãi mới
            </a>
        </div>
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
            <span class="text-xs font-bold text-slate-400">Tổng cộng: {{ $promotions->count() }} voucher</span>
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
                        <th class="py-4 px-6 text-center">Gửi Gmail</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($promotions as $index => $promo)
                        @php
                            $isShopee = str_contains($promo->coupon_code, 'SHOPEE') || str_contains($promo->coupon_code, 'FLASH') || str_contains($promo->coupon_code, '1010') || str_contains($promo->coupon_code, '1111') || str_contains($promo->coupon_code, '1212');
                            $isSpecial = str_contains($promo->coupon_code, 'TET') || str_contains($promo->coupon_code, 'BLACKFRIDAY') || str_contains($promo->coupon_code, 'XMAS') || str_contains($promo->coupon_code, 'DAILE');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-400">#{{ $index + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-black text-[#ee4d2d] text-xs tracking-wider">
                                        #{{ $promo->coupon_code }}
                                    </span>
                                    @if($isShopee)
                                        <span class="px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 text-[9px] font-black">
                                            ⚡ Ngày đôi Shopee
                                        </span>
                                    @elseif($isSpecial)
                                        <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-black">
                                            🧧 Dịp đặc biệt
                                        </span>
                                    @endif
                                </div>
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
                                {{ $promo->start_date ? $promo->start_date->format('d/m/Y') : 'Không giới hạn' }} - {{ $promo->end_date ? $promo->end_date->format('d/m/Y') : 'Vô thời hạn' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button type="button" 
                                        onclick="openBroadcastModal({{ $promo->id }}, '{{ $promo->coupon_code }}', '{{ $promo->discount_type === 'percent' ? number_format($promo->discount_value) . '%' : number_format($promo->discount_value) . 'đ' }}')" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-[#ee4d2d] border border-orange-200 text-xs font-black transition-colors cursor-pointer shadow-2xs">
                                    <i class="fas fa-paper-plane text-[10px]"></i>
                                    <span>Gửi Gmail</span>
                                </button>
                            </td>
                            <td class="py-4 px-6 text-right space-x-1.5">
                                <a href="{{ route('khuyenmai_xem', $promo->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Xem chi tiết">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('khuyenmai_chinhsua', $promo->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors" title="Chỉnh sửa">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('khuyenmai_xoa', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa khuyến mãi này?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-colors" title="Xóa">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 font-medium">Chưa có mã khuyến mãi nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- MODAL GỬI GMAIL HÀNG LOẠT CHO MÃ ĐÃ CHỌN -->
<div id="broadcastModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-[#ee4d2d] to-amber-500 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shadow-inner">
                    📧
                </span>
                <div>
                    <h3 class="text-base font-black tracking-tight">Gửi Gmail Hàng Loạt Cho Khách Hàng</h3>
                    <p class="text-[11px] text-rose-100 font-semibold" id="modal-coupon-subtitle">Mã: #...</p>
                </div>
            </div>
            <button type="button" onclick="closeBroadcastModal()" class="w-8 h-8 rounded-full bg-black/15 hover:bg-black/30 text-white flex items-center justify-center text-lg font-bold transition-colors">
                &times;
            </button>
        </div>

        <form id="broadcastForm" method="POST" action="" class="p-6 space-y-4 overflow-y-auto">
            @csrf
            
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                <p class="font-extrabold flex items-center gap-1.5">
                    <i class="fas fa-info-circle text-amber-600"></i>
                    Gửi chiến dịch marketing tới hộp thư Gmail:
                </p>
                <p class="text-[11px] text-amber-800">
                    Hệ thống sẽ lọc các tài khoản khách hàng có đuôi <strong>@gmail.com</strong> và đưa vào hàng đợi gửi email tự động kèm mã ưu đãi độc quyền.
                </p>
            </div>

            <div class="space-y-2">
                <label for="modal_email_subject" class="block text-xs font-bold text-slate-700">
                    Tiêu đề Email <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="modal_email_subject" name="email_subject" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none">
            </div>

            <div class="space-y-2">
                <label for="modal_email_message" class="block text-xs font-bold text-slate-700">
                    Lời nhắn gửi kèm (Tùy chọn)
                </label>
                <textarea id="modal_email_message" name="email_message" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:border-[#ee4d2d] outline-none" placeholder="Để trống để sử dụng lời nhắn mặc định của FOODDAILY..."></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="modal_auto_save_wallet" name="auto_save_wallet" value="1" checked class="w-4 h-4 text-[#ee4d2d] rounded">
                <label for="modal_auto_save_wallet" class="text-xs font-bold text-slate-700 cursor-pointer">
                    Tự động lưu mã vào Ví Voucher của khách hàng
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeBroadcastModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                    Hủy bỏ
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#ee4d2d] to-amber-500 hover:from-red-600 hover:to-amber-600 text-white text-xs font-black shadow-md transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-paper-plane"></i> Bắt đầu gửi hàng loạt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBroadcastModal(couponId, couponCode, discountText) {
        const modal = document.getElementById('broadcastModal');
        const form = document.getElementById('broadcastForm');
        const subtitle = document.getElementById('modal-coupon-subtitle');
        const subjectInput = document.getElementById('modal_email_subject');

        form.action = "{{ url('/khuyenmai_guimail') }}/" + couponId;
        subtitle.innerText = `Mã: #${couponCode} (${discountText})`;
        subjectInput.value = `[FOODDAILY] 🎁 Tặng bạn mã giảm giá #${couponCode} (${discountText}) - Ưu đãi độc quyền!`;

        modal.classList.remove('hidden');
    }

    function closeBroadcastModal() {
        const modal = document.getElementById('broadcastModal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Tạo Mã Khuyến Mãi & Ngày Đôi Shopee - FOODDAILY Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <span>Tạo Mã Khuyến Mãi Mới</span>
                <span class="px-2.5 py-0.5 rounded-full bg-gradient-to-r from-red-500 to-amber-500 text-white text-[10px] font-black uppercase tracking-wider shadow-xs">
                    Shopee Campaign & Email Broadcast
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thiết lập voucher ngày đôi Shopee, dịp lễ đặc biệt và gửi email hàng loạt cho khách hàng qua Gmail</p>
        </div>
        <a href="{{ route('quanly_khuyenmai') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- KHỐI 1: GỢI Ý CHIẾN DỊCH NGÀY ĐÔI SHOPEE & DỊP LỄ ĐẶC BIỆT -->
    <div class="bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-rose-500/10 rounded-3xl p-5 sm:p-6 border border-orange-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-orange-200/60">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-[#ee4d2d] text-white flex items-center justify-center text-lg shadow-sm">
                    ⚡
                </span>
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">
                        Gợi ý chiến dịch khuyến mãi (1-Click Chọn Nhanh)
                    </h3>
                    <p class="text-[11px] text-slate-500 font-medium">Bấm vào mẫu bên dưới để tự động điền mã, tỷ lệ giảm sốc và ngày tháng sự kiện</p>
                </div>
            </div>
            <span class="text-[11px] font-extrabold text-[#ee4d2d] bg-orange-100/80 px-3 py-1 rounded-full shrink-0">
                Chuẩn phong cách Shopee & Ngày Lễ
            </span>
        </div>

        <!-- Nhóm 1: Shopee Ngày Đôi Siêu Sale (10.10, 11.11, 12.12...) -->
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-xs font-black text-slate-700 uppercase tracking-wider">
                <i class="fas fa-bolt text-amber-500"></i>
                <span>Shopee Siêu Sale Ngày Đôi (Double Days):</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
                <!-- Ngày đôi tháng hiện tại -->
                @if(isset($doubleDayCode))
                <button type="button" 
                        onclick="applyPreset('{{ $doubleDayCode }}', 'percent', 50, 100000, 3, '🔥 SIÊU SALE NGÀY ĐÔI {{ $doubleDayStr }}', 'Đại tiệc ngày đôi siêu sale trùng tháng')" 
                        class="p-2.5 rounded-2xl bg-white border-2 border-red-400 hover:border-red-600 hover:shadow-md transition-all text-left group cursor-pointer relative overflow-hidden">
                    <span class="absolute top-0 right-0 bg-red-500 text-white text-[8px] font-black px-1.5 py-0.2 rounded-bl">HOT NHẤT</span>
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-[#ee4d2d] truncate">⚡ {{ $doubleDayStr }} Tháng này</p>
                    <p class="text-[10px] text-red-600 font-extrabold">Giảm 50%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 100K</p>
                </button>
                @endif

                <button type="button" 
                        onclick="applyPreset('SHOPEE_1010', 'percent', 50, 100000, 3, '🎉 10.10 ĐẠI TIỆC SIÊU SALE', 'Đại tiệc siêu sale thương hiệu 10.10')" 
                        class="p-2.5 rounded-2xl bg-white border border-orange-200 hover:border-[#ee4d2d] hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-[#ee4d2d]">⚡ 10.10 Mega Sale</p>
                    <p class="text-[10px] text-orange-600 font-extrabold">Giảm 50%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 100K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('SHOPEE_1111', 'percent', 50, 120000, 3, '👑 11.11 SIÊU SALE ĐỘC THÂN', 'Siêu đại hội mua sắm độc thân lớn nhất năm 11.11')" 
                        class="p-2.5 rounded-2xl bg-white border border-orange-200 hover:border-[#ee4d2d] hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-[#ee4d2d]">⚡ 11.11 Độc Thân</p>
                    <p class="text-[10px] text-orange-600 font-extrabold">Giảm 50%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 120K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('SHOPEE_1212', 'percent', 50, 150000, 4, '🎊 12.12 SIÊU ĐẠI TIỆC CUỐI NĂM', 'Đại tiệc sinh nhật & siêu sale cuối năm 12.12')" 
                        class="p-2.5 rounded-2xl bg-white border border-orange-200 hover:border-[#ee4d2d] hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-[#ee4d2d]">⚡ 12.12 Cuối Năm</p>
                    <p class="text-[10px] text-orange-600 font-extrabold">Giảm 50%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 150K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('GIUATHANG15', 'percent', 30, 80000, 3, '🛒 SALE GIỮA THÁNG 15', 'Siêu tiệc giữa tháng giảm sâu cực đã')" 
                        class="p-2.5 rounded-2xl bg-white border border-orange-200 hover:border-[#ee4d2d] hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-[#ee4d2d]">📅 Giữa Tháng 15</p>
                    <p class="text-[10px] text-orange-600 font-extrabold">Giảm 30%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 80K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('PAYDAY25', 'percent', 35, 90000, 4, '💰 PAYDAY 25 - LƯƠNG ĐÃ VỀ', 'Lương đã về, khao tiệc thịnh soạn thả ga')" 
                        class="p-2.5 rounded-2xl bg-white border border-orange-200 hover:border-[#ee4d2d] hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-[#ee4d2d]">💰 PayDay 25</p>
                    <p class="text-[10px] text-orange-600 font-extrabold">Giảm 35%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 90K</p>
                </button>
            </div>
        </div>

        <!-- Nhóm 2: Dịp Lễ & Sự Kiện Đặc Biệt -->
        <div class="space-y-2 pt-2 border-t border-orange-200/40">
            <div class="flex items-center gap-2 text-xs font-black text-slate-700 uppercase tracking-wider">
                <i class="fas fa-gift text-rose-500"></i>
                <span>Dịp Lễ & Sự Kiện Đặc Biệt (Special Occasions):</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                <button type="button" 
                        onclick="applyPreset('TET2026_LIXI', 'percent', 25, 100000, 7, '🧧 TẾT NGUYÊN ĐÁN - LỘC XUÂN NHƯ Ý', 'Lì xì đầu năm giảm 25% phát tài phát lộc')" 
                        class="p-2.5 rounded-2xl bg-white border border-rose-200 hover:border-rose-500 hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-rose-600 truncate">🧧 Tết Nguyên Đán</p>
                    <p class="text-[10px] text-rose-600 font-extrabold">Giảm 25%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 100K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('BLACKFRIDAY50', 'percent', 50, 100000, 3, '🖤 BLACK FRIDAY - BÃO SALE SẬP SÀN', 'Bão giảm giá Black Friday 50% lớn nhất mùa đông')" 
                        class="p-2.5 rounded-2xl bg-white border border-slate-300 hover:border-slate-800 hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-black truncate">🖤 Black Friday</p>
                    <p class="text-[10px] text-slate-900 font-extrabold">Giảm 50%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 100K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('XMAS_NEWYEAR', 'percent', 20, 100000, 5, '🎄 GIÁNG SINH & CHÀO NĂM MỚI', 'Mừng Giáng Sinh an lành và Năm Mới rực rỡ')" 
                        class="p-2.5 rounded-2xl bg-white border border-emerald-200 hover:border-emerald-600 hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-emerald-700 truncate">🎄 Giáng Sinh & Năm Mới</p>
                    <p class="text-[10px] text-emerald-600 font-extrabold">Giảm 20%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 100K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('DAILE_TRIAN', 'percent', 25, 100000, 4, '🇻🇳 MỪNG ĐẠI LỄ NƯỚC NHÀ', 'Khai tiệc mừng Đại Lễ non sông')" 
                        class="p-2.5 rounded-2xl bg-white border border-red-200 hover:border-red-600 hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-red-600 truncate">🇻🇳 Đại Lễ 30/4 & 2/9</p>
                    <p class="text-[10px] text-red-600 font-extrabold">Giảm 25%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 100K</p>
                </button>

                <button type="button" 
                        onclick="applyPreset('FREESHIP_MAX', 'percent', 15, 50000, 7, '🛵 SIÊU HỘI FREESHIP', 'Freeship giảm 15% cho mọi bữa ăn')" 
                        class="p-2.5 rounded-2xl bg-white border border-amber-200 hover:border-amber-600 hover:shadow-md transition-all text-left group cursor-pointer">
                    <p class="text-[11px] font-black text-slate-900 group-hover:text-amber-600 truncate">🛵 Siêu Hội Freeship</p>
                    <p class="text-[10px] text-amber-600 font-extrabold">Giảm 15%</p>
                    <p class="text-[9px] text-slate-400">Đơn từ 50K</p>
                </button>
            </div>
        </div>
    </div>

    <!-- KHỐI 2: FORM THIẾT LẬP THÔNG TIN VOUCHER -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Chi tiết thông số mã khuyến mãi</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Nhập thông tin hoặc chọn từ mẫu gợi ý bên trên</p>
                </div>
            </div>

            <!-- Live Preview Card Mini -->
            <div id="voucher-preview-pill" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-black text-[#ee4d2d]">
                <span>Mã xem trước:</span>
                <span id="preview-code-tag" class="underline uppercase">CHƯA NHẬP</span>
            </div>
        </div>

        <form id="couponForm" action="{{ route('khuyenmai_them.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Mã khuyến mãi -->
                <div class="space-y-2">
                    <label for="coupon_code" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Mã khuyến mãi (Coupon Code) <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}" oninput="updatePreview()" required placeholder="Ví dụ: SHOPEE_1010, TET2026_LIXI" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black uppercase text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Loại giảm giá -->
                <div class="space-y-2">
                    <label for="discount_type" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Loại chiết khấu <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="discount_type" name="discount_type" onchange="updatePreview()" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Giảm tiền cố định (đ)</option>
                    </select>
                </div>

                <!-- Mức giảm giá -->
                <div class="space-y-2">
                    <label for="discount_value" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Mức giảm giá <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="number" step="1" min="0" id="discount_value" name="discount_value" value="{{ old('discount_value') }}" oninput="updatePreview()" required placeholder="Ví dụ: 50 (nếu là %) hoặc 50000 (nếu là tiền)" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black text-rose-600 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Đơn tối thiểu -->
                <div class="space-y-2">
                    <label for="min_order_value" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Đơn tối thiểu áp dụng (đ) <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="number" step="1" min="0" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', 0) }}" oninput="updatePreview()" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Ngày bắt đầu -->
                <div class="space-y-2">
                    <label for="start_date" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Ngày bắt đầu áp dụng <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                </div>

                <!-- Ngày kết thúc -->
                <div class="space-y-2">
                    <label for="end_date" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Ngày kết thúc áp dụng <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                </div>

                <!-- Giới hạn lượt dùng -->
                <div class="space-y-2 sm:col-span-2">
                    <label for="usage_limit" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Giới hạn tổng số lần sử dụng (Bỏ trống nếu không giới hạn)
                    </label>
                    <input type="number" min="1" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="Ví dụ: 1000 (Để trống = Vô hạn lượt dùng trên toàn hệ thống)" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>
            </div>

            <!-- KHỐI 3: GỬI EMAIL HÀNG LOẠT DỰA THEO GMAIL CỦA KHÁCH HÀNG -->
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <div class="p-5 rounded-3xl bg-gradient-to-br from-rose-50/70 via-orange-50/40 to-amber-50/70 border border-rose-200/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-white text-[#ee4d2d] shadow-sm flex items-center justify-center text-lg shrink-0">
                                📧
                            </span>
                            <div>
                                <h4 class="text-xs font-black text-slate-900 uppercase tracking-wide">
                                    Gửi email hàng loạt tới các tài khoản khách hàng qua Gmail
                                </h4>
                                <p class="text-[11px] text-slate-500 font-medium">Hệ thống sẽ quét địa chỉ Gmail của khách hàng và gửi thông báo nhận mã voucher ngay lập tức</p>
                            </div>
                        </div>

                        <!-- Badge thống kê số lượng Gmail khách hàng -->
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-rose-200 text-xs font-black text-[#ee4d2d] shadow-2xs shrink-0">
                            <i class="fab fa-google text-red-500"></i>
                            <span>{{ $gmailCustomerCount ?? 0 }} Khách hàng Gmail</span>
                        </div>
                    </div>

                    <!-- Toggle Checkbox kích hoạt gửi email -->
                    <div class="flex items-center justify-between p-3.5 bg-white rounded-2xl border border-rose-100 shadow-2xs">
                        <label for="send_email_now" class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="send_email_now" name="send_email_now" value="1" onchange="toggleEmailInputs()" class="w-4 h-4 text-[#ee4d2d] rounded focus:ring-[#ee4d2d]">
                            <div>
                                <span class="text-xs font-black text-slate-900">Kích hoạt gửi email thông báo mã voucher sau khi tạo</span>
                                <p class="text-[10px] text-slate-400">Gửi chiến dịch marketing trực tiếp đến hộp thư đến của khách hàng</p>
                            </div>
                        </label>
                        <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            Hàng đợi tự động
                        </span>
                    </div>

                    <!-- Các trường tùy chỉnh nội dung email (Ẩn/Hiện theo toggle) -->
                    <div id="email-details-box" class="space-y-4 pt-2 hidden">
                        <div class="space-y-2">
                            <label for="email_subject" class="block text-xs font-bold text-slate-700">
                                Tiêu đề Email (Email Subject)
                            </label>
                            <input type="text" id="email_subject" name="email_subject" placeholder="Ví dụ: [FOODDAILY] 🎁 Tặng bạn mã giảm giá siêu sale Ngày Đôi Shopee!" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none bg-white">
                        </div>

                        <div class="space-y-2">
                            <label for="email_message" class="block text-xs font-bold text-slate-700">
                                Lời nhắn thân gửi khách hàng (Tùy chọn)
                            </label>
                            <textarea id="email_message" name="email_message" rows="3" placeholder="Nhập lời chúc, mô tả sự kiện hoặc để trống để sử dụng lời nhắn chuẩn của hệ thống..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:border-[#ee4d2d] outline-none bg-white"></textarea>
                        </div>

                        <!-- Checkbox tự động lưu vào ví -->
                        <div class="flex items-center gap-2.5">
                            <input type="checkbox" id="auto_save_wallet" name="auto_save_wallet" value="1" checked class="w-4 h-4 text-[#ee4d2d] rounded">
                            <label for="auto_save_wallet" class="text-xs font-bold text-slate-700 cursor-pointer">
                                Tự động lưu mã này vào Ví Voucher trên web của khách hàng (để khi họ đăng nhập và đặt món là có sẵn ngay)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('quanly_khuyenmai') }}" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors text-center">
                    Hủy bỏ
                </a>
                <button type="submit" onclick="document.getElementById('send_email_now').checked = false;" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-slate-800 hover:bg-slate-900 text-white font-extrabold text-xs transition-colors cursor-pointer">
                    <i class="fas fa-save mr-1.5"></i> Chỉ lưu hệ thống
                </button>
                <button type="submit" onclick="document.getElementById('send_email_now').checked = true;" class="w-full sm:w-auto px-8 py-3 rounded-2xl bg-gradient-to-r from-[#ee4d2d] to-amber-500 hover:from-red-600 hover:to-amber-600 text-white font-black text-xs shadow-lg shadow-rose-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>🚀 Lưu & Gửi Gmail Hàng Loạt</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Áp dụng Preset mẫu chiến dịch (Shopee Ngày đôi & Dịp đặc biệt)
    function applyPreset(code, type, value, minOrder, durationDays, subjectTitle, campaignDesc) {
        document.getElementById('coupon_code').value = code;
        document.getElementById('discount_type').value = type;
        document.getElementById('discount_value').value = value;
        document.getElementById('min_order_value').value = minOrder;

        // Ngày bắt đầu: hôm nay
        const now = new Date();
        const startStr = now.toISOString().split('T')[0];
        document.getElementById('start_date').value = startStr;

        // Ngày kết thúc: hôm nay + durationDays
        const end = new Date(now.getTime() + durationDays * 24 * 60 * 60 * 1000);
        const endStr = end.toISOString().split('T')[0];
        document.getElementById('end_date').value = endStr;

        // Cập nhật email subject và message
        document.getElementById('email_subject').value = `[FOODDAILY] 🎁 Tặng bạn mã ${code} - ${subjectTitle}!`;
        document.getElementById('email_message').value = `Nhân dịp ${campaignDesc}, FOODDAILY gửi tặng bạn mã giảm giá ${code} cực khủng. Hãy mở app và đặt món ngay hôm nay nhé!`;

        // Tự động bật hộp gửi email
        document.getElementById('send_email_now').checked = true;
        toggleEmailInputs();
        updatePreview();

        // Cuộn mượt xuống form
        document.getElementById('coupon_code').scrollIntoView({ behavior: 'smooth', block: 'center' });
        document.getElementById('coupon_code').classList.add('ring-2', 'ring-[#ee4d2d]');
        setTimeout(() => {
            document.getElementById('coupon_code').classList.remove('ring-2', 'ring-[#ee4d2d]');
        }, 1500);
    }

    // Cập nhật Live Preview Tag
    function updatePreview() {
        const code = document.getElementById('coupon_code').value.trim();
        const tag = document.getElementById('preview-code-tag');
        const pill = document.getElementById('voucher-preview-pill');

        if (code) {
            tag.innerText = '#' + code.toUpperCase();
            pill.classList.remove('hidden');
        } else {
            pill.classList.add('hidden');
        }
    }

    // Bật/tắt chi tiết gửi email
    function toggleEmailInputs() {
        const checkbox = document.getElementById('send_email_now');
        const box = document.getElementById('email-details-box');
        if (checkbox.checked) {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        updatePreview();
    });
</script>
@endsection

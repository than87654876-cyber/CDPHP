@extends('layouts.admin')

@section('title', 'Bộ lọc & Sinh mã tự động - FOODDAILY Admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Bộ Lọc Thông Minh & Sinh Mã Tự Động</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] font-extrabold border border-rose-200 uppercase">
                    Automation
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quét nhóm khách hàng mục tiêu và tự động phát hành coupon tri ân độc quyền</p>
        </div>
        <a href="{{ route('quanly_khuyenmai') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Danh sách khuyến mãi
        </a>
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
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs text-xs font-semibold">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-600 text-sm"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-800 text-lg leading-none">&times;</button>
        </div>
    @endif

    <!-- Generated Coupons Log (If any) -->
    @if(session('details'))
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Nhật ký chi tiết các mã đã gửi giả lập</h3>
            </div>
            <div class="overflow-x-auto max-h-64">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider border-b border-slate-100">
                            <th class="py-3 px-4">Khách hàng</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Mã Coupon</th>
                            <th class="py-3 px-4">Nội dung thông điệp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach(session('details') as $detail)
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $detail['fullname'] }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $detail['email'] }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-extrabold border border-emerald-200 uppercase">
                                        {{ $detail['code'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-500 font-medium">{{ $detail['body'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <form action="{{ route('quanly_guima.post') }}" method="POST" class="space-y-6">
        @csrf

        <!-- BƯỚC 1: ĐIỀU KIỆN LỌC -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                    <i class="fas fa-filter"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Bước 1: Thiết lập điều kiện lọc khách hàng chi tiết</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Chọn phân khúc khách hàng mục tiêu để tặng mã ưu đãi</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Theo hạng thành viên -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3">
                    <h4 class="text-xs font-extrabold text-rose-600 flex items-center gap-1.5 uppercase tracking-wider">
                        <i class="fas fa-crown text-amber-500"></i> 1. Hạng thành viên
                    </h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Chọn các hạng áp dụng:</p>
                    <div class="space-y-2 text-xs font-semibold text-slate-700">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="ranks[]" value="diamond" class="w-4 h-4 rounded text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300">
                            <span>💎 Kim Cương (VIP 2)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="ranks[]" value="gold" class="w-4 h-4 rounded text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300">
                            <span>👑 Hạng Vàng (VIP 1)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="ranks[]" value="silver" class="w-4 h-4 rounded text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300">
                            <span>🥈 Hạng Bạc</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="ranks[]" value="standard" class="w-4 h-4 rounded text-[#ee4d2d] focus:ring-[#ee4d2d] border-slate-300">
                            <span>🥉 Hạng Đồng (Mới)</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Thời gian chưa mua hàng -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-extrabold text-amber-600 flex items-center gap-1.5 uppercase tracking-wider">
                            <i class="fas fa-history"></i> 2. Khách chưa mua lại
                        </h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-3 mb-1">Đơn cuối cách đây:</p>
                        <select id="inactive_period" name="inactive_period" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none bg-white">
                            <option value="all">-- Không lọc tiêu chí này --</option>
                            <option value="1_month">Từ 1 tháng trở lên (>30 ngày)</option>
                            <option value="6_months">Từ 6 tháng trở lên (>180 ngày)</option>
                            <option value="1_year">Từ 1 năm trở lên (>365 ngày)</option>
                        </select>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-tight">Hệ thống quét lịch sử đơn để tìm khách hàng vắng mặt.</p>
                </div>

                <!-- 3. Khách hàng mới -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-extrabold text-emerald-600 flex items-center gap-1.5 uppercase tracking-wider">
                            <i class="fas fa-user-plus"></i> 3. Khách hàng mới
                        </h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-3 mb-1">Thời gian đăng ký:</p>
                        <select id="new_customer_range" name="new_customer_range" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none bg-white">
                            <option value="all">-- Không lọc tiêu chí này --</option>
                            <option value="7_days" selected>Mới đăng ký trong 7 ngày qua</option>
                            <option value="15_days">Mới đăng ký trong 15 ngày qua</option>
                            <option value="30_days">Mới đăng ký trong 30 ngày qua</option>
                        </select>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-tight">Thúc đẩy khách hàng vừa tạo tài khoản đặt đơn đầu tiên.</p>
                </div>

                <!-- 4. Gửi đích danh Email -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xs font-extrabold text-indigo-600 flex items-center gap-1.5 uppercase tracking-wider">
                            <i class="fas fa-envelope"></i> 4. Đích danh Email
                        </h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-3 mb-1">Địa chỉ Email nhận:</p>
                        <input type="email" id="target_email" name="target_email" placeholder="customer@gmail.com" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none bg-white">
                    </div>
                    <p class="text-[10px] text-slate-400 leading-tight">Nếu điền ô này, hệ thống sẽ ưu tiên gửi riêng cho Email này.</p>
                </div>
            </div>
        </div>

        <!-- BƯỚC 2: QUY TẮC SINH MÃ TỰ ĐỘNG -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold shadow-xs">
                    <i class="fas fa-gear"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Bước 2: Cấu hình quy tắc sinh mã tự động</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Thiết lập cấu trúc mã, mức giảm và thời hạn áp dụng</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="space-y-1.5">
                    <label for="code_prefix" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Tiền tố mã (Prefix)
                    </label>
                    <input type="text" id="code_prefix" name="code_prefix" placeholder="Ví dụ: VIP7D, TRIAN" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black uppercase text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="code_length" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Độ dài chuỗi ngẫu nhiên
                    </label>
                    <select id="code_length" name="code_length" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs bg-white">
                        <option value="6" selected>6 ký tự (Ví dụ: AX89E1)</option>
                        <option value="8">8 ký tự (Ví dụ: K4M2P7Q9)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="discount_type" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Loại giảm giá
                    </label>
                    <select id="discount_type" name="discount_type" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs bg-white">
                        <option value="fixed" selected>Giảm tiền cố định (đ)</option>
                        <option value="percentage">Giảm theo phần trăm (%)</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="discount_value" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Mức giảm giá <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="number" id="discount_value" name="discount_value" value="20000" min="1" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-black text-rose-600 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="expiry_days" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Số ngày hết hạn
                    </label>
                    <input type="number" id="expiry_days" name="expiry_days" value="5" min="1" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="min_order" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Đơn tối thiểu áp dụng (đ)
                    </label>
                    <input type="number" id="min_order" name="min_order" value="0" min="0" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>

                <div class="space-y-1.5 sm:col-span-2">
                    <label for="send_channel" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Kênh thông báo
                    </label>
                    <select id="send_channel" name="send_channel" class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs bg-white">
                        <option value="email">Hệ thống gửi Email trực tiếp</option>
                        <option value="sms">Gửi SMS Brandname</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- BƯỚC 3: NỘI DUNG TIN NHẮN -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base font-bold shadow-xs">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Bước 3: Nội dung tin nhắn gửi đi</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Tùy biến tiêu đề và thông điệp cá nhân hóa tới khách hàng</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="email_subject" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Tiêu đề tin nhắn / Email <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="email_subject" name="email_subject" value="Món quà bất ngờ dành tặng riêng bạn từ FOODDAILY!" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>

                <div class="space-y-1.5">
                    <label for="message_body" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Nội dung chi tiết <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <textarea id="message_body" name="message_body" rows="4" required class="w-full p-4 rounded-2xl border border-slate-200 text-xs font-medium text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs leading-relaxed">Chào bạn thân mến, FOODDAILY gửi tặng riêng bạn mã ưu đãi giảm giá độc quyền tự sinh: [MÃ_TỰ_SINH]. Mã áp dụng cho mọi đơn hàng trong vòng [SỐ_NGÀY] ngày tới. Đừng bỏ lỡ nhé!</textarea>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <p class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                    <i class="fas fa-shield-halved text-emerald-500"></i>
                    <span>Hệ thống tự động loại bỏ trùng lặp nếu khách hàng thỏa mãn nhiều nhóm lọc.</span>
                </p>
                <button type="submit" class="px-8 py-3.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/25 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Bắt đầu sinh mã & Gửi
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

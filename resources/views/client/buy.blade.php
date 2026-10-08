@extends('layouts.app')

@section('title', 'Giỏ Hàng & Thanh Toán - FOODDAILY')

@section('content')
<div class="py-8 bg-transparent min-h-screen">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation Breadcrumb -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl border border-slate-200/80 shadow-xs">
                <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d]">Trang chủ</a>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                <span class="text-[#ee4d2d] font-bold">Giỏ hàng & Thanh toán</span>
            </div>
            <a href="{{ route('trangchu') }}#menu" class="text-xs font-bold text-white bg-[#ee4d2d] hover:bg-red-600 px-4 py-2 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                <i class="fas fa-plus-circle"></i> Thêm món ăn khác
            </a>
        </div>

        <!-- 2-COLUMN SIDE-BY-SIDE FLEX CONTAINER -->
        <div class="flex flex-col lg:flex-row items-start gap-8 w-full">

            <!-- CỘT BÊN TRÁI: DANH SÁCH GIỎ HÀNG & GỢI Ý MÓN CÙNG LOẠI -->
            <div class="flex-1 w-full min-w-0 space-y-6">
                <!-- Thẻ 1: Món ăn đã chọn -->
                <div class="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-7 shadow-sm border border-slate-200/80 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-9 h-9 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-sm shadow-xs">
                                <i class="fas fa-shopping-basket"></i>
                            </span>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">Món Ăn Đã Chọn (<span id="cart-item-count">0</span>)</h2>
                                <p class="text-[11px] text-slate-400 font-medium">Kiểm tra số lượng và áp dụng mã khuyến mãi</p>
                            </div>
                        </div>
                        <button type="button" onclick="clearCart()" class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors flex items-center gap-1">
                            <i class="far fa-trash-can"></i> Xóa tất cả
                        </button>
                    </div>

                    <!-- Danh sách món render từ Javascript -->
                    <div id="cart-items-list" class="divide-y divide-slate-100 min-h-[140px]">
                        <!-- Rendered by JavaScript -->
                    </div>

                    <!-- KHU VỰC MÃ GIẢM GIÁ (SMART PROMO COUPON SYSTEM) -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        @guest
                            <!-- Trạng thái CHƯA ĐĂNG NHẬP: Báo rõ điều kiện đăng nhập -->
                            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-700 flex items-center justify-center font-bold text-sm shrink-0">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <div>
                                        <strong class="font-extrabold block text-slate-800">Ưu đãi giảm giá đặc quyền</strong>
                                        <p class="text-[11px] text-amber-800">Mã khuyến mãi chỉ sử dụng được <strong>sau khi đăng nhập thành công</strong>.</p>
                                    </div>
                                </div>
                                <a href="{{ route('dangnhap') }}" class="px-4 py-2 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-xl text-xs font-black shadow-xs transition-colors shrink-0 flex items-center gap-1.5">
                                    <i class="fas fa-arrow-right-to-bracket text-[10px]"></i> Đăng nhập ngay
                                </a>
                            </div>

                            <div class="relative flex-1 w-full opacity-60">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                    <i class="fas fa-ticket"></i>
                                </span>
                                <input type="text" disabled placeholder="Đăng nhập để sử dụng mã khuyến mãi" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-bold text-slate-400 cursor-not-allowed">
                            </div>
                        @else
                            <!-- Trạng thái ĐÃ ĐĂNG NHẬP: Tự động chọn mã tốt nhất & Cho phép đổi mã -->
                            <div id="auto-coupon-banner" class="hidden p-3 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 text-emerald-900 text-xs items-center justify-between gap-2 shadow-2xs">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs shrink-0 font-bold">
                                        <i class="fas fa-wand-magic-sparkles"></i>
                                    </span>
                                    <span id="auto-coupon-text" class="font-bold truncate text-[11px]"></span>
                                </div>
                                <button type="button" onclick="openCouponModal()" class="text-emerald-700 hover:text-emerald-900 font-extrabold text-[11px] underline shrink-0">
                                    Đổi mã khác
                                </button>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <div class="relative flex-1 w-full">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                        <i class="fas fa-ticket"></i>
                                    </span>
                                    <input type="text" id="coupon_code" placeholder="MÃ GIẢM GIÁ (VD: FOODDAILY30)" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none uppercase shadow-xs">
                                </div>
                                <div class="flex items-center gap-2 w-full sm:w-auto">
                                    <button type="button" onclick="applyCoupon()" class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-900 hover:bg-[#ee4d2d] text-white rounded-xl text-xs font-extrabold transition-all shadow-xs cursor-pointer shrink-0">
                                        Áp dụng
                                    </button>
                                    <button type="button" onclick="openCouponModal()" class="flex-1 sm:flex-none px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-[#ee4d2d] border border-rose-200 rounded-xl text-xs font-extrabold transition-all shadow-2xs flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
                                        <i class="fas fa-tags text-[10px]"></i> Chọn voucher
                                    </button>
                                </div>
                            </div>
                            <div id="coupon-message" class="text-xs font-bold mt-1 hidden"></div>
                        @endguest
                    </div>
                </div>

                <!-- MODAL CHỌN MÃ KHUYẾN MÃI (VOUCHER SELECTOR MODAL) -->
                <div id="couponSelectorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 hidden">
                    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-rose-200 flex flex-col max-h-[85vh]">
                        <div class="p-5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between shrink-0">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center text-sm font-bold border border-amber-400/30">
                                    <i class="fas fa-ticket"></i>
                                </span>
                                <div>
                                    <h3 class="font-extrabold text-sm uppercase tracking-wider">Chọn Mã Khuyến Mãi</h3>
                                    <p class="text-[11px] text-slate-400">Danh sách các mã ưu đãi áp dụng cho giỏ hàng của bạn</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeCouponModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm">
                                &times;
                            </button>
                        </div>

                        <!-- Danh sách mã cuộn -->
                        <div id="coupon-modal-list" class="p-4 sm:p-5 overflow-y-auto space-y-3 flex-1 divide-y divide-slate-100">
                            <div class="text-center py-6 text-xs text-slate-400 font-semibold">Đang tải danh sách ưu đãi...</div>
                        </div>

                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                            <button type="button" onclick="removeCoupon()" class="text-xs font-bold text-slate-500 hover:text-rose-600 transition-colors">
                                Không sử dụng mã
                            </button>
                            <button type="button" onclick="closeCouponModal()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black transition-colors">
                                Xong
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CỘT BÊN PHẢI: FORM THANH TOÁN & GIAO HÀNG (CỐ ĐỊNH & THU GỌN VỪA KHUNG HÌNH) -->
            <div class="w-full lg:w-[420px] xl:w-[450px] shrink-0 space-y-4 lg:sticky lg:top-20 z-10">
                <form action="{{ route('muahang.process') }}" method="POST" id="checkoutForm" class="bg-white rounded-2xl p-4 sm:p-5 shadow-lg border border-slate-200 space-y-3.5">
                    @csrf
                    
                    <!-- Hidden Inputs -->
                    <input type="hidden" name="cart_items" id="cart_items_input" value="[]">
                    <input type="hidden" name="coupon_code" id="hidden_coupon_code" value="">

                    <div class="flex items-center gap-2.5 pb-2.5 border-b border-slate-100">
                        <span class="w-7 h-7 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shadow-xs">
                            <i class="fas fa-truck-fast"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 leading-tight">Thông Tin Nhận Hàng</h3>
                            <p class="text-[10px] text-slate-400 font-medium">Điền địa chỉ giao hàng và phương thức</p>
                        </div>
                    </div>

                    <!-- Form thông tin nhận hàng -->
                    @guest
                        <div class="space-y-2">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-0.5">
                                    Họ và tên <span class="text-[#ee4d2d]">*</span>
                                </label>
                                <input type="text" name="cart_fullname" required placeholder="Nguyễn Văn A" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-0.5">
                                    Email nhận hóa đơn <span class="text-[#ee4d2d]">*</span>
                                </label>
                                <input type="email" name="cart_email" required placeholder="email@example.com" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                            </div>
                        </div>
                    @endguest

                    @auth
                        @php
                            $checkoutUser = auth()->user();
                            $coDefaultType = $checkoutUser->default_address_type ?: 'home';
                            $coHomeAddr = $checkoutUser->address ?: '';
                            $coCompanyAddr = $checkoutUser->company_address ?: '';
                            $coOtherAddr = $checkoutUser->other_address ?: '';
                            $coInitialAddr = match($coDefaultType) {
                                'company' => $coCompanyAddr ?: ($coHomeAddr ?: $coOtherAddr),
                                'other' => $coOtherAddr ?: ($coHomeAddr ?: $coCompanyAddr),
                                default => $coHomeAddr ?: ($coCompanyAddr ?: $coOtherAddr),
                            };
                        @endphp
                    @endauth

                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                Số điện thoại nhận hàng <span class="text-[#ee4d2d]">*</span>
                            </label>
                            <input type="tel" name="cart_phone" value="{{ auth()->check() ? auth()->user()->phone : '' }}" required placeholder="0901234567" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">
                                    Địa chỉ giao hàng <span class="text-[#ee4d2d]">*</span>
                                </label>
                                @auth
                                    <a href="{{ route('hoso', ['tab' => 'address']) }}" target="_blank" class="text-[10px] font-bold text-[#ee4d2d] hover:underline flex items-center gap-1" title="Quản lý sổ địa chỉ trong hồ sơ">
                                        <i class="fas fa-id-card text-[9px]"></i> Sổ địa chỉ
                                    </a>
                                @endauth
                            </div>

                            @auth
                                <!-- Address Quick Switcher Pills -->
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <input type="hidden" name="active_address_type" id="active_address_type" value="{{ $coDefaultType }}">
                                    
                                    <button type="button" onclick="selectCheckoutAddress('home')" id="btn-addr-home" class="checkout-addr-pill flex-1 py-1 px-1.5 rounded-lg text-[10px] font-bold border transition-all flex items-center justify-center gap-1 cursor-pointer {{ $coDefaultType === 'home' ? 'bg-rose-50 text-[#ee4d2d] border-[#ee4d2d] font-black' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                                        <span>🏠 Nhà</span>
                                    </button>

                                    <button type="button" onclick="selectCheckoutAddress('company')" id="btn-addr-company" class="checkout-addr-pill flex-1 py-1 px-1.5 rounded-lg text-[10px] font-bold border transition-all flex items-center justify-center gap-1 cursor-pointer {{ $coDefaultType === 'company' ? 'bg-rose-50 text-[#ee4d2d] border-[#ee4d2d] font-black' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                                        <span>🏢 Công ty</span>
                                    </button>

                                    <button type="button" onclick="selectCheckoutAddress('other')" id="btn-addr-other" class="checkout-addr-pill flex-1 py-1 px-1.5 rounded-lg text-[10px] font-bold border transition-all flex items-center justify-center gap-1 cursor-pointer {{ $coDefaultType === 'other' ? 'bg-rose-50 text-[#ee4d2d] border-[#ee4d2d] font-black' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                                        <span>📍 Khác</span>
                                    </button>
                                </div>
                            @endauth

                            <textarea name="cart_address" id="checkout_cart_address" required rows="2" placeholder="Số nhà, tên đường, Phường/Quận, TP. HCM..." class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs leading-normal resize-none">{{ auth()->check() ? $coInitialAddr : '' }}</textarea>

                            @auth
                                <label class="flex items-center gap-1.5 text-[10px] text-slate-500 font-semibold mt-1 cursor-pointer select-none">
                                    <input type="checkbox" name="save_to_profile" value="1" checked class="rounded accent-[#ee4d2d]">
                                    <span>Tự động lưu SĐT & địa chỉ này vào Hồ sơ</span>
                                </label>
                            @endauth
                        </div>

                        <div>
                            <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                Thời gian nhận hàng
                            </label>
                            <select name="cart_time" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none shadow-xs">
                                <option value="now" selected>⚡ Giao siêu tốc 20 phút</option>
                                <option value="h1">Hẹn giờ (30 - 45 phút tới)</option>
                                <option value="h2">Hẹn giờ (Buổi trưa 11:30 - 12:30)</option>
                                <option value="h3">Hẹn giờ (Buổi chiều 17:30 - 18:30)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Chọn hình thức thanh toán -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <label class="block text-[10px] font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                            Hình thức thanh toán
                        </label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:border-[#ee4d2d] transition-all bg-slate-50/50 has-checked:border-[#ee4d2d] has-checked:bg-rose-50/40">
                                <input type="radio" name="cart_payment" value="cod" checked class="w-3.5 h-3.5 text-[#ee4d2d] focus:ring-[#ee4d2d]">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <span>💵 Tiền mặt khi nhận (COD)</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:border-[#ee4d2d] transition-all bg-slate-50/50 has-checked:border-[#ee4d2d] has-checked:bg-rose-50/40">
                                <input type="radio" name="cart_payment" value="atm" class="w-3.5 h-3.5 text-[#ee4d2d] focus:ring-[#ee4d2d]">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <span>💳 Chuyển khoản VietQR Hỏa Tốc</span>
                                </span>
                            </label>
                            <label class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-slate-200 cursor-pointer hover:border-[#ee4d2d] transition-all bg-slate-50/50 has-checked:border-[#ee4d2d] has-checked:bg-rose-50/40">
                                <input type="radio" name="cart_payment" value="momo" class="w-3.5 h-3.5 text-[#ee4d2d] focus:ring-[#ee4d2d]">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <span>👛 Ví điện tử MoMo</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Bảng tính tổng tiền -->
                    <div class="pt-2.5 border-t border-slate-100 space-y-1 text-xs">
                        <div class="flex justify-between text-slate-500 font-medium text-[11px]">
                            <span>Tạm tính món ăn:</span>
                            <span id="summary-subtotal" class="font-bold text-slate-800">0đ</span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-medium text-[11px]">
                            <span>Phí giao hàng:</span>
                            <span class="font-extrabold text-emerald-600">Miễn phí (Freeship)</span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-medium text-[11px]" id="discount-row" style="display:none;">
                            <span>Giảm giá voucher:</span>
                            <span id="summary-discount" class="font-black text-[#ee4d2d]">-0đ</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-black text-slate-900 pt-2 border-t border-slate-100">
                            <span class="uppercase tracking-wider">TỔNG THANH TOÁN:</span>
                            <span id="summary-total" class="text-xl font-black text-[#ee4d2d]">0đ</span>
                        </div>
                    </div>

                    <!-- Nút đặt hàng -->
                    <button type="submit" id="btn-submit-order" class="w-full py-2.5 sm:py-3 rounded-xl text-white font-black text-xs sm:text-sm shadow-md shadow-rose-500/25 hover:brightness-110 active:scale-[0.99] transition-all uppercase tracking-wider cursor-pointer flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5d3b 100%) !important; color: #ffffff !important; border: none !important;">
                        <i class="fas fa-shield-check text-sm"></i>
                        <span>ĐẶT HÀNG NGAY</span>
                    </button>
                    <p class="text-[9px] text-center text-slate-400 font-medium">
                        Cam kết giao hàng đúng hẹn & đảm bảo chất lượng món ăn
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    let appliedDiscount = 0;

    function getCart() {
        return JSON.parse(localStorage.getItem('fooddelicious_cart') || '[]');
    }

    function saveCart(cart) {
        localStorage.setItem('fooddelicious_cart', JSON.stringify(cart));
        if (typeof updateGlobalHeaderCartBadge === 'function') {
            updateGlobalHeaderCartBadge();
        }
        renderCartItems();
    }

    function quickAddDish(id, name, price, image) {
        let cart = getCart();
        const idx = cart.findIndex(i => i.id == id);
        if (idx > -1) {
            cart[idx].quantity = (parseInt(cart[idx].quantity) || 1) + 1;
        } else {
            cart.push({
                id: parseInt(id),
                name: name,
                price: parseFloat(price),
                quantity: 1,
                image: image || ''
            });
        }
        saveCart(cart);
    }

    function clearCart() {
        if (confirm('Bạn có chắc muốn xóa tất cả món ăn khỏi giỏ hàng?')) {
            localStorage.removeItem('fooddelicious_cart');
            if (typeof updateGlobalHeaderCartBadge === 'function') {
                updateGlobalHeaderCartBadge();
            }
            renderCartItems();
        }
    }

    function updateQty(index, delta) {
        let cart = getCart();
        if (cart[index]) {
            cart[index].quantity = (parseInt(cart[index].quantity) || 1) + delta;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart(cart);
        }
    }

    function removeItem(index) {
        let cart = getCart();
        if (cart[index]) {
            cart.splice(index, 1);
            saveCart(cart);
        }
    }

    function formatMoney(num) {
        return (num || 0).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + 'đ';
    }

    function renderCartItems() {
        const cart = getCart();
        const container = document.getElementById('cart-items-list');
        const countSpan = document.getElementById('cart-item-count');
        const cartInput = document.getElementById('cart_items_input');
        const submitBtn = document.getElementById('btn-submit-order');

        countSpan.innerText = cart.length;
        cartInput.value = JSON.stringify(cart);

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="py-12 text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-500">Giỏ hàng của bạn đang trống</p>
                    <a href="{{ route('trangchu') }}" class="inline-block px-5 py-2.5 rounded-xl bg-[#ee4d2d] text-white text-xs font-extrabold shadow-sm hover:bg-red-600 transition-colors">
                        Khám phá thực đơn ngay
                    </a>
                </div>
            `;
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
            document.getElementById('summary-subtotal').innerText = '0đ';
            document.getElementById('summary-total').innerText = '0đ';
            return;
        }

        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        let html = '';
        let subtotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 1);
            subtotal += itemTotal;

            html += `
                <div class="py-4 flex items-center gap-4 group border-b border-slate-100 last:border-0">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                        ${item.image ? `<img src="${item.image}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils"></i></div>`}
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <h4 class="font-extrabold text-slate-900 text-xs truncate">${item.name}</h4>
                        <p class="text-xs text-[#ee4d2d] font-black">${formatMoney(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-1.5 bg-slate-100 rounded-xl p-1 border border-slate-200">
                        <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 rounded-lg bg-white text-slate-800 font-black text-xs hover:bg-slate-200 flex items-center justify-center border border-slate-200 shadow-2xs">-</button>
                        <span class="text-xs font-black text-slate-900 px-2">${item.quantity}</span>
                        <button type="button" onclick="updateQty(${index}, 1)" class="w-6 h-6 rounded-lg bg-white text-slate-800 font-black text-xs hover:bg-slate-200 flex items-center justify-center border border-slate-200 shadow-2xs">+</button>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black text-slate-900 block">${formatMoney(itemTotal)}</span>
                        <button type="button" onclick="removeItem(${index})" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 transition-colors">Xóa</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        document.getElementById('summary-subtotal').innerText = formatMoney(subtotal);

        const finalTotal = (subtotal > 1000) ? Math.max(1000, subtotal - appliedDiscount) : subtotal;
        document.getElementById('summary-total').innerText = formatMoney(finalTotal);

        if (shouldAutoApply !== false) {
            autoApplyBestVoucher();
        }
    }

    const IS_LOGGED_IN = {{ auth()->check() ? 'true' : 'false' }};
    let userHasManuallyChosenCoupon = false;

    function applyCoupon(forcedCode) {
        if (!IS_LOGGED_IN) {
            alert('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập để tiếp tục!');
            window.location.href = "{{ route('dangnhap') }}";
            return;
        }

        const codeInput = document.getElementById('coupon_code');
        const hiddenCodeInput = document.getElementById('hidden_coupon_code');
        const code = (forcedCode || (codeInput ? codeInput.value : '')).trim();
        const msgDiv = document.getElementById('coupon-message');
        const cart = getCart();
        let subtotal = cart.reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (parseInt(i.quantity) || 1)), 0);

        if (!code) {
            if (msgDiv) {
                msgDiv.innerText = 'Vui lòng nhập mã giảm giá!';
                msgDiv.className = 'text-xs font-bold text-rose-500 block';
            }
            return;
        }

        fetch("{{ route('api.coupon.validate') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ code: code, total_amount: subtotal })
        })
        .then(res => res.json())
        .then(data => {
            if (data.require_login) {
                alert(data.message);
                window.location.href = "{{ route('dangnhap') }}";
                return;
            }

            if (data.success) {
                appliedDiscount = data.discount_amount;
                if (hiddenCodeInput) hiddenCodeInput.value = code;
                if (codeInput) codeInput.value = code;
                userHasManuallyChosenCoupon = true;

                if (msgDiv) {
                    msgDiv.innerText = data.message + ' (-' + formatMoney(appliedDiscount) + ')';
                    msgDiv.className = 'text-xs font-bold text-emerald-600 block';
                }

                // Show banner
                const banner = document.getElementById('auto-coupon-banner');
                const bannerText = document.getElementById('auto-coupon-text');
                if (banner && bannerText) {
                    bannerText.innerHTML = `Đang áp dụng: <strong>#${code}</strong> (Tiết kiệm ${formatMoney(appliedDiscount)})`;
                    banner.classList.remove('hidden');
                    banner.classList.add('flex');
                }

                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('summary-discount').innerText = '-' + formatMoney(appliedDiscount);
                renderCartItems(false);
            } else {
                appliedDiscount = 0;
                if (hiddenCodeInput) hiddenCodeInput.value = '';
                if (msgDiv) {
                    msgDiv.innerText = data.message;
                    msgDiv.className = 'text-xs font-bold text-rose-500 block';
                }
                document.getElementById('discount-row').style.display = 'none';
                renderCartItems(false);
            }
        })
        .catch(err => {
            console.error('Coupon error:', err);
        });
    }

    // Tự động tìm & áp dụng mã giảm giá tốt nhất khi vào trang hoặc giỏ hàng đổi
    function autoApplyBestVoucher() {
        if (!IS_LOGGED_IN || userHasManuallyChosenCoupon) return;

        const cart = getCart();
        let subtotal = cart.reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (parseInt(i.quantity) || 1)), 0);
        if (subtotal <= 0) return;

        fetch("{{ route('api.coupon.best') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ total_amount: subtotal })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.has_coupon && data.best_coupon) {
                const best = data.best_coupon;
                appliedDiscount = best.discount_amount;
                
                const hiddenCodeInput = document.getElementById('hidden_coupon_code');
                const codeInput = document.getElementById('coupon_code');
                if (hiddenCodeInput) hiddenCodeInput.value = best.coupon_code;
                if (codeInput) codeInput.value = best.coupon_code;

                const banner = document.getElementById('auto-coupon-banner');
                const bannerText = document.getElementById('auto-coupon-text');
                if (banner && bannerText) {
                    bannerText.innerHTML = `✨ Đã tự động chọn mã tốt nhất: <strong>#${best.coupon_code}</strong> (Tiết kiệm ${formatMoney(appliedDiscount)})`;
                    banner.classList.remove('hidden');
                    banner.classList.add('flex');
                }

                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('summary-discount').innerText = '-' + formatMoney(appliedDiscount);
                renderCartItems(false);
            }
        })
        .catch(err => console.error('Auto coupon error:', err));
    }

    // Modal chọn mã khuyến mãi
    function openCouponModal() {
        if (!IS_LOGGED_IN) {
            alert('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập!');
            window.location.href = "{{ route('dangnhap') }}";
            return;
        }

        const modal = document.getElementById('couponSelectorModal');
        const listDiv = document.getElementById('coupon-modal-list');
        modal.classList.remove('hidden');

        const cart = getCart();
        let subtotal = cart.reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (parseInt(i.quantity) || 1)), 0);

        listDiv.innerHTML = '<div class="text-center py-6 text-xs text-slate-400 font-semibold"><i class="fas fa-spinner fa-spin mr-1"></i> Đang tải danh sách ưu đãi...</div>';

        fetch("{{ route('api.coupon.best') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ total_amount: subtotal })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                listDiv.innerHTML = `<div class="p-4 text-center text-xs text-rose-500 font-bold">${data.message}</div>`;
                return;
            }

            const currentCode = (document.getElementById('hidden_coupon_code')?.value || '').trim();
            let html = '';

            if (!data.all_coupons || data.all_coupons.length === 0) {
                listDiv.innerHTML = '<div class="text-center py-6 text-xs text-slate-400 font-bold">Hiện không có mã khuyến mãi nào khả dụng.</div>';
                return;
            }

            data.all_coupons.forEach(c => {
                const isSelected = currentCode === c.coupon_code;
                const isEligible = c.is_eligible;

                html += `
                    <div class="p-3.5 rounded-2xl border transition-all flex items-center justify-between gap-3 ${isSelected ? 'bg-rose-50/60 border-[#ee4d2d]' : (isEligible ? 'bg-white border-slate-200 hover:border-rose-300' : 'bg-slate-50/60 border-slate-200 opacity-60')}">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-xl ${isEligible ? 'bg-[#ee4d2d] text-white' : 'bg-slate-300 text-slate-600'} flex flex-col items-center justify-center font-black text-center shrink-0">
                                <span class="text-xs leading-none">${c.discount_type === 'percent' ? c.discount_value + '%' : (c.discount_value/1000) + 'K'}</span>
                                <span class="text-[8px] uppercase mt-0.5">GIẢM</span>
                            </div>
                            <div class="space-y-0.5 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-black text-slate-900 text-xs tracking-wider">#${c.coupon_code}</span>
                                    ${c.is_saved ? '<span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 text-[9px] font-black">Trong ví</span>' : ''}
                                    ${c.is_expiring_soon ? `<span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-black">⏰ Còn ${c.days_remaining} ngày</span>` : ''}
                                </div>
                                <p class="text-xs font-bold text-slate-800 truncate">${c.formatted_discount} - ${c.formatted_min_order}</p>
                                ${!isEligible ? `<p class="text-[10px] text-rose-500 font-bold">${c.ineligible_reason}</p>` : `<p class="text-[10px] text-emerald-600 font-bold">Tiết kiệm: -${formatMoney(c.discount_amount)}</p>`}
                            </div>
                        </div>

                        <div>
                            ${isSelected ? `
                                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-300 font-extrabold text-xs flex items-center gap-1">
                                    <i class="fas fa-check text-[10px]"></i> Đang dùng
                                </span>
                            ` : (isEligible ? `
                                <button type="button" onclick="selectSpecificCoupon('${c.coupon_code}')" class="px-3.5 py-1.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-xs transition-colors cursor-pointer">
                                    Áp dụng
                                </button>
                            ` : `
                                <button type="button" disabled class="px-3 py-1.5 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs cursor-not-allowed">
                                    Chưa đủ ĐK
                                </button>
                            `)}
                        </div>
                    </div>
                `;
            });

            listDiv.innerHTML = html;
        })
        .catch(err => {
            listDiv.innerHTML = '<div class="p-4 text-center text-xs text-rose-500 font-bold">Không thể tải danh sách ưu đãi.</div>';
        });
    }

    function selectSpecificCoupon(code) {
        closeCouponModal();
        applyCoupon(code);
    }

    function removeCoupon() {
        appliedDiscount = 0;
        userHasManuallyChosenCoupon = true;
        const hiddenCodeInput = document.getElementById('hidden_coupon_code');
        const codeInput = document.getElementById('coupon_code');
        const msgDiv = document.getElementById('coupon-message');
        const banner = document.getElementById('auto-coupon-banner');
        
        if (hiddenCodeInput) hiddenCodeInput.value = '';
        if (codeInput) codeInput.value = '';
        if (msgDiv) {
            msgDiv.innerText = 'Đã hủy áp dụng mã giảm giá.';
            msgDiv.className = 'text-xs font-bold text-slate-500 block';
        }
        if (banner) {
            banner.classList.add('hidden');
            banner.classList.remove('flex');
        }
        document.getElementById('discount-row').style.display = 'none';
        closeCouponModal();
        renderCartItems(false);
    }

    function closeCouponModal() {
        const modal = document.getElementById('couponSelectorModal');
        if (modal) modal.classList.add('hidden');
    }

    @auth
        const userSavedAddresses = {
            home: @json($coHomeAddr),
            company: @json($coCompanyAddr),
            other: @json($coOtherAddr)
        };

        function selectCheckoutAddress(type) {
            const inputActiveType = document.getElementById('active_address_type');
            const addressTextarea = document.getElementById('checkout_cart_address');
            if (inputActiveType) inputActiveType.value = type;

            ['home', 'company', 'other'].forEach(t => {
                const btn = document.getElementById('btn-addr-' + t);
                if (btn) {
                    if (t === type) {
                        btn.className = 'checkout-addr-pill flex-1 py-1 px-1.5 rounded-lg text-[10px] font-black border transition-all flex items-center justify-center gap-1 cursor-pointer bg-rose-50 text-[#ee4d2d] border-[#ee4d2d]';
                    } else {
                        btn.className = 'checkout-addr-pill flex-1 py-1 px-1.5 rounded-lg text-[10px] font-bold border transition-all flex items-center justify-center gap-1 cursor-pointer bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100';
                    }
                }
            });

            if (userSavedAddresses[type]) {
                addressTextarea.value = userSavedAddresses[type];
            }
        }

        document.getElementById('checkout_cart_address')?.addEventListener('input', function() {
            const currentType = document.getElementById('active_address_type')?.value || 'home';
            userSavedAddresses[currentType] = this.value;
        });
    @endauth

    document.addEventListener("DOMContentLoaded", function () {
        @if(session('added_dish'))
            const added = @json(session('added_dish'));
            if (added && added.id) {
                let cart = getCart();
                const idx = cart.findIndex(i => i.id == added.id);
                if (idx > -1) {
                    cart[idx].quantity = (parseInt(cart[idx].quantity) || 1) + (parseInt(added.quantity) || 1);
                } else {
                    cart.push({
                        id: parseInt(added.id),
                        name: added.name,
                        price: parseFloat(added.price),
                        quantity: parseInt(added.quantity) || 1,
                        image: added.image || ''
                    });
                }
                saveCart(cart);
            }
        @endif
        renderCartItems(true);
    });
</script>
@endsection

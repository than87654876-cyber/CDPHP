@extends('layouts.app')

@section('title', 'Giỏ Hàng & Thanh Toán - FOODDAILY')

@section('content')
<div class="py-8 bg-transparent min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation Breadcrumb -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d]">Trang chủ</a>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                <span class="text-[#ee4d2d] font-bold">Giỏ hàng & Thanh toán</span>
            </div>
            <a href="{{ route('trangchu') }}#menu" class="text-xs font-bold text-[#ee4d2d] hover:underline flex items-center gap-1">
                <i class="fas fa-plus-circle"></i> Thêm món ăn khác
            </a>
        </div>

        <form action="{{ route('muahang.process') }}" method="POST" id="checkoutForm" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            @csrf
            
            <!-- Hidden Input JSON for Cart Items -->
            <input type="hidden" name="cart_items" id="cart_items_input" value="[]">

            <!-- LEFT COLUMN: CART ITEMS LIST (8 Columns) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Cart Items Container -->
                <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-6 shadow-sm border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-sm">
                                <i class="fas fa-shopping-basket"></i>
                            </span>
                            <h2 class="text-base font-extrabold text-slate-900">Món Ăn Đã Chọn (<span id="cart-item-count">0</span>)</h2>
                        </div>
                        <button type="button" onclick="clearCart()" class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors">
                            <i class="far fa-trash-can mr-1"></i> Xóa tất cả
                        </button>
                    </div>

                    <!-- Cart List Render Target -->
                    <div id="cart-items-list" class="divide-y divide-slate-100 min-h-[160px]">
                        <!-- Rendered by JavaScript -->
                    </div>

                    <!-- Coupon Code Input Box -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                        <div class="relative flex-1 w-full">
                            <input type="text" id="coupon_code" name="coupon_code" placeholder="Nhập mã giảm giá (VD: FOODDAILY30)" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none uppercase">
                        </div>
                        <button type="button" onclick="applyCoupon()" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-extrabold transition-colors">
                            Áp dụng
                        </button>
                    </div>
                    <div id="coupon-message" class="text-xs font-bold hidden"></div>
                </div>

                <!-- ORDER HISTORY (Đơn hàng gần đây) -->
                @auth
                    @php
                        $userOrders = \App\Models\Order::where('user_id', auth()->id())->with('orderItems.dish')->orderBy('created_at', 'desc')->take(5)->get();
                    @endphp
                    @if($userOrders->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-clock-rotate-left text-[#ee4d2d]"></i> Đơn Hàng Vừa Đặt Gần Đây
                            </h3>
                            <a href="{{ route('giohang') }}" class="text-xs font-bold text-[#0099ff] hover:underline">Xem tất cả</a>
                        </div>
                        <div class="space-y-3">
                            @foreach($userOrders as $uOrder)
                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2.5 py-0.5 rounded bg-rose-100 dark:bg-rose-950 text-[#ee4d2d] text-[10px] font-black">#FDL-{{ $uOrder->id }}</span>
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $uOrder->created_at->format('H:i d/m/Y') }}</span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                            {{ $uOrder->orderItems->pluck('dish.dish_name')->filter()->implode(', ') }}
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-4">
                                        <span class="text-xs font-black text-[#ee4d2d]">{{ number_format($uOrder->final_amount) }}đ</span>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                            @if($uOrder->order_status == 'completed') bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300
                                            @elseif($uOrder->order_status == 'cancelled') bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300
                                            @else bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 @endif">
                                            {{ $uOrder->order_status == 'preparing' ? 'Đang chế biến' : ($uOrder->order_status == 'delivering' ? 'Đang giao' : $uOrder->order_status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endauth
            </div>

            <!-- RIGHT COLUMN: CHECKOUT & PAYMENT FORM (4 Columns) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 space-y-5 sticky top-20">
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white pb-3 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                        <i class="fas fa-truck-fast text-[#ee4d2d]"></i> Thông Tin Nhận Hàng
                    </h3>

                    <!-- Non-logged User Info Fields -->
                    @guest
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Họ và tên <span class="text-[#ee4d2d]">*</span></label>
                                <input type="text" name="cart_fullname" required placeholder="Nguyễn Văn A" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-white focus:border-[#ee4d2d] outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email xác nhận <span class="text-[#ee4d2d]">*</span></label>
                                <input type="email" name="cart_email" required placeholder="email@example.com" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-white focus:border-[#ee4d2d] outline-none">
                            </div>
                        </div>
                    @endguest

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Số điện thoại nhận hàng <span class="text-[#ee4d2d]">*</span></label>
                            <input type="tel" name="cart_phone" value="{{ auth()->check() ? auth()->user()->phone : '' }}" required placeholder="0901234567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-white focus:border-[#ee4d2d] outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Địa chỉ giao hàng <span class="text-[#ee4d2d]">*</span></label>
                            <textarea name="cart_address" required rows="2" placeholder="Số nhà, tên đường, Phường/Quận, TP. HCM..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-white focus:border-[#ee4d2d] outline-none">{{ auth()->check() ? auth()->user()->notes : '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Thời gian nhận hàng</label>
                            <select name="cart_time" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-800 dark:text-white focus:border-[#ee4d2d] outline-none">
                                <option value="now" selected>⚡ Giao siêu tốc 20 phút</option>
                                <option value="h1">Hẹn giờ (30 - 45 phút tới)</option>
                                <option value="h2">Hẹn giờ (Buổi trưa 11:30 - 12:30)</option>
                                <option value="h3">Hẹn giờ (Buổi chiều 17:30 - 18:30)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Payment Method Selector -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                        <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Hình thức thanh toán</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-[#ee4d2d] transition-colors">
                                <input type="radio" name="cart_payment" value="cod" checked class="text-[#ee4d2d] focus:ring-[#ee4d2d]">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">💵 Tiền mặt khi nhận (COD)</span>
                            </label>
                            <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-[#ee4d2d] transition-colors">
                                <input type="radio" name="cart_payment" value="atm" class="text-[#ee4d2d] focus:ring-[#ee4d2d]">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">💳 Chuyển khoản VietQR Hỏa Tốc</span>
                            </label>
                            <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-[#ee4d2d] transition-colors">
                                <input type="radio" name="cart_payment" value="momo" class="text-[#ee4d2d] focus:ring-[#ee4d2d]">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">👛 Ví điện tử MoMo</span>
                            </label>
                        </div>
                    </div>

                    <!-- Order Price Summary -->
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-700 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Tạm tính món ăn:</span>
                            <span id="summary-subtotal" class="font-bold text-slate-800 dark:text-slate-200">0đ</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Phí giao hàng (Freeship):</span>
                            <span class="font-bold text-emerald-600">0đ</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400" id="discount-row" style="display:none;">
                            <span>Giảm giá voucher:</span>
                            <span id="summary-discount" class="font-bold text-[#ee4d2d]">-0đ</span>
                        </div>
                        <div class="flex justify-between text-sm font-black text-slate-900 dark:text-white pt-2 border-t border-slate-100 dark:border-slate-700">
                            <span>TỔNG THANH TOÁN:</span>
                            <span id="summary-total" class="text-base text-[#ee4d2d]">0đ</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-order" class="w-full py-3.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all uppercase tracking-wider">
                        ĐẶT HÀNG NGAY
                    </button>
                </div>
            </div>
        </form>

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
            cart[index].quantity += delta;
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
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + 'đ';
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
                    <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-950/50 text-[#ee4d2d] flex items-center justify-center text-2xl mx-auto">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm">Giỏ hàng của bạn đang trống</h3>
                    <p class="text-xs text-slate-400">Hãy thêm món ăn tươi ngon từ thực đơn để tiếp tục đặt hàng.</p>
                    <a href="{{ route('trangchu') }}#menu" class="inline-block px-5 py-2.5 bg-[#ee4d2d] text-white font-extrabold text-xs rounded-xl hover:bg-red-600 transition-colors">
                        Khám phá thực đơn ngay
                    </a>
                </div>
            `;
            if (submitBtn) submitBtn.disabled = true;
            document.getElementById('summary-subtotal').innerText = '0đ';
            document.getElementById('summary-total').innerText = '0đ';
            return;
        }

        if (submitBtn) submitBtn.disabled = false;

        let subtotal = 0;
        let html = '';

        cart.forEach((item, index) => {
            const itemTotal = (item.price || 0) * (item.quantity || 1);
            subtotal += itemTotal;

            html += `
                <div class="py-4 flex items-center gap-4 group">
                    <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-slate-700 overflow-hidden shrink-0 border border-slate-200 dark:border-slate-600">
                        ${item.image ? `<img src="${item.image}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils"></i></div>`}
                    </div>
                    <div class="flex-1 min-w-0 space-y-1">
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs truncate">${item.name}</h4>
                        <p class="text-xs text-[#ee4d2d] font-black">${formatMoney(item.price)}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-700 rounded-lg p-1 border border-slate-200 dark:border-slate-600">
                        <button type="button" onclick="updateQty(${index}, -1)" class="w-6 h-6 rounded bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-200 flex items-center justify-center">-</button>
                        <span class="text-xs font-black text-slate-900 dark:text-white px-2">${item.quantity}</span>
                        <button type="button" onclick="updateQty(${index}, 1)" class="w-6 h-6 rounded bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold text-xs hover:bg-slate-200 flex items-center justify-center">+</button>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs font-black text-slate-900 dark:text-white block">${formatMoney(itemTotal)}</span>
                        <button type="button" onclick="removeItem(${index})" class="text-[11px] font-bold text-slate-400 hover:text-rose-600 transition-colors">Xóa</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        document.getElementById('summary-subtotal').innerText = formatMoney(subtotal);

        const finalTotal = Math.max(0, subtotal - appliedDiscount);
        document.getElementById('summary-total').innerText = formatMoney(finalTotal);
    }

    function applyCoupon() {
        const code = document.getElementById('coupon_code').value.trim();
        const msgDiv = document.getElementById('coupon-message');
        const cart = getCart();
        let subtotal = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);

        if (!code) {
            msgDiv.innerText = 'Vui lòng nhập mã giảm giá!';
            msgDiv.className = 'text-xs font-bold text-rose-500 block';
            return;
        }

        fetch("{{ route('api.coupon.validate') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ code: code, total_amount: subtotal })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appliedDiscount = data.discount_amount;
                msgDiv.innerText = data.message + ' (-' + formatMoney(appliedDiscount) + ')';
                msgDiv.className = 'text-xs font-bold text-emerald-600 block';
                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('summary-discount').innerText = '-' + formatMoney(appliedDiscount);
                renderCartItems();
            } else {
                appliedDiscount = 0;
                msgDiv.innerText = data.message;
                msgDiv.className = 'text-xs font-bold text-rose-500 block';
                document.getElementById('discount-row').style.display = 'none';
                renderCartItems();
            }
        })
        .catch(err => {
            console.error('Coupon error:', err);
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        renderCartItems();
    });
</script>
@endsection

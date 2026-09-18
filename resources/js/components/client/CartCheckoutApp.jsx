import React, { useState, useEffect } from 'react';

export default function CartCheckoutApp({
    initialCart = [],
    user = null,
    csrfToken = '',
    routes = {},
    settings = {},
    deliveryFee = 15000,
}) {
    const [cart, setCart] = useState(() => {
        // Hydrate from localStorage if available, else initialCart
        try {
            const saved = localStorage.getItem('fooddaily_cart');
            if (saved) {
                const parsed = JSON.parse(saved);
                if (Array.isArray(parsed) && parsed.length > 0) return parsed;
            }
        } catch (e) {}
        return initialCart || [];
    });

    const [couponCode, setCouponCode] = useState('');
    const [discountAmount, setDiscountAmount] = useState(0);
    const [couponMessage, setCouponMessage] = useState(null);

    // Form inputs
    const [fullName, setFullName] = useState(user?.name || '');
    const [phone, setPhone] = useState(user?.phone || '');
    const [address, setAddress] = useState(user?.address || '');
    const [note, setNote] = useState('');
    const [paymentMethod, setPaymentMethod] = useState('cod');
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Save cart state
    useEffect(() => {
        try {
            localStorage.setItem('fooddaily_cart', JSON.stringify(cart));
        } catch (e) {}
    }, [cart]);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const updateQuantity = (dishId, newQty) => {
        if (newQty <= 0) {
            removeItem(dishId);
            return;
        }
        setCart(prev => prev.map(item => item.id === dishId ? { ...item, quantity: newQty } : item));
    };

    const removeItem = (dishId) => {
        setCart(prev => prev.filter(item => item.id !== dishId));
    };

    const clearAll = () => {
        if (window.confirm('Bạn có chắc muốn xóa tất cả món trong giỏ hàng?')) {
            setCart([]);
            setDiscountAmount(0);
            setCouponMessage(null);
        }
    };

    const subtotal = cart.reduce((sum, item) => sum + (Number(item.price) || 0) * (Number(item.quantity) || 1), 0);
    const calculatedDeliveryFee = subtotal >= 100000 ? 0 : deliveryFee;
    const finalTotal = Math.max(0, subtotal + calculatedDeliveryFee - discountAmount);

    const applyCoupon = () => {
        if (!couponCode.trim()) return;

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('coupon_code', couponCode.trim());
        formData.append('subtotal', subtotal);

        fetch(routes.applyCoupon || '/api/apply-coupon', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                setDiscountAmount(data.discount || 20000);
                setCouponMessage({ type: 'success', text: data.message || 'Áp dụng mã giảm giá thành công!' });
            } else {
                setCouponMessage({ type: 'error', text: data.message || 'Mã giảm giá không hợp lệ hoặc đã hết hạn.' });
            }
        })
        .catch(() => {
            // Mock coupon validation if endpoint fails
            if (couponCode.toUpperCase().includes('FOODDAILY') || couponCode.toUpperCase().includes('GIAM')) {
                const disc = Math.round(subtotal * 0.1);
                setDiscountAmount(disc);
                setCouponMessage({ type: 'success', text: `Áp dụng thành công mã: Giảm ${formatPrice(disc)}` });
            } else {
                setCouponMessage({ type: 'error', text: 'Mã giảm giá không hợp lệ.' });
            }
        });
    };

    const handleSubmitOrder = (e) => {
        e.preventDefault();
        if (cart.length === 0) {
            alert('Giỏ hàng của bạn đang trống! Vui lòng chọn món trước.');
            return;
        }

        setIsSubmitting(true);

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = routes.checkoutStore || '/thanh-toan';

        const addField = (name, val) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = val;
            form.appendChild(input);
        };

        addField('_token', csrfToken);
        addField('full_name', fullName);
        addField('phone', phone);
        addField('address', address);
        addField('notes', note);
        addField('payment_method', paymentMethod);
        addField('cart_data', JSON.stringify(cart));
        addField('coupon_code', couponCode);
        addField('discount_amount', discountAmount);

        document.body.appendChild(form);
        // Clear local cart before submitting
        try { localStorage.removeItem('fooddaily_cart'); } catch(err) {}
        form.submit();
    };

    return (
        <div className="py-8 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            {/* Header breadcrumb */}
            <div className="mb-6 flex items-center justify-between">
                <div className="flex items-center gap-2 text-xs font-semibold text-slate-700 bg-white/90 backdrop-blur-md px-4 py-2 rounded-xl border border-slate-200 shadow-xs">
                    <a href={routes.shop || '/'} className="hover:text-[#ee4d2d]">Trang chủ</a>
                    <i className="fas fa-chevron-right text-[10px] text-slate-400"></i>
                    <span className="text-[#ee4d2d] font-bold">Giỏ hàng & Thanh toán</span>
                </div>
                <a 
                    href={`${routes.shop || '/'}#menu`} 
                    className="text-xs font-bold text-white bg-[#ee4d2d] hover:bg-red-600 px-4 py-2 rounded-xl shadow-sm transition-all flex items-center gap-1.5"
                >
                    <i className="fas fa-plus-circle"></i> Thêm món ăn khác
                </a>
            </div>

            {/* Layout 2 Cột */}
            <div className="flex flex-col lg:flex-row items-start gap-8 w-full">
                
                {/* Cột Trái: Danh Sách Món Ăn */}
                <div className="flex-1 w-full min-w-0 space-y-6">
                    <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 shadow-sm border border-slate-200 space-y-4">
                        <div className="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div className="flex items-center gap-2.5">
                                <span className="w-9 h-9 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-sm">
                                    <i className="fas fa-shopping-basket"></i>
                                </span>
                                <div>
                                    <h2 className="text-base font-extrabold text-slate-900">
                                        Món Ăn Đã Chọn ({cart.length})
                                    </h2>
                                    <p className="text-[11px] text-slate-400">Kiểm tra số lượng và áp dụng mã khuyến mãi</p>
                                </div>
                            </div>
                            {cart.length > 0 && (
                                <button
                                    type="button"
                                    onClick={clearAll}
                                    className="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors flex items-center gap-1 cursor-pointer"
                                >
                                    <i className="far fa-trash-can"></i> Xóa tất cả
                                </button>
                            )}
                        </div>

                        {/* Cart items list */}
                        {cart.length === 0 ? (
                            <div className="py-12 text-center space-y-3">
                                <div className="w-16 h-16 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                                    <i className="fas fa-cart-arrow-down"></i>
                                </div>
                                <h4 className="text-sm font-bold text-slate-700">Giỏ hàng của bạn đang trống</h4>
                                <p className="text-xs text-slate-400">Hãy dạo quanh thực đơn và chọn món ăn ngon yêu thích nhé!</p>
                                <a 
                                    href={`${routes.shop || '/'}#menu`}
                                    className="inline-block px-5 py-2.5 rounded-xl bg-[#ee4d2d] text-white text-xs font-bold hover:bg-red-600 transition-colors"
                                >
                                    Khám phá thực đơn
                                </a>
                            </div>
                        ) : (
                            <div className="divide-y divide-slate-100">
                                {cart.map((item) => (
                                    <div key={`cart-${item.id}`} className="py-4 flex items-center justify-between gap-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                                <img 
                                                    src={item.image?.startsWith('http') ? item.image : `/${item.image || 'logo.jpg'}`} 
                                                    alt={item.dish_name} 
                                                    className="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div>
                                                <h4 className="font-extrabold text-xs text-slate-800 line-clamp-1">{item.dish_name}</h4>
                                                <p className="text-xs font-black text-[#ee4d2d] mt-0.5">{formatPrice(item.price)}</p>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-4">
                                            <div className="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
                                                <button
                                                    type="button"
                                                    onClick={() => updateQuantity(item.id, (item.quantity || 1) - 1)}
                                                    className="px-2.5 py-1 text-slate-600 hover:bg-slate-200 text-xs font-bold cursor-pointer"
                                                >
                                                    -
                                                </button>
                                                <span className="px-3 py-1 text-xs font-bold text-slate-800 bg-white min-w-[32px] text-center">
                                                    {item.quantity || 1}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() => updateQuantity(item.id, (item.quantity || 1) + 1)}
                                                    className="px-2.5 py-1 text-slate-600 hover:bg-slate-200 text-xs font-bold cursor-pointer"
                                                >
                                                    +
                                                </button>
                                            </div>

                                            <span className="text-xs font-black text-slate-800 min-w-[70px] text-right">
                                                {formatPrice((item.price || 0) * (item.quantity || 1))}
                                            </span>

                                            <button
                                                type="button"
                                                onClick={() => removeItem(item.id)}
                                                className="text-slate-400 hover:text-rose-600 transition-colors cursor-pointer text-sm p-1"
                                                title="Xóa món này"
                                            >
                                                <i className="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}

                        {/* Coupon Code Section */}
                        {cart.length > 0 && (
                            <div className="pt-4 border-t border-slate-100">
                                <div className="flex flex-col sm:flex-row items-center gap-2.5">
                                    <div className="relative flex-1 w-full">
                                        <span className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                            <i className="fas fa-ticket"></i>
                                        </span>
                                        <input
                                            type="text"
                                            value={couponCode}
                                            onChange={(e) => setCouponCode(e.target.value)}
                                            placeholder="NHẬP MÃ GIẢM GIÁ (VD: FOODDAILY30)"
                                            className="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-[#ee4d2d] outline-none uppercase"
                                        />
                                    </div>
                                    <button
                                        type="button"
                                        onClick={applyCoupon}
                                        className="w-full sm:w-auto px-6 py-2.5 bg-slate-900 hover:bg-[#ee4d2d] text-white rounded-xl text-xs font-extrabold transition-all cursor-pointer shrink-0"
                                    >
                                        Áp dụng
                                    </button>
                                </div>

                                {couponMessage && (
                                    <p className={`mt-2 text-xs font-bold flex items-center gap-1.5 ${
                                        couponMessage.type === 'success' ? 'text-emerald-600' : 'text-rose-600'
                                    }`}>
                                        <i className={`fas ${couponMessage.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}`}></i>
                                        {couponMessage.text}
                                    </p>
                                )}
                            </div>
                        )}
                    </div>
                </div>

                {/* Cột Phải: Thông Tin Nhận Hàng & Thanh Toán */}
                <div className="w-full lg:w-[450px] shrink-0 space-y-6">
                    <form onSubmit={handleSubmitOrder} className="bg-white/95 backdrop-blur-md rounded-3xl p-6 shadow-sm border border-slate-200 space-y-5">
                        <div className="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <span className="w-8 h-8 rounded-xl bg-blue-50 text-[#0099ff] flex items-center justify-center font-bold text-sm">
                                <i className="fas fa-location-dot"></i>
                            </span>
                            <h3 className="font-extrabold text-slate-900 text-sm">Thông Tin Giao Hàng</h3>
                        </div>

                        {/* User info fields */}
                        <div className="space-y-3">
                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Họ và tên người nhận <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={fullName}
                                    onChange={(e) => setFullName(e.target.value)}
                                    placeholder="Nguyễn Văn A"
                                    className="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none"
                                />
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Số điện thoại <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="tel"
                                    required
                                    value={phone}
                                    onChange={(e) => setPhone(e.target.value)}
                                    placeholder="0901234567"
                                    className="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none"
                                />
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Địa chỉ giao tận nơi <span className="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    required
                                    value={address}
                                    onChange={(e) => setAddress(e.target.value)}
                                    placeholder="Số nhà, tên đường, phường, quận..."
                                    className="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none"
                                />
                            </div>

                            <div>
                                <label className="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Ghi chú cho tài xế
                                </label>
                                <input
                                    type="text"
                                    value={note}
                                    onChange={(e) => setNote(e.target.value)}
                                    placeholder="Gọi trước khi giao, gửi bảo vệ..."
                                    className="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none"
                                />
                            </div>
                        </div>

                        {/* Phương thức thanh toán */}
                        <div className="pt-3 border-t border-slate-100 space-y-2.5">
                            <label className="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                                Phương thức thanh toán
                            </label>

                            <div className="space-y-2">
                                <label className={`flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all ${
                                    paymentMethod === 'cod' ? 'border-[#ee4d2d] bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'
                                }`}>
                                    <input
                                        type="radio"
                                        name="payment_choice"
                                        value="cod"
                                        checked={paymentMethod === 'cod'}
                                        onChange={() => setPaymentMethod('cod')}
                                        className="text-[#ee4d2d] focus:ring-[#ee4d2d]"
                                    />
                                    <div className="flex items-center gap-2">
                                        <i className="fas fa-money-bill-wave text-emerald-600 text-sm"></i>
                                        <div>
                                            <p className="text-xs font-bold text-slate-800">Tiền mặt khi nhận món (COD)</p>
                                            <p className="text-[10px] text-slate-400">Thanh toán cho tài xế khi nhận thức ăn</p>
                                        </div>
                                    </div>
                                </label>

                                <label className={`flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all ${
                                    paymentMethod === 'bank_transfer' ? 'border-[#ee4d2d] bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'
                                }`}>
                                    <input
                                        type="radio"
                                        name="payment_choice"
                                        value="bank_transfer"
                                        checked={paymentMethod === 'bank_transfer'}
                                        onChange={() => setPaymentMethod('bank_transfer')}
                                        className="text-[#ee4d2d] focus:ring-[#ee4d2d]"
                                    />
                                    <div className="flex items-center gap-2">
                                        <i className="fas fa-qrcode text-blue-600 text-sm"></i>
                                        <div>
                                            <p className="text-xs font-bold text-slate-800">Chuyển khoản VietQR</p>
                                            <p className="text-[10px] text-slate-400">Quét mã QR qua ứng dụng ngân hàng</p>
                                        </div>
                                    </div>
                                </label>

                                <label className={`flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all ${
                                    paymentMethod === 'momo' ? 'border-[#ee4d2d] bg-rose-50/50' : 'border-slate-200 hover:bg-slate-50'
                                }`}>
                                    <input
                                        type="radio"
                                        name="payment_choice"
                                        value="momo"
                                        checked={paymentMethod === 'momo'}
                                        onChange={() => setPaymentMethod('momo')}
                                        className="text-[#ee4d2d] focus:ring-[#ee4d2d]"
                                    />
                                    <div className="flex items-center gap-2">
                                        <i className="fas fa-wallet text-pink-600 text-sm"></i>
                                        <div>
                                            <p className="text-xs font-bold text-slate-800">Ví MoMo</p>
                                            <p className="text-[10px] text-slate-400">Thanh toán siêu tốc qua ví MoMo</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {/* Bill Breakdown */}
                        <div className="pt-3 border-t border-slate-100 space-y-2 text-xs">
                            <div className="flex items-center justify-between text-slate-500">
                                <span>Tạm tính ({cart.length} món):</span>
                                <span className="font-bold text-slate-800">{formatPrice(subtotal)}</span>
                            </div>
                            <div className="flex items-center justify-between text-slate-500">
                                <span>Phí giao hàng (20 phút):</span>
                                <span className="font-bold text-slate-800">
                                    {calculatedDeliveryFee === 0 ? (
                                        <span className="text-emerald-600 uppercase font-black text-[11px]">Miễn phí</span>
                                    ) : (
                                        formatPrice(calculatedDeliveryFee)
                                    )}
                                </span>
                            </div>
                            {discountAmount > 0 && (
                                <div className="flex items-center justify-between text-emerald-600 font-bold">
                                    <span>Giảm giá Voucher:</span>
                                    <span>-{formatPrice(discountAmount)}</span>
                                </div>
                            )}
                            <div className="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span className="text-sm font-black text-slate-900">Tổng thanh toán:</span>
                                <span className="text-lg font-black text-[#ee4d2d]">{formatPrice(finalTotal)}</span>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <button
                            type="submit"
                            disabled={isSubmitting || cart.length === 0}
                            className={`w-full py-3.5 rounded-2xl text-white font-extrabold text-xs shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer ${
                                isSubmitting || cart.length === 0
                                    ? 'bg-slate-300 shadow-none cursor-not-allowed'
                                    : 'bg-[#ee4d2d] hover:bg-red-600 shadow-rose-500/25'
                            }`}
                        >
                            {isSubmitting ? (
                                <>
                                    <i className="fas fa-spinner fa-spin"></i> Đang xử lý đơn hàng...
                                </>
                            ) : (
                                <>
                                    <i className="fas fa-check"></i> Đặt Hàng Ngay ({formatPrice(finalTotal)})
                                </>
                            )}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}

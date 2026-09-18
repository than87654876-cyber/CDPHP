import React, { useState } from 'react';

export default function DishDetailApp({
    dish = {},
    relatedDishes = [],
    reviews = [],
    csrfToken = '',
    routes = {},
    auth = null
}) {
    const [quantity, setQuantity] = useState(1);
    const [notes, setNotes] = useState('');
    const [toast, setToast] = useState(null);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const handleAddToCart = (isBuyNow = false) => {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('dish_id', dish.id);
        formData.append('quantity', quantity);
        if (notes) formData.append('notes', notes);

        fetch(routes.addToCart || '/gio-hang/them', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json().catch(() => ({ success: true })))
        .then(() => {
            if (isBuyNow) {
                window.location.href = routes.checkout || '/gio-hang';
            } else {
                setToast(`Đã thêm ${quantity}x "${dish.dish_name}" vào giỏ hàng!`);
                setTimeout(() => setToast(null), 3000);
            }
        })
        .catch(() => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = routes.addToCart || '/gio-hang/them';
            const inputToken = document.createElement('input');
            inputToken.type = 'hidden';
            inputToken.name = '_token';
            inputToken.value = csrfToken;
            const inputDish = document.createElement('input');
            inputDish.type = 'hidden';
            inputDish.name = 'dish_id';
            inputDish.value = dish.id;
            const inputQty = document.createElement('input');
            inputQty.type = 'hidden';
            inputQty.name = 'quantity';
            inputQty.value = quantity;
            form.appendChild(inputToken);
            form.appendChild(inputDish);
            form.appendChild(inputQty);
            document.body.appendChild(form);
            form.submit();
        });
    };

    const resolveImageUrl = (img, displayImg) => {
        if (displayImg) return displayImg;
        if (!img) return '/logo.jpg';
        if (img.startsWith('http') || img.startsWith('/')) return img;
        return `/${img}`;
    };

    const dishImg = resolveImageUrl(dish.image_url || dish.image, dish.display_image);

    return (
        <div className="py-6 sm:py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            {toast && (
                <div className="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 animate-bounce">
                    <i className="fas fa-check-circle text-lg"></i>
                    <span className="text-xs font-bold">{toast}</span>
                </div>
            )}

            {/* Breadcrumb */}
            <nav className="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href={routes.shop || '/'} className="hover:text-[#ee4d2d] transition-colors">
                    <i className="fas fa-home mr-1"></i> Trang chủ
                </a>
                <i className="fas fa-chevron-right text-[9px] text-slate-400"></i>
                <a href={`${routes.shop || '/'}#menu`} className="hover:text-[#ee4d2d] transition-colors">
                    {dish.category?.category_name || 'Thực đơn'}
                </a>
                <i className="fas fa-chevron-right text-[9px] text-slate-400"></i>
                <span className="text-[#ee4d2d] font-bold truncate max-w-xs">{dish.dish_name}</span>
            </nav>

            {/* Main Dish Details Card */}
            <div className="bg-white/95 backdrop-blur-md rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {/* Image */}
                <div className="lg:col-span-5 space-y-4">
                    <div className="aspect-square w-full rounded-2xl bg-slate-100 overflow-hidden relative border border-slate-200 shadow-inner group">
                        <img 
                            src={dishImg} 
                            alt={dish.dish_name} 
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                        />
                        <span className="absolute top-3 left-3 bg-[#ee4d2d] text-white text-xs font-black px-3 py-1 rounded-lg shadow-md flex items-center gap-1.5">
                            <i className="fas fa-thumbs-up text-[10px]"></i> Yêu thích
                        </span>
                        <span className="absolute bottom-3 left-3 px-3 py-1 rounded-lg bg-slate-900/80 backdrop-blur-xs text-white text-[11px] font-extrabold tracking-wide uppercase shadow-sm">
                            <i className="fas fa-tag mr-1 text-rose-400"></i> {dish.category?.category_name || 'Món ngon'}
                        </span>
                    </div>
                </div>

                {/* Details & Actions */}
                <div className="lg:col-span-7 space-y-6">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 border border-rose-200 text-[#ee4d2d] text-xs font-black uppercase tracking-wider">
                            {dish.category?.category_name || 'Món Ăn'}
                        </span>
                        <span className="inline-flex items-center gap-1 text-emerald-600 text-xs font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Còn hàng & Giao 20'
                        </span>
                    </div>

                    <div>
                        <h1 className="text-2xl sm:text-3xl font-black text-slate-900 leading-tight">
                            {dish.dish_name}
                        </h1>
                        <div className="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-semibold mt-2">
                            <span className="text-amber-500 font-extrabold flex items-center gap-1">
                                ⭐ {Number(dish.rating_score || 5).toFixed(1)} / 5.0 ({reviews.length} đánh giá)
                            </span>
                            <span>•</span>
                            <span>Đã bán {dish.sold_count || 100}+ suất</span>
                        </div>
                    </div>

                    <div className="bg-rose-50/70 border border-rose-100 rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <span className="text-xs text-slate-400 font-bold block uppercase tracking-wider">Đơn giá:</span>
                            <span className="text-3xl font-black text-[#ee4d2d]">
                                {formatPrice(dish.price)}
                            </span>
                        </div>
                        <span className="text-xs font-bold text-emerald-600 bg-white px-3 py-1.5 rounded-xl border border-emerald-200">
                            🚚 Freeship từ 100k
                        </span>
                    </div>

                    <div className="space-y-2">
                        <h4 className="text-xs font-bold text-slate-700 uppercase tracking-wider">Mô tả món ăn:</h4>
                        <p className="text-xs sm:text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            {dish.description || 'Món ăn được chọn lọc nguyên liệu tươi ngon nhất, cân bằng dinh dưỡng, đóng hộp chịu nhiệt đạt chuẩn an toàn vệ sinh thực phẩm.'}
                        </p>
                    </div>

                    {/* Quantity & Notes */}
                    <div className="space-y-4 pt-2">
                        <div className="flex items-center gap-4">
                            <span className="text-xs font-bold text-slate-700">Số lượng:</span>
                            <div className="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                <button
                                    type="button"
                                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                                    className="px-3 py-1.5 text-slate-600 hover:bg-slate-200 text-sm font-bold cursor-pointer"
                                >
                                    -
                                </button>
                                <span className="px-4 py-1.5 text-xs font-bold text-slate-900 bg-white min-w-[40px] text-center">
                                    {quantity}
                                </span>
                                <button
                                    type="button"
                                    onClick={() => setQuantity(quantity + 1)}
                                    className="px-3 py-1.5 text-slate-600 hover:bg-slate-200 text-sm font-bold cursor-pointer"
                                >
                                    +
                                </button>
                            </div>
                            <span className="text-xs font-bold text-slate-400">
                                Tổng: <strong className="text-[#ee4d2d]">{formatPrice(dish.price * quantity)}</strong>
                            </span>
                        </div>

                        <div>
                            <input
                                type="text"
                                value={notes}
                                onChange={(e) => setNotes(e.target.value)}
                                placeholder="Ghi chú cho đầu bếp (VD: ít cay, không hành, nhiều cơm...)"
                                className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 outline-none focus:border-[#ee4d2d]"
                            />
                        </div>

                        {/* Buttons */}
                        <div className="flex flex-col sm:flex-row gap-3 pt-2">
                            <button
                                type="button"
                                onClick={() => handleAddToCart(false)}
                                className="flex-1 py-3.5 rounded-2xl bg-rose-50 hover:bg-rose-100 text-[#ee4d2d] font-extrabold text-xs transition-colors flex items-center justify-center gap-2 cursor-pointer border border-rose-200"
                            >
                                <i className="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                            </button>
                            <button
                                type="button"
                                onClick={() => handleAddToCart(true)}
                                className="flex-1 py-3.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs transition-colors shadow-lg shadow-rose-500/25 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <i className="fas fa-bolt"></i> Mua ngay bây giờ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Related Dishes */}
            {relatedDishes && relatedDishes.length > 0 && (
                <section className="space-y-4">
                    <div className="flex items-center justify-between">
                        <h3 className="text-base sm:text-lg font-black text-slate-900 flex items-center gap-2">
                            <i className="fas fa-utensils text-[#ee4d2d]"></i> Món Cùng Danh Mục ({dish.category?.category_name})
                        </h3>
                        <a href={`${routes.shop || '/'}#menu`} className="text-xs font-bold text-[#ee4d2d] hover:underline">
                            Xem tất cả →
                        </a>
                    </div>

                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        {relatedDishes.map((item) => (
                            <a 
                                key={`related-${item.id}`}
                                href={`/mon/${item.id}`}
                                className="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-xs hover:shadow-md hover:border-rose-200 transition-all group"
                            >
                                <div className="aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 mb-2">
                                    <img 
                                        src={resolveImageUrl(item.image_url || item.image, item.display_image)}
                                        alt={item.dish_name}
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform" 
                                    />
                                </div>
                                <h4 className="font-bold text-xs text-slate-800 line-clamp-1 group-hover:text-[#ee4d2d]">
                                    {item.dish_name}
                                </h4>
                                <p className="text-xs font-black text-[#ee4d2d] mt-1">
                                    {formatPrice(item.price)}
                                </p>
                            </a>
                        ))}
                    </div>
                </section>
            )}
        </div>
    );
}

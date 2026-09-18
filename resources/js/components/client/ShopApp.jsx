import React, { useState, useMemo } from 'react';

export default function ShopApp({
    categories = [],
    allDishes = [],
    query = '',
    sort = 'rating_desc',
    sameCategoryDishes = [],
    frequentDishes = [],
    currentSelectedDish = null,
    timeRecommendation = null,
    lastSearchDishes = [],
    settings = {},
    csrfToken = '',
    routes = {},
}) {
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [searchQuery, setSearchQuery] = useState(query || '');
    const [currentSort, setCurrentSort] = useState(sort || 'rating_desc');
    const [showGroupModal, setShowGroupModal] = useState(false);
    const [hostName, setHostName] = useState('');
    const [cartToast, setCartToast] = useState(null);

    // Filter and sort dishes
    const filteredDishes = useMemo(() => {
        let list = [...allDishes];

        if (selectedCategory !== 'all') {
            list = list.filter(d => String(d.category_id) === String(selectedCategory));
        }

        if (searchQuery.trim()) {
            const q = searchQuery.toLowerCase();
            list = list.filter(d => d.dish_name?.toLowerCase().includes(q));
        }

        if (currentSort === 'price_asc') {
            list.sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0));
        } else if (currentSort === 'price_desc') {
            list.sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0));
        } else {
            list.sort((a, b) => ((b.rating_score || 0) * 1000 + (b.reviews_count || 0)) - ((a.rating_score || 0) * 1000 + (a.reviews_count || 0)));
        }

        return list;
    }, [allDishes, selectedCategory, searchQuery, currentSort]);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    };

    const handleAddToCart = (dish) => {
        // Post via FormData to maintain Laravel session / cart
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('dish_id', dish.id);
        formData.append('quantity', 1);

        fetch(routes.addToCart || '/gio-hang/them', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json().catch(() => ({ success: true })))
        .then(() => {
            setCartToast(`Đã thêm "${dish.dish_name}" vào giỏ hàng!`);
            setTimeout(() => setCartToast(null), 3000);
            // Trigger cart count update if listener exists
            window.dispatchEvent(new CustomEvent('cart-updated', { detail: { dish } }));
        })
        .catch(() => {
            // Fallback: Submit form natively if API endpoint differs
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
            form.appendChild(inputToken);
            form.appendChild(inputDish);
            document.body.appendChild(form);
            form.submit();
        });
    };

    const heroBg = settings?.banner_image
        ? (settings.banner_image.startsWith('http') ? settings.banner_image : `/${settings.banner_image}`)
        : '/uploads/ve-dep-sai-gon-qua-ong-kinh-cua-nguoi-me-anh-ivivu-2.jpg';

    return (
        <div className="space-y-8 pb-16">
            {/* Toast Notification */}
            {cartToast && (
                <div className="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3 animate-bounce transition-all">
                    <i className="fas fa-check-circle text-lg"></i>
                    <span className="text-xs font-bold">{cartToast}</span>
                </div>
            )}

            {/* HERO SECTION */}
            <section className="bg-transparent py-4">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                        
                        {/* LEFT HERO BOX */}
                        <div 
                            className="lg:col-span-7 rounded-3xl p-6 sm:p-8 text-white flex flex-col justify-between space-y-6 shadow-xl relative overflow-hidden"
                            style={{
                                background: `linear-gradient(rgba(15, 23, 42, 0.78), rgba(15, 23, 42, 0.88)), url('${heroBg}') center/cover no-repeat`
                            }}
                        >
                            <div className="space-y-4 relative z-10">
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#ee4d2d] text-white text-[11px] font-extrabold uppercase shadow-sm">
                                        <i className="fas fa-bolt"></i> Giao siêu tốc 20'
                                    </span>
                                    <button 
                                        type="button" 
                                        onClick={() => setShowGroupModal(true)}
                                        className="px-3.5 py-1.5 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5"
                                    >
                                        <i className="fas fa-users text-emerald-400"></i> Đặt đơn theo nhóm
                                    </button>
                                </div>

                                <h1 className="text-2xl sm:text-4xl font-black tracking-tight leading-snug drop-shadow-md">
                                    {settings?.banner_title || "Đặt Đồ ăn, giao hàng từ 20'..."}
                                </h1>
                                <p className="text-xs sm:text-sm text-slate-200 font-medium leading-relaxed">
                                    {settings?.banner_subtitle || "Có 110.625 Địa Điểm Ở TP. HCM Từ 00:00 - 23:59"}
                                </p>

                                {/* Big Search Bar */}
                                <div className="flex items-center bg-white rounded-xl overflow-hidden p-1.5 shadow-lg">
                                    <input 
                                        type="text" 
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        placeholder="Tìm địa điểm, món ăn, thức uống..." 
                                        className="w-full px-4 py-2.5 text-xs font-semibold text-slate-800 bg-transparent outline-none"
                                    />
                                    <button 
                                        type="button"
                                        className="px-6 py-2.5 bg-[#0099ff] hover:bg-blue-600 text-white font-extrabold text-xs rounded-lg transition-colors shrink-0 cursor-pointer flex items-center gap-1.5"
                                    >
                                        <i className="fas fa-magnifying-glass"></i>
                                        <span className="hidden sm:inline">Tìm kiếm</span>
                                    </button>
                                </div>

                                {/* Category Pills in Hero */}
                                <div className="pt-2 flex flex-wrap gap-2 text-xs font-semibold">
                                    <button
                                        type="button"
                                        onClick={() => setSelectedCategory('all')}
                                        className={`px-3.5 py-1.5 rounded-xl backdrop-blur-md border transition-all cursor-pointer ${
                                            selectedCategory === 'all'
                                                ? 'bg-[#ee4d2d] border-[#ee4d2d] text-white shadow-md'
                                                : 'bg-white/10 hover:bg-white/20 border-white/20 text-white'
                                        }`}
                                    >
                                        Tất cả món
                                    </button>
                                    {categories.map((cat) => (
                                        <button
                                            key={cat.id}
                                            type="button"
                                            onClick={() => setSelectedCategory(cat.id)}
                                            className={`px-3.5 py-1.5 rounded-xl backdrop-blur-md border transition-all cursor-pointer ${
                                                String(selectedCategory) === String(cat.id)
                                                    ? 'bg-[#ee4d2d] border-[#ee4d2d] text-white shadow-md'
                                                    : 'bg-white/10 hover:bg-white/20 border-white/20 text-white'
                                            }`}
                                        >
                                            {cat.category_name}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Download Badges */}
                            <div className="pt-4 border-t border-white/15 flex flex-wrap items-center justify-between gap-3 text-[11px] text-slate-300">
                                <span>Tải app FOODDAILY trải nghiệm mượt mà:</span>
                                <div className="flex gap-2">
                                    <span className="px-3 py-1 bg-black/60 rounded-lg border border-white/20 text-[10px] font-bold cursor-pointer hover:bg-black">
                                        <i className="fab fa-apple mr-1"></i>App Store
                                    </span>
                                    <span className="px-3 py-1 bg-black/60 rounded-lg border border-white/20 text-[10px] font-bold cursor-pointer hover:bg-black">
                                        <i className="fab fa-google-play mr-1"></i>Google Play
                                    </span>
                                </div>
                            </div>
                        </div>

                        {/* RIGHT PROMO CARD */}
                        <div className="lg:col-span-5 bg-white/95 backdrop-blur-md rounded-3xl p-5 sm:p-6 shadow-sm border border-slate-200 flex flex-col justify-between space-y-4">
                            <div className="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 flex items-center justify-between">
                                <span className="truncate"><strong className="text-[#ee4d2d]">Giao tới:</strong> TP. Hồ Chí Minh (20 phút)</span>
                                <i className="fas fa-location-dot text-xs text-[#ee4d2d]"></i>
                            </div>

                            <div className="flex items-center justify-between border-b border-slate-100 pb-2">
                                <div className="flex items-center gap-2">
                                    <span className="text-base">🔥</span>
                                    <h3 className="font-extrabold text-slate-900 text-sm">Món Ưu Đãi HOT Nhất</h3>
                                </div>
                                <span className="text-xs font-bold text-[#0099ff]">Gợi ý đặc biệt</span>
                            </div>

                            {/* 2x3 Grid */}
                            <div className="grid grid-cols-3 gap-2.5 sm:gap-3">
                                {allDishes.slice(0, 6).map((dish) => (
                                    <div 
                                        key={`promo-${dish.id}`}
                                        className="bg-slate-50/80 hover:bg-white rounded-xl border border-slate-200/80 p-2 space-y-1.5 hover:border-[#ee4d2d] hover:shadow-md transition-all group flex flex-col justify-between"
                                    >
                                        <a href={`/mon-an/${dish.id}`} className="space-y-1.5 block">
                                            <div className="aspect-square rounded-lg overflow-hidden bg-slate-100 relative">
                                                {dish.image ? (
                                                    <img 
                                                        src={dish.image.startsWith('http') ? dish.image : `/${dish.image}`} 
                                                        alt={dish.dish_name} 
                                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                    />
                                                ) : (
                                                    <div className="w-full h-full flex items-center justify-center text-rose-400 font-bold text-xs bg-rose-50">
                                                        <i className="fas fa-utensils"></i>
                                                    </div>
                                                )}
                                                <span className="absolute top-1 left-1 bg-[#ee4d2d] text-white text-[9px] font-black px-1.5 py-0.5 rounded">
                                                    HOT
                                                </span>
                                            </div>
                                            <h4 className="font-bold text-slate-900 text-[11px] line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                                {dish.dish_name}
                                            </h4>
                                            <p className="text-[10px] text-[#ee4d2d] font-black">
                                                {formatPrice(dish.price)}
                                            </p>
                                        </a>

                                        <button 
                                            type="button" 
                                            onClick={() => handleAddToCart(dish)}
                                            className="w-full py-1 bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white rounded-md text-[10px] font-extrabold transition-colors cursor-pointer flex items-center justify-center gap-1"
                                        >
                                            <i className="fas fa-plus text-[9px]"></i> Thêm
                                        </button>
                                    </div>
                                ))}
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            {/* MEAL RECOMMENDATION BY TIME */}
            {timeRecommendation && timeRecommendation.dishes && timeRecommendation.dishes.length > 0 && (
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 rounded-3xl p-5 sm:p-6 shadow-xs">
                        <div className="flex items-center justify-between mb-4">
                            <div className="flex items-center gap-2">
                                <span className="text-xl">☀️</span>
                                <h3 className="font-black text-slate-800 text-base sm:text-lg">
                                    {timeRecommendation.title || 'Gợi Ý Món Ăn Cho Bữa Ăn Này'}
                                </h3>
                            </div>
                            <span className="text-xs font-extrabold text-amber-700 bg-amber-100 px-3 py-1 rounded-full">
                                Khung giờ {timeRecommendation.period}
                            </span>
                        </div>

                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            {timeRecommendation.dishes.map((dish) => (
                                <div key={`time-${dish.id}`} className="bg-white rounded-2xl p-3 border border-amber-100 shadow-xs hover:shadow-md transition-all group flex flex-col justify-between">
                                    <a href={`/mon-an/${dish.id}`} className="space-y-2 block">
                                        <div className="aspect-video rounded-xl overflow-hidden bg-slate-100">
                                            <img 
                                                src={dish.image?.startsWith('http') ? dish.image : `/${dish.image || 'logo.jpg'}`} 
                                                alt={dish.dish_name}
                                                className="w-full h-full object-cover group-hover:scale-105 transition-transform" 
                                            />
                                        </div>
                                        <h4 className="font-bold text-xs text-slate-800 line-clamp-1 group-hover:text-[#ee4d2d]">
                                            {dish.dish_name}
                                        </h4>
                                        <p className="text-xs font-black text-[#ee4d2d]">
                                            {formatPrice(dish.price)}
                                        </p>
                                    </a>
                                    <button
                                        type="button"
                                        onClick={() => handleAddToCart(dish)}
                                        className="mt-2 w-full py-1.5 rounded-xl bg-amber-50 hover:bg-[#ee4d2d] text-amber-800 hover:text-white font-bold text-xs transition-colors cursor-pointer"
                                    >
                                        + Thêm món
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            )}

            {/* MAIN CATALOG FILTER & DISHES GRID */}
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" id="menu">
                
                {/* Controls Bar: Category Pills & Sorting */}
                <div className="bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center justify-between gap-4">
                    {/* Category Tabs */}
                    <div className="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 scrollbar-none">
                        <button
                            type="button"
                            onClick={() => setSelectedCategory('all')}
                            className={`px-4 py-2 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer ${
                                selectedCategory === 'all'
                                    ? 'bg-[#ee4d2d] text-white shadow-md shadow-rose-500/20'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                            }`}
                        >
                            Tất cả ({allDishes.length})
                        </button>
                        {categories.map((cat) => (
                            <button
                                key={`tab-${cat.id}`}
                                type="button"
                                onClick={() => setSelectedCategory(cat.id)}
                                className={`px-4 py-2 rounded-xl text-xs font-extrabold transition-all shrink-0 cursor-pointer ${
                                    String(selectedCategory) === String(cat.id)
                                        ? 'bg-[#ee4d2d] text-white shadow-md shadow-rose-500/20'
                                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                                }`}
                            >
                                {cat.category_name}
                            </button>
                        ))}
                    </div>

                    {/* Sort Dropdown */}
                    <div className="flex items-center gap-2 shrink-0 self-end md:self-center">
                        <span className="text-xs font-bold text-slate-400">Sắp xếp:</span>
                        <select
                            value={currentSort}
                            onChange={(e) => setCurrentSort(e.target.value)}
                            className="bg-slate-100 border border-slate-200 text-xs font-extrabold text-slate-700 px-3 py-2 rounded-xl outline-none focus:border-[#ee4d2d] cursor-pointer"
                        >
                            <option value="rating_desc">⭐ Đánh giá cao nhất</option>
                            <option value="price_asc">💵 Giá: Thấp đến Cao</option>
                            <option value="price_desc">💎 Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                {/* Dishes Grid */}
                {filteredDishes.length === 0 ? (
                    <div className="bg-white rounded-3xl p-12 text-center border border-slate-200 space-y-3">
                        <div className="w-16 h-16 rounded-full bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-2xl mx-auto">
                            <i className="fas fa-bowl-food"></i>
                        </div>
                        <h3 className="text-base font-bold text-slate-800">Không tìm thấy món ăn phù hợp</h3>
                        <p className="text-xs text-slate-400">Hãy thử tìm với từ khóa khác hoặc chọn danh mục "Tất cả"</p>
                        <button
                            type="button"
                            onClick={() => { setSelectedCategory('all'); setSearchQuery(''); }}
                            className="px-4 py-2 rounded-xl bg-[#ee4d2d] text-white text-xs font-bold shadow-sm hover:bg-red-600 transition-colors cursor-pointer"
                        >
                            Xem tất cả món
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        {filteredDishes.map((dish) => (
                            <div 
                                key={`card-${dish.id}`}
                                className="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 overflow-hidden shadow-xs hover:shadow-xl hover:border-rose-200 transition-all duration-300 flex flex-col justify-between group"
                            >
                                <div>
                                    {/* Image with Tag */}
                                    <div className="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                                        <a href={`/mon-an/${dish.id}`}>
                                            <img 
                                                src={dish.image?.startsWith('http') ? dish.image : `/${dish.image || 'logo.jpg'}`} 
                                                alt={dish.dish_name}
                                                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                            />
                                        </a>

                                        <span className="absolute top-2.5 left-2.5 bg-emerald-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow-sm flex items-center gap-1">
                                            <i className="fas fa-check-circle text-[9px]"></i> Có sẵn
                                        </span>

                                        <span className="absolute bottom-2.5 right-2.5 bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                                            ⭐ {Number(dish.rating_score || 5).toFixed(1)}
                                        </span>
                                    </div>

                                    {/* Content */}
                                    <div className="p-4 space-y-2">
                                        <a href={`/mon-an/${dish.id}`} className="block">
                                            <h3 className="font-extrabold text-sm text-slate-800 line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                                {dish.dish_name}
                                            </h3>
                                        </a>
                                        
                                        <p className="text-[11px] text-slate-400 line-clamp-2 leading-relaxed">
                                            {dish.description || 'Hương vị thơm ngon, chế biến tươi mới mỗi ngày phục vụ bạn.'}
                                        </p>

                                        <div className="flex items-center justify-between pt-1">
                                            <span className="text-sm sm:text-base font-black text-[#ee4d2d]">
                                                {formatPrice(dish.price)}
                                            </span>
                                            <span className="text-[10px] font-semibold text-slate-400">
                                                Đã bán {dish.sold_count || 120}+
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Add To Cart Action */}
                                <div className="p-4 pt-0">
                                    <button
                                        type="button"
                                        onClick={() => handleAddToCart(dish)}
                                        className="w-full py-2.5 rounded-xl bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white font-extrabold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs"
                                    >
                                        <i className="fas fa-cart-plus"></i> Thêm vào giỏ
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </main>

            {/* GROUP ORDER MODAL */}
            {showGroupModal && (
                <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
                    <div className="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2.5">
                                <span className="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                                    <i className="fas fa-users"></i>
                                </span>
                                <div>
                                    <h3 className="font-extrabold text-slate-900 text-base">Tạo Phòng Đặt Nhóm</h3>
                                    <p className="text-xs text-slate-400">Cùng bạn bè chọn món dễ dàng</p>
                                </div>
                            </div>
                            <button 
                                type="button" 
                                onClick={() => setShowGroupModal(false)}
                                className="text-slate-400 hover:text-slate-600 text-lg cursor-pointer"
                            >
                                &times;
                            </button>
                        </div>

                        <form action={routes.groupOrder || '/don-nhom/tao'} method="POST" className="space-y-4">
                            <input type="hidden" name="_token" value={csrfToken} />
                            <div>
                                <label className="block text-xs font-bold text-slate-700 mb-1.5">Tên trưởng nhóm của bạn:</label>
                                <input 
                                    type="text" 
                                    name="host_name" 
                                    required 
                                    value={hostName}
                                    onChange={(e) => setHostName(e.target.value)}
                                    placeholder="Ví dụ: Hoàng Long, Trâm Anh..." 
                                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-emerald-500 outline-none"
                                />
                            </div>

                            <p className="text-[11px] text-slate-400 leading-relaxed">
                                Sau khi tạo phòng, bạn sẽ nhận được một đường link và mã QR để gửi cho bạn bè trong công ty/nhóm cùng chọn món vào chung 1 đơn!
                            </p>

                            <div className="flex gap-2.5 pt-2">
                                <button 
                                    type="button" 
                                    onClick={() => setShowGroupModal(false)}
                                    className="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold cursor-pointer"
                                >
                                    Hủy
                                </button>
                                <button 
                                    type="submit" 
                                    className="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold shadow-md shadow-emerald-500/20 cursor-pointer"
                                >
                                    Khởi tạo ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

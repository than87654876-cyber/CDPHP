@extends('layouts.app')

@section('title', 'FOODDAILY - Đặt Đồ Ăn, Giao Hàng Siêu Tốc Từ 20 phút')

@section('content')
<!-- REALTIME TOAST CONTAINER (Floating in top-right) -->
<div id="realtime-toast-container" class="fixed top-20 right-6 z-50 flex flex-col space-y-2.5 max-w-sm pointer-events-none"></div>

<!-- ShopeeFood Hero Section (Matches Image 1 Exactly) -->
<section class="bg-transparent py-6 border-b border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            @php
                $heroBgUrl = isset($settings['banner_image']) && $settings['banner_image']
                    ? (\Illuminate\Support\Str::startsWith($settings['banner_image'], 'http') ? $settings['banner_image'] : asset($settings['banner_image']))
                    : asset('uploads/ve-dep-sai-gon-qua-ong-kinh-cua-nguoi-me-anh-ivivu-2.jpg');
            @endphp
            <!-- LEFT PANEL: Dark Cityscape Hero Box (Giống Ảnh 1) -->
            <div class="lg:col-span-7 shopee-hero-bg rounded-2xl p-6 sm:p-8 text-white flex flex-col justify-between space-y-6 shadow-md" style="background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.85)), url('{{ $heroBgUrl }}') center/cover no-repeat;">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#ee4d2d] text-white text-[11px] font-extrabold uppercase">
                            <i class="fas fa-bolt"></i> Giao siêu tốc 20'
                        </span>
                        <!-- Nút đặt đơn nhóm -->
                        <button type="button" onclick="openGroupModal()" class="px-3.5 py-1.5 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-md text-white text-xs font-bold transition-all">
                            <i class="fas fa-users text-emerald-400 mr-1"></i> 👥 Đặt đơn theo nhóm (QR/Link)
                        </button>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black tracking-tight leading-snug drop-shadow-md">
                        {{ $settings['banner_title'] ?? "Đặt Đồ ăn, giao hàng từ 20'..." }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-200 font-medium leading-relaxed">
                        {{ $settings['banner_subtitle'] ?? "Đồng hành cùng bạn trên hành trình khám phá những món ngon tại TP. HCM" }}
                    </p>

                    <!-- Big Search Bar (ShopeeFood Style with Blue Button) -->
                    <form action="{{ route('trangchu') }}" method="GET" class="flex items-center bg-white rounded-lg overflow-hidden p-1 shadow-lg">
                        <input type="text" name="search" value="{{ $query }}" placeholder="Tìm địa điểm, món ăn, địa chỉ..." class="w-full px-4 py-2.5 text-xs font-semibold text-slate-800 bg-transparent outline-none">
                        <button type="submit" class="px-6 py-2.5 bg-[#0099ff] hover:bg-blue-600 text-white font-extrabold text-xs rounded-md transition-colors shrink-0">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </form>

                    <!-- Category Filter Pills Grid (ShopeeFood Style Tag Buttons - Vuốt ngang trên Mobile) -->
                    <div class="pt-2 flex flex-nowrap sm:flex-wrap overflow-x-auto no-scrollbar gap-2 text-xs font-semibold pb-1">
                        <a href="#menu" onclick="filterCategory('all')" id="hero-tab-all" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-[#ee4d2d] backdrop-blur-xs border border-white/20 text-white transition-all shrink-0">Tất cả</a>
                        @foreach($categories as $cat)
                            <a href="#menu" onclick="filterCategory('cat-{{ $cat->id }}')" id="hero-tab-cat-{{ $cat->id }}" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-[#ee4d2d] backdrop-blur-xs border border-white/20 text-white transition-all shrink-0">{{ $cat->category_name }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Bottom Download Badges -->
                <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                    <span class="text-[11px] text-slate-300 font-medium">Sử dụng App FOODDAILY để có nhiều giảm giá hơn:</span>
                    <div class="flex gap-2">
                        <span onclick="triggerPWAInstall()" class="px-3 py-1 bg-black/60 rounded border border-white/20 text-[10px] font-bold cursor-pointer hover:bg-black transition-colors" title="Cài đặt App PWA cho iOS"><i class="fab fa-apple mr-1"></i>App Store</span>
                        <span onclick="triggerPWAInstall()" class="px-3 py-1 bg-black/60 rounded border border-white/20 text-[10px] font-bold cursor-pointer hover:bg-black transition-colors" title="Cài đặt App PWA cho Android"><i class="fab fa-google-play mr-1"></i>Google Play</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: White Promo Floating Card Widget (Giống 100% ShopeeFood) -->
            <div class="lg:col-span-5 bg-white/95 backdrop-blur-sm rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between space-y-4">
                <!-- Location Address Bar -->
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 flex items-center justify-between cursor-pointer hover:bg-slate-100 transition-colors" onclick="alert('Đã tự động xác định vị trí giao hàng: TP. Hồ Chí Minh (Giao hàng siêu tốc 20 phút)')">
                    <span class="truncate"><strong class="text-[#ee4d2d]">Đồ ăn</strong> → TP. HCM (Giao hàng tận nơi)</span>
                    <i class="fas fa-location-dot text-xs text-[#ee4d2d]"></i>
                </div>

                <!-- Subcard "Ưu đãi" Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="text-sm">🔥</span>
                        <h3 class="font-extrabold text-slate-900 text-sm">Món Ưu Đãi HOT</h3>
                    </div>
                    <a href="#menu" class="text-xs font-bold text-[#0099ff] hover:underline">≡ Xem tất cả</a>
                </div>

                <!-- 2x3 Grid of 6 Promo Items với Logic Thêm Vào Giỏ Hàng (2 cột trên Mobile, 3 cột trên PC) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3" id="hero-promo-grid">
                    @php
                        $promoDishes = $allDishes->take(6);
                    @endphp
                    @foreach($promoDishes as $pIndex => $pDish)
                        <form action="{{ route('giohang.add') }}" method="POST" data-dish-id="{{ $pDish->id }}" class="bg-white rounded-lg border border-slate-200 p-2 space-y-1.5 hover:border-[#ee4d2d] hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between" onclick="this.submit()">
                            @csrf
                            <input type="hidden" name="dish_id" value="{{ $pDish->id }}">
                            <div class="space-y-1.5">
                                <div class="aspect-square rounded-md overflow-hidden bg-slate-100 relative border border-slate-100">
                                    @if($pDish->image)
                                        <img src="{{ asset($pDish->image) }}" alt="{{ $pDish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" loading="lazy" decoding="async">
                                    @else
                                        <div class="w-full h-full bg-rose-50 flex items-center justify-center text-rose-400 font-bold text-xs"><i class="fas fa-utensils"></i></div>
                                    @endif
                                </div>
                                <h4 class="font-extrabold text-slate-900 text-[11px] line-clamp-1 leading-snug group-hover:text-[#ee4d2d] transition-colors">{{ $pDish->dish_name }}</h4>
                                <p class="text-[10px] text-[#ee4d2d] font-black">{{ number_format($pDish->price) }}đ</p>
                            </div>
                            <!-- Red Promo Tag & Add Button -->
                            <div class="flex items-center justify-between pt-1">
                                <span class="px-1.5 py-0.5 rounded bg-rose-50 text-[#ee4d2d] text-[9px] font-extrabold border border-rose-200">
                                    🏷️ Giảm {{ ($pIndex % 2 == 0) ? '10%' : '20%' }}
                                </span>
                                <span class="w-5 h-5 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center text-[10px] font-black group-hover:bg-red-600 shadow-xs">
                                    +
                                </span>
                            </div>
                        </form>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

<!-- MÓN ĐÃ XEM GẦN ĐÂY (Recently Viewed Dishes) -->
@if(isset($recentlyViewedDishes) && $recentlyViewedDishes->isNotEmpty())
<section class="py-3 bg-transparent" id="recently-viewed-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 sm:p-5 shadow-sm border border-amber-200/90 space-y-4 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-amber-100">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 text-white text-sm font-black shadow-xs shadow-amber-500/20">
                        <i class="fas fa-clock-rotate-left"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                🕒 Món Ăn Bạn Đã Xem Gần Đây
                            </h3>
                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black tracking-wide uppercase">
                                {{ $recentlyViewedDishes->count() }} món
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">
                            Lịch sử các món bạn vừa tìm hiểu, dễ dàng xem lại và đặt hàng nhanh
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <form action="{{ route('api.recently_viewed.clear') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-2.5 py-1 rounded-lg hover:bg-rose-50 text-[11px] font-bold text-slate-400 hover:text-rose-600 transition-colors flex items-center gap-1 cursor-pointer" title="Xóa toàn bộ lịch sử xem gần đây">
                            <i class="fas fa-trash-can text-[10px]"></i> Xóa lịch sử xem
                        </button>
                    </form>
                </div>
            </div>

            <!-- Responsive Cards Grid for Recently Viewed Dishes -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($recentlyViewedDishes as $vDish)
                    <div class="bg-white hover:bg-amber-50/30 rounded-xl p-2.5 sm:p-3 border border-amber-100 hover:border-amber-400/60 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all duration-300 group">
                        <a href="{{ route('dish.detail', $vDish->id) }}" class="space-y-2 block" title="{{ $vDish->dish_name }}">
                            <div class="aspect-square w-full rounded-lg bg-slate-100 overflow-hidden relative border border-slate-100">
                                <img src="{{ $vDish->display_image }}" alt="{{ $vDish->dish_name }}" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-300" loading="lazy">
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-white/95 backdrop-blur-xs text-amber-700 text-[9px] font-black uppercase shadow-2xs border border-amber-200">
                                    <i class="fas fa-eye text-[8px] mr-0.5 text-amber-500"></i> Đã xem
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                    {{ $vDish->dish_name }}
                                </h4>
                                <p class="text-[10px] text-slate-400 line-clamp-1 font-medium">
                                    {{ $vDish->category->category_name ?? 'Món ngon' }}
                                </p>
                            </div>
                        </a>
                        <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between gap-1.5">
                            <span class="text-xs text-[#ee4d2d] font-black">{{ number_format($vDish->price) }}đ</span>
                            <form action="{{ route('giohang.add') }}" method="POST" class="shrink-0">
                                @csrf
                                <input type="hidden" name="dish_id" value="{{ $vDish->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-lg text-[11px] font-extrabold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-cart-plus text-[10px]"></i> + Đặt
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- KHO ƯU ĐÃI & MÃ GIẢM GIÁ ĐỘC QUYỀN (Ẩn hoàn toàn trên Mobile để nhường không gian cho món ăn) -->
@if(isset($activeCoupons) && $activeCoupons->isNotEmpty())
<section class="hidden md:block py-5 bg-transparent" id="coupons-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="coupon-section-box bg-white rounded-3xl p-5 sm:p-7 border border-slate-200/90 shadow-xl relative overflow-hidden" style="background: #ffffff !important;">
            <!-- Dải màu gradient viền trên cao cấp -->
            <div class="h-1.5 w-full bg-gradient-to-r from-[#ee4d2d] via-orange-500 to-amber-400 absolute top-0 left-0"></div>
            
            <!-- Header bar của Kho Ưu Đãi -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3.5">
                    <span class="w-12 h-12 rounded-2xl flex items-center justify-center text-white text-xl font-black shadow-md shadow-rose-500/25 shrink-0" style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 100%) !important;">
                        <i class="fas fa-ticket-alt"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="font-black text-slate-900 text-lg sm:text-xl tracking-tight">
                                🎟️ Kho Ưu Đãi & Mã Giảm Giá Hôm Nay
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-white text-[11px] font-black uppercase shadow-xs tracking-wider" style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 100%) !important;">
                                {{ $activeCoupons->count() }} mã hot
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium mt-1">
                            Thu thập mã giảm giá ngay — Hệ thống sẽ <strong class="text-slate-900">tự động áp dụng mã hời nhất</strong> khi bạn thanh toán!
                        </p>
                    </div>
                </div>

                <!-- Action buttons: Filter tabs & Save All -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <div class="bg-slate-100 p-1 rounded-2xl border border-slate-200 flex items-center gap-1 shadow-inner text-xs font-black">
                        <button type="button" onclick="filterCouponTab('all')" id="coupon-tab-all" class="px-3.5 py-2 rounded-xl text-white transition-all shadow-xs cursor-pointer" style="background: #ee4d2d !important;">
                            Tất cả ({{ $activeCoupons->count() }})
                        </button>
                        <button type="button" onclick="filterCouponTab('saved')" id="coupon-tab-saved" class="px-3.5 py-2 rounded-xl text-slate-600 hover:text-[#ee4d2d] transition-all cursor-pointer">
                            Đã lưu (<span id="saved-badge-count">{{ count($savedCouponIds) }}</span>)
                        </button>
                        <button type="button" onclick="filterCouponTab('expiring')" id="coupon-tab-expiring" class="px-3.5 py-2 rounded-xl text-slate-600 hover:text-[#ee4d2d] transition-all flex items-center gap-1.5 cursor-pointer">
                            <span>⏰ Sắp hết hạn</span>
                            <span class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black">{{ $expiringCoupons->count() }}</span>
                        </button>
                    </div>

                    <button type="button" onclick="saveAllCoupons()" class="px-4 py-2 text-white rounded-2xl font-black text-xs shadow-md hover:shadow-lg hover:scale-102 transition-all flex items-center gap-1.5 cursor-pointer" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;">
                        <i class="fas fa-bolt text-yellow-200"></i> Thu thập tất cả
                    </button>
                </div>
            </div>

            <!-- Notice bar về mẹo mua sắm và điều kiện đăng nhập -->
            <div class="mt-4 p-3 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs" style="background: #fffbeb !important; border: 1px solid #fde68a !important;">
                <div class="flex items-start sm:items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-xs">
                        <i class="fas fa-lightbulb"></i>
                    </span>
                    <span class="text-xs sm:text-sm font-medium" style="color: #92400e !important;">
                        <strong>Mẹo mua sắm:</strong> Sau khi lưu mã, khi vào đặt món hệ thống sẽ <strong>tự động chọn mã hời nhất</strong> cho bạn. <em>(Lưu ý: Mã khuyến mãi chỉ dùng được sau khi đăng nhập thành công)</em>
                    </span>
                </div>
                @guest
                    <a href="{{ route('dangnhap') }}" class="px-3.5 py-1.5 text-white rounded-xl text-xs font-black shrink-0 transition-all shadow-xs hover:opacity-95 text-center" style="background: #ee4d2d !important;">
                        <i class="fas fa-sign-in-alt mr-1"></i> Đăng nhập ngay
                    </a>
                @endguest
            </div>

            <!-- Grid danh sách mã khuyến mãi phong cách Ticket Card ShopeeFood -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4.5 mt-5" id="coupons-grid">
                @foreach($activeCoupons as $coupon)
                    @php
                        $isSaved = in_array($coupon->id, $savedCouponIds);
                        $isExpiring = $coupon->is_expiring_soon;
                    @endphp
                    <div class="coupon-card bg-white rounded-2xl border border-slate-200 hover:border-orange-400 hover:shadow-lg transition-all duration-300 flex overflow-hidden relative group"
                         data-is-saved="{{ $isSaved ? '1' : '0' }}"
                         data-is-expiring="{{ $isExpiring ? '1' : '0' }}"
                         id="coupon-card-{{ $coupon->id }}">

                        <!-- Cuống vé bên trái (Ticket Stub) với gradient cam đỏ ép buộc inline style -->
                        <div class="w-32 sm:w-36 coupon-stub p-3 sm:p-3.5 flex flex-col justify-between items-center text-center relative border-r-2 border-dashed border-white/60 shrink-0 select-none"
                             style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 100%) !important; color: #ffffff !important;">
                            <!-- Rãnh tròn đục lỗ vé trên và dưới (Ticket Notches) -->
                            <div class="w-4 h-4 rounded-full bg-white absolute -top-2 -right-2 z-10 border-b border-l border-slate-200"></div>
                            <div class="w-4 h-4 rounded-full bg-white absolute -bottom-2 -right-2 z-10 border-t border-l border-slate-200"></div>

                            <span class="text-[10px] font-black uppercase tracking-wider text-white px-2 py-0.5 rounded-full"
                                  style="background: rgba(255, 255, 255, 0.25);">
                                {{ $coupon->discount_type === 'percent' ? 'VOUCHER' : 'GIẢM GIÁ' }}
                            </span>

                            <div class="my-auto py-1 text-center">
                                <span class="text-2xl sm:text-3xl font-black block tracking-tight leading-none text-white drop-shadow-sm">
                                    {{ $coupon->formatted_discount }}
                                </span>
                                <span class="text-[11px] text-white/95 font-bold block mt-1.5 drop-shadow-2xs">
                                    {{ $coupon->formatted_min_order }}
                                </span>
                            </div>

                            <span class="text-[9px] text-white/80 font-bold uppercase tracking-widest flex items-center gap-1 justify-center">
                                <i class="fas fa-utensils text-[8px]"></i> FOODDAILY
                            </span>
                        </div>

                        <!-- Thân vé bên phải (Main Body) -->
                        <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between space-y-2.5 min-w-0 bg-white">
                            <div>
                                <div class="flex items-center justify-between gap-1.5 flex-wrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-900 text-amber-300 font-mono font-black text-xs tracking-wider shadow-xs">
                                            #{{ $coupon->coupon_code }}
                                        </span>
                                        <button type="button" onclick="copyCouponCode('{{ $coupon->coupon_code }}', this)" class="p-1 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors text-xs cursor-pointer" title="Sao chép mã">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>

                                    @if($isExpiring)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 border border-rose-200 text-[10px] sm:text-[11px] font-black animate-pulse flex items-center gap-1" title="Sắp hết hạn sử dụng">
                                            <i class="fas fa-fire text-rose-500"></i> Còn {{ $coupon->days_remaining }} ngày
                                        </span>
                                    @endif
                                </div>

                                <h4 class="text-xs sm:text-sm font-black text-slate-900 mt-2 line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                    {{ $coupon->discount_type === 'percent' ? 'Giảm ' . (int)$coupon->discount_value . '% toàn menu' : 'Giảm trực tiếp ' . number_format($coupon->discount_value, 0, ',', '.') . 'đ' }}
                                </h4>
                                <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">
                                    Áp dụng cho đơn hàng từ <strong class="text-slate-700">{{ number_format($coupon->min_order_value, 0, ',', '.') }}đ</strong>
                                </p>
                            </div>

                            <!-- Footer của vé: HSD & Nút lưu -->
                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-slate-400 font-semibold flex items-center gap-1.5">
                                    <i class="far fa-calendar-alt text-slate-400"></i> HSD: {{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Vô thời hạn' }}
                                </span>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" 
                                            onclick="saveCoupon({{ $coupon->id }}, '{{ $coupon->coupon_code }}', this)" 
                                            class="btn-save-coupon px-3.5 py-1.5 rounded-xl text-xs font-black transition-all flex items-center gap-1
                                                   {{ $isSaved ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 cursor-default' : 'bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white border border-rose-200 cursor-pointer shadow-2xs hover:shadow-xs' }}"
                                            {{ $isSaved ? 'disabled' : '' }}>
                                        <i class="fas {{ $isSaved ? 'fa-check' : 'fa-bookmark' }} text-[11px]"></i>
                                        <span>{{ $isSaved ? 'Đã lưu' : 'Lưu mã' }}</span>
                                    </button>

                                    <a href="{{ route('muahang') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-900 text-slate-700 hover:text-white text-xs font-extrabold transition-all">
                                        Dùng
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Empty state khi lọc tab -->
            <div id="coupons-empty" class="hidden text-center py-12 space-y-2 bg-slate-50 rounded-2xl mt-5 border border-dashed border-slate-200">
                <div class="w-14 h-14 rounded-full bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-2xl mx-auto shadow-inner">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <p class="text-sm font-bold text-slate-700">Chưa có mã ưu đãi nào trong mục này</p>
                <p class="text-xs text-slate-400">Hãy chuyển sang tab "Tất cả" để lưu các mã giảm giá hời nhất nhé!</p>
            </div>

        </div>
    </div>
</section>
@endif

<!-- THUẬT TOÁN ĐỀ XUẤT: Món ăn cùng loại (Cùng category_id với món đang chọn/xem hoặc đã mua) -->
@if(isset($sameCategoryDishes) && $sameCategoryDishes->isNotEmpty())
<section class="py-4 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 sm:p-5 shadow-sm border border-rose-200 space-y-4 relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-rose-100/70">
                <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-tr from-[#ee4d2d] to-rose-400 text-white text-sm font-black shadow-xs shadow-rose-500/20">
                        <i class="fas fa-utensils"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                {{ isset($currentSelectedDish) ? '🔥 Món Cùng Loại Bạn Có Thể Thích: ' . ($currentSelectedDish->category->category_name ?? 'Món Ăn') : '🔥 Món Cùng Loại Bạn Có Thể Thích' }}
                            </h3>
                            @if(isset($currentSelectedDish->category))
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-[#ee4d2d] text-[10px] font-black tracking-wide uppercase hidden md:inline-block">
                                    {{ $currentSelectedDish->category->category_name }}
                                </span>
                            @endif
                            @if(!empty($isPurchasedDish))
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black tracking-wide flex items-center gap-1 shadow-2xs">
                                    <i class="fas fa-history text-[9px] text-amber-600"></i> Dựa trên món bạn đã đặt
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">
                            @if(!empty($isPurchasedDish))
                                Tự động đề xuất các món ăn cùng danh mục với món "{{ $currentSelectedDish->dish_name }}" bạn đã từng mua
                            @elseif(isset($currentSelectedDish))
                                Tự động đề xuất các món ăn cùng danh mục với món "{{ $currentSelectedDish->dish_name }}"
                            @else
                                Thuật toán tự động đề xuất các món ăn cùng danh mục
                            @endif
                        </p>
                    </div>
                </div>
                <a href="#menu" class="text-xs font-extrabold text-[#ee4d2d] hover:text-red-700 transition-colors flex items-center gap-1 shrink-0">
                    <span>≡ Xem tất cả thực đơn</span>
                </a>
            </div>

            <!-- 6 Card Grid Món Cùng Loại (Responsive 2/3/6 cột) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                @foreach($sameCategoryDishes as $rDish)
                    <div class="bg-white hover:bg-rose-50/40 rounded-xl p-2.5 sm:p-3 border border-rose-100 hover:border-[#ee4d2d]/40 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all duration-300 group">
                        <a href="{{ route('dish.detail', $rDish->id) }}" class="space-y-2 block" title="{{ $rDish->dish_name }}">
                            <div class="aspect-square w-full rounded-lg bg-slate-100 overflow-hidden relative border border-slate-100">
                                <img src="{{ $rDish->display_image }}" alt="{{ $rDish->dish_name }}" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-300" loading="lazy">
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded bg-white/90 backdrop-blur-xs text-[#ee4d2d] text-[9px] font-black uppercase shadow-2xs">
                                    {{ $rDish->category->category_name ?? 'Cùng loại' }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                    {{ $rDish->dish_name }}
                                </h4>
                                <p class="text-[10px] text-slate-400 line-clamp-1 font-medium">
                                    {{ $rDish->description ?? 'Món ngon đậm đà' }}
                                </p>
                            </div>
                        </a>
                        <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between gap-1.5">
                            <span class="text-xs text-[#ee4d2d] font-black">{{ number_format($rDish->price) }}đ</span>
                            <form action="{{ route('giohang.add') }}" method="POST" class="shrink-0">
                                @csrf
                                <input type="hidden" name="dish_id" value="{{ $rDish->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-lg text-[11px] font-extrabold shadow-2xs transition-colors flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-cart-plus text-[10px]"></i> + Đặt
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- THUẬT TOÁN (HÌNH 2): Đề xuất món ngon theo khung giờ thời gian thực tế chuẩn Việt Nam -->
@if(isset($timeRecommendation) && !empty($timeRecommendation['dishes']) && $timeRecommendation['dishes']->isNotEmpty())
<section class="py-4 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-200/80 space-y-4 relative overflow-hidden">
            <!-- Header Khung Giờ Thực Tế -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-tr {{ $timeRecommendation['badge_color'] ?? 'from-orange-500 to-rose-500' }} text-white text-base font-black shadow-xs shadow-orange-500/20">
                        <i class="fas {{ $timeRecommendation['icon'] ?? 'fa-utensils' }}"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                {{ $timeRecommendation['title'] }}
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-orange-50 text-[#ee4d2d] text-[10px] font-black tracking-wide border border-orange-100">
                                Khung giờ: {{ $timeRecommendation['period'] }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                            {{ $timeRecommendation['subtitle'] }}
                        </p>
                    </div>
                </div>

                <!-- Badge Thời Gian Thực Tế & Đang Phục Vụ -->
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200/80 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Đang phục vụ: {{ $timeRecommendation['time_range'] }}</span>
                    </span>
                </div>
            </div>

            <!-- Grid 4 Card Món Ăn Thiết Kế Cao Cấp Hiện Đại (Đầy đủ ảnh, giá, rating, nút đặt) -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @foreach($timeRecommendation['dishes'] as $tDish)
                    <div class="bg-white hover:bg-orange-50/20 rounded-xl p-3 border border-slate-200/70 hover:border-[#ee4d2d]/40 flex flex-col justify-between shadow-2xs hover:shadow-md transition-all duration-300 group">
                        <a href="{{ route('dish.detail', $tDish->id) }}" class="space-y-2.5 block" title="{{ $tDish->dish_name }}">
                            <!-- Thumbnail ảnh món ăn -->
                            <div class="aspect-4/3 w-full rounded-lg bg-slate-100 overflow-hidden relative border border-slate-100">
                                <img src="{{ $tDish->display_image }}" alt="{{ $tDish->dish_name }}" class="w-full h-full object-cover group-hover:scale-108 transition-transform duration-300" loading="lazy">
                                
                                <!-- Badge Danh mục -->
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-white/95 backdrop-blur-xs text-[#ee4d2d] text-[10px] font-extrabold shadow-xs">
                                    {{ $tDish->category->category_name ?? 'Món ngon' }}
                                </span>

                                <!-- Đánh giá sao -->
                                <div class="absolute bottom-2 left-2 px-1.5 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold flex items-center gap-1">
                                    <i class="fas fa-star text-amber-400 text-[9px]"></i>
                                    <span>{{ number_format($tDish->rating_score ?? 5.0, 1) }}</span>
                                </div>
                            </div>

                            <!-- Tên và mô tả -->
                            <div class="space-y-1">
                                <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm line-clamp-1 group-hover:text-[#ee4d2d] transition-colors">
                                    {{ $tDish->dish_name }}
                                </h4>
                                <p class="text-[11px] text-slate-400 line-clamp-1 font-medium">
                                    {{ $tDish->description ?? 'Hương vị thơm ngon, chế biến tươi mới mỗi ngày' }}
                                </p>
                            </div>
                        </a>

                        <!-- Giá tiền & Nút Đặt món -->
                        <div class="pt-2.5 mt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                            <div>
                                <span class="text-xs sm:text-sm text-[#ee4d2d] font-black">{{ number_format($tDish->price) }}đ</span>
                            </div>
                            <form action="{{ route('giohang.add') }}" method="POST" class="shrink-0">
                                @csrf
                                <input type="hidden" name="dish_id" value="{{ $tDish->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-lg text-xs font-extrabold shadow-2xs hover:shadow-xs transition-all flex items-center gap-1.5 cursor-pointer active:scale-95">
                                    <i class="fas fa-cart-plus text-[11px]"></i>
                                    <span>+ Đặt</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- SHOPEEFOOD PRODUCT GRID SECTION -->
<section class="py-6 bg-transparent" id="menu">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        
        <!-- Filter & Sort Bar -->
        <div class="bg-white/95 backdrop-blur-sm rounded-xl p-3 shadow-sm border border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs font-bold text-slate-700">
            <div class="flex items-center gap-4">
                <div class="cursor-pointer hover:text-[#ee4d2d] flex items-center gap-1">
                    <span>KHU VỰC</span> <i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                </div>
                <div class="cursor-pointer hover:text-[#ee4d2d] flex items-center gap-1">
                    <span>PHÂN LOẠI</span> <i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span id="menu-dishes-count" class="text-slate-400 font-semibold">{{ count($allDishes) }} Kết quả</span>
                <select id="sort-select" onchange="handleSortChange(this.value)" class="bg-slate-50 border border-slate-200 rounded px-2.5 py-1 text-xs font-bold text-slate-700 outline-none cursor-pointer">
                    <option value="rating_desc" {{ request('sort') == 'rating_desc' || !request('sort') ? 'selected' : '' }}>⭐ Đánh giá cao nhất</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                </select>
            </div>
        </div>

        <!-- CATEGORY TABS (Sticky cố định khi cuộn màn hình trên Mobile như App ShopeeFood/GrabFood) -->
        <div class="sticky top-16 z-30 bg-white/95 backdrop-blur-md py-2.5 px-1 -mx-1 border-b border-slate-200 flex items-center gap-2 overflow-x-auto no-scrollbar text-xs font-bold shadow-2xs">
            <button type="button" onclick="filterCategory('all')" id="tab-btn-all" class="cat-tab-btn px-4 py-2 rounded-xl bg-[#ee4d2d] text-white shrink-0 shadow-xs cursor-pointer">
                Tất cả món ăn
            </button>
            @foreach($categories as $category)
                <button type="button" onclick="filterCategory('cat-{{ $category->id }}')" id="tab-btn-cat-{{ $category->id }}" class="cat-tab-btn px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200/60 shrink-0 transition-colors cursor-pointer">
                    {{ $category->category_name }}
                </button>
            @endforeach
        </div>

        <!-- TAB CONTENT: ALL DISHES -->
        <div id="cat-view-all" class="cat-view-pane">
            <div id="all-dishes-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                @forelse($allDishes as $dIndex => $dish)
                    @include('client.partials.dish_card', ['dish' => $dish, 'dIndex' => $dIndex])
                @empty
                    <div id="all-dishes-empty" class="col-span-full py-12 text-center text-slate-400 font-medium">Chưa có món ăn nào trong hệ thống</div>
                @endforelse
            </div>
        </div>

        <!-- TAB CONTENT: SPECIFIC CATEGORIES VIEW -->
        @foreach($categories as $category)
            <div id="cat-view-cat-{{ $category->id }}" class="cat-view-pane hidden">
                <div id="cat-grid-{{ $category->id }}" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4">
                    @forelse($category->dishes as $cIndex => $dish)
                        @include('client.partials.dish_card', ['dish' => $dish, 'dIndex' => $cIndex])
                    @empty
                        <div id="cat-empty-{{ $category->id }}" class="col-span-full py-8 text-center text-slate-400">Danh mục này hiện chưa có món ăn.</div>
                    @endforelse
                </div>
            </div>
        @endforeach

        <!-- INFINITE SCROLL LOADER & CONTROLS -->
        <div id="infinite-scroll-container" class="py-6 flex flex-col items-center justify-center space-y-3">
            <!-- Scroll Sentinel (Quan sát bởi IntersectionObserver để tự động tải thêm món) -->
            <div id="scroll-sentinel" class="flex items-center gap-2.5 px-5 py-2.5 rounded-full bg-white/95 backdrop-blur-xs border border-rose-200 text-[#ee4d2d] text-xs font-extrabold shadow-sm transition-all">
                <i class="fas fa-circle-notch fa-spin text-sm"></i>
                <span>Đang tải thêm món ngon...</span>
            </div>

            <!-- Nút dự phòng Xem thêm món ăn -->
            <button type="button" id="btn-load-more" onclick="loadMoreDishes()" class="hidden px-6 py-2.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-extrabold text-xs shadow-xs hover:border-[#ee4d2d] hover:text-[#ee4d2d] transition-all cursor-pointer">
                <i class="fas fa-chevron-down mr-1"></i> Xem thêm món ăn
            </button>

            <!-- Thông báo khi đã hiển thị hết danh sách -->
            <div id="all-loaded-indicator" class="hidden text-xs font-bold text-slate-400 flex items-center gap-2 py-2">
                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                <span>Bạn đã xem hết danh sách món ăn trong mục này</span>
                <span class="w-2 h-2 rounded-full bg-slate-300"></span>
            </div>
        </div>

    </div>
</section>

<!-- MODAL TẠO ĐẶT ĐƠN THEO NHÓM -->
<div id="groupModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-100 space-y-4">
        <div class="text-center space-y-1">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-lg font-bold mx-auto">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-lg font-extrabold text-slate-900">Tạo Đặt Đơn Theo Nhóm (FOODDAILY)</h3>
            <p class="text-xs text-slate-500 font-medium">Nhập tên bạn để tạo phòng và nhận ngay link / mã QR cho cả nhóm cùng đặt món</p>
        </div>

        <form action="{{ route('nhom.create') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tên Trưởng Nhóm <span class="text-[#ee4d2d]">*</span></label>
                <input type="text" name="host_name" value="{{ auth()->check() ? (auth()->user()->fullname ?? auth()->user()->name) : '' }}" required placeholder="Ví dụ: Nguyễn Văn A" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeGroupModal()" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">Hủy</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md">TẠO PHÒNG NHÓM</button>
            </div>
        </form>
    </div>
    <!-- FLOATING ACTION BUTTON: KHO ƯU ĐÃI NỔI BẬT GÓC DƯỚI (Xếp trên nút Trợ lý AI) -->
    @if(isset($activeCoupons) && $activeCoupons->isNotEmpty())
    <div class="fixed bottom-20 right-6 z-40 group">
        <button type="button" onclick="openVoucherModal()" 
                class="relative w-12 h-12 rounded-full text-white shadow-xl hover:shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 border-2 border-white flex items-center justify-center cursor-pointer" 
                style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 100%) !important;"
                title="Kho Ưu Đãi ({{ $activeCoupons->count() }} mã)">
            <span class="text-xl animate-bounce">🎁</span>
            <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-white text-[#ee4d2d] font-black text-[10px] flex items-center justify-center shadow-md border border-rose-200">
                {{ $activeCoupons->count() }}
            </span>
        </button>
    </div>

    <!-- POP-UP MODAL: CHÀO MỪNG HÀNG LOẠT MÃ KHUYẾN MÃI (ShopeeFood Style) -->
    <div id="voucherModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 hidden">
        <div class="bg-white rounded-3xl max-w-xl w-full overflow-hidden shadow-2xl border border-rose-200 transform transition-all duration-300 scale-95 opacity-0 flex flex-col max-h-[90vh]" id="voucherModalContent">
            
            <!-- Header Pop-up với hình hộp quà hoạt họa và gradient đỏ cam -->
            <div class="p-6 text-white text-center relative overflow-hidden shrink-0" style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 50%, #f59e0b 100%) !important;">
                <!-- Nút đóng -->
                <button type="button" onclick="closeVoucherModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center transition-colors text-lg font-bold cursor-pointer">
                    &times;
                </button>

                <!-- Bong bóng trang trí -->
                <div class="w-32 h-32 rounded-full bg-white/10 absolute -top-10 -left-10 blur-xl"></div>
                <div class="w-32 h-32 rounded-full bg-white/10 absolute -bottom-10 -right-10 blur-xl"></div>

                <div class="relative z-10 space-y-2">
                    <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/20 text-3xl shadow-inner mb-1 animate-pulse">
                        🎁
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black tracking-tight leading-tight">
                        TẶNG BẠN KHO MÃ GIẢM GIÁ ĐỘC QUYỀN!
                    </h3>
                    <p class="text-xs text-rose-100 max-w-md mx-auto">
                        Chọn thu thập các mã siêu hời bên dưới. Hệ thống sẽ <strong>tự động áp dụng mã tốt nhất</strong> khi bạn thanh toán!
                    </p>
                    <div class="pt-1">
                        <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-[11px] font-bold text-yellow-200">
                            🔒 Lưu ý: Mã khuyến mãi chỉ dùng được sau khi đăng nhập thành công
                        </span>
                    </div>
                </div>
            </div>

            <!-- Thân Modal: Danh sách các mã cuộn mượt -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-3 flex-1 divide-y divide-slate-100">
                @foreach($activeCoupons as $coupon)
                    @php
                        $isSaved = in_array($coupon->id, $savedCouponIds);
                    @endphp
                    <div class="pt-3 first:pt-0 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-14 h-14 rounded-2xl text-white flex flex-col items-center justify-center font-black text-center shrink-0 shadow-xs" style="background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 100%) !important;">
                                <span class="text-xs leading-none drop-shadow-2xs">{{ $coupon->discount_type === 'percent' ? (int)$coupon->discount_value . '%' : number_format($coupon->discount_value/1000) . 'K' }}</span>
                                <span class="text-[9px] uppercase tracking-wider text-rose-100 font-bold mt-0.5">GIẢM</span>
                            </div>
                            <div class="min-w-0 space-y-0.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-black text-slate-900 text-xs tracking-wider">#{{ $coupon->coupon_code }}</span>
                                    @if($coupon->is_expiring_soon)
                                        <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-black animate-pulse">
                                            ⏰ Còn {{ $coupon->days_remaining }} ngày
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs font-bold text-slate-700 truncate">{{ $coupon->formatted_discount }} - {{ $coupon->formatted_min_order }}</p>
                                <p class="text-[10px] text-slate-400">HSD: {{ $coupon->end_date ? $coupon->end_date->format('d/m/Y') : 'Vô thời hạn' }}</p>
                            </div>
                        </div>

                        <button type="button" 
                                onclick="saveCoupon({{ $coupon->id }}, '{{ $coupon->coupon_code }}', this)" 
                                class="px-3.5 py-1.5 rounded-xl text-xs font-black transition-all shrink-0
                                       {{ $isSaved ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 cursor-default' : 'text-white shadow-xs hover:opacity-90 cursor-pointer' }}"
                                style="{{ $isSaved ? '' : 'background: #ee4d2d !important;' }}"
                                {{ $isSaved ? 'disabled' : '' }}>
                            {{ $isSaved ? 'Đã lưu ✓' : 'Lưu mã' }}
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Footer Modal -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <button type="button" onclick="saveAllCoupons()" class="w-full sm:w-auto px-5 py-2.5 text-white rounded-xl text-xs font-black shadow-xs flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-95" style="background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;">
                    <i class="fas fa-bolt text-yellow-200"></i> Thu thập tất cả mã
                </button>
                <button type="button" onclick="closeVoucherModal()" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-extrabold transition-colors cursor-pointer">
                    Khám phá món ngay →
                </button>
            </div>

        </div>
    </div>
    @endif
@endsection

@section('styles')
<style>
    @keyframes dishCardFadeIn {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .dish-card-appear {
        animation: dishCardFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection


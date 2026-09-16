@extends('layouts.app')

@section('title', 'FOODDAILY - Đặt Đồ Ăn, Giao Hàng Siêu Tốc Từ 20 phút')

@section('content')
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
                        {{ $settings['banner_subtitle'] ?? "Có 110.625 Địa Điểm Ở TP. HCM Từ 00:00 - 23:59" }}
                    </p>

                    <!-- Big Search Bar (ShopeeFood Style with Blue Button) -->
                    <form action="{{ route('trangchu') }}" method="GET" class="flex items-center bg-white rounded-lg overflow-hidden p-1 shadow-lg">
                        <input type="text" name="search" value="{{ $query }}" placeholder="Tìm địa điểm, món ăn, địa chỉ..." class="w-full px-4 py-2.5 text-xs font-semibold text-slate-800 bg-transparent outline-none">
                        <button type="submit" class="px-6 py-2.5 bg-[#0099ff] hover:bg-blue-600 text-white font-extrabold text-xs rounded-md transition-colors shrink-0">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </form>

                    <!-- Category Filter Pills Grid (ShopeeFood Style Tag Buttons) -->
                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-semibold">
                        <a href="#menu" onclick="filterCategory('all')" id="hero-tab-all" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-[#ee4d2d] backdrop-blur-xs border border-white/20 text-white transition-all">Tất cả</a>
                        @foreach($categories as $cat)
                            <a href="#menu" onclick="filterCategory('cat-{{ $cat->id }}')" id="hero-tab-cat-{{ $cat->id }}" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-[#ee4d2d] backdrop-blur-xs border border-white/20 text-white transition-all">{{ $cat->category_name }}</a>
                        @endforeach
                    </div>
                </div>

                <!-- Bottom Download Badges -->
                <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                    <span class="text-[11px] text-slate-300 font-medium">Sử dụng App FOODDAILY để có nhiều giảm giá hơn:</span>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-black/60 rounded border border-white/20 text-[10px] font-bold cursor-pointer hover:bg-black"><i class="fab fa-apple mr-1"></i>App Store</span>
                        <span class="px-3 py-1 bg-black/60 rounded border border-white/20 text-[10px] font-bold cursor-pointer hover:bg-black"><i class="fab fa-google-play mr-1"></i>Google Play</span>
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

                <!-- 2x3 Grid of 6 Promo Items với Logic Thêm Vào Giỏ Hàng -->
                <div class="grid grid-cols-3 gap-3">
                    @php
                        $promoDishes = $allDishes->take(6);
                    @endphp
                    @foreach($promoDishes as $pIndex => $pDish)
                        <form action="{{ route('giohang.add') }}" method="POST" class="bg-white rounded-lg border border-slate-200 p-2 space-y-1.5 hover:border-[#ee4d2d] hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between" onclick="this.submit()">
                            @csrf
                            <input type="hidden" name="dish_id" value="{{ $pDish->id }}">
                            <div class="space-y-1.5">
                                <div class="aspect-square rounded-md overflow-hidden bg-slate-100 relative border border-slate-100">
                                    @if($pDish->image)
                                        <img src="{{ asset($pDish->image) }}" alt="{{ $pDish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
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

<!-- THUẬT TOÁN ĐỀ XUẤT: Món ăn cùng loại (Cùng category_id với món đang chọn/xem) -->
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
                        <div class="flex items-center gap-2">
                            <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                {{ isset($currentSelectedDish) ? '🔥 Món Cùng Loại Bạn Có Thể Thích: ' . ($currentSelectedDish->category->category_name ?? 'Món Ăn') : '🔥 Món Cùng Loại Bạn Có Thể Thích' }}
                            </h3>
                            @if(isset($currentSelectedDish->category))
                                <span class="px-2 py-0.5 rounded-full bg-rose-100 text-[#ee4d2d] text-[10px] font-black tracking-wide uppercase hidden md:inline-block">
                                    {{ $currentSelectedDish->category->category_name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">
                            {{ isset($currentSelectedDish) ? 'Tự động đề xuất các món ăn cùng danh mục với món "' . $currentSelectedDish->dish_name . '"' : 'Thuật toán tự động đề xuất các món ăn cùng danh mục' }}
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

<!-- THUẬT TOÁN: Đề xuất theo khung giờ -->
@if(isset($timeRecommendation) && $timeRecommendation['dishes']->isNotEmpty())
<section class="py-4 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-sm border border-slate-200 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 text-sm">{{ $timeRecommendation['title'] }}</h3>
                <span class="text-[11px] text-slate-400 font-bold">Khung giờ: {{ $timeRecommendation['period'] }}</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($timeRecommendation['dishes'] as $tDish)
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100 flex items-center justify-between">
                        <a href="{{ route('dish.detail', $tDish->id) }}" class="truncate mr-2 block hover:text-[#ee4d2d]">
                            <h4 class="font-extrabold text-slate-900 text-xs truncate hover:text-[#ee4d2d]">{{ $tDish->dish_name }}</h4>
                            <span class="text-[11px] font-bold text-slate-700">{{ number_format($tDish->price) }}đ</span>
                        </a>
                        <form action="{{ route('giohang.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="dish_id" value="{{ $tDish->id }}">
                            <button type="submit" class="w-6 h-6 rounded bg-[#ee4d2d] text-white text-xs font-bold">+</button>
                        </form>
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
                <span class="text-slate-400 font-semibold">{{ count($allDishes) }} Kết quả</span>
                <select id="sort-select" onchange="handleSortChange(this.value)" class="bg-slate-50 border border-slate-200 rounded px-2.5 py-1 text-xs font-bold text-slate-700 outline-none cursor-pointer">
                    <option value="rating_desc" {{ request('sort') == 'rating_desc' || !request('sort') ? 'selected' : '' }}>⭐ Đánh giá cao nhất</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp đến cao</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao đến thấp</option>
                </select>
            </div>
        </div>

        <!-- CATEGORY TABS (Tất cả, Đồ ăn, Đồ uống...) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 border-b border-slate-200 text-xs font-bold">
            <button type="button" onclick="filterCategory('all')" id="tab-btn-all" class="cat-tab-btn px-4 py-2 rounded-lg bg-[#ee4d2d] text-white shrink-0">
                Tất cả món ăn
            </button>
            @foreach($categories as $category)
                <button type="button" onclick="filterCategory('cat-{{ $category->id }}')" id="tab-btn-cat-{{ $category->id }}" class="cat-tab-btn px-4 py-2 rounded-lg bg-white text-slate-700 hover:bg-slate-100 border border-slate-200 shrink-0">
                    {{ $category->category_name }}
                </button>
            @endforeach
        </div>

        <!-- TAB CONTENT: ALL DISHES -->
        <div id="cat-view-all" class="cat-view-pane">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @forelse($allDishes as $dIndex => $dish)
                    <div class="dish-card-item bg-white/95 backdrop-blur-sm rounded-xl shadow-xs hover:shadow-md transition-all duration-300 border border-slate-200 overflow-hidden flex flex-col justify-between group {{ $dIndex >= 15 ? 'hidden' : '' }}" data-index="{{ $dIndex }}">
                        <div class="space-y-2">
                            <!-- Image Thumbnail with Rating Badge & Favorite Badge -->
                            <a href="{{ route('dish.detail', $dish->id) }}" class="aspect-square bg-slate-100 relative overflow-hidden block">
                                <img src="{{ $dish->display_image }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                
                                <!-- Star Rating Badge on Top Right -->
                                <span class="absolute top-2 right-2 bg-slate-900/80 backdrop-blur-xs text-amber-400 text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs flex items-center gap-1 border border-white/20">
                                    <i class="fas fa-star text-[9px] text-amber-400"></i>
                                    <span>{{ number_format($dish->rating_score, 1) }}</span>
                                </span>

                                @if($dish->rating_score >= 4.9 || $dIndex % 3 == 0)
                                    <span class="absolute top-2 left-0 bg-[#ee4d2d] text-white text-[9px] font-extrabold px-2 py-0.5 rounded-r-md shadow-xs flex items-center gap-1">
                                        <i class="fas fa-thumbs-up text-[8px]"></i> Yêu thích
                                    </span>
                                @endif
                            </a>

                            <!-- Content Details (Checkmark + Title + Address + Rating Stars + Promo Tag) -->
                            <div class="p-2.5 space-y-1.5">
                                <a href="{{ route('dish.detail', $dish->id) }}" class="flex items-start gap-1 group/title block">
                                    <span class="text-amber-500 text-xs shrink-0 mt-0.5">✔</span>
                                    <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 leading-snug group-hover/title:text-[#ee4d2d] transition-colors">
                                        {{ $dish->dish_name }}
                                    </h4>
                                </a>
                                <p class="text-[10px] text-slate-500 font-medium line-clamp-1 leading-tight">
                                    {{ $dish->description ?? 'FOODDAILY Store - TP. HCM' }}
                                </p>
                                
                                <!-- Rating Stars & Review Count -->
                                <div class="flex items-center gap-1.5 pt-0.5">
                                    <div class="flex items-center gap-1 text-[11px] font-black text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-200/70">
                                        <i class="fas fa-star text-[9px] text-amber-500"></i>
                                        <span>{{ number_format($dish->rating_score, 1) }}</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-slate-400">({{ $dish->reviews_count }} đánh giá)</span>
                                </div>

                                <!-- Red Promo Tag Badge -->
                                <div>
                                    <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-[#ee4d2d] text-[9px] font-extrabold border border-rose-200">
                                        🏷️ Mã giảm {{ ($dIndex % 2 == 0) ? '11%' : '20%' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer Price & Add Button -->
                        <div class="p-2.5 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                            <span class="text-xs font-black text-slate-900">{{ number_format($dish->price) }}đ</span>
                            <form action="{{ route('giohang.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="dish_id" value="{{ $dish->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-7 h-7 rounded-lg bg-[#ee4d2d] hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" title="Thêm vào giỏ hàng">
                                    +
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400 font-medium">Chưa có món ăn nào trong hệ thống</div>
                @endforelse
            </div>
        </div>

        <!-- TAB CONTENT: SPECIFIC CATEGORIES VIEW -->
        @foreach($categories as $category)
            <div id="cat-view-cat-{{ $category->id }}" class="cat-view-pane hidden">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @forelse($category->dishes as $cIndex => $dish)
                        <div class="dish-card-item bg-white/95 backdrop-blur-sm rounded-xl shadow-xs hover:shadow-md transition-all duration-300 border border-slate-200 overflow-hidden flex flex-col justify-between group {{ $cIndex >= 15 ? 'hidden' : '' }}" data-index="{{ $cIndex }}">
                            <div class="space-y-2">
                                <a href="{{ route('dish.detail', $dish->id) }}" class="aspect-square bg-slate-100 relative overflow-hidden block">
                                    <img src="{{ $dish->display_image }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                    
                                    <!-- Star Rating Badge on Top Right -->
                                    <span class="absolute top-2 right-2 bg-slate-900/80 backdrop-blur-xs text-amber-400 text-[10px] font-black px-2 py-0.5 rounded-full shadow-xs flex items-center gap-1 border border-white/20">
                                        <i class="fas fa-star text-[9px] text-amber-400"></i>
                                        <span>{{ number_format($dish->rating_score, 1) }}</span>
                                    </span>
                                </a>
                                <div class="p-2.5 space-y-1.5">
                                    <a href="{{ route('dish.detail', $dish->id) }}" class="flex items-start gap-1 block">
                                        <span class="text-amber-500 text-xs shrink-0 mt-0.5">✔</span>
                                        <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 leading-snug hover:text-[#ee4d2d]">
                                            {{ $dish->dish_name }}
                                        </h4>
                                    </a>
                                    <p class="text-[10px] text-slate-400 line-clamp-1">{{ $dish->description ?? 'FOODDAILY Store' }}</p>
                                    
                                    <!-- Rating Stars & Review Count -->
                                    <div class="flex items-center gap-1.5 pt-0.5">
                                        <div class="flex items-center gap-1 text-[11px] font-black text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-200/70">
                                            <i class="fas fa-star text-[9px] text-amber-500"></i>
                                            <span>{{ number_format($dish->rating_score, 1) }}</span>
                                        </div>
                                        <span class="text-[10px] font-semibold text-slate-400">({{ $dish->reviews_count }} đánh giá)</span>
                                    </div>

                                    <div>
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-[#ee4d2d] text-[9px] font-extrabold border border-rose-200">
                                            🏷️ Mã giảm 11%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2.5 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                                <span class="text-xs font-black text-slate-900">{{ number_format($dish->price) }}đ</span>
                                <form action="{{ route('giohang.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="dish_id" value="{{ $dish->id }}">
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-[#ee4d2d] hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold transition-colors cursor-pointer">+</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400">Danh mục này hiện chưa có món ăn.</div>
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
</div>
@endsection

@section('scripts')
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

<script>
    const INITIAL_LIMIT = 15; // 3 hàng (mỗi hàng 5 card trên desktop)
    const BATCH_SIZE = 10;    // Tải thêm 2 hàng mỗi lần cuộn
    let currentActiveTab = 'all';
    let visibleLimits = { 'all': INITIAL_LIMIT };
    let isLoadingMore = false;
    let scrollObserver = null;

    function filterCategory(targetId) {
        let key = targetId;
        if (key !== 'all' && !String(key).startsWith('cat-')) {
            key = 'cat-' + key;
        }

        currentActiveTab = key;
        if (!visibleLimits[key]) {
            visibleLimits[key] = INITIAL_LIMIT;
        }

        // Ẩn tất cả các khung hiển thị món
        document.querySelectorAll('.cat-view-pane').forEach(pane => pane.classList.add('hidden'));

        // Hiển thị khung danh mục được chọn
        const targetPane = document.getElementById('cat-view-' + key);
        if (targetPane) {
            targetPane.classList.remove('hidden');
        }

        // Cập nhật kiểu dáng nút tab bên dưới
        document.querySelectorAll('.cat-tab-btn').forEach(btn => {
            btn.classList.remove('bg-[#ee4d2d]', 'text-white');
            btn.classList.add('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
        });

        const activeBtn = document.getElementById('tab-btn-' + key);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
            activeBtn.classList.add('bg-[#ee4d2d]', 'text-white');
        }

        // Cập nhật hiển thị số lượng card món cho tab này
        updateActiveTabVisibility();
    }

    function updateActiveTabVisibility() {
        const activePane = document.getElementById('cat-view-' + currentActiveTab);
        if (!activePane) return;

        const items = activePane.querySelectorAll('.dish-card-item');
        const total = items.length;
        const currentLimit = visibleLimits[currentActiveTab] || INITIAL_LIMIT;

        items.forEach(item => {
            const index = parseInt(item.dataset.index) || 0;
            if (index < currentLimit) {
                if (item.classList.contains('hidden')) {
                    item.classList.remove('hidden');
                    item.classList.add('dish-card-appear');
                }
            } else {
                item.classList.add('hidden');
                item.classList.remove('dish-card-appear');
            }
        });

        const sentinel = document.getElementById('scroll-sentinel');
        const btnMore = document.getElementById('btn-load-more');
        const allLoaded = document.getElementById('all-loaded-indicator');

        if (total === 0 || currentLimit >= total) {
            if (sentinel) sentinel.classList.add('hidden');
            if (btnMore) btnMore.classList.add('hidden');
            if (allLoaded) {
                if (total > INITIAL_LIMIT) {
                    allLoaded.classList.remove('hidden');
                } else {
                    allLoaded.classList.add('hidden');
                }
            }
        } else {
            if (sentinel) sentinel.classList.remove('hidden');
            if (btnMore) btnMore.classList.add('hidden');
            if (allLoaded) allLoaded.classList.add('hidden');
        }
    }

    function loadMoreDishes() {
        if (isLoadingMore) return;

        const activePane = document.getElementById('cat-view-' + currentActiveTab);
        if (!activePane) return;

        const items = activePane.querySelectorAll('.dish-card-item');
        const total = items.length;
        const currentLimit = visibleLimits[currentActiveTab] || INITIAL_LIMIT;

        if (currentLimit >= total) return;

        isLoadingMore = true;
        const sentinel = document.getElementById('scroll-sentinel');
        if (sentinel) {
            sentinel.classList.remove('hidden');
            sentinel.classList.add('opacity-100');
        }

        setTimeout(() => {
            visibleLimits[currentActiveTab] = (visibleLimits[currentActiveTab] || INITIAL_LIMIT) + BATCH_SIZE;
            updateActiveTabVisibility();
            isLoadingMore = false;
        }, 300);
    }

    // Thiết lập IntersectionObserver phát hiện cuộn trang
    function initInfiniteScrollObserver() {
        const sentinel = document.getElementById('scroll-sentinel');
        if (!sentinel) return;

        if ('IntersectionObserver' in window) {
            scrollObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !isLoadingMore) {
                        loadMoreDishes();
                    }
                });
            }, {
                rootMargin: '250px 0px',
                threshold: 0.1
            });

            scrollObserver.observe(sentinel);
        } else {
            // Dự phòng cho trình duyệt cũ không hỗ trợ IntersectionObserver
            const btnMore = document.getElementById('btn-load-more');
            if (btnMore) btnMore.classList.remove('hidden');
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        updateActiveTabVisibility();
        initInfiniteScrollObserver();
    });

    function handleSortChange(sortVal) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortVal);
        window.location.href = url.toString();
    }

    function openGroupModal() {
        document.getElementById('groupModal').classList.remove('hidden');
    }
    function closeGroupModal() {
        document.getElementById('groupModal').classList.add('hidden');
    }
</script>
@endsection


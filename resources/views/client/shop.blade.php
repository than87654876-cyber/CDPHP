@extends('layouts.app')

@section('title', 'FOODDAILY - Đặt Đồ Ăn, Giao Hàng Siêu Tốc Từ 20 phút')

@section('content')
<!-- ShopeeFood Hero Section (Matches Image 1 Exactly) -->
<section class="bg-[#f5f5f5] py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            
            @php
                $heroBgUrl = asset('uploads/ve-dep-sai-gon-qua-ong-kinh-cua-nguoi-me-anh-ivivu-2.jpg');
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
                        <input type="text" name="search" value="{{ $query }}" placeholder="Tìm địa điểm, món ăn, địa chỉ..." class="w-full px-4 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                        <button type="submit" class="px-6 py-2.5 bg-[#0099ff] hover:bg-blue-600 text-white font-extrabold text-xs rounded-md transition-colors shrink-0">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </form>

                    <!-- Category Filter Pills Grid (ShopeeFood Style Tag Buttons) -->
                    <div class="pt-2 flex flex-wrap gap-2 text-xs font-semibold">
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">All</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Đồ ăn</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Đồ uống</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Đồ chay</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Bánh kem</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Tráng miệng</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Pizza/Burger</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Món lẩu</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Mì phở</button>
                        <button type="button" onclick="filterCategory('all')" class="px-3.5 py-1.5 rounded-md bg-white/10 hover:bg-white/20 backdrop-blur-xs border border-white/20 text-white transition-all">Cơm hộp</button>
                    </div>
                </div>

                <!-- Bottom Download Badges -->
                <div class="pt-4 border-t border-white/10 flex items-center gap-3">
                    <span class="text-[11px] text-slate-300 font-medium">Sử dụng App FOODDAILY để có nhiều giảm giá hơn:</span>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-black/60 rounded border border-white/20 text-[10px] font-bold"><i class="fab fa-apple mr-1"></i>App Store</span>
                        <span class="px-3 py-1 bg-black/60 rounded border border-white/20 text-[10px] font-bold"><i class="fab fa-google-play mr-1"></i>Google Play</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: White Promo Floating Card Widget (Giống 100% Ảnh 1) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 shadow-sm border border-slate-200 flex flex-col justify-between space-y-4">
                <!-- Location Address Bar -->
                <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 flex items-center justify-between cursor-pointer hover:bg-slate-100">
                    <span class="truncate"><strong class="text-[#ee4d2d]">Đồ ăn</strong> → Chọn địa chỉ giao hàng</span>
                    <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
                </div>

                <!-- Subcard "Ưu đãi" Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="font-extrabold text-slate-900 text-sm">Ưu đãi</h3>
                    <a href="#menu" class="text-xs font-bold text-[#0099ff] hover:underline">≡ Xem tất cả</a>
                </div>

                <!-- 2x3 Grid of 6 Promo Items (Giống Ảnh 1) -->
                <div class="grid grid-cols-3 gap-3">
                    @php
                        $promoDishes = $allDishes->take(6);
                    @endphp
                    @foreach($promoDishes as $pIndex => $pDish)
                        <div class="bg-white rounded-lg border border-slate-200 p-2 space-y-1.5 hover:border-[#ee4d2d] transition-all cursor-pointer">
                            <div class="aspect-square rounded-md overflow-hidden bg-slate-100 relative">
                                @if($pDish->image)
                                    <img src="{{ asset($pDish->image) }}" alt="{{ $pDish->dish_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils"></i></div>
                                @endif
                            </div>
                            <h4 class="font-extrabold text-slate-900 text-[11px] line-clamp-1 leading-snug">{{ $pDish->dish_name }}</h4>
                            <p class="text-[10px] text-slate-400 truncate">FOODDAILY Store</p>
                            <!-- Red Promo Tag -->
                            <div class="inline-block px-1.5 py-0.5 rounded bg-rose-50 text-[#ee4d2d] text-[9px] font-extrabold border border-rose-200">
                                🏷️ Mã giảm {{ ($pIndex % 2 == 0) ? '10%' : '20%' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ShopeeFood Collection Banner Section ("Bộ sưu tập") -->
<section class="py-4 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 text-sm">Bộ sưu tập</h3>
                <a href="#menu" class="text-xs font-bold text-[#0099ff] hover:underline">≡ Xem tất cả</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="h-16 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-black flex items-center justify-center p-3 text-xs shadow-xs cursor-pointer hover:opacity-95">
                    🥗 ĂN CHẠY GIẢM 30%
                </div>
                <div class="h-16 rounded-xl bg-gradient-to-r from-orange-500 to-rose-600 text-white font-black flex items-center justify-center p-3 text-xs shadow-xs cursor-pointer hover:opacity-95">
                    🔥 QUÁN RUỘT GIẢM 50K
                </div>
                <div class="h-16 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white font-black flex items-center justify-center p-3 text-xs shadow-xs cursor-pointer hover:opacity-95">
                    🏷️ GIẢM TỚI 70K
                </div>
                <div class="h-16 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-black flex items-center justify-center p-3 text-xs shadow-xs cursor-pointer hover:opacity-95">
                    🚚 FREESHIP XTRA 0Đ
                </div>
            </div>
        </div>
    </div>
</section>

<!-- THUẬT TOÁN: Món hay mua (người dùng mua >= 2 lần) -->
@if(auth()->check() && isset($frequentDishes) && $frequentDishes->isNotEmpty())
<section class="py-4 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-rose-50/80 rounded-2xl p-4 border border-rose-200 space-y-3">
            <div class="flex items-center gap-2">
                <span class="text-base">❤️</span>
                <h3 class="font-extrabold text-slate-900 text-sm">Món Ăn Bạn Hay Mua Nhất</h3>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($frequentDishes as $fDish)
                    <div class="bg-white rounded-xl p-3 border border-rose-100 flex items-center gap-3 shadow-xs">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                            @if($fDish->image)
                                <img src="{{ asset($fDish->image) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="space-y-0.5 flex-1 min-w-0">
                            <h4 class="font-extrabold text-slate-900 text-xs truncate">{{ $fDish->dish_name }}</h4>
                            <p class="text-[11px] text-[#ee4d2d] font-bold">{{ number_format($fDish->price) }}đ</p>
                        </div>
                        <form action="{{ route('giohang.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="dish_id" value="{{ $fDish->id }}">
                            <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] text-white rounded text-[10px] font-extrabold hover:bg-red-600">+ Đặt lại</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- THUẬT TOÁN: Đề xuất theo khung giờ -->
@if(isset($timeRecommendation) && $timeRecommendation['dishes']->isNotEmpty())
<section class="py-4 bg-[#f5f5f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="font-extrabold text-slate-900 text-sm">{{ $timeRecommendation['title'] }}</h3>
                <span class="text-[11px] text-slate-400 font-bold">Khung giờ: {{ $timeRecommendation['period'] }}</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($timeRecommendation['dishes'] as $tDish)
                    <div class="bg-slate-50 rounded-xl p-2.5 border border-slate-100 flex items-center justify-between">
                        <div class="truncate mr-2">
                            <h4 class="font-extrabold text-slate-900 text-xs truncate">{{ $tDish->dish_name }}</h4>
                            <span class="text-[11px] font-bold text-slate-700">{{ number_format($tDish->price) }}đ</span>
                        </div>
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

<!-- SHOPEEFOOD PRODUCT GRID SECTION (Matches Image 2 Exactly) -->
<section class="py-6 bg-[#f5f5f5]" id="menu">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        
        <!-- Filter & Sort Bar (Giống 100% Ảnh 2 Header Bar) -->
        <div class="bg-white rounded-xl p-3 shadow-sm border border-slate-200 flex flex-wrap items-center justify-between gap-4 text-xs font-bold text-slate-700">
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
                <select class="bg-slate-50 border border-slate-200 rounded px-2.5 py-1 text-xs font-bold text-slate-700 outline-none">
                    <option>Đúng nhất</option>
                    <option>Gần tôi</option>
                    <option>Giá thấp đến cao</option>
                </select>
            </div>
        </div>

        <!-- CATEGORY TABS (Tất cả, Ăn sáng, Tráng miệng...) -->
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

        <!-- TAB CONTENT: ALL DISHES (ShopeeFood 5-Column Card Grid - Giống Ảnh 2) -->
        <div id="cat-view-all" class="cat-view-pane">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @forelse($allDishes as $dIndex => $dish)
                    <div class="bg-white rounded-xl shadow-xs hover:shadow-md transition-all border border-slate-200 overflow-hidden flex flex-col justify-between group">
                        <div class="space-y-2">
                            <!-- Image Thumbnail with "👍 Yêu thích" Red Badge -->
                            <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                @if($dish->image)
                                    <img src="{{ asset($dish->image) }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-100"><i class="fas fa-utensils text-2xl"></i></div>
                                @endif
                                
                                @if($dIndex % 3 == 0)
                                    <span class="absolute top-2 left-0 bg-[#ee4d2d] text-white text-[9px] font-extrabold px-2 py-0.5 rounded-r-md shadow-xs flex items-center gap-1">
                                        <i class="fas fa-thumbs-up text-[8px]"></i> Yêu thích
                                    </span>
                                @endif
                            </div>

                            <!-- Content Details (Checkmark + Title + Address + Red Promo Tag) -->
                            <div class="p-2.5 space-y-1">
                                <div class="flex items-start gap-1">
                                    <span class="text-amber-500 text-xs shrink-0 mt-0.5">✔</span>
                                    <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 leading-snug group-hover:text-[#ee4d2d] transition-colors">
                                        {{ $dish->dish_name }}
                                    </h4>
                                </div>
                                <p class="text-[10px] text-slate-400 line-clamp-1 leading-tight">
                                    {{ $dish->description ?? 'FOODDAILY Store - TP. HCM' }}
                                </p>
                                
                                <!-- Red Promo Tag Badge (Giống 100% Ảnh 2) -->
                                <div class="pt-1">
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
                                <button type="submit" class="w-7 h-7 rounded-lg bg-[#ee4d2d] hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold transition-colors">
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
                        <div class="bg-white rounded-xl shadow-xs hover:shadow-md transition-all border border-slate-200 overflow-hidden flex flex-col justify-between group">
                            <div class="space-y-2">
                                <div class="aspect-square bg-slate-100 relative overflow-hidden">
                                    @if($dish->image)
                                        <img src="{{ asset($dish->image) }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils"></i></div>
                                    @endif
                                </div>
                                <div class="p-2.5 space-y-1">
                                    <div class="flex items-start gap-1">
                                        <span class="text-amber-500 text-xs shrink-0 mt-0.5">✔</span>
                                        <h4 class="font-extrabold text-slate-900 text-xs line-clamp-1 leading-snug group-hover:text-[#ee4d2d]">
                                            {{ $dish->dish_name }}
                                        </h4>
                                    </div>
                                    <p class="text-[10px] text-slate-400 line-clamp-1">{{ $dish->description ?? 'FOODDAILY Store' }}</p>
                                    <div class="pt-1">
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
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-[#ee4d2d] text-white flex items-center justify-center text-xs font-bold">+</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400">Danh mục này hiện chưa có món ăn.</div>
                    @endforelse
                </div>
            </div>
        @endforeach

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
<script>
    function filterCategory(targetId) {
        document.querySelectorAll('.cat-view-pane').forEach(pane => pane.classList.add('hidden'));
        const targetPane = document.getElementById('cat-view-' + targetId);
        if (targetPane) {
            targetPane.classList.remove('hidden');
        }

        document.querySelectorAll('.cat-tab-btn').forEach(btn => {
            btn.classList.remove('bg-[#ee4d2d]', 'text-white');
            btn.classList.add('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
        });

        const activeBtn = document.getElementById('tab-btn-' + targetId);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-slate-700', 'hover:bg-slate-100', 'border', 'border-slate-200');
            activeBtn.classList.add('bg-[#ee4d2d]', 'text-white');
        }
    }

    function openGroupModal() {
        document.getElementById('groupModal').classList.remove('hidden');
    }
    function closeGroupModal() {
        document.getElementById('groupModal').classList.add('hidden');
    }
</script>
@endsection

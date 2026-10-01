<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FOODDAILY - Đặt Đồ Ăn, Giao Hàng Siêu Tốc')</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}">
    
    <!-- Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Vite Assets & Tailwind CDN -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        shopee: {
                            DEFAULT: '#ee4d2d',
                            hover: '#d73211',
                            orange: '#ff4726',
                            blue: '#0099ff'
                        }
                    }
                }
            }
        }
    </script>

    @php
        $globalSettings = \App\Models\Setting::pluck('value', 'key')->all();
        $siteBgUrl = isset($globalSettings['site_background']) && $globalSettings['site_background'] 
            ? (\Illuminate\Support\Str::startsWith($globalSettings['site_background'], 'http') ? $globalSettings['site_background'] : asset($globalSettings['site_background']))
            : asset('uploads/cf1a02d49dc2b801e809fcb9adefd77e.jpg');
        $siteLogoUrl = isset($globalSettings['logo_url']) && $globalSettings['logo_url']
            ? (\Illuminate\Support\Str::startsWith($globalSettings['logo_url'], 'http') ? $globalSettings['logo_url'] : asset($globalSettings['logo_url']))
            : asset('logo.jpg');
    @endphp

    <style>
        body, .site-bg-overlay {
            background-image: linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.25)), url("{{ $siteBgUrl }}") !important;
            background-attachment: fixed !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            background-size: cover !important;
            min-height: 100vh !important;
        }
    </style>
    @yield('styles')
</head>
<body class="font-sans antialiased site-bg-overlay text-slate-800 flex flex-col min-h-screen selection:bg-rose-500 selection:text-white">

    <!-- ShopeeFood Style Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Left: Brand Logo & Location -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('trangchu') }}" class="flex items-center gap-2.5 group">
                        <img src="{{ $siteLogoUrl }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover shadow-xs border border-slate-100">
                        <div class="flex flex-col">
                            <span class="font-black text-2xl tracking-tighter text-[#ee4d2d] group-hover:opacity-90 transition-opacity">FOODDAILY</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest -mt-1.5">Food Delivery</span>
                        </div>
                    </a>

                    <!-- City Selector Pill -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 cursor-pointer hover:bg-slate-200/80 transition-all">
                        <span>TP. HCM</span>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                    </div>
                </div>

                <!-- Center: ShopeeFood Navigation Tabs -->
                <nav class="hidden lg:flex items-center gap-6 text-xs font-bold text-slate-600 h-full">
                    <a href="{{ route('trangchu') }}" class="h-full flex items-center {{ request()->routeIs('trangchu') ? 'border-b-2 border-[#ee4d2d] text-[#ee4d2d] font-extrabold' : 'hover:text-[#ee4d2d] transition-colors' }} px-1">
                        Đồ ăn
                    </a>
                    <a href="{{ route('tracuu') }}" class="h-full flex items-center {{ request()->routeIs('tracuu') ? 'border-b-2 border-[#ee4d2d] text-[#ee4d2d] font-extrabold' : 'hover:text-[#ee4d2d] transition-colors' }} px-1">
                        Tra cứu đơn
                    </a>
                    <a href="{{ route('yeucauhoan') }}" class="h-full flex items-center {{ request()->routeIs('yeucauhoan') ? 'border-b-2 border-[#ee4d2d] text-[#ee4d2d] font-extrabold' : 'hover:text-[#ee4d2d] transition-colors' }} px-1">
                        Đổi/Hoàn tiền
                    </a>
                    <button type="button" onclick="openGlobalUserVoucherModal('all')" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1 cursor-pointer">
                        <i class="fas fa-ticket-alt text-[#ee4d2d] mr-1.5 text-xs"></i>
                        <span>Kho Ưu Đãi</span>
                        <span class="ml-1.5 px-1.5 py-0.2 bg-gradient-to-r from-red-500 to-amber-500 text-[9px] text-white font-black rounded-full animate-pulse">HOT</span>
                    </button>
                </nav>


                <!-- Right: Search, Auth & Language -->
                <div class="flex items-center gap-4">
                    <!-- Search Icon Trigger -->
                    <button type="button" onclick="toggleHeaderSearch()" class="p-2 text-slate-600 hover:text-[#ee4d2d] text-base transition-colors" title="Tìm kiếm món ăn">
                        <i class="fas fa-magnifying-glass"></i>
                    </button>

                    <!-- Cart Icon (Dành cho cả Khách vãng lai & Thành viên) -->
                    <a href="{{ route('muahang') }}" class="relative p-2 text-slate-600 hover:text-[#ee4d2d] transition-colors" title="Giỏ hàng & Thanh toán">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span id="global-header-cart-badge" class="absolute -top-1.5 -right-2 bg-[#ee4d2d] text-white text-xs font-black min-w-[20px] h-5 px-1.5 rounded-full flex items-center justify-center border-2 border-white shadow-sm hidden">
                            0
                        </span>
                    </a>

                    @auth
                        <!-- Nút truy cập nhanh Trang Admin cho Superadmin/Admin/Staff -->
                        @if(in_array(Auth::user()->role, ['superadmin', 'admin', 'staff', 'kitchen', 'shipper']) || (method_exists(Auth::user(), 'hasAnyRole') && Auth::user()->hasAnyRole(['superadmin', 'admin', 'staff', 'kitchen', 'shipper'])))
                            <a href="{{ in_array(Auth::user()->role, ['superadmin', 'admin']) ? route('quanly') : (Auth::user()->role === 'kitchen' ? route('quanly_bep') : route('quanly_banlamviec')) }}" 
                               class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900 hover:bg-slate-800 text-amber-300 font-extrabold text-xs shadow-xs border border-slate-700 transition-all hover:scale-102"
                               title="Truy cập Bảng điều khiển Quản trị">
                                <i class="fas fa-shield-halved text-amber-400"></i>
                                <span>Trang Admin</span>
                            </a>
                        @endif

                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 p-1 pl-2 pr-2.5 bg-slate-100 hover:bg-slate-200/80 rounded-full border border-slate-200 transition-all">
                                <div class="w-6 h-6 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center font-bold text-[11px]">
                                    {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-800 hidden sm:inline">{{ Auth::user()->fullname ?? Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->fullname ?? Auth::user()->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                    @if(Auth::user()->role === 'superadmin')
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-purple-100 text-purple-700 text-[10px] font-black uppercase">👑 Super Admin</span>
                                    @elseif(Auth::user()->role === 'admin')
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md bg-rose-100 text-[#ee4d2d] text-[10px] font-black uppercase">⭐ Admin</span>
                                    @endif
                                </div>
                                @if(in_array(Auth::user()->role, ['superadmin', 'admin', 'staff', 'kitchen', 'shipper']) || (method_exists(Auth::user(), 'hasAnyRole') && Auth::user()->hasAnyRole(['superadmin', 'admin', 'staff', 'kitchen', 'shipper'])))
                                    <a href="{{ in_array(Auth::user()->role, ['superadmin', 'admin']) ? route('quanly') : (Auth::user()->role === 'kitchen' ? route('quanly_bep') : route('quanly_banlamviec')) }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-extrabold text-[#ee4d2d] bg-rose-50/50 hover:bg-rose-50 border-b border-rose-100">
                                        <i class="fas fa-shield-halved text-[#ee4d2d]"></i>Vào trang Quản trị (Admin)
                                    </a>
                                @endif
                                <a href="{{ route('giohang') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-receipt text-slate-400"></i>Đơn hàng của tôi
                                </a>
                                <button type="button" onclick="openGlobalUserVoucherModal('saved')" class="w-full text-left flex items-center justify-between px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-[#ee4d2d] transition-colors cursor-pointer">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-ticket-alt text-amber-500"></i>Ví voucher của tôi
                                    </span>
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-black px-1.5 py-0.5 rounded-full">Ưu đãi</span>
                                </button>
                                <hr class="my-1 border-slate-100">
                                <a href="{{ route('dangxuat') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">
                                    <i class="fas fa-right-from-bracket"></i>Đăng xuất
                                </a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('dangnhap') }}" class="px-4 py-1.5 rounded-lg border border-[#ee4d2d] text-[#ee4d2d] hover:bg-[#ee4d2d] hover:text-white text-xs font-bold transition-all">
                            Đăng nhập
                        </a>
                    @endauth

                    <!-- Language Flag Badge -->
                    <div class="flex items-center gap-1 text-xs font-bold text-slate-600 pl-2 border-l border-slate-200">
                        <span class="text-sm">🇻🇳</span>
                        <i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- Header Live Search Modal Overlay -->
    <div id="header-search-modal" class="hidden fixed inset-x-0 top-16 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 p-4 shadow-xl">
        <div class="max-w-3xl mx-auto relative">
            <form action="{{ route('trangchu') }}" method="GET" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-sm"></i>
                    <input type="text" name="search" id="header-search-input" oninput="handleLiveSearch(this.value)" autocomplete="off" placeholder="Nhập tên món ăn cần tìm (VD: Bánh mì, Cơm tấm, Trà sữa)..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none shadow-xs">
                </div>
                <button type="submit" class="px-5 py-3 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md transition-all">TÌM KIẾM</button>
                <button type="button" onclick="toggleHeaderSearch()" class="px-4 py-3 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition-colors">Đóng</button>
            </form>

            <!-- Live Search Results Dropdown -->
            <div id="live-search-results" class="hidden mt-3 bg-white rounded-2xl border border-slate-200 shadow-2xl overflow-hidden divide-y divide-slate-100 max-h-96 overflow-y-auto">
                <!-- Populated via AJAX -->
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- ===== VÒNG QUAY HÔM NAY MÌNH ĂN GÌ – FLOATING BUTTON ===== -->
    <button type="button" id="spin-wheel-fab"
            onclick="openSpinWheelModal()"
            class="fixed z-50 flex items-center gap-2.5 px-4 py-3 rounded-full font-extrabold text-xs shadow-xl cursor-pointer transition-all duration-300 hover:scale-105 select-none"
            style="bottom: 5rem; right: 1.5rem; background: linear-gradient(135deg, #f59e0b 0%, #ee4d2d 100%); color:#fff; box-shadow: 0 8px 28px rgba(238,77,45,0.45);">
        <span class="relative flex h-2.5 w-2.5 shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
        </span>
        <span class="text-base">🎡</span>
        <span>Hôm nay mình ăn gì?</span>
    </button>

    <!-- ===== SPIN WHEEL MODAL ===== -->
    <div id="spin-wheel-modal"
         class="hidden fixed inset-0 z-[999] flex items-center justify-center p-3"
         style="background: rgba(15,23,42,0.75); backdrop-filter: blur(6px);">

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col"
             style="max-height: 94vh;">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 shrink-0"
                 style="background: linear-gradient(135deg, #f59e0b 0%, #ee4d2d 100%);">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎡</span>
                    <div>
                        <h3 class="font-black text-white text-base leading-tight">Hôm nay mình ăn gì?</h3>
                        <p class="text-amber-100 text-[11px] font-medium">Quay ngẫu nhiên & khám phá món ngon theo túi tiền!</p>
                    </div>
                </div>
                <button type="button" onclick="closeSpinWheelModal()"
                        class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/35 text-white flex items-center justify-center text-lg font-bold transition-colors cursor-pointer">
                    &times;
                </button>
            </div>

            <!-- Scrollable body -->
            <div class="overflow-y-auto flex-1 px-5 py-4 bg-gradient-to-b from-amber-50/40 to-white">

                <!-- Canvas Vòng Quay -->
                <div class="relative flex justify-center items-center mb-4">
                    <!-- Arrow pointer -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center" style="margin-top:-4px;">
                        <div style="width:0;height:0;border-left:12px solid transparent;border-right:12px solid transparent;border-top:26px solid #ee4d2d;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.3));"></div>
                    </div>
                    <canvas id="spin-wheel-canvas" width="300" height="300"
                            class="rounded-full shadow-xl border-4 border-amber-200 cursor-pointer"
                            style="display:block;"
                            onclick="startSpin()"></canvas>
                </div>

                <!-- Spin Button -->
                <div class="flex justify-center mb-4">
                    <button type="button" id="spin-btn" onclick="startSpin()"
                            class="px-8 py-3 rounded-full font-black text-sm text-white shadow-lg cursor-pointer transition-all duration-300 hover:scale-105 active:scale-95 flex items-center gap-2"
                            style="background: linear-gradient(135deg, #f59e0b, #ee4d2d); box-shadow: 0 6px 20px rgba(238,77,45,0.4);">
                        <i class="fas fa-dice text-base"></i>
                        <span id="spin-btn-text">Quay!</span>
                    </button>
                </div>

                <!-- Price Slider -->
                <div class="bg-white rounded-2xl p-4 border border-amber-100 shadow-xs mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-black text-slate-700 flex items-center gap-1.5">
                            <i class="fas fa-wallet text-amber-500"></i>
                            Ngân sách tối đa
                        </label>
                        <span id="spin-price-display"
                              class="px-3 py-1 rounded-full text-xs font-black text-white"
                              style="background: linear-gradient(135deg, #f59e0b, #ee4d2d);">100.000đ</span>
                    </div>

                    <input type="range" id="spin-price-slider"
                           min="20000" max="400000" step="10000" value="100000"
                           oninput="onPriceSliderChange(this.value)"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer"
                           style="accent-color: #ee4d2d; background: linear-gradient(to right, #ee4d2d 0%, #ee4d2d 18.75%, #e2e8f0 18.75%, #e2e8f0 100%);">

                    <div class="flex items-center justify-between mt-1.5 text-[10px] text-slate-400 font-bold">
                        <span>18.000đ</span>
                        <span>200.000đ</span>
                        <span>400.000đ</span>
                    </div>
                </div>

                <!-- Kết quả quay -->
                <div id="spin-result-area" class="hidden bg-gradient-to-br from-amber-50 to-rose-50 rounded-2xl border border-amber-200 p-4 space-y-3">
                    <div class="flex items-center gap-2 pb-2 border-b border-amber-200">
                        <span class="text-lg">🎉</span>
                        <span class="font-black text-slate-800 text-sm">Kết quả vòng quay!</span>
                    </div>
                    <div id="spin-result-card" class="space-y-2"></div>
                    <div class="flex gap-2 pt-1">
                        <button type="button" onclick="startSpin()"
                                class="flex-1 py-2.5 rounded-xl text-xs font-black border border-amber-300 text-amber-800 bg-amber-50 hover:bg-amber-100 transition-colors cursor-pointer flex items-center justify-center gap-1.5">
                            <i class="fas fa-redo text-xs"></i> Quay lại
                        </button>
                        <a id="spin-order-btn" href="{{ route('muahang') }}"
                           class="flex-1 py-2.5 rounded-xl text-xs font-black text-white text-center flex items-center justify-center gap-1.5 cursor-pointer transition-all"
                           style="background: linear-gradient(135deg, #f59e0b, #ee4d2d);">
                            <i class="fas fa-shopping-cart text-xs"></i> Đặt ngay!
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== SPIN WHEEL JAVASCRIPT ===== -->
    <script>
    (function() {
        // ---- State ----
        let spinDishes        = [];   // dishes loaded from API matching price
        let spinRotation      = 0;    // current canvas rotation in degrees
        let isSpinning        = false;
        let spinMaxPrice      = 100000;
        let spinAnimationId   = null;
        let spinSegmentColors = [
            '#ee4d2d','#f59e0b','#10b981','#3b82f6','#8b5cf6',
            '#ec4899','#14b8a6','#f97316','#06b6d4','#84cc16',
            '#a855f7','#ef4444'
        ];

        // ---- Format money ----
        function fmtMoney(n) {
            return Number(n).toLocaleString('vi-VN') + 'đ';
        }

        // ---- Open / Close Modal ----
        window.openSpinWheelModal = function() {
            document.getElementById('spin-wheel-modal').classList.remove('hidden');
            loadSpinDishes(spinMaxPrice);
        };
        window.closeSpinWheelModal = function() {
            document.getElementById('spin-wheel-modal').classList.add('hidden');
            if (spinAnimationId) { cancelAnimationFrame(spinAnimationId); spinAnimationId = null; }
        };

        // ---- Price Slider ----
        window.onPriceSliderChange = function(val) {
            spinMaxPrice = parseInt(val);
            const pct = ((spinMaxPrice - 20000) / (400000 - 20000)) * 100;
            const slider = document.getElementById('spin-price-slider');
            if (slider) slider.style.background = `linear-gradient(to right, #ee4d2d 0%, #ee4d2d ${pct}%, #e2e8f0 ${pct}%, #e2e8f0 100%)`;
            document.getElementById('spin-price-display').textContent = fmtMoney(spinMaxPrice);
            loadSpinDishes(spinMaxPrice);
        };

        // ---- Load dishes from API ----
        function loadSpinDishes(maxPrice) {
            fetch(`{{ route('api.spin_dishes') }}?max_price=${maxPrice}`)
                .then(r => r.json())
                .then(data => {
                    spinDishes = data.dishes || [];
                    if (spinDishes.length === 0) {
                        spinDishes = [{ dish_name: 'Thử ngân sách cao hơn nhé!', price: 0, id: null }];
                    }
                    drawWheel(spinRotation);
                    // Reset result
                    document.getElementById('spin-result-area').classList.add('hidden');
                })
                .catch(() => {
                    spinDishes = [{ dish_name: 'Không tải được món', price: 0, id: null }];
                    drawWheel(spinRotation);
                });
        }

        // ---- Draw Wheel on Canvas ----
        function drawWheel(rotateDeg) {
            const canvas = document.getElementById('spin-wheel-canvas');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const W = canvas.width, H = canvas.height;
            const cx = W / 2, cy = H / 2, R = Math.min(cx, cy) - 4;
            const n = spinDishes.length;
            const arc = (2 * Math.PI) / n;

            ctx.clearRect(0, 0, W, H);

            // Outer ring shadow
            ctx.save();
            ctx.shadowColor = 'rgba(0,0,0,0.18)';
            ctx.shadowBlur  = 14;
            ctx.beginPath();
            ctx.arc(cx, cy, R + 3, 0, 2 * Math.PI);
            ctx.fillStyle = '#fff';
            ctx.fill();
            ctx.restore();

            const offsetRad = (rotateDeg - 90) * (Math.PI / 180); // 0° at top

            for (let i = 0; i < n; i++) {
                const startAngle = offsetRad + i * arc;
                const endAngle   = startAngle + arc;
                const color      = spinSegmentColors[i % spinSegmentColors.length];

                // Segment fill
                ctx.beginPath();
                ctx.moveTo(cx, cy);
                ctx.arc(cx, cy, R, startAngle, endAngle);
                ctx.closePath();
                ctx.fillStyle = color;
                ctx.fill();
                ctx.strokeStyle = 'rgba(255,255,255,0.5)';
                ctx.lineWidth = 1.5;
                ctx.stroke();

                // Text
                ctx.save();
                ctx.translate(cx, cy);
                ctx.rotate(startAngle + arc / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fff';
                ctx.font = `bold ${n > 8 ? 9 : 11}px "Plus Jakarta Sans", sans-serif`;
                ctx.shadowColor = 'rgba(0,0,0,0.4)';
                ctx.shadowBlur  = 3;

                const name  = spinDishes[i].dish_name || '';
                const label = name.length > 16 ? name.slice(0, 14) + '…' : name;
                const price = spinDishes[i].price > 0 ? fmtMoney(spinDishes[i].price) : '';

                ctx.fillText(label, R - 8, -3);
                if (price) {
                    ctx.font = `${n > 8 ? 8 : 9}px "Plus Jakarta Sans", sans-serif`;
                    ctx.fillStyle = 'rgba(255,255,255,0.85)';
                    ctx.fillText(price, R - 8, 9);
                }
                ctx.restore();
            }

            // Center circle
            ctx.beginPath();
            ctx.arc(cx, cy, 26, 0, 2 * Math.PI);
            ctx.fillStyle = '#1e293b';
            ctx.fill();
            ctx.beginPath();
            ctx.arc(cx, cy, 20, 0, 2 * Math.PI);
            const grad = ctx.createRadialGradient(cx - 4, cy - 4, 2, cx, cy, 20);
            grad.addColorStop(0, '#f59e0b');
            grad.addColorStop(1, '#ee4d2d');
            ctx.fillStyle = grad;
            ctx.fill();

            // Center icon
            ctx.fillStyle = '#fff';
            ctx.font = '14px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('🎡', cx, cy);
        }

        // ---- Spin Animation ----
        window.startSpin = function() {
            if (isSpinning) return;
            if (spinDishes.length === 0) return;

            isSpinning = true;
            document.getElementById('spin-result-area').classList.add('hidden');
            const btn = document.getElementById('spin-btn');
            const btnText = document.getElementById('spin-btn-text');
            if (btn)     btn.disabled = true;
            if (btnText) btnText.textContent = 'Đang quay...';

            // Random total rotation: 5-10 full turns + random offset
            const extraDeg  = 360 * (5 + Math.floor(Math.random() * 5));
            const n         = spinDishes.length;
            const arc       = 360 / n;
            // Pick a random winner index
            const winnerIdx = Math.floor(Math.random() * n);
            // The winner segment center should land at top (270° from positive x axis in canvas coords)
            const targetAngle = (360 - (winnerIdx * arc + arc / 2)) % 360;
            const totalDeg  = extraDeg + targetAngle;

            const startRot   = spinRotation;
            const endRot     = startRot + totalDeg;
            const duration   = 4000 + Math.random() * 1200; // 4–5.2s
            const startTime  = performance.now();

            function easeOut(t) {
                return 1 - Math.pow(1 - t, 4);
            }

            function animate(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                spinRotation = startRot + (endRot - startRot) * easeOut(progress);
                drawWheel(spinRotation);

                if (progress < 1) {
                    spinAnimationId = requestAnimationFrame(animate);
                } else {
                    spinRotation = spinRotation % 360;
                    isSpinning = false;
                    if (btn)     btn.disabled = false;
                    if (btnText) btnText.textContent = 'Quay lại!';
                    showSpinResult(winnerIdx);
                }
            }

            spinAnimationId = requestAnimationFrame(animate);
        };

        // ---- Show Result ----
        function showSpinResult(idx) {
            const dish = spinDishes[idx];
            if (!dish) return;

            const resultArea = document.getElementById('spin-result-area');
            const resultCard = document.getElementById('spin-result-card');
            const orderBtn   = document.getElementById('spin-order-btn');

            resultCard.innerHTML = `
                <div class="flex items-center gap-3 bg-white rounded-xl p-3 border border-amber-200 shadow-xs">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                         style="background: linear-gradient(135deg, #fef3c7, #fee2e2);">
                        🍽️
                    </div>
                    <div class="min-w-0">
                        <p class="font-black text-slate-900 text-sm leading-tight">${dish.dish_name}</p>
                        ${dish.price > 0 ? `<p class="text-[#ee4d2d] font-extrabold text-xs mt-0.5">${fmtMoney(dish.price)}</p>` : ''}
                        ${dish.description ? `<p class="text-slate-400 text-[10px] mt-0.5 truncate">${dish.description}</p>` : ''}
                    </div>
                </div>
            `;

            if (dish.id && orderBtn) {
                orderBtn.href = '{{ route("muahang") }}';
            }

            resultArea.classList.remove('hidden');
            resultArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // ---- Init on DOM ready ----
        document.addEventListener('DOMContentLoaded', function() {
            drawWheel(0); // Draw empty / placeholder wheel immediately
            onPriceSliderChange(100000);
        });
    })();
    </script>

    <!-- Floating AI Chatbot Widget -->
    <button type="button" onclick="toggleChatbot()" class="fixed bottom-6 right-6 z-50 px-4 py-3 rounded-full bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-xl shadow-rose-500/30 flex items-center gap-2.5 hover:scale-105 transition-all cursor-pointer">
        <span class="relative flex h-2.5 w-2.5">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
        </span>
        <i class="fas fa-robot text-base"></i> <span>Trợ lý AI FOODDAILY</span>
    </button>

    <!-- Chatbot Modal Window -->
    <div id="chatbot-modal" class="hidden fixed bottom-20 right-6 z-50 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden transition-all duration-300">
        <!-- Chat Header -->
        <div class="bg-[#ee4d2d] text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center font-bold text-lg">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm leading-tight">FOODDAILY Assistant</h3>
                    <p class="text-[10px] text-white/80 font-medium">Sẵn sàng gợi ý & tìm món cho bạn 24/7</p>
                </div>
            </div>
            <button type="button" onclick="toggleChatbot()" class="text-white/80 hover:text-white text-lg"><i class="fas fa-xmark"></i></button>
        </div>

        <!-- Chat Messages Area -->
        <div id="chatbot-messages" class="p-4 space-y-3 h-80 overflow-y-auto bg-slate-50/70 text-xs font-medium">
            <div class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs text-slate-800 max-w-[85%] leading-relaxed">
                    Xin chào! 👋 Mình là Trợ lý AI FOODDAILY. Bạn đang thèm món gì hoặc cần tìm món ăn gì hôm nay?
                </div>
            </div>
        </div>

        <!-- Chat Input Form -->
        <form id="chatbot-form" onsubmit="sendChatbotMessage(event)" class="p-3 bg-white border-t border-slate-100 flex items-center gap-2">
            <input type="text" id="chatbot-input" placeholder="Nhập tên món ăn hoặc hỏi AI..." class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 outline-none focus:border-[#ee4d2d]">
            <button type="submit" class="w-9 h-9 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white flex items-center justify-center text-xs font-bold shrink-0">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>


    <!-- ShopeeFood Footer -->
    <footer class="bg-white/95 backdrop-blur-md border-t border-slate-200 pt-12 pb-8 mt-0 text-xs text-slate-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 pb-8 border-b border-slate-100">
                <div class="space-y-3">
                    <a href="{{ route('trangchu') }}" class="flex items-center">
                        <span class="font-black text-xl text-[#ee4d2d]">FOODDAILY</span>
                    </a>
                    <p class="text-slate-500 text-[11px] leading-relaxed">
                        FOODDAILY - Hệ thống giao đồ ăn, thực phẩm sạch tận nơi nhanh chóng từ 20 phút.
                    </p>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Về FOODDAILY</h4>
                    <ul class="space-y-2 text-slate-500">
                        <li><a href="{{ route('trangchu') }}" class="hover:text-[#ee4d2d]">Giới thiệu</a></li>
                        <li><a href="{{ route('tracuu') }}" class="hover:text-[#ee4d2d]">Tra cứu đơn hàng</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Trung tâm hỗ trợ</h4>
                    <ul class="space-y-2 text-slate-500">
                        <li><a href="{{ route('yeucauhoan') }}" class="hover:text-[#ee4d2d]">Chính sách đổi trả / hoàn tiền</a></li>
                        <li><a href="#" class="hover:text-[#ee4d2d]">Quy chế hoạt động</a></li>
                        <li><a href="#" class="hover:text-[#ee4d2d]">Giải quyết khiếu nại</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Tải ứng dụng FOODDAILY</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 p-2 bg-slate-900 text-white rounded-lg w-36 cursor-pointer hover:bg-slate-800">
                            <i class="fab fa-apple text-xl"></i>
                            <div class="leading-none"><span class="text-[9px] block">App Store</span><span class="font-bold text-xs">iOS App</span></div>
                        </div>
                        <div class="flex items-center gap-2 p-2 bg-slate-900 text-white rounded-lg w-36 cursor-pointer hover:bg-slate-800">
                            <i class="fab fa-google-play text-lg"></i>
                            <div class="leading-none"><span class="text-[9px] block">Google Play</span><span class="font-bold text-xs">Android</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 text-center text-slate-400 text-[11px]">
                © 2026 FOODDAILY Company. Tất cả quyền được bảo lưu.
            </div>
        </div>
    </footer>

    <script>
        // Header Cart Badge Update
        function updateGlobalHeaderCartBadge() {
            try {
                const cart = JSON.parse(localStorage.getItem('fooddelicious_cart') || '[]');
                const badge = document.getElementById('global-header-cart-badge');
                if (badge) {
                    if (cart.length > 0) {
                        badge.innerText = cart.length;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch(e){}
        }
        document.addEventListener("DOMContentLoaded", updateGlobalHeaderCartBadge);
        window.addEventListener("storage", updateGlobalHeaderCartBadge);

        // Toggle Header Search Modal Bar
        function toggleHeaderSearch() {
            const modal = document.getElementById('header-search-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                document.getElementById('header-search-input').focus();
            }
        }

        // Live AJAX Dish Search by Name
        let liveSearchTimer = null;
        function handleLiveSearch(query) {
            clearTimeout(liveSearchTimer);
            const resultsBox = document.getElementById('live-search-results');
            if (!query.trim()) {
                resultsBox.classList.add('hidden');
                resultsBox.innerHTML = '';
                return;
            }

            liveSearchTimer = setTimeout(() => {
                fetch(`{{ route('api.dishes.search') }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.dishes && data.dishes.length > 0) {
                            resultsBox.innerHTML = data.dishes.map(d => `
                                <div class="p-3 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden border border-slate-100 shrink-0">
                                            ${d.image ? `<img src="${d.image}" class="w-full h-full object-cover">` : `<div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils text-xs"></i></div>`}
                                        </div>
                                        <div>
                                            <h4 class="font-extrabold text-slate-900 text-xs">${d.dish_name}</h4>
                                            <p class="text-[11px] text-[#ee4d2d] font-black">${d.formatted_price}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('giohang.add') }}" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="dish_id" value="${d.id}">
                                        <button type="submit" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-red-600 text-white rounded-lg text-xs font-bold shadow-xs">
                                            + Đặt món
                                        </button>
                                    </form>
                                </div>
                            `).join('');
                            resultsBox.classList.remove('hidden');
                        } else {
                            resultsBox.innerHTML = `<div class="p-4 text-center text-slate-400 text-xs font-semibold">Không tìm thấy món nào có tên "${query}"</div>`;
                            resultsBox.classList.remove('hidden');
                        }
                    })
                    .catch(err => console.error(err));
            }, 250);
        }

        // Toggle Chatbot Window Modal
        function toggleChatbot() {
            const modal = document.getElementById('chatbot-modal');
            modal.classList.toggle('hidden');
            if (!modal.classList.contains('hidden')) {
                document.getElementById('chatbot-input').focus();
            }
        }

        // Send Chatbot Message API
        function sendChatbotMessage(e) {
            e.preventDefault();
            const input = document.getElementById('chatbot-input');
            const msg = input.value.trim();
            if (!msg) return;

            const msgContainer = document.getElementById('chatbot-messages');

            // Render User Message
            msgContainer.innerHTML += `
                <div class="flex items-start justify-end gap-2">
                    <div class="bg-[#ee4d2d] text-white p-3 rounded-2xl text-xs max-w-[85%] font-semibold shadow-xs">
                        ${msg}
                    </div>
                </div>
            `;
            input.value = '';
            msgContainer.scrollTop = msgContainer.scrollHeight;

            // Render Loading Indicator
            const loadingId = 'cb-loading-' + Date.now();
            msgContainer.innerHTML += `
                <div id="${loadingId}" class="flex items-start gap-2">
                    <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                    <div class="bg-white p-3 rounded-2xl border border-slate-100 text-slate-400 italic text-xs">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Trợ lý AI đang tìm món...
                    </div>
                </div>
            `;
            msgContainer.scrollTop = msgContainer.scrollHeight;

            // Fetch Chatbot API
            fetch(`{{ route('api.chatbot.ask') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msg })
            })
            .then(res => res.json())
            .then(data => {
                const loader = document.getElementById(loadingId);
                if (loader) loader.remove();

                let dishesHtml = '';
                if (data.dishes && data.dishes.length > 0) {
                    dishesHtml = `<div class="mt-2.5 space-y-2">` + data.dishes.map(d => `
                        <div class="bg-slate-50 p-2 rounded-xl border border-slate-100 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 truncate">
                                ${d.image ? `<img src="${d.image}" class="w-8 h-8 rounded-lg object-cover">` : ''}
                                <div class="truncate">
                                    <h5 class="font-bold text-[11px] text-slate-800 truncate">${d.dish_name}</h5>
                                    <p class="text-[10px] text-[#ee4d2d] font-extrabold">${d.price}</p>
                                </div>
                            </div>
                            <form action="{{ route('giohang.add') }}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="dish_id" value="${d.id}">
                                <button type="submit" class="px-2.5 py-1 bg-[#ee4d2d] text-white rounded-md text-[10px] font-bold">+ Đặt</button>
                            </form>
                        </div>
                    `).join('') + `</div>`;
                }

                msgContainer.innerHTML += `
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                        <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs text-slate-800 max-w-[85%] leading-relaxed">
                            ${data.reply}
                            ${dishesHtml}
                        </div>
                    </div>
                `;
                msgContainer.scrollTop = msgContainer.scrollHeight;
            })
            .catch(err => {
                const loader = document.getElementById(loadingId);
                if (loader) loader.remove();
                msgContainer.innerHTML += `
                    <div class="flex items-start gap-2">
                        <div class="w-7 h-7 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center font-bold text-xs shrink-0">AI</div>
                        <div class="bg-rose-50 text-rose-600 p-3 rounded-2xl border border-rose-200 text-xs">
                            Có lỗi khi kết nối với AI. Vui lòng thử lại sau!
                        </div>
                    </div>
                `;
                msgContainer.scrollTop = msgContainer.scrollHeight;
            });
        }

        // Ngăn chặn hiển thị trang từ bộ nhớ đệm (Back-Forward Cache) khi đã đăng xuất
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });

        // ================= KHO ƯU ĐÃI & VÍ VOUCHER TOÀN TRANG =================
        let globalCouponsData = [];
        let currentVoucherTab = 'all';

        function openGlobalUserVoucherModal(tab = 'all') {
            currentVoucherTab = tab;
            const modal = document.getElementById('globalUserVoucherModal');
            if (!modal) return;
            modal.classList.remove('hidden');

            loadGlobalCoupons();
        }

        function closeGlobalUserVoucherModal() {
            const modal = document.getElementById('globalUserVoucherModal');
            if (modal) modal.classList.add('hidden');
        }

        function switchGlobalVoucherTab(tab) {
            currentVoucherTab = tab;
            document.querySelectorAll('.global-tab-btn').forEach(btn => {
                const isActive = btn.getAttribute('data-tab') === tab;
                btn.className = `global-tab-btn flex-1 py-2 rounded-xl text-xs font-black transition-all ${
                    isActive ? 'bg-[#ee4d2d] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                }`;
            });
            renderGlobalCoupons();
        }

        function loadGlobalCoupons() {
            const listDiv = document.getElementById('global-voucher-list');
            if (!listDiv) return;

            listDiv.innerHTML = '<div class="py-12 text-center text-xs text-slate-400 font-bold"><i class="fas fa-spinner fa-spin mr-2"></i>Đang nạp kho voucher...</div>';

            fetch("{{ route('api.coupons.my') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        globalCouponsData = data.coupons || [];
                        const savedCount = globalCouponsData.filter(c => c.is_saved).length;
                        const expiringCount = globalCouponsData.filter(c => c.is_expiring_soon).length;

                        const savedCountBadge = document.getElementById('global-badge-saved');
                        const expiringCountBadge = document.getElementById('global-badge-expiring');
                        if (savedCountBadge) savedCountBadge.innerText = savedCount;
                        if (expiringCountBadge) expiringCountBadge.innerText = expiringCount;

                        switchGlobalVoucherTab(currentVoucherTab);
                    } else {
                        listDiv.innerHTML = '<div class="p-6 text-center text-xs text-rose-500 font-bold">Không thể tải kho ưu đãi. Vui lòng thử lại.</div>';
                    }
                })
                .catch(err => {
                    listDiv.innerHTML = '<div class="p-6 text-center text-xs text-rose-500 font-bold">Lỗi kết nối máy chủ.</div>';
                });
        }

        function renderGlobalCoupons() {
            const listDiv = document.getElementById('global-voucher-list');
            if (!listDiv) return;

            let filtered = [...globalCouponsData];
            if (currentVoucherTab === 'saved') {
                filtered = filtered.filter(c => c.is_saved);
            } else if (currentVoucherTab === 'expiring') {
                filtered = filtered.filter(c => c.is_expiring_soon);
            }

            if (filtered.length === 0) {
                let emptyMsg = 'Chưa có voucher nào trong mục này.';
                if (currentVoucherTab === 'saved') {
                    @guest
                        emptyMsg = 'Bạn chưa đăng nhập. Vui lòng đăng nhập để lưu và quản lý mã giảm giá của bạn!';
                    @else
                        emptyMsg = 'Bạn chưa lưu mã ưu đãi nào. Hãy chuyển sang tab "Tất cả" để lưu mã nhé!';
                    @endguest
                } else if (currentVoucherTab === 'expiring') {
                    emptyMsg = 'Hiện không có mã nào sắp hết hạn trong 3 ngày tới.';
                }

                listDiv.innerHTML = `
                    <div class="py-12 px-4 text-center">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-orange-50 text-orange-400 flex items-center justify-center text-2xl">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-600 max-w-sm mx-auto leading-relaxed">${emptyMsg}</p>
                        ${currentVoucherTab === 'saved' && {{ Auth::check() ? 'false' : 'true' }} ? `
                            <a href="{{ route('dangnhap') }}" class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-[#ee4d2d] text-white rounded-xl text-xs font-black shadow-xs hover:bg-red-600">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập ngay
                            </a>
                        ` : ''}
                    </div>
                `;
                return;
            }

            let html = '';
            filtered.forEach(c => {
                html += `
                    <div class="p-3.5 bg-white rounded-2xl border border-slate-200 hover:border-orange-300 transition-all flex items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-13 h-13 rounded-2xl bg-rose-50 border border-rose-200 text-[#ee4d2d] flex flex-col items-center justify-center font-black text-center shrink-0">
                                <span class="text-xs leading-none">${c.discount_type === 'percent' ? c.discount_value + '%' : (c.discount_value/1000) + 'K'}</span>
                                <span class="text-[8px] uppercase tracking-wider text-slate-500 font-bold mt-0.5">GIẢM</span>
                            </div>
                            <div class="min-w-0 space-y-0.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-black text-slate-900 text-xs tracking-wider">#${c.coupon_code}</span>
                                    <button type="button" onclick="navigator.clipboard.writeText('${c.coupon_code}'); alert('Đã sao chép mã #${c.coupon_code}');" class="text-[10px] text-slate-400 hover:text-slate-700" title="Sao chép mã">
                                        <i class="far fa-copy"></i>
                                    </button>
                                    ${c.is_saved ? '<span class="px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 text-[9px] font-black">Trong ví</span>' : ''}
                                    ${c.is_expiring_soon ? `<span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 text-[9px] font-black animate-pulse">⏰ Còn ${c.days_remaining} ngày</span>` : ''}
                                </div>
                                <p class="text-xs font-bold text-slate-700 truncate">${c.formatted_discount} - ${c.formatted_min_order}</p>
                                <p class="text-[10px] text-slate-400">HSD: ${c.end_date}</p>
                            </div>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">
                            ${c.is_saved ? `
                                <a href="{{ route('muahang') }}" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-red-600 text-white font-black text-xs rounded-xl shadow-xs inline-flex items-center gap-1">
                                    Dùng <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            ` : `
                                <button type="button" onclick="handleGlobalSaveCoupon(${c.id}, '${c.coupon_code}', this)" class="px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#ee4d2d] border border-orange-200 font-black text-xs rounded-xl transition-all cursor-pointer">
                                    + Lưu mã
                                </button>
                            `}
                        </div>
                    </div>
                `;
            });

            listDiv.innerHTML = html;
        }

        function handleGlobalSaveCoupon(couponId, couponCode, btn) {
            @guest
                alert('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập!');
                window.location.href = "{{ route('dangnhap') }}";
                return;
            @endguest

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch("{{ route('api.coupons.save') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ coupon_id: couponId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.className = "px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-300 font-bold text-xs rounded-xl";
                    btn.innerText = "Đã lưu ✓";
                    loadGlobalCoupons();
                } else {
                    btn.disabled = false;
                    btn.innerText = "+ Lưu mã";
                    alert(data.message || 'Lưu mã thất bại.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = "+ Lưu mã";
                alert('Có lỗi kết nối máy chủ.');
            });
        }

        function handleGlobalSaveAllCoupons() {
            @guest
                alert('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công. Vui lòng đăng nhập!');
                window.location.href = "{{ route('dangnhap') }}";
                return;
            @endguest

            const btn = document.getElementById('global-save-all-btn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Đang thu thập...';
            }

            fetch("{{ route('api.coupons.save_all') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(`Đã lưu thành công ${data.saved_count} mã vào ví ưu đãi của bạn!`);
                    loadGlobalCoupons();
                } else {
                    alert(data.message || 'Không thể lưu mã.');
                }
            })
            .catch(err => alert('Lỗi kết nối máy chủ.'))
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-bolt mr-1"></i> Thu thập tất cả mã';
                }
            });
        }
    </script>

    <!-- GLOBAL VÍ VOUCHER & KHO ƯU ĐÃI MODAL -->
    <div id="globalUserVoucherModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-slate-100 flex flex-col max-h-[85vh] animate-in fade-in zoom-in duration-200">
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-[#ee4d2d] to-amber-500 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shadow-inner">
                        🎟️
                    </span>
                    <div>
                        <h3 class="text-base font-black tracking-tight">Kho Ưu Đãi & Ví Voucher</h3>
                        <p class="text-[11px] text-rose-100 font-semibold">Tự động áp dụng mã tốt nhất khi thanh toán</p>
                    </div>
                </div>
                <button type="button" onclick="closeGlobalUserVoucherModal()" class="w-8 h-8 rounded-full bg-black/15 hover:bg-black/30 text-white flex items-center justify-center text-lg font-bold transition-colors">
                    &times;
                </button>
            </div>

            <!-- Tab Switcher -->
            <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center gap-1.5 shrink-0">
                <button type="button" data-tab="all" onclick="switchGlobalVoucherTab('all')" class="global-tab-btn flex-1 py-2 rounded-xl text-xs font-black transition-all bg-[#ee4d2d] text-white shadow-xs">
                    Tất cả
                </button>
                <button type="button" data-tab="saved" onclick="switchGlobalVoucherTab('saved')" class="global-tab-btn flex-1 py-2 rounded-xl text-xs font-black transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">
                    Đã lưu (<span id="global-badge-saved">0</span>)
                </button>
                <button type="button" data-tab="expiring" onclick="switchGlobalVoucherTab('expiring')" class="global-tab-btn flex-1 py-2 rounded-xl text-xs font-black transition-all bg-slate-100 text-slate-600 hover:bg-slate-200">
                    ⏰ Sắp hết (<span id="global-badge-expiring">0</span>)
                </button>
            </div>

            <!-- Notice for login -->
            <div class="px-4 py-2 bg-amber-50 border-b border-amber-100 flex items-center gap-2 text-[11px] text-amber-800 font-bold shrink-0">
                <i class="fas fa-shield-alt text-amber-600"></i>
                <span>Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công.</span>
            </div>

            <!-- Modal List Content -->
            <div id="global-voucher-list" class="p-4 overflow-y-auto space-y-2.5 flex-1 min-h-[260px] bg-slate-50/50">
                <!-- Voucher items rendered via JS -->
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-white border-t border-slate-100 flex items-center justify-between gap-3 shrink-0">
                <button type="button" id="global-save-all-btn" onclick="handleGlobalSaveAllCoupons()" class="px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl text-xs font-black shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-bolt"></i> Thu thập tất cả mã
                </button>
                <a href="{{ route('muahang') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-extrabold transition-colors">
                    Đặt món ngay →
                </a>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>

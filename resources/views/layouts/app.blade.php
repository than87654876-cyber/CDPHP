<!DOCTYPE html>
<html lang="vi" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FOODDAILY - Đặt Đồ Ăn, Giao Hàng Siêu Tốc')</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}">
    
    <!-- PWA & Mobile Web App Meta Tags -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#ee4d2d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="FOODDAILY">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">
    
    <!-- Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <script>
        window.FoodDailyConfig = {
            csrfToken: "{{ csrf_token() }}",
            isLoggedIn: {{ Auth::check() ? 'true' : 'false' }},
            routes: {
                spinDishes: "{{ route('api.spin_dishes') }}",
                dishSearch: "{{ route('api.dishes.search') }}",
                chatbotAsk: "{{ route('api.chatbot.ask') }}",
                couponsMy: "{{ route('api.coupons.my') }}",
                couponsSave: "{{ route('api.coupons.save') }}",
                couponsSaveAll: "{{ route('api.coupons.save_all') }}",
                cartAdd: "{{ route('giohang.add') }}",
                login: "{{ route('dangnhap') }}",
                buy: "{{ route('muahang') }}"
            }
        };
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $globalSettings = \Illuminate\Support\Facades\Cache::remember('site_global_settings', 3600, function() {
            return \App\Models\Setting::pluck('value', 'key')->all();
        });
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
<body class="font-sans antialiased site-bg-overlay text-slate-800 flex flex-col min-h-screen selection:bg-rose-500 selection:text-white pb-16 md:pb-0">

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
                </div>

                <!-- Center: ShopeeFood Navigation Tabs -->
                <nav class="hidden lg:flex items-center gap-6 text-xs font-bold text-slate-600 h-full">
                    <a href="{{ route('trangchu') }}" class="h-full flex items-center {{ request()->routeIs('trangchu') ? 'border-b-2 border-[#ee4d2d] text-[#ee4d2d] font-extrabold' : 'hover:text-[#ee4d2d] transition-colors' }} px-1">
                        Đồ ăn
                    </a>
                    <a href="{{ route('tracuu') }}" class="h-full flex items-center {{ request()->routeIs('tracuu') ? 'border-b-2 border-[#ee4d2d] text-[#ee4d2d] font-extrabold' : 'hover:text-[#ee4d2d] transition-colors' }} px-1">
                        Tra cứu đơn
                    </a>
                    <button type="button" onclick="openGlobalUserVoucherModal('all')" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1 cursor-pointer">
                        <i class="fas fa-ticket-alt text-[#ee4d2d] mr-1.5 text-xs"></i>
                        <span>Kho Ưu Đãi</span>
                        <span class="ml-1.5 px-1.5 py-0.2 bg-gradient-to-r from-red-500 to-amber-500 text-[9px] text-white font-black rounded-full animate-pulse">HOT</span>
                    </button>
                    <button type="button" onclick="openSpinWheelModal()" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1 cursor-pointer font-bold text-slate-600" title="Quay chọn món ngẫu nhiên">
                        <span class="mr-1 text-sm">🎡</span>
                        <span>Hôm nay ăn gì?</span>
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

                        <!-- User Profile Dropdown (Hỗ trợ cả chạm/click trên mobile và hover trên desktop) -->
                        <div class="relative group" id="user-profile-container">
                            <button type="button" id="user-profile-toggle-btn" onclick="toggleUserProfileDropdown(event)" class="flex items-center gap-2 p-1 pl-2 pr-2.5 bg-slate-100 hover:bg-slate-200/80 rounded-full border border-slate-200 transition-all cursor-pointer">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-6 h-6 rounded-full object-cover border border-rose-200">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center font-bold text-[11px]">
                                        {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-xs font-bold text-slate-800 hidden sm:inline">{{ Auth::user()->fullname ?? Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div id="user-profile-menu" class="hidden md:group-hover:block absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 transition-all duration-200 z-50 overflow-hidden">
                                <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-100 bg-slate-50/70">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-rose-200 shrink-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-extrabold text-slate-900 truncate">{{ Auth::user()->fullname ?? Auth::user()->name }}</p>
                                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded text-[9px] font-black {{ Auth::user()->membership_details['bg'] ?? 'bg-amber-100 text-amber-800' }}">
                                                <i class="fas {{ Auth::user()->membership_details['icon'] ?? 'fa-shield-halved' }} text-[8px]"></i> {{ Auth::user()->membership_details['badge'] ?? 'Thành viên' }}
                                            </span>
                                            <span class="text-[10px] font-black text-[#ee4d2d]">{{ number_format(Auth::user()->points ?? 0) }} xu</span>
                                        </div>
                                    </div>
                                </div>

                                @if(in_array(Auth::user()->role, ['superadmin', 'admin', 'staff', 'kitchen', 'shipper']) || (method_exists(Auth::user(), 'hasAnyRole') && Auth::user()->hasAnyRole(['superadmin', 'admin', 'staff', 'kitchen', 'shipper'])))
                                    <a href="{{ in_array(Auth::user()->role, ['superadmin', 'admin']) ? route('quanly') : (Auth::user()->role === 'kitchen' ? route('quanly_bep') : route('quanly_banlamviec')) }}" class="flex items-center gap-2 px-4 py-2 text-xs font-extrabold text-[#ee4d2d] bg-rose-50/60 hover:bg-rose-50 border-b border-rose-100">
                                        <i class="fas fa-shield-halved text-[#ee4d2d]"></i>Vào trang Quản trị (Admin)
                                    </a>
                                @endif

                                <!-- HỒ SƠ TÀI KHOẢN -->
                                <a href="{{ route('hoso') }}" class="flex items-center justify-between px-4 py-2.5 text-xs font-bold text-slate-800 hover:bg-rose-50 hover:text-[#ee4d2d] transition-colors border-b border-slate-100">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-lg bg-rose-100 text-[#ee4d2d] flex items-center justify-center text-xs">
                                            <i class="fas fa-id-card"></i>
                                        </span>
                                        <span>Hồ sơ tài khoản</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
                                </a>

                                <!-- Nhóm các mục con của Hồ sơ -->
                                <div class="py-1 px-2 space-y-0.5 text-[11px] font-semibold text-slate-600 bg-slate-50/50 rounded-xl mx-2 my-1 border border-slate-100">
                                    <a href="{{ route('hoso', ['tab' => 'info']) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white hover:text-[#ee4d2d] transition-colors">
                                        <i class="fas fa-user text-slate-400 text-[10px] w-4"></i>Thông tin cá nhân
                                    </a>
                                    <a href="{{ route('hoso', ['tab' => 'address']) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white hover:text-[#ee4d2d] transition-colors">
                                        <i class="fas fa-map-location-dot text-slate-400 text-[10px] w-4"></i>Địa chỉ giao hàng
                                    </a>
                                    <a href="{{ route('hoso', ['tab' => 'account']) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white hover:text-[#ee4d2d] transition-colors">
                                        <i class="fas fa-key text-slate-400 text-[10px] w-4"></i>Tài khoản & Bảo mật
                                    </a>
                                    <a href="{{ route('hoso', ['tab' => 'member']) }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-white hover:text-[#ee4d2d] transition-colors">
                                        <i class="fas fa-gem text-amber-500 text-[10px] w-4"></i>Thành viên & Voucher
                                    </a>
                                </div>

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

    <!-- ===== VÒNG QUAY HÔM NAY MÌNH ĂN GÌ – FLOATING BUTTON (Chỉ hiện trên máy tính, mobile đã có tab ở đáy) ===== -->
    <button type="button" id="spin-wheel-fab"
            onclick="openSpinWheelModal()"
            class="hidden md:flex fixed bottom-6 left-6 z-50 items-center gap-2 px-4 py-3 rounded-full font-black text-xs text-white shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 select-none cursor-pointer border border-amber-300/40"
            style="background: linear-gradient(135deg, #f59e0b 0%, #ee4d2d 100%); box-shadow: 0 8px 24px rgba(238, 77, 45, 0.35);"
            title="Quay ngẫu nhiên chọn món ngon">
        <span class="text-base">🎡</span>
        <span>Hôm nay mình ăn gì?</span>
        <span class="px-1.5 py-0.5 rounded-full bg-white/25 text-[10px] font-black uppercase tracking-wider">Quay</span>
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



    <!-- Floating AI Chatbot Widget (Tránh đè thanh điều hướng đáy và thanh đặt món trên Mobile) -->
    <button type="button" id="chatbot-toggle-btn" onclick="toggleChatbot()" 
            class="fixed bottom-32 right-3 md:bottom-6 md:right-6 z-50 w-11 h-11 md:w-12 md:h-12 rounded-full bg-[#ee4d2d] hover:bg-red-600 text-white shadow-xl shadow-rose-500/40 flex items-center justify-center text-lg md:text-xl hover:scale-110 active:scale-95 transition-all duration-200 cursor-pointer border-2 border-white" 
            title="Trợ lý AI FOODDAILY">
        <span class="relative flex items-center justify-center">
            <i class="fas fa-robot text-base md:text-lg"></i>
            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-[#ee4d2d] animate-pulse"></span>
        </span>
    </button>

    <!-- Chatbot Modal Window -->
    <div id="chatbot-modal" class="hidden fixed bottom-36 right-3 left-3 sm:left-auto sm:right-6 md:bottom-20 z-50 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden transition-all duration-300">
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
                        <li><a href="#" class="hover:text-[#ee4d2d]">Chính sách hỗ trợ khách hàng</a></li>
                        <li><a href="#" class="hover:text-[#ee4d2d]">Quy chế hoạt động</a></li>
                        <li><a href="#" class="hover:text-[#ee4d2d]">Giải quyết khiếu nại</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 uppercase text-[11px] mb-3">Tải ứng dụng FOODDAILY</h4>
                    <div class="space-y-2">
                        <div onclick="triggerPWAInstall()" class="flex items-center gap-2 p-2 bg-slate-900 text-white rounded-lg w-36 cursor-pointer hover:bg-slate-800 transition-colors" title="Cài đặt trên iOS (PWA)">
                            <i class="fab fa-apple text-xl"></i>
                            <div class="leading-none"><span class="text-[9px] block">Cài đặt App</span><span class="font-bold text-xs">iOS App</span></div>
                        </div>
                        <div onclick="triggerPWAInstall()" class="flex items-center gap-2 p-2 bg-slate-900 text-white rounded-lg w-36 cursor-pointer hover:bg-slate-800 transition-colors" title="Cài đặt trên Android (PWA)">
                            <i class="fab fa-google-play text-lg"></i>
                            <div class="leading-none"><span class="text-[9px] block">Cài đặt App</span><span class="font-bold text-xs">Android</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 text-center text-slate-400 text-[11px]">
                © 2026 FOODDAILY Company. Tất cả quyền được bảo lưu.
            </div>
        </div>
    </footer>



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

    <!-- MOBILE FLOATING QUICK CART BAR (Chuẩn ShopeeFood / GrabFood) -->
    <div id="mobile-floating-cart-bar" class="hidden md:hidden fixed bottom-16 left-3 right-3 z-45 animate-in slide-in-from-bottom duration-200">
        <a href="{{ route('muahang') }}" class="flex items-center justify-between p-3 rounded-2xl bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-2xl border border-slate-700/60 active:scale-98 transition-all">
            <div class="flex items-center gap-2.5">
                <div class="relative w-8 h-8 rounded-xl bg-[#ee4d2d] flex items-center justify-center font-bold text-xs text-white shadow-inner">
                    <i class="fas fa-shopping-bag"></i>
                    <span id="mobile-float-cart-count" class="absolute -top-1.5 -right-1.5 bg-amber-400 text-slate-900 text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center">0</span>
                </div>
                <div>
                    <span class="text-[9px] text-slate-400 block leading-none font-bold uppercase tracking-wider">Giỏ hàng của bạn</span>
                    <span id="mobile-float-cart-total" class="text-xs font-black text-amber-300 leading-tight">0đ</span>
                </div>
            </div>
            <div class="flex items-center gap-1.5 text-xs font-black text-[#ee4d2d] bg-white px-3 py-1.5 rounded-xl shadow-xs">
                <span>Đặt món</span>
                <i class="fas fa-arrow-right text-[10px]"></i>
            </div>
        </a>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION (Chuẩn App ShopeeFood / GrabFood) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-1 py-1.5 flex items-center justify-around shadow-lg">
        <a href="{{ route('trangchu') }}" class="flex flex-col items-center justify-center py-1 px-2 {{ request()->routeIs('trangchu') ? 'text-[#ee4d2d] font-bold' : 'text-slate-500 hover:text-[#ee4d2d]' }} transition-colors">
            <i class="fas fa-utensils text-sm"></i>
            <span class="text-[10px] mt-0.5 font-semibold">Đồ ăn</span>
        </a>

        <a href="{{ route('tracuu') }}" class="flex flex-col items-center justify-center py-1 px-2 {{ request()->routeIs('tracuu') ? 'text-[#ee4d2d] font-bold' : 'text-slate-500 hover:text-[#ee4d2d]' }} transition-colors">
            <i class="fas fa-receipt text-sm"></i>
            <span class="text-[10px] mt-0.5 font-semibold">Tra cứu</span>
        </a>

        <button type="button" onclick="openGlobalUserVoucherModal('all')" class="flex flex-col items-center justify-center py-1 px-2 text-slate-500 hover:text-[#ee4d2d] transition-colors cursor-pointer">
            <i class="fas fa-ticket-alt text-sm"></i>
            <span class="text-[10px] mt-0.5 font-semibold">Ưu đãi</span>
        </button>

        <a href="{{ route('muahang') }}" class="relative flex flex-col items-center justify-center py-1 px-2 {{ request()->routeIs('muahang') || request()->routeIs('giohang') ? 'text-[#ee4d2d] font-bold' : 'text-slate-500 hover:text-[#ee4d2d]' }} transition-colors">
            <i class="fas fa-shopping-cart text-sm"></i>
            <span id="mobile-nav-cart-badge" class="absolute top-0 right-2 bg-[#ee4d2d] text-white text-[9px] font-black min-w-[15px] h-3.5 px-1 rounded-full flex items-center justify-center border border-white shadow-xs hidden">
                0
            </span>
            <span class="text-[10px] mt-0.5 font-semibold">Giỏ hàng</span>
        </a>

        @auth
            <a href="{{ route('hoso') }}" class="flex flex-col items-center justify-center py-1 px-2 {{ request()->routeIs('hoso*') ? 'text-[#ee4d2d] font-bold' : 'text-slate-500 hover:text-[#ee4d2d]' }} transition-colors">
                <i class="fas fa-user-circle text-sm"></i>
                <span class="text-[10px] mt-0.5 font-semibold">Hồ sơ</span>
            </a>
        @else
            <a href="{{ route('dangnhap') }}" class="flex flex-col items-center justify-center py-1 px-2 text-slate-500 hover:text-[#ee4d2d] transition-colors">
                <i class="fas fa-user-circle text-sm"></i>
                <span class="text-[10px] mt-0.5 font-semibold">Tài khoản</span>
            </a>
        @endauth
    </nav>

    <!-- PWA MOBILE INSTALL BANNER -->
    <div id="pwa-mobile-install-banner" class="hidden fixed bottom-16 left-3 right-3 z-40 bg-white/95 backdrop-blur-md border border-orange-200 p-3 rounded-2xl shadow-xl animate-in slide-in-from-bottom duration-300 md:hidden">
        <div class="flex items-center justify-between gap-3">
            <img src="{{ asset('icons/icon-192x192.png') }}" alt="FOODDAILY App" class="w-10 h-10 rounded-xl shadow-xs shrink-0 object-cover border border-orange-100">
            <div class="flex-1 min-w-0">
                <h4 class="font-extrabold text-xs text-slate-900 leading-tight">Cài đặt App FOODDAILY</h4>
                <p class="text-[11px] text-slate-500 truncate">Mở toàn màn hình & nhận ưu đãi giờ ăn</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" onclick="triggerPWAInstall()" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-[#d03b1d] text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                    Cài đặt
                </button>
                <button type="button" onclick="dismissPWABanner()" class="w-7 h-7 text-slate-400 hover:text-slate-600 rounded-full flex items-center justify-center text-sm cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- PWA iOS GUIDE MODAL -->
    <div id="pwa-ios-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 text-center animate-in zoom-in-95 duration-200">
            <img src="{{ asset('icons/icon-192x192.png') }}" class="w-14 h-14 rounded-2xl mx-auto mb-3 shadow-md border border-orange-100">
            <h3 class="text-base font-extrabold text-slate-900">Cài đặt FOODDAILY cho iPhone</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">
                Thêm vào màn hình chính để mở ứng dụng toàn màn hình như ứng dụng gốc:
            </p>
            <div class="bg-slate-50 rounded-2xl p-3.5 text-left text-xs text-slate-700 space-y-2.5 mb-5 border border-slate-100 font-medium">
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-orange-100 text-[#ee4d2d] font-bold flex items-center justify-center text-[11px] shrink-0">1</span>
                    <span>Nhấn biểu tượng <strong>Chia sẻ (Share) <i class="fas fa-arrow-up-from-bracket text-blue-500"></i></strong> trên thanh Safari dưới cùng.</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-orange-100 text-[#ee4d2d] font-bold flex items-center justify-center text-[11px] shrink-0">2</span>
                    <span>Cuộn xuống và chọn <strong>"Thêm vào MH chính" (Add to Home Screen) <i class="far fa-plus-square"></i></strong>.</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-orange-100 text-[#ee4d2d] font-bold flex items-center justify-center text-[11px] shrink-0">3</span>
                    <span>Nhấn <strong>Thêm (Add)</strong> ở góc trên bên phải để hoàn tất!</span>
                </div>
            </div>
            <button type="button" onclick="closeIOSInstallGuideModal()" class="w-full py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors cursor-pointer">
                Đã hiểu
            </button>
        </div>
    </div>

    <!-- PWA UNIVERSAL INSTALL MODAL (Desktop / Laptop / Mobile QR) -->
    <div id="pwa-universal-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 text-center animate-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('icons/icon-192x192.png') }}" class="w-10 h-10 rounded-xl shadow-xs border border-orange-100 object-cover" alt="FOODDAILY">
                    <div class="text-left">
                        <h3 class="text-sm font-extrabold text-slate-900 leading-tight">Cài Đặt App FOODDAILY (PWA)</h3>
                        <p class="text-[11px] text-slate-500">Mở toàn màn hình & nhận thông báo giờ ăn</p>
                    </div>
                </div>
                <button type="button" onclick="closeUniversalPWAModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold transition-colors cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-3 gap-2 my-4 text-center">
                <div class="p-2.5 bg-orange-50/60 border border-orange-100 rounded-2xl">
                    <div class="text-base mb-1">⚡</div>
                    <div class="text-[11px] font-black text-slate-800">Cực Nhanh</div>
                    <div class="text-[9px] text-slate-500">Toàn màn hình</div>
                </div>
                <div class="p-2.5 bg-rose-50/60 border border-rose-100 rounded-2xl">
                    <div class="text-base mb-1">🔔</div>
                    <div class="text-[11px] font-black text-slate-800">Nhắc Giờ Ăn</div>
                    <div class="text-[9px] text-slate-500">Báo giờ trưa/tối</div>
                </div>
                <div class="p-2.5 bg-amber-50/60 border border-amber-100 rounded-2xl">
                    <div class="text-base mb-1">💾</div>
                    <div class="text-[11px] font-black text-slate-800">Dưới 2MB</div>
                    <div class="text-[9px] text-slate-500">Không tốn bộ nhớ</div>
                </div>
            </div>

            <!-- Guides for Desktop & Phone -->
            <div class="bg-slate-50 rounded-2xl p-4 text-left text-xs text-slate-700 space-y-3 mb-5 border border-slate-100">
                <div>
                    <h5 class="font-extrabold text-[#ee4d2d] flex items-center gap-1.5 mb-1 text-xs">
                        <i class="fas fa-laptop"></i> Cách cài trên Máy tính (Chrome / Edge):
                    </h5>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Nhấn vào biểu tượng <strong>Cài đặt ứng dụng</strong> (biểu tượng máy tính nhỏ hoặc dấu <strong>[+]</strong>) ở góc phải thanh địa chỉ URL của trình duyệt, hoặc chọn menu <strong>[⋮] → "Cài đặt FOODDAILY..."</strong>.
                    </p>
                </div>

                <div class="pt-2 border-t border-slate-200">
                    <h5 class="font-extrabold text-[#ee4d2d] flex items-center gap-1.5 mb-1 text-xs">
                        <i class="fas fa-mobile-screen"></i> Trải nghiệm trên Điện thoại di động:
                    </h5>
                    <p class="text-[11px] text-slate-600 leading-relaxed mb-2">
                        Quét mã QR bằng Camera điện thoại hoặc truy cập trên trình duyệt di động:
                    </p>
                    <div class="flex items-center gap-3 p-2 bg-white rounded-xl border border-slate-200">
                        <img src="{{ asset('qr_code.png') }}" class="w-14 h-14 rounded-lg object-contain shrink-0 border border-slate-100" alt="QR App">
                        <div class="text-[11px] text-slate-600">
                            <span class="font-bold text-slate-800 block">Quét mã mở trên điện thoại</span>
                            Trình duyệt sẽ tự động hiện thanh: <em>"Cài đặt App FOODDAILY"</em>!
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="closeUniversalPWAModal()" class="w-full py-2.5 bg-gradient-to-r from-[#ee4d2d] to-orange-500 text-white rounded-xl text-xs font-black shadow-xs hover:opacity-95 transition-all cursor-pointer">
                Đã hiểu
            </button>
        </div>
    </div>

    <!-- PWA MEAL NOTIFICATION PROMPT BANNER -->
    <div id="pwa-notification-prompt-banner" class="hidden fixed top-20 right-4 z-40 max-w-sm w-full bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border border-orange-200 animate-in slide-in-from-top-4 duration-300">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-[#ee4d2d] flex items-center justify-center text-lg shrink-0">
                🔔
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-extrabold text-xs text-slate-900 leading-tight">Bật thông báo giờ ăn?</h4>
                <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                    Nhận thông báo nhắc bữa trưa (11:00) & tối (18:00) kèm mã giảm giá đặc quyền mỗi ngày!
                </p>
                <div class="flex items-center gap-2 mt-3">
                    <button type="button" onclick="requestMealNotificationPermission()" class="px-3 py-1.5 bg-[#ee4d2d] hover:bg-[#d03b1d] text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer">
                        Bật thông báo
                    </button>
                    <button type="button" onclick="dismissNotificationPrompt()" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                        Để sau
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleUserProfileDropdown(event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }
            const menu = document.getElementById('user-profile-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function(event) {
            const container = document.getElementById('user-profile-container');
            const menu = document.getElementById('user-profile-menu');
            if (menu && container && !container.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>

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

    <!-- ===== VÒNG QUAY HÔM NAY MÌNH ĂN GÌ – FLOATING BUTTON (ĐẶT GÓC DƯỚI BÊN TRÁI ĐỂ TRÁNH ĐÈ LÊN MÃ KHUYẾN MÃI) ===== -->
    <button type="button" id="spin-wheel-fab"
            onclick="openSpinWheelModal()"
            class="fixed bottom-6 left-6 z-50 flex items-center gap-2 px-4 py-3 rounded-full font-black text-xs text-white shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 select-none cursor-pointer border border-amber-300/40"
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



    <!-- Floating AI Chatbot Widget (Chỉ hiện icon tròn gọn gàng) -->
    <button type="button" onclick="toggleChatbot()" 
            class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-[#ee4d2d] hover:bg-red-600 text-white shadow-xl shadow-rose-500/40 flex items-center justify-center text-xl hover:scale-110 active:scale-95 transition-all cursor-pointer border-2 border-white" 
            title="Trợ lý AI FOODDAILY">
        <span class="relative flex items-center justify-center">
            <i class="fas fa-robot text-lg"></i>
            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-[#ee4d2d] animate-pulse"></span>
        </span>
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
                        <li><a href="#" class="hover:text-[#ee4d2d]">Chính sách hỗ trợ khách hàng</a></li>
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

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
    @yield('styles')
</head>
<body class="font-sans antialiased bg-[#f5f5f5] text-slate-800 flex flex-col min-h-screen selection:bg-rose-500 selection:text-white">

    <!-- ShopeeFood Style Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                @php
                    $globalSettings = \App\Models\Setting::pluck('value', 'key')->all();
                @endphp
                <!-- Left: Brand Logo & Location -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('trangchu') }}" class="flex items-center gap-2 group">
                        @if(isset($globalSettings['logo_url']) && !empty($globalSettings['logo_url']))
                            <img src="{{ \Illuminate\Support\Str::startsWith($globalSettings['logo_url'], 'http') ? $globalSettings['logo_url'] : asset($globalSettings['logo_url']) }}" alt="FOODDAILY" class="w-9 h-9 rounded-xl object-cover shadow-xs group-hover:scale-105 transition-transform border border-slate-200">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-[#ee4d2d] flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform">
                                <i class="fas fa-utensils text-base"></i>
                            </div>
                        @endif
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
                    <a href="{{ route('trangchu') }}" class="h-full flex items-center border-b-2 border-[#ee4d2d] text-[#ee4d2d] px-1 font-extrabold">
                        Đồ ăn
                    </a>
                    <a href="{{ route('goidichvu') }}" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1">
                        Gói Combo
                    </a>
                    <a href="{{ route('tracuu') }}" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1">
                        Tra cứu đơn
                    </a>
                    <a href="{{ route('yeucauhoan') }}" class="h-full flex items-center hover:text-[#ee4d2d] transition-colors px-1">
                        Đổi/Hoàn tiền
                    </a>
                </nav>

                <!-- Right: Search, Auth & Language -->
                <div class="flex items-center gap-4">
                    <!-- Search Icon Trigger -->
                    <a href="#menu" class="p-2 text-slate-500 hover:text-[#ee4d2d] text-base transition-colors">
                        <i class="fas fa-magnifying-glass"></i>
                    </a>

                    @auth
                        <!-- Cart Icon -->
                        <a href="{{ route('giohang') }}" class="relative p-2 text-slate-600 hover:text-[#ee4d2d] transition-colors">
                            <i class="fas fa-shopping-cart text-lg"></i>
                            @php $cartCount = session('cart') ? count(session('cart')) : 0; @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 bg-[#ee4d2d] text-white text-[10px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 p-1 pl-2 pr-2.5 bg-slate-100 hover:bg-slate-200/80 rounded-full border border-slate-200 transition-all">
                                <div class="w-6 h-6 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center font-bold text-[11px]">
                                    {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-800 hidden sm:inline">{{ Auth::user()->fullname ?? Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->fullname ?? Auth::user()->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if(in_array(Auth::user()->role, ['admin', 'staff']))
                                    <a href="{{ route('quanly') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-[#ee4d2d] hover:bg-rose-50">
                                        <i class="fas fa-chart-line"></i>Admin Portal
                                    </a>
                                @endif
                                <a href="{{ route('giohang') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    <i class="fas fa-receipt text-slate-400"></i>Đơn hàng của tôi
                                </a>
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

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- ShopeeFood Footer -->
    <footer class="bg-white border-t border-slate-200 pt-12 pb-8 mt-16 text-xs text-slate-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 pb-8 border-b border-slate-100">
                <div class="space-y-3">
                    <a href="{{ route('trangchu') }}" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-[#ee4d2d] flex items-center justify-center text-white font-bold">
                            <i class="fas fa-utensils text-xs"></i>
                        </div>
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
                        <li><a href="{{ route('goidichvu') }}" class="hover:text-[#ee4d2d]">Gói dịch vụ Combo</a></li>
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

    @yield('scripts')
</body>
</html>

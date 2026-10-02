<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-900">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'FOODDAILY - Admin Portal')</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                        }
                    }
                }
            }
        }
    </script>
    <style>

        /* Sleek Dark Scrollbar for Admin Sidebar */
        aside nav::-webkit-scrollbar {
            width: 5px;
        }
        aside nav::-webkit-scrollbar-track {
            background: #0f172a;
        }
        aside nav::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        aside nav::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
        aside nav {
            scrollbar-width: thin;
            scrollbar-color: #334155 #0f172a;
        }
    </style>
    @yield('styles')
</head>


<body id="page-top" class="font-sans antialiased text-slate-800 bg-[#f8fafc] min-h-full selection:bg-[#ee4d2d] selection:text-white">
    <div id="wrapper" class="flex min-h-screen">

        <!-- REALTIME COUNTS FOR SIDEBAR BADGES -->
        @php
            $pendingOrdersCount = \App\Models\Order::where('order_status', 'preparing')->count();
            $deliveringOrdersCount = \App\Models\Order::where('order_status', 'delivering')->count();
            $kitchenCount = \App\Models\Order::whereIn('order_status', ['confirmed', 'preparing'])->count();
            $lowStockInventoryCount = \App\Models\InventoryItem::where('current_stock', '<=', \Illuminate\Support\Facades\DB::raw('min_stock'))->count();
        @endphp

        <!-- Ultra-Modern Dark Sidebar Navigation -->
        <aside class="w-64 bg-[#0f172a] text-slate-300 flex-shrink-0 flex flex-col justify-between z-30 border-r border-slate-800/80 shadow-2xl">
            <div>
                <!-- Brand Header -->
                <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80">
                    <a class="flex items-center gap-3 group" href="{{ route('quanly') }}">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#ee4d2d] to-[#ff6b4a] flex items-center justify-center text-white shadow-lg shadow-rose-500/30 group-hover:scale-105 transition-all">
                            <i class="fas fa-utensils text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg text-white tracking-wider">FOODDAILY</span>
                            <span class="text-[10px] font-extrabold text-[#ee4d2d] uppercase tracking-widest -mt-1">Admin Portal</span>
                        </div>
                    </a>
                </div>

                <!-- Quick Link to Storefront -->
                <div class="px-4 pt-3 pb-1">
                    <a href="{{ route('trangchu') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-amber-400 hover:text-white text-xs font-bold transition-all border border-slate-700/80 shadow-xs group">
                        <span class="flex items-center gap-2.5"><i class="fas fa-store text-amber-400 group-hover:scale-110 transition-transform"></i>Xem Trang Chủ</span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-white"></i>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="p-4 space-y-1.5 overflow-y-auto max-h-[calc(100vh-140px)]">
                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ route('quanly') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all {{ Route::is('quanly') ? 'bg-[#ee4d2d] text-white shadow-lg shadow-rose-500/30' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chart-pie w-5 text-center text-sm"></i>
                            <span>Báo cáo doanh thu</span>
                        </div>
                    </a>
                    @endif

                    <a href="{{ route('quanly_banlamviec') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl text-xs font-bold transition-all {{ Route::is('quanly_banlamviec') ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'text-slate-400 hover:bg-slate-800/80 hover:text-amber-400' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-desktop w-5 text-center text-sm"></i>
                            <span>Bàn làm việc Nhân viên</span>
                        </div>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Dịch vụ & Thực đơn</p>
                    </div>

                    @if(Auth::user()->role === 'superadmin')
                    <a href="{{ route('quanly_cauhinh') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_cauhinh') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-sliders w-5 text-center"></i>
                            <span>Cấu hình trang chủ</span>
                        </div>
                    </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ route('quanly_danhmuc') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_danhmuc') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-list w-5 text-center"></i>
                            <span>Danh mục món ăn</span>
                        </div>
                    </a>

                    <a href="{{ route('quanly_khuyenmai') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_khuyenmai') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-ticket w-5 text-center"></i>
                            <span>Mã khuyến mãi</span>
                        </div>
                    </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Đơn hàng & Khách hàng</p>
                    </div>

                    <a href="{{ route('quanly_donhang') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_donhang') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                            <span>Danh sách đơn hàng</span>
                        </div>
                        @if($pendingOrdersCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-[#ee4d2d] text-white text-[10px] font-black shadow-xs">
                                {{ $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('quanly_reviews') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_reviews') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-star w-5 text-center"></i>
                            <span>Đánh giá khách hàng</span>
                        </div>
                    </a>
                    @endif

                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Nghiệp vụ Vận hành</p>
                    </div>

                    <a href="{{ route('quanly_bep') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_bep') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-fire-burner w-5 text-center text-amber-400"></i>
                            <span>Màn hình Bếp nấu</span>
                        </div>
                        @if($kitchenCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black">
                                {{ $kitchenCount }}
                            </span>
                        @endif
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Kho Vận & Nhập Hàng</p>
                    </div>

                    <a href="{{ route('quanly_kho') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_kho*') || Route::is('kho_*') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-boxes-stacked w-5 text-center text-blue-400"></i>
                            <span>Kho & Hàng tồn thực tế</span>
                        </div>
                        @if($lowStockInventoryCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black animate-pulse" title="Mặt hàng cần nhập thêm">
                                {{ $lowStockInventoryCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('quanly_nhacungcap') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_nhacungcap*') || Route::is('nhacungcap_*') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-truck-field w-5 text-center text-amber-400"></i>
                            <span>Nguồn hàng & Nhà cung cấp</span>
                        </div>
                    </a>

                    <a href="{{ route('quanly_nhaphang') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_nhaphang*') || Route::is('nhaphang_*') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar w-5 text-center text-emerald-400"></i>
                            <span>Phiếu Nhập Hàng (Nhập kho)</span>
                        </div>
                    </a>

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <div class="pt-4 pb-1">
                        <p class="px-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-widest">Tài khoản & Hệ thống</p>
                    </div>

                    <a href="{{ route('quanly_khachhang') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_khachhang') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users w-5 text-center"></i>
                            <span>Quản lý Khách hàng</span>
                        </div>
                    </a>
                    @endif

                    @if(Auth::user()->role === 'superadmin' || (method_exists(Auth::user(), 'can') && Auth::user()->can('employees.manage')))
                    <a href="{{ route('quanly_nhanvien') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_nhanvien*') || Route::is('nhanvien_*') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-id-badge w-5 text-center text-blue-400"></i>
                            <span>Tài khoản Nhân viên</span>
                        </div>
                    </a>
                    @endif

                    @if(Auth::user()->role === 'superadmin' || (method_exists(Auth::user(), 'can') && Auth::user()->can('roles.manage')))
                    <a href="{{ route('quanly_vaitro') }}" class="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-semibold transition-all {{ Route::is('quanly_vaitro*') || Route::is('vaitro_*') ? 'bg-slate-800 text-white font-bold' : 'text-slate-400 hover:bg-slate-800/80 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-crown w-5 text-center text-amber-400"></i>
                            <span>Vai trò & Phân quyền</span>
                        </div>
                        <span class="px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 text-[9px] font-black uppercase">RBAC</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Footer Sidebar Account Quick Action -->
            <div class="p-4 border-t border-slate-800">
                <a href="{{ route('trangchu') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 transition-colors">
                    <i class="fas fa-globe text-[#ee4d2d]"></i>
                    <span>Xem Trang Web</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-grow flex flex-col min-w-0 bg-[#f8fafc]">

            <!-- Topbar Header -->
            <header class="h-20 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between shadow-xs sticky top-0 z-20">
                <div class="flex items-center gap-6">
                    <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">
                        @yield('title', 'Bảng điều khiển Quản trị')
                    </h1>

                    <span class="hidden md:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Hệ thống Vận hành Tốt
                    </span>
                </div>

                <!-- User Profile & Quick Actions -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('trangchu') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold shadow-2xs hover:shadow-xs transition-all" title="Về trang chủ khách hàng">
                        <i class="fas fa-store text-[#ee4d2d]"></i>
                        <span class="hidden sm:inline">Xem Trang Chủ</span>
                    </a>

                    <div class="relative group">
                        <button class="flex items-center gap-3 p-1.5 pr-3 rounded-full hover:bg-slate-100 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center font-black text-sm shadow-md shadow-rose-500/20">
                                {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-xs font-bold text-slate-900">{{ Auth::user()->fullname ?? Auth::user()->name }}</p>
                                @php
                                    $userRoleName = Auth::user()->role ?? '';
                                    $roleLabelsMap = [
                                        'superadmin' => '👑 Quản trị viên tối cao',
                                        'admin' => '⭐ Quản trị viên',
                                        'staff' => '🎧 CSKH / Thu ngân',
                                        'kitchen' => '🍳 Nhân viên Bếp',
                                        'shipper' => '🛵 Nhân viên Giao hàng',
                                    ];
                                @endphp
                                <p class="text-[10px] font-extrabold uppercase tracking-wider {{ $userRoleName === 'superadmin' ? 'text-purple-600' : ($userRoleName === 'admin' ? 'text-[#ee4d2d]' : ($userRoleName === 'kitchen' ? 'text-amber-600' : ($userRoleName === 'shipper' ? 'text-emerald-600' : 'text-blue-600'))) }}">
                                    {{ $roleLabelsMap[$userRoleName] ?? ('⚙️ ' . ucfirst($userRoleName)) }}
                                </p>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('trangchu') }}">
                                <i class="fas fa-globe mr-2.5 text-[#ee4d2d] text-sm"></i>Xem trang web
                            </a>
                            <hr class="my-1 border-slate-100">
                            <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50" href="{{ route('dangxuat') }}">
                                <i class="fas fa-right-from-bracket mr-2.5 text-sm"></i>Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content Body -->
            <main class="p-8 flex-grow">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-xs text-slate-500 font-medium">
                <span>© 2026 <strong>FOODDAILY Admin System</strong>. All rights reserved.</span>
            </footer>

        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="{{ asset('admin/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @yield('scripts')

    <!-- Realtime Polling Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let lastCheckedTime = null;

            fetch("{{ route('api.orders.poll') }}")
                .then(response => response.json())
                .then(data => {
                    lastCheckedTime = data.timestamp;
                    setInterval(pollUpdates, 3000);
                })
                .catch(err => console.error('Error initializing admin polling:', err));

            function pollUpdates() {
                if (!lastCheckedTime) return;

                fetch(`{{ route('api.orders.poll') }}?since=${encodeURIComponent(lastCheckedTime)}`)
                    .then(response => response.json())
                    .then(data => {
                        lastCheckedTime = data.timestamp;
                        if (data.updates && data.updates.length > 0) {
                            data.updates.forEach(e => {
                                const path = window.location.pathname;
                                const isManagerPage = path.includes('quanly') || path.includes('donhang') || path.includes('goidangky') || path.includes('bep') || path.includes('kho');
                                
                                if (e.action === 'created') {
                                    alert(`🔔 [Realtime Notification] Có đơn hàng mới #${e.order.id}! Vui lòng kiểm tra!`);
                                    if (isManagerPage) window.location.reload();
                                } else if (e.action === 'reviewed') {
                                    alert(`🔔 [Realtime Notification] Đơn hàng #FDL-${e.order.id} vừa được đánh giá!`);
                                    if (isManagerPage) window.location.reload();
                                } else if (isManagerPage) {
                                    window.location.reload();
                                }
                            });
                        }
                    })
                    .catch(err => console.error('Error during admin polling:', err));
            }
        });

        // Ngăn chặn hiển thị trang từ bộ nhớ đệm (Back-Forward Cache) khi đã đăng xuất
        window.addEventListener('pageshow', function (event) {
            if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-900">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FOODDAILY - Admin Portal')</title>

    <link rel="icon" href="{{ asset('logo.jpg') }}">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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


        <!-- Mobile Sidebar Overlay Backdrop -->
        <div id="admin-sidebar-overlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-35 hidden lg:hidden transition-opacity"></div>

        <!-- Ultra-Modern Dark Sidebar Navigation (Cố định trên PC, Drawer trượt trên Mobile) -->
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#0f172a] text-slate-300 flex flex-col justify-between z-40 border-r border-slate-800/80 shadow-2xl h-screen transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
            <div class="flex flex-col h-full min-h-0">
                <!-- Brand Header -->
                <div class="h-16 lg:h-20 shrink-0 flex items-center justify-between px-6 border-b border-slate-800/80">
                    <a class="flex items-center gap-3 group" href="{{ route('quanly') }}">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#ee4d2d] to-[#ff6b4a] flex items-center justify-center text-white shadow-lg shadow-rose-500/30 group-hover:scale-105 transition-all">
                            <i class="fas fa-utensils text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg text-white tracking-wider">FOODDAILY</span>
                            <span class="text-[10px] font-extrabold text-[#ee4d2d] uppercase tracking-widest -mt-1">Admin Portal</span>
                        </div>
                    </a>
                    <!-- Mobile Close Button -->
                    <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm transition-colors cursor-pointer" title="Đóng menu">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Quick Link to Storefront -->
                <div class="shrink-0 px-4 pt-3 pb-1">
                    <a href="{{ route('trangchu') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-amber-400 hover:text-white text-xs font-bold transition-all border border-slate-700/80 shadow-xs group">
                        <span class="flex items-center gap-2.5"><i class="fas fa-store text-amber-400 group-hover:scale-110 transition-transform"></i>Xem Trang Chủ</span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-slate-400 group-hover:text-white"></i>
                    </a>
                </div>

                <!-- Navigation Links (Cuộn độc lập mượt mà) -->
                <nav class="flex-1 min-h-0 p-4 space-y-1.5 overflow-y-auto">
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
            <div class="shrink-0 p-4 border-t border-slate-800">
                <a href="{{ route('trangchu') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 transition-colors">
                    <i class="fas fa-globe text-[#ee4d2d]"></i>
                    <span>Xem Trang Web</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area (ml-0 trên Mobile, ml-64 trên Desktop) -->
        <div class="flex-grow flex flex-col min-w-0 bg-[#f8fafc] ml-0 lg:ml-64 min-h-screen transition-all">

            <!-- Topbar Header -->
            <header class="h-16 lg:h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shadow-xs sticky top-0 z-20">
                <div class="flex items-center gap-2.5 sm:gap-4 lg:gap-6 min-w-0">
                    <!-- Hamburger Toggle Button for Mobile -->
                    <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-sm shadow-2xs transition-colors shrink-0 cursor-pointer" title="Mở menu quản trị">
                        <i class="fas fa-bars"></i>
                    </button>

                    <h1 class="text-sm sm:text-base lg:text-lg font-extrabold text-slate-900 tracking-tight truncate">
                        @yield('title', 'Bảng điều khiển Quản trị')
                    </h1>

                    <span class="hidden xl:inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-bold shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Hệ thống Vận hành Tốt
                    </span>
                </div>

                <!-- User Profile & Quick Actions -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <!-- Nút Bắn Thông Báo Giờ Ăn (Demo Web Push Notification) -->
                    <button type="button" onclick="openAdminPushDemoModal()" class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3.5 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl bg-gradient-to-r from-orange-500 to-[#ee4d2d] text-white text-xs font-black shadow-xs hover:shadow-md hover:scale-102 transition-all cursor-pointer" title="Gửi thông báo đẩy (Web Push) theo khung giờ cho khách hàng">
                        <i class="fas fa-bullhorn text-xs"></i>
                        <span class="hidden md:inline">Bắn Noti Giờ Ăn</span>
                        <span class="md:hidden text-[11px]">Bắn Noti</span>
                    </button>

                    <a href="{{ route('trangchu') }}" class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl sm:rounded-2xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-bold shadow-2xs hover:shadow-xs transition-all" title="Về trang chủ khách hàng">
                        <i class="fas fa-store text-[#ee4d2d]"></i>
                        <span class="hidden md:inline">Xem Trang Chủ</span>
                    </a>

                    <!-- Notification Bell Dropdown -->
                    @include('layouts.notification_bell')

                    <!-- User Profile Dropdown -->
                    <div class="relative group" id="admin-user-container">
                        <button type="button" onclick="toggleAdminUserMenu(event)" class="flex items-center gap-2 sm:gap-3 p-1 sm:p-1.5 sm:pr-3 rounded-full hover:bg-slate-100 transition-colors cursor-pointer">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#ee4d2d] text-white flex items-center justify-center font-black text-xs sm:text-sm shadow-md shadow-rose-500/20">
                                {{ strtoupper(substr(Auth::user()->fullname ?? Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="text-left hidden md:block">
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
                            <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                        </button>
                        <div id="admin-user-menu" class="hidden md:group-hover:block absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 transition-all duration-200 z-50">
                            <div class="px-4 py-2 border-b border-slate-100 md:hidden">
                                <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->fullname ?? Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('trangchu') }}">
                                <i class="fas fa-globe mr-2.5 text-[#ee4d2d] text-sm"></i>Xem trang web
                            </a>
                            <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50" href="{{ route('hoso') }}">
                                <i class="fas fa-id-card mr-2.5 text-[#ee4d2d] text-sm"></i>Hồ sơ cá nhân
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
            <main class="p-4 sm:p-6 lg:p-8 flex-grow overflow-x-hidden">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200 py-3 sm:py-4 px-4 sm:px-8 text-center text-xs text-slate-500 font-medium">
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
                    setInterval(function() {
                        if (!document.hidden) {
                            pollUpdates();
                        }
                    }, 6000);
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
                                const isManagerPage = path.includes('quanly') || path.includes('donhang') || path.includes('bep') || path.includes('kho');
                                
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

        // Xử lý Modal Bắn Thông Báo Đẩy (Web Push)
        function openAdminPushDemoModal() {
            const modal = document.getElementById('admin-push-demo-modal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeAdminPushDemoModal() {
            const modal = document.getElementById('admin-push-demo-modal');
            if (modal) modal.classList.add('hidden');
        }

        function executeAdminPush(type) {
            const feedback = document.getElementById('admin-push-feedback');
            if (feedback) {
                feedback.className = 'p-3 rounded-xl text-xs font-bold text-center mb-3 bg-amber-50 text-amber-700 border border-amber-200 block';
                feedback.innerText = '⏳ Đang phát sóng thông báo tới toàn bộ hệ thống...';
            }

            fetch('{{ route("api.notifications.trigger_demo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ type: type })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (feedback) {
                        feedback.className = 'p-3 rounded-xl text-xs font-bold text-center mb-3 bg-emerald-50 text-emerald-700 border border-emerald-200 block';
                        feedback.innerHTML = `✅ Bắn thông báo thành công: <strong>${data.data.title}</strong>! Thiết bị khách hàng sẽ nhận ngay bây giờ.`;
                    }
                    // Nếu admin đã cấp quyền thông báo, hiển thị thử luôn trên máy admin
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification(data.data.title, {
                            body: data.data.body,
                            icon: '/icons/icon-192x192.png'
                        });
                    }
                } else {
                    throw new Error(data.message || 'Lỗi gửi');
                }
            })
            .catch(err => {
                if (feedback) {
                    feedback.className = 'p-3 rounded-xl text-xs font-bold text-center mb-3 bg-rose-50 text-rose-700 border border-rose-200 block';
                    feedback.innerText = '❌ Gửi thất bại: ' + err.message;
                }
            });
        }
    </script>

    <!-- MODAL BẮN THÔNG BÁO ĐẨY WEB PUSH (DEMO CHO BÁO CÁO & ĐỒ ÁN) -->
    <div id="admin-push-demo-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 text-left animate-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-orange-100 text-[#ee4d2d] flex items-center justify-center text-lg">
                        📢
                    </span>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Trung Tâm Phát Sóng Thông Báo (Web Push)</h3>
                        <p class="text-[11px] text-slate-500 font-medium">Bắn thông báo theo khung giờ đến máy khách hàng & điện thoại PWA</p>
                    </div>
                </div>
                <button type="button" onclick="closeAdminPushDemoModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center text-sm font-bold cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Scheduled Info Card -->
            <div class="my-4 p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-600 flex items-center justify-between">
                <div>
                    <span class="font-bold text-slate-800 block text-xs">⏰ Kịch bản Cronjob tự động:</span>
                    <span class="text-[11px] text-slate-500">Đã cài đặt chạy tự động vào đúng <strong>11:00 Trưa</strong> và <strong>18:00 Tối</strong> mỗi ngày.</span>
                </div>
                <span class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase">Đang kích hoạt</span>
            </div>

            <p class="text-xs font-bold text-slate-700 mb-2.5">Chọn kịch bản để bắn thử nghiệm (Demo) ngay bây giờ:</p>
            
            <div class="space-y-2.5 mb-5">
                <!-- Option 1: Lunch 11h -->
                <div onclick="executeAdminPush('lunch')" class="group p-3.5 rounded-2xl border border-orange-200/80 bg-orange-50/30 hover:bg-orange-50 hover:border-orange-400 transition-all cursor-pointer flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🍱</span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-xs text-slate-900">Thông báo Giờ Cơm Trưa (11:00 AM)</h4>
                                <span class="px-1.5 py-0.5 rounded-md bg-orange-100 text-[#ee4d2d] text-[9px] font-black">Khuyên dùng</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">"Đến giờ cơm trưa rồi bạn ơi! Xem ngay thực đơn hôm nay và chọn món..."</p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl bg-[#ee4d2d] text-white text-xs font-black shadow-2xs group-hover:scale-105 transition-transform shrink-0">
                        Bắn ngay
                    </span>
                </div>

                <!-- Option 2: Dinner 18h -->
                <div onclick="executeAdminPush('dinner')" class="group p-3.5 rounded-2xl border border-blue-200/80 bg-blue-50/30 hover:bg-blue-50 hover:border-blue-400 transition-all cursor-pointer flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🍲</span>
                        <div>
                            <h4 class="font-extrabold text-xs text-slate-900">Thông báo Bữa Ăn Tối (18:00 PM)</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">"Bữa tối ấm cúng cùng gia đình! Nghỉ ngơi thôi, hàng trăm món nóng hổi..."</p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-black shadow-2xs group-hover:scale-105 transition-transform shrink-0">
                        Bắn ngay
                    </span>
                </div>

                <!-- Option 3: Flash Sale Promo -->
                <div onclick="executeAdminPush('promo')" class="group p-3.5 rounded-2xl border border-rose-200/80 bg-rose-50/30 hover:bg-rose-50 hover:border-rose-400 transition-all cursor-pointer flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <h4 class="font-extrabold text-xs text-slate-900">Flash Sale - Giờ Vàng Ưu Đãi</h4>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">"Giảm giá tới 20% cho tất cả các món ăn HOT. Đặt ngay nhận ưu đãi!"</p>
                        </div>
                    </div>
                    <span class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-red-500 to-amber-500 text-white text-xs font-black shadow-2xs group-hover:scale-105 transition-transform shrink-0">
                        Bắn ngay
                    </span>
                </div>
            </div>

            <!-- Feedback Alert -->
            <div id="admin-push-feedback" class="hidden p-3 rounded-xl text-xs font-bold text-center mb-3"></div>

            <button type="button" onclick="closeAdminPushDemoModal()" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-colors cursor-pointer">
                Đóng
            </button>
        </div>
    </div>

    <!-- Mobile Sidebar & Dropdown JS Handlers -->
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('admin-sidebar-overlay');
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
            if (overlay) {
                overlay.classList.toggle('hidden');
            }
        }

        function toggleAdminUserMenu(event) {
            if (event) {
                event.stopPropagation();
            }
            const menu = document.getElementById('admin-user-menu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        document.addEventListener('click', function (e) {
            const container = document.getElementById('admin-user-container');
            const menu = document.getElementById('admin-user-menu');
            if (menu && container && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

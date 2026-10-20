<!DOCTYPE html>
<html lang="vi" class="h-full bg-[#f8fafc]">

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
        /* Sleek Light Scrollbar for Admin Sidebar */
        aside nav::-webkit-scrollbar {
            width: 4px;
        }
        aside nav::-webkit-scrollbar-track {
            background: transparent;
        }
        aside nav::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 9999px;
        }
        aside nav::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
        aside nav {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
    </style>
    @yield('styles')
</head>


<body id="page-top" class="font-sans antialiased text-slate-800 bg-[#f8fafc] min-h-full selection:bg-[#ee4d2d] selection:text-white">
    <div id="wrapper" class="flex min-h-screen">


        <!-- Mobile Sidebar Overlay Backdrop -->
        <!-- Mobile Sidebar Overlay Backdrop -->
        <div id="admin-sidebar-overlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-35 hidden lg:hidden transition-opacity"></div>

        <!-- Ultra-Modern Light Sidebar Navigation (Cố định trên PC, Drawer trượt trên Mobile) -->
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 w-64 bg-white text-slate-700 flex flex-col justify-between z-40 border-r border-slate-200/80 shadow-xl lg:shadow-none h-screen transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0">
            <div class="flex flex-col h-full min-h-0">
                <!-- Brand Header -->
                <div class="h-16 lg:h-20 shrink-0 flex items-center justify-between px-6 border-b border-slate-100">
                    <a class="flex items-center gap-3 group" href="{{ route('quanly') }}">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#ee4d2d] to-[#ff6b4a] flex items-center justify-center text-white shadow-md shadow-rose-500/20 group-hover:scale-105 transition-all">
                            <i class="fas fa-utensils text-lg"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-lg text-slate-900 tracking-wider">FOODDAILY</span>
                            <span class="text-[10px] font-extrabold text-[#ee4d2d] uppercase tracking-widest -mt-1">Admin Portal</span>
                        </div>
                    </a>
                    <!-- Mobile Close Button -->
                    <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center text-sm transition-colors cursor-pointer" title="Đóng menu">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Quick Link to Storefront -->
                <div class="shrink-0 px-4 pt-3.5 pb-1">
                    <a href="{{ route('trangchu') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-amber-50/80 hover:bg-amber-100/90 text-amber-900 text-xs font-bold transition-all border border-amber-200/70 shadow-2xs group">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-store text-amber-600 group-hover:scale-110 transition-transform"></i>
                            <span>Xem Trang Chủ</span>
                        </span>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-amber-500 group-hover:text-amber-800 transition-colors"></i>
                    </a>
                </div>

                <!-- Navigation Links (Cuộn độc lập mượt mà) -->
                <nav class="flex-1 min-h-0 p-4 space-y-1.5 overflow-y-auto">
                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ route('quanly') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly') ? 'bg-[#ee4d2d] text-white font-black shadow-md shadow-rose-500/20' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chart-pie w-5 text-center text-sm {{ Route::is('quanly') ? 'text-white' : 'text-slate-400 group-hover:text-[#ee4d2d]' }} transition-colors"></i>
                            <span>Báo cáo doanh thu</span>
                        </div>
                    </a>
                    @endif

                    <a href="{{ route('quanly_banlamviec') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_banlamviec') ? 'bg-amber-500 text-white font-black shadow-md shadow-amber-500/20' : 'text-slate-600 hover:bg-amber-50/70 hover:text-amber-700 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-desktop w-5 text-center text-sm {{ Route::is('quanly_banlamviec') ? 'text-white' : 'text-amber-500 group-hover:text-amber-600' }} transition-colors"></i>
                            <span>Bàn làm việc Nhân viên</span>
                        </div>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Dịch vụ & Thực đơn</p>
                    </div>

                    @if(Auth::user()->role === 'superadmin')
                    <a href="{{ route('quanly_cauhinh') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_cauhinh') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-sliders w-5 text-center {{ Route::is('quanly_cauhinh') ? 'text-[#ee4d2d]' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors"></i>
                            <span>Cấu hình trang chủ</span>
                        </div>
                    </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ route('quanly_danhmuc') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_danhmuc') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-list w-5 text-center {{ Route::is('quanly_danhmuc') ? 'text-[#ee4d2d]' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors"></i>
                            <span>Danh mục món ăn</span>
                        </div>
                    </a>

                    <a href="{{ route('quanly_khuyenmai') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_khuyenmai') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-ticket w-5 text-center {{ Route::is('quanly_khuyenmai') ? 'text-[#ee4d2d]' : 'text-slate-400 group-hover:text-slate-700' }} transition-colors"></i>
                            <span>Mã khuyến mãi</span>
                        </div>
                    </a>
                    @endif

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Đơn hàng & Khách hàng</p>
                    </div>

                    <a href="{{ route('quanly_donhang') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_donhang') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar w-5 text-center {{ Route::is('quanly_donhang') ? 'text-[#ee4d2d]' : 'text-slate-400 group-hover:text-[#ee4d2d]' }} transition-colors"></i>
                            <span>Danh sách đơn hàng</span>
                        </div>
                        @if($pendingOrdersCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-[#ee4d2d] text-white text-[10px] font-black shadow-xs">
                                {{ $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('quanly_reviews') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_reviews') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-star w-5 text-center {{ Route::is('quanly_reviews') ? 'text-[#ee4d2d]' : 'text-amber-400 group-hover:text-amber-500' }} transition-colors"></i>
                            <span>Đánh giá khách hàng</span>
                        </div>
                    </a>
                    @endif

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Nghiệp vụ Vận hành</p>
                    </div>

                    <a href="{{ route('quanly_bep') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_bep') ? 'bg-amber-50 text-amber-800 font-black border border-amber-200/70 shadow-2xs' : 'text-slate-600 hover:bg-amber-50/60 hover:text-amber-700 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-fire-burner w-5 text-center text-amber-500 group-hover:scale-105 transition-transform"></i>
                            <span>Màn hình Bếp nấu</span>
                        </div>
                        @if($kitchenCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black shadow-xs">
                                {{ $kitchenCount }}
                            </span>
                        @endif
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Kho Vận & Nhập Hàng</p>
                    </div>

                    <a href="{{ route('quanly_kho') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_kho*') || Route::is('kho_*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-boxes-stacked w-5 text-center {{ Route::is('quanly_kho*') || Route::is('kho_*') ? 'text-[#ee4d2d]' : 'text-blue-500 group-hover:text-blue-600' }} transition-colors"></i>
                            <span>Kho & Hàng tồn thực tế</span>
                        </div>
                        @if($lowStockInventoryCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-black shadow-xs animate-pulse" title="Mặt hàng cần nhập thêm">
                                {{ $lowStockInventoryCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('quanly_nhacungcap') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_nhacungcap*') || Route::is('nhacungcap_*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-truck-field w-5 text-center {{ Route::is('quanly_nhacungcap*') || Route::is('nhacungcap_*') ? 'text-[#ee4d2d]' : 'text-amber-500 group-hover:text-amber-600' }} transition-colors"></i>
                            <span>Nguồn hàng & Nhà cung cấp</span>
                        </div>
                    </a>

                    <a href="{{ route('quanly_nhaphang') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_nhaphang*') || Route::is('nhaphang_*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-invoice-dollar w-5 text-center {{ Route::is('quanly_nhaphang*') || Route::is('nhaphang_*') ? 'text-[#ee4d2d]' : 'text-emerald-500 group-hover:text-emerald-600' }} transition-colors"></i>
                            <span>Phiếu Nhập Hàng (Nhập kho)</span>
                        </div>
                    </a>

                    @if(in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-black text-slate-400 uppercase tracking-wider">Tài khoản & Hệ thống</p>
                    </div>

                    <a href="{{ route('quanly_khachhang') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_khachhang') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-users w-5 text-center {{ Route::is('quanly_khachhang') ? 'text-[#ee4d2d]' : 'text-cyan-600 group-hover:text-cyan-700' }} transition-colors"></i>
                            <span>Quản lý Khách hàng</span>
                        </div>
                    </a>
                    @endif

                    @if(Auth::user()->role === 'superadmin' || (method_exists(Auth::user(), 'can') && Auth::user()->can('employees.manage')))
                    <a href="{{ route('quanly_nhanvien') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_nhanvien*') || Route::is('nhanvien_*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-id-badge w-5 text-center {{ Route::is('quanly_nhanvien*') || Route::is('nhanvien_*') ? 'text-[#ee4d2d]' : 'text-blue-500 group-hover:text-blue-600' }} transition-colors"></i>
                            <span>Tài khoản Nhân viên</span>
                        </div>
                    </a>
                    @endif

                    @if(Auth::user()->role === 'superadmin' || (method_exists(Auth::user(), 'can') && Auth::user()->can('roles.manage')))
                    <a href="{{ route('quanly_vaitro') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_vaitro*') || Route::is('vaitro_*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-crown w-5 text-center {{ Route::is('quanly_vaitro*') || Route::is('vaitro_*') ? 'text-[#ee4d2d]' : 'text-amber-500 group-hover:text-amber-600' }} transition-colors"></i>
                            <span>Vai trò & Phân quyền</span>
                        </div>
                        <span class="px-1.5 py-0.5 rounded-md bg-purple-100 text-purple-700 border border-purple-200/80 text-[9px] font-black uppercase">RBAC</span>
                    </a>

                    <a href="{{ route('quanly_nhatky') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_nhatky*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-fingerprint w-5 text-center {{ Route::is('quanly_nhatky*') ? 'text-[#ee4d2d]' : 'text-rose-500 group-hover:text-rose-600' }} transition-colors"></i>
                            <span>Nhật ký Audit Trail</span>
                        </div>
                        <span class="px-1.5 py-0.5 rounded-md bg-rose-100 text-rose-700 border border-rose-200/80 text-[9px] font-black uppercase">LOG</span>
                    </a>

                    <a href="{{ route('quanly_pheduyet') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs transition-all group {{ Route::is('quanly_pheduyet*') ? 'bg-rose-50 text-[#ee4d2d] font-black border border-rose-200/70 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/80 hover:text-slate-900 font-semibold' }}">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-signature w-5 text-center {{ Route::is('quanly_pheduyet*') ? 'text-[#ee4d2d]' : 'text-indigo-500 group-hover:text-indigo-600' }} transition-colors"></i>
                            <span>Phê duyệt 2 bước</span>
                        </div>
                        <span class="px-1.5 py-0.5 rounded-md bg-indigo-100 text-indigo-700 border border-indigo-200/80 text-[9px] font-black uppercase">DUAL</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Footer Sidebar Account Quick Action -->
            <div class="shrink-0 p-3.5 border-t border-slate-100 bg-slate-50/60">
                <a href="{{ route('trangchu') }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-white hover:bg-slate-100 text-xs font-bold text-slate-700 border border-slate-200/90 shadow-2xs hover:shadow-xs hover:border-slate-300 hover:text-slate-900 transition-all group">
                    <i class="fas fa-globe text-[#ee4d2d] group-hover:scale-110 transition-transform"></i>
                    <span>Xem Trang Web</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area (ml-0 trên Mobile, ml-64 trên Desktop) -->
        <div class="flex-grow flex flex-col min-w-0 bg-[#f8fafc] ml-0 lg:ml-64 min-h-screen transition-all relative">

            <!-- Global Admin Floating Toast Notification Container -->
            <div id="admin-toast-container" class="fixed top-20 right-4 sm:right-6 z-50 flex flex-col gap-3 max-w-sm sm:max-w-md w-full pointer-events-none">
                {{-- Session Success --}}
                @if(session('success'))
                    <div class="admin-toast pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-emerald-200/90 text-slate-800 transition-all duration-300 transform translate-x-0">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs">
                            <i class="fas fa-circle-check"></i>
                        </div>
                        <div class="flex-grow min-w-0 pr-2">
                            <h5 class="text-xs font-black text-emerald-700 uppercase tracking-wider">Thành công</h5>
                            <p class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                        </div>
                        <button type="button" onclick="dismissAdminToast(this.closest('.admin-toast'))" class="text-slate-400 hover:text-slate-600 p-1 text-sm shrink-0 cursor-pointer">&times;</button>
                    </div>
                @endif

                {{-- Session Error --}}
                @if(session('error'))
                    <div class="admin-toast pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-rose-200/90 text-slate-800 transition-all duration-300 transform translate-x-0">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-[#ee4d2d] flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs">
                            <i class="fas fa-circle-exclamation"></i>
                        </div>
                        <div class="flex-grow min-w-0 pr-2">
                            <h5 class="text-xs font-black text-[#ee4d2d] uppercase tracking-wider">Thông báo lỗi</h5>
                            <p class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        </div>
                        <button type="button" onclick="dismissAdminToast(this.closest('.admin-toast'))" class="text-slate-400 hover:text-slate-600 p-1 text-sm shrink-0 cursor-pointer">&times;</button>
                    </div>
                @endif

                {{-- Session Warning --}}
                @if(session('warning'))
                    <div class="admin-toast pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-amber-200/90 text-slate-800 transition-all duration-300 transform translate-x-0">
                        <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="flex-grow min-w-0 pr-2">
                            <h5 class="text-xs font-black text-amber-700 uppercase tracking-wider">Cảnh báo</h5>
                            <p class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">{{ session('warning') }}</p>
                        </div>
                        <button type="button" onclick="dismissAdminToast(this.closest('.admin-toast'))" class="text-slate-400 hover:text-slate-600 p-1 text-sm shrink-0 cursor-pointer">&times;</button>
                    </div>
                @endif

                {{-- Session Info --}}
                @if(session('info'))
                    <div class="admin-toast pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-sky-200/90 text-slate-800 transition-all duration-300 transform translate-x-0">
                        <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs">
                            <i class="fas fa-circle-info"></i>
                        </div>
                        <div class="flex-grow min-w-0 pr-2">
                            <h5 class="text-xs font-black text-sky-700 uppercase tracking-wider">Thông tin</h5>
                            <p class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">{{ session('info') }}</p>
                        </div>
                        <button type="button" onclick="dismissAdminToast(this.closest('.admin-toast'))" class="text-slate-400 hover:text-slate-600 p-1 text-sm shrink-0 cursor-pointer">&times;</button>
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="admin-toast pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-rose-200/90 text-slate-800 transition-all duration-300 transform translate-x-0">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-[#ee4d2d] flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="flex-grow min-w-0 pr-2">
                            <h5 class="text-xs font-black text-[#ee4d2d] uppercase tracking-wider">Dữ liệu không hợp lệ</h5>
                            <ul class="text-xs font-medium text-slate-700 mt-1 list-disc list-inside space-y-0.5 leading-relaxed">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" onclick="dismissAdminToast(this.closest('.admin-toast'))" class="text-slate-400 hover:text-slate-600 p-1 text-sm shrink-0 cursor-pointer">&times;</button>
                    </div>
                @endif
            </div>

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
                    if (window.showAdminToast) {
                        window.showAdminToast(`📢 Bắn thông báo "${data.data.title}" thành công!`, 'success');
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
                if (window.showAdminToast) {
                    window.showAdminToast('❌ Bắn thông báo thất bại: ' + err.message, 'error');
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

        // Global Toast Notification Utilities for Admin
        function dismissAdminToast(toastEl) {
            if (!toastEl) return;
            toastEl.classList.remove('translate-x-0', 'opacity-100');
            toastEl.classList.add('translate-x-8', 'opacity-0');
            setTimeout(() => {
                if (toastEl && toastEl.parentElement) {
                    toastEl.remove();
                }
            }, 300);
        }

        function showAdminToast(message, type = 'success', duration = 5000) {
            const container = document.getElementById('admin-toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = 'admin-toast pointer-events-auto flex items-start gap-3 p-4 bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border text-slate-800 transition-all duration-300 transform translate-x-8 opacity-0';

            let iconHtml = '';
            let titleText = 'Thông báo';
            let titleClass = '';
            let borderClass = 'border-slate-200/90';

            switch (type) {
                case 'success':
                    iconHtml = '<div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs"><i class="fas fa-circle-check"></i></div>';
                    titleText = 'Thành công';
                    titleClass = 'text-emerald-700';
                    borderClass = 'border-emerald-200/90';
                    break;
                case 'error':
                    iconHtml = '<div class="w-8 h-8 rounded-xl bg-rose-100 text-[#ee4d2d] flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs"><i class="fas fa-circle-exclamation"></i></div>';
                    titleText = 'Thông báo lỗi';
                    titleClass = 'text-[#ee4d2d]';
                    borderClass = 'border-rose-200/90';
                    break;
                case 'warning':
                    iconHtml = '<div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs"><i class="fas fa-triangle-exclamation"></i></div>';
                    titleText = 'Cảnh báo';
                    titleClass = 'text-amber-700';
                    borderClass = 'border-amber-200/90';
                    break;
                case 'info':
                default:
                    iconHtml = '<div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 text-sm font-bold shadow-2xs"><i class="fas fa-circle-info"></i></div>';
                    titleText = 'Thông tin';
                    titleClass = 'text-sky-700';
                    borderClass = 'border-sky-200/90';
                    break;
            }

            toast.classList.add(...borderClass.split(' '));
            toast.innerHTML = `
                ${iconHtml}
                <div class="flex-grow min-w-0 pr-2">
                    <h5 class="text-xs font-black ${titleClass} uppercase tracking-wider">${titleText}</h5>
                    <p class="text-xs font-medium text-slate-700 mt-0.5 leading-relaxed">${message}</p>
                </div>
                <button type="button" onclick="dismissAdminToast(this.closest('.admin-toast'))" class="text-slate-400 hover:text-slate-600 p-1 text-sm shrink-0 cursor-pointer">&times;</button>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-8', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');
            });

            // Auto dismiss
            setTimeout(() => {
                dismissAdminToast(toast);
            }, duration);
        }

        window.dismissAdminToast = dismissAdminToast;
        window.showAdminToast = showAdminToast;

        // Auto dismiss initial session toasts
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#admin-toast-container .admin-toast').forEach(toast => {
                setTimeout(() => {
                    dismissAdminToast(toast);
                }, 6000);
            });
        });
    </script>
</body>
</html>

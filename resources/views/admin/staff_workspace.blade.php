@extends('layouts.admin')

@section('title', 'Bàn Làm Việc Nhân Viên - FOODDAILY Admin')

@section('content')
<div class="space-y-8">
    
    <!-- Top Workspace Header -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-[#ee4d2d] rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 z-10">
            <span class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-xs font-extrabold uppercase tracking-widest text-rose-200">
                <i class="fas fa-desktop mr-1.5"></i> Operational Center
            </span>
            <h1 class="text-3xl font-black tracking-tight">Bàn Làm Việc Nhân Viên</h1>
            <p class="text-xs text-slate-300 font-medium">Trung tâm điều hành bếp nấu, xác nhận đơn hàng và điều phối shipper giao hàng</p>
        </div>
        
        <div class="z-10 flex items-center gap-3">
            <span class="px-4 py-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-xs font-bold flex items-center gap-2">
                <i class="far fa-calendar text-rose-400"></i> Ngày: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
            </span>
        </div>
    </div>

    <!-- 3 Core Operational Kanban Columns -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Column 1: NHÀ BẾP (KITCHEN) -->
        <div class="bg-white rounded-3xl p-6 shadow-md border border-slate-200/80 flex flex-col justify-between space-y-6 hover:shadow-xl transition-all">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold border border-amber-100">
                            <i class="fas fa-fire-burner"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Nhà Bếp Chế Biến</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Món ăn cần chế biến ngay</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-extrabold border border-amber-200">
                        {{ $kitchenSingleQty }} món
                    </span>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Theo dõi số lượng suất ăn cần chuẩn bị để điều phối đầu bếp nấu món đúng giờ giao.
                </p>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-800">Món ăn cần chế biến</p>
                            <p class="text-[10px] text-slate-400">Từ các đơn hàng mới</p>
                        </div>
                        <span class="px-3 py-1 bg-amber-500 text-white rounded-xl text-xs font-black">{{ $kitchenSingleQty }} suất</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('quanly_bep') }}" class="w-full py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-center text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-tv"></i> Mở Màn Hình Bếp Nấu
            </a>
        </div>

        <!-- Column 2: CSKH & XÁC NHẬN ĐƠN -->
        <div class="bg-white rounded-3xl p-6 shadow-md border border-slate-200/80 flex flex-col justify-between space-y-6 hover:shadow-xl transition-all">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-lg font-bold border border-rose-100">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Xác Nhận & CSKH</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Hỗ trợ khách & Hoàn tiền</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-rose-50 text-[#ee4d2d] rounded-full text-xs font-extrabold border border-rose-200">
                        {{ $cskhPendingOrders + $cskhPendingRefunds }} việc
                    </span>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Theo dõi đơn hàng đang chuẩn bị món và xử lý các yêu cầu hỗ trợ từ khách hàng.
                </p>

                <div class="space-y-3">
                    <a href="{{ route('quanly_donhang') }}" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/50 border border-slate-100 flex items-center justify-between transition-colors block">
                        <div>
                            <p class="text-xs font-bold text-slate-800">Đơn hàng đang chuẩn bị</p>
                            <p class="text-[10px] text-slate-400">Hệ thống chuyển tự động</p>
                        </div>
                        <span class="px-3 py-1 bg-[#ee4d2d] text-white rounded-xl text-xs font-black">{{ $cskhPendingOrders }} đơn</span>
                    </a>

                    <a href="{{ route('quanly_yeucauhoan') }}" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/50 border border-slate-100 flex items-center justify-between transition-colors block">
                        <div>
                            <p class="text-xs font-bold text-slate-800">Yêu cầu đổi / hoàn tiền</p>
                            <p class="text-[10px] text-slate-400">Chờ duyệt từ khách</p>
                        </div>
                        <span class="px-3 py-1 bg-rose-600 text-white rounded-xl text-xs font-black">{{ $cskhPendingRefunds }} đơn</span>
                    </a>
                </div>
            </div>

            <a href="{{ route('quanly_donhang') }}" class="w-full py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-center text-xs font-extrabold shadow-md shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-list-check"></i> Quản Lý Danh Sách Đơn
            </a>
        </div>

        <!-- Column 3: SHIPPER & GIAO HÀNG -->
        <div class="bg-white rounded-3xl p-6 shadow-md border border-slate-200/80 flex flex-col justify-between space-y-6 hover:shadow-xl transition-all">
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold border border-blue-100">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider">Vận Chuyển Shipper</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Shipper đang trên đường giao</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-extrabold border border-blue-200">
                        {{ $shipDeliveringOrders + $shipTodaySubscriptions }} đơn giao
                    </span>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    Theo dõi vị trí và tiến độ giao hàng của shipper cho các đơn lẻ và gói combo đăng ký.
                </p>

                <div class="space-y-3">
                    <a href="{{ route('quanly_donhang', ['status' => 'delivering']) }}" class="p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50/50 border border-slate-100 flex items-center justify-between transition-colors block">
                        <div>
                            <p class="text-xs font-bold text-slate-800">Shipper đang giao đơn lẻ</p>
                            <p class="text-[10px] text-slate-400">Đơn hàng đang trên đường</p>
                        </div>
                        <span class="px-3 py-1 bg-blue-600 text-white rounded-xl text-xs font-black">{{ $shipDeliveringOrders }} đơn</span>
                    </a>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-800">Giao hàng gói Combo tuần/tháng</p>
                            <p class="text-[10px] text-slate-400">Lịch trình hôm nay</p>
                        </div>
                        <span class="px-3 py-1 bg-slate-800 text-white rounded-xl text-xs font-black">{{ $shipTodaySubscriptions }} đơn</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('quanly_donhang', ['status' => 'delivering']) }}" class="w-full py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-center text-xs font-extrabold shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-motorcycle"></i> Kiểm Tra Tiến Độ Shipper
            </a>
        </div>

    </div>
</div>
@endsection

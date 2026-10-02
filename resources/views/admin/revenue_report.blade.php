@extends('layouts.admin')

@section('title', 'Báo cáo doanh thu & Thống kê - FOODDAILY Admin')

@section('content')
<div class="space-y-8">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thống Kê & Báo Cáo Doanh Thu FOODDAILY</h2>
            <p class="text-xs text-slate-500 mt-1">Theo dõi hoạt động kinh doanh, sản lượng món ăn và phát hiện các món bán chạy / tồn đọng</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Year Selector Form -->
            <form action="{{ route('quanly') }}" method="GET" class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500">Năm xem:</span>
                <select name="year" onchange="this.form.submit()" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20 shadow-xs cursor-pointer">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Năm {{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <div class="relative group">
                <button class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-2 shadow-md transition-all">
                    <i class="fas fa-download text-rose-400"></i> Xuất báo cáo
                    <i class="fas fa-chevron-down text-[10px]"></i>
                </button>
                <div class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-rose-600" href="{{ route('baocao_xuat_orders') }}">
                        <i class="fas fa-file-invoice mr-2 text-rose-500"></i>Xuất danh sách đơn hàng
                    </a>
                    <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-emerald-600" href="{{ route('baocao_xuat_customers') }}">
                        <i class="fas fa-users mr-2 text-emerald-500"></i>Xuất thông tin khách hàng
                    </a>
                    <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-amber-600" href="{{ route('baocao_xuat_dishes') }}">
                        <i class="fas fa-hamburger mr-2 text-amber-500"></i>Xuất báo cáo món chạy
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Weather AI Business Widget -->
    <div class="rounded-3xl food-gradient p-6 text-white shadow-xl shadow-rose-500/15 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-amber-300 font-extrabold text-xs uppercase tracking-wider">
                    <i class="fas fa-cloud-sun text-base"></i> Dự báo thời tiết kinh doanh AI
                </div>
                <p class="text-xs text-rose-100 font-medium" id="weather-text">Đang tải dữ liệu thời tiết khu vực...</p>
                <p class="text-sm font-bold text-amber-200 bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl inline-block" id="weather-recommendation"></p>
            </div>
            <div class="text-left md:text-right flex flex-col justify-center">
                <span class="text-4xl font-extrabold tracking-tight" id="weather-temp">--°C</span>
                <span class="text-xs font-semibold text-rose-100" id="weather-location">Hồ Chí Minh, VN</span>
            </div>
        </div>
    </div>

    <!-- Metrics Cards Grid: 4 Cột KPI chính -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- 1. Doanh thu Tháng này -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-rose-200 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Doanh thu Tháng {{ now()->format('m/Y') }}</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1.5">{{ number_format($monthlyRevenue, 0, ',', '.') }}đ</h3>
                <p class="text-[11px] font-bold mt-1 {{ $monthlyGrowth >= 0 ? 'text-emerald-600' : 'text-rose-500' }} flex items-center gap-1">
                    <i class="fas {{ $monthlyGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                    <span>{{ $monthlyGrowth >= 0 ? '+' : '' }}{{ $monthlyGrowth }}% so với tháng trước</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <!-- 2. Doanh thu Năm được chọn -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-emerald-200 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Doanh thu Năm {{ $selectedYear }}</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1.5">{{ number_format($yearlyRevenue, 0, ',', '.') }}đ</h3>
                <p class="text-[11px] text-slate-400 font-semibold mt-1">
                    {{ number_format($yearlyOrderCount) }} đơn hoàn thành trong năm
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-sack-dollar"></i>
            </div>
        </div>

        <!-- 3. Tổng đơn hàng hệ thống -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-amber-200 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng đơn hàng giao</p>
                <h3 class="text-2xl font-black text-amber-500 mt-1.5">{{ number_format($orderCount) }}</h3>
                <p class="text-[11px] text-amber-600 font-semibold mt-1 flex items-center gap-1">
                    <i class="fas fa-check-circle"></i> Đơn hợp lệ hệ thống
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-bag-shopping"></i>
            </div>
        </div>

        <!-- 4. TỔNG SỐ MÓN ĂN ĐÃ ĐẶT (MỚI) -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-blue-200 transition-all">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng số món đã đặt</p>
                <h3 class="text-2xl font-black text-blue-600 mt-1.5">{{ number_format($totalDishesOrdered) }}</h3>
                <p class="text-[11px] text-blue-500 font-bold mt-1 flex items-center gap-1">
                    <i class="fas fa-bowl-food"></i> {{ number_format($monthDishesOrdered) }} suất trong tháng này
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-utensils"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Area Chart (Revenue Trend) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-rose-500"></i> Biểu đồ tổng quan doanh thu năm {{ $selectedYear }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Biểu đồ đường trực quan theo dõi diễn biến doanh thu từng tháng</p>
                </div>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart (Revenue Breakdown by Payment Method) -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-rose-500"></i> Phương thức thanh toán
                </h3>
            </div>
            <div class="h-64 w-full relative my-auto">
                <canvas id="myPieChart"></canvas>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-around text-[11px] font-bold">
                <span class="flex items-center gap-1.5 text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> COD ({{ $codPercent }}%)
                </span>
                <span class="flex items-center gap-1.5 text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Chuyển khoản ({{ $bankPercent }}%)
                </span>
                <span class="flex items-center gap-1.5 text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span> MoMo ({{ $momoPercent }}%)
                </span>
            </div>
        </div>
    </div>

    <!-- BẢNG THỐNG KÊ CHI TIẾT DOANH THU THEO TỪNG THÁNG TRONG NĂM (MỚI) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 border border-slate-100 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-2">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-calendar-days text-[#ee4d2d]"></i> Bảng thống kê chi tiết doanh thu 12 tháng (Năm {{ $selectedYear }})
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Chi tiết số đơn hàng, sản lượng món ăn và mức đóng góp doanh thu của từng tháng</p>
            </div>
            <span class="text-xs font-bold text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200 self-start sm:self-auto">
                Tổng cả năm: <strong class="text-[#ee4d2d]">{{ number_format($yearlyRevenue, 0, ',', '.') }}đ</strong>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3 px-4">Tháng</th>
                        <th class="py-3 px-4">Doanh thu</th>
                        <th class="py-3 px-4 text-center">Số đơn hàng</th>
                        <th class="py-3 px-4 text-center">Số món đã đặt</th>
                        <th class="py-3 px-4 text-right">Giá trị đơn TB (AOV)</th>
                        <th class="py-3 px-4 text-right">Tỷ trọng năm</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($monthlyStats as $stat)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $stat['month'] == now()->month && $selectedYear == now()->year ? 'bg-rose-50/30 font-bold' : '' }}">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-[11px]">
                                        {{ $stat['month'] }}
                                    </span>
                                    <span class="font-extrabold text-slate-800">
                                        {{ $stat['month_name'] }}
                                        @if($stat['month'] == now()->month && $selectedYear == now()->year)
                                            <span class="ml-1 text-[9px] px-1.5 py-0.5 rounded bg-rose-100 text-[#ee4d2d] uppercase">Hiện tại</span>
                                        @endif
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-black {{ $stat['revenue'] > 0 ? 'text-slate-900' : 'text-slate-300' }}">
                                {{ number_format($stat['revenue'], 0, ',', '.') }}đ
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full {{ $stat['orders_count'] > 0 ? 'bg-blue-50 text-blue-700 font-bold' : 'text-slate-300' }}">
                                    {{ $stat['orders_count'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ number_format($stat['dishes_count']) }} suất
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-600">
                                {{ number_format($stat['average_order'], 0, ',', '.') }}đ
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden hidden sm:block">
                                        <div class="bg-[#ee4d2d] h-full rounded-full" style="width: {{ $stat['share_percent'] }}%"></div>
                                    </div>
                                    <span class="font-bold text-slate-700 text-[11px]">{{ $stat['share_percent'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- KHỐI PHÂN TÍCH: MÓN BÁN CHẠY VÀ MÓN TỒN / BÁN CHẬM (MỚI) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- CỘT 1: MÓN ĐANG BÁN CHẠY (BEST SELLERS) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="text-lg">🔥</span> Top Món Ăn Bán Chạy Nhất
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Xếp hạng theo số lượng suất món đã được khách hàng đặt nhiều nhất</p>
                </div>
                <span class="px-2.5 py-1 rounded-full bg-rose-50 text-[#ee4d2d] border border-rose-200 text-[10px] font-black uppercase">
                    Bán chạy
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-2.5 px-3 text-center">Hạng</th>
                            <th class="py-2.5 px-3">Món ăn</th>
                            <th class="py-2.5 px-3 text-center">Đã bán</th>
                            <th class="py-2.5 px-3 text-right">Doanh thu món</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($bestSellingDishes as $index => $bDish)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3 text-center">
                                    @if($index === 0)
                                        <span class="text-lg" title="Quán quân bán chạy">🥇</span>
                                    @elseif($index === 1)
                                        <span class="text-lg" title="Á quân">🥈</span>
                                    @elseif($index === 2)
                                        <span class="text-lg" title="Hạng ba">🥉</span>
                                    @else
                                        <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 font-extrabold text-[10px] inline-flex items-center justify-center">
                                            {{ $index + 1 }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden relative shrink-0 border border-slate-100">
                                            <img src="{{ $bDish->display_image }}" alt="{{ $bDish->dish_name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-xs line-clamp-1 hover:text-[#ee4d2d] transition-colors">
                                                <a href="{{ route('dish.detail', $bDish->id) }}" target="_blank">{{ $bDish->dish_name }}</a>
                                            </p>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $bDish->category->category_name ?? 'Món chính' }} • {{ number_format($bDish->price) }}đ
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 font-black text-xs">
                                        {{ number_format($bDish->total_sold) }} suất
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-black text-slate-800">
                                    {{ number_format($bDish->total_revenue, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">Chưa có dữ liệu món bán chạy</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CỘT 2: MÓN TỒN / BÁN CHẬM CẦN CHÚ Ý (SLOW MOVING & UNSOLD DISHES) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 border border-slate-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span class="text-lg">⚠️</span> Món Tồn & Bán Chậm Cần Chú Ý
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Danh sách các món có 0 lượt đặt hoặc lượng đặt rất thấp cần kích cầu</p>
                </div>
                @if($unsoldDishesCount > 0)
                    <span class="px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 border border-rose-200 text-[10px] font-black uppercase">
                        {{ $unsoldDishesCount }} món 0 đơn
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-black uppercase">
                        Tất cả đều bán
                    </span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-2.5 px-3">Món ăn</th>
                            <th class="py-2.5 px-3">Giá bán</th>
                            <th class="py-2.5 px-3 text-center">Đã bán</th>
                            <th class="py-2.5 px-3 text-right">Tình trạng / Xử lý</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($slowMovingDishes as $sDish)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden relative shrink-0 border border-slate-100">
                                            <img src="{{ $sDish->display_image }}" alt="{{ $sDish->dish_name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 text-xs line-clamp-1">
                                                {{ $sDish->dish_name }}
                                            </p>
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                {{ $sDish->category->category_name ?? 'Món chính' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-bold text-slate-700">
                                    {{ number_format($sDish->price) }}đ
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($sDish->total_sold == 0)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 font-black text-xs border border-rose-200">
                                            0 suất
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold text-xs border border-amber-200">
                                            {{ $sDish->total_sold }} suất
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($sDish->total_sold == 0)
                                            <span class="text-[10px] text-rose-500 font-bold mr-1 hidden sm:inline">Chưa có khách đặt</span>
                                        @else
                                            <span class="text-[10px] text-amber-600 font-bold mr-1 hidden sm:inline">Bán chậm</span>
                                        @endif
                                        <a href="{{ route('monandon_chinhsua', $sDish->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-[#ee4d2d] text-slate-600 hover:text-white font-bold text-[11px] transition-colors" title="Chỉnh sửa giá/thông tin">
                                            Sửa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-400">Không có món ăn nào tồn</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Suggestion tip -->
            <div class="p-3.5 bg-amber-50/70 rounded-2xl border border-amber-200/80 text-amber-900 text-xs flex items-start gap-2.5">
                <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                <div class="text-[11px] leading-relaxed">
                    <strong>Gợi ý kinh doanh:</strong> Đối với các món bán chậm hoặc chưa có người đặt, bạn có thể tạo mã voucher giảm giá, thêm vào gói combo ăn trưa hoặc chụp ảnh món hấp dẫn hơn để kích cầu.
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js failed to load.');
                return;
            }

            Chart.defaults.global.defaultFontFamily = "'Inter', 'Segoe UI', sans-serif";
            Chart.defaults.global.defaultFontColor = '#64748b';

            function formatMoney(num) {
                if (!num) return '0đ';
                return Number(num).toLocaleString('vi-VN') + 'đ';
            }

            // 1. AREA CHART: DOANH THU NĂM {{ $selectedYear }}
            var ctxArea = document.getElementById("myAreaChart");
            if (ctxArea) {
                var areaContext = ctxArea.getContext('2d');
                var gradientFill = areaContext.createLinearGradient(0, 0, 0, 300);
                gradientFill.addColorStop(0, "rgba(238, 77, 45, 0.25)");
                gradientFill.addColorStop(1, "rgba(238, 77, 45, 0.00)");

                var monthlyData = @json($chartAreaValues);

                new Chart(ctxArea, {
                    type: 'line',
                    data: {
                        labels: ["Thg 1", "Thg 2", "Thg 3", "Thg 4", "Thg 5", "Thg 6", "Thg 7", "Thg 8", "Thg 9", "Thg 10", "Thg 11", "Thg 12"],
                        datasets: [{
                            label: "Doanh thu",
                            lineTension: 0.35,
                            backgroundColor: gradientFill,
                            borderColor: "#ee4d2d",
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: "#ee4d2d",
                            pointBorderColor: "#ffffff",
                            pointBorderWidth: 2,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: "#ee4d2d",
                            pointHoverBorderColor: "#ffffff",
                            pointHoverBorderWidth: 2,
                            data: monthlyData,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        layout: {
                            padding: { left: 10, right: 20, top: 20, bottom: 10 }
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false, drawBorder: false },
                                ticks: { maxTicksLimit: 12, fontColor: '#94a3b8', fontSize: 11, fontStyle: 'bold' }
                            }],
                            yAxes: [{
                                ticks: {
                                    maxTicksLimit: 6,
                                    padding: 10,
                                    fontColor: '#94a3b8',
                                    fontSize: 11,
                                    callback: function(value) { return formatMoney(value); }
                                },
                                gridLines: { color: "#f1f5f9", zeroLineColor: "#e2e8f0", drawBorder: false }
                            }],
                        },
                        legend: { display: false },
                        tooltips: {
                            backgroundColor: "#0f172a",
                            bodyFontColor: "#ffffff",
                            titleMarginBottom: 10,
                            titleFontColor: '#cbd5e1',
                            titleFontSize: 12,
                            borderColor: '#334155',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            caretPadding: 10,
                            callbacks: {
                                label: function(tooltipItem, chart) {
                                    return "Doanh thu: " + formatMoney(tooltipItem.yLabel);
                                }
                            }
                        }
                    }
                });
            }

            // 2. PIE CHART: TỶ LỆ THANH TOÁN
            var ctxPie = document.getElementById("myPieChart");
            if (ctxPie) {
                var codRev = {{ $codRevenue }};
                var bankRev = {{ $bankRevenue }};
                var momoRev = {{ $momoRevenue }};

                var pieLabels = ["COD", "Chuyển khoản", "MoMo"];
                var pieData = [codRev, bankRev, momoRev];

                if (codRev === 0 && bankRev === 0 && momoRev === 0) {
                    pieData = [40, 40, 20];
                }

                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieData,
                            backgroundColor: ['#ee4d2d', '#3b82f6', '#ec4899'],
                            hoverBackgroundColor: ['#dc2626', '#2563eb', '#db2777'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                            borderWidth: 3,
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        cutoutPercentage: 70,
                        legend: { display: false },
                        tooltips: {
                            backgroundColor: "#0f172a",
                            bodyFontColor: "#ffffff",
                            borderColor: '#334155',
                            borderWidth: 1,
                            xPadding: 12,
                            yPadding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var dataset = data.datasets[tooltipItem.datasetIndex];
                                    var index = tooltipItem.index;
                                    var val = dataset.data[index];
                                    return data.labels[index] + ": " + formatMoney(val);
                                }
                            }
                        }
                    }
                });
            }

            // 3. WEATHER WIDGET SCRIPT
            (function fetchWeather() {
                var weatherText = document.getElementById('weather-text');
                var weatherTemp = document.getElementById('weather-temp');
                var weatherRec = document.getElementById('weather-recommendation');

                if (!weatherText || !weatherTemp || !weatherRec) return;

                fetch('https://api.open-meteo.com/v1/forecast?latitude=10.8231&longitude=106.6297&current_weather=true')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.current_weather) {
                            var temp = Math.round(data.current_weather.temperature);
                            var code = data.current_weather.weathercode;
                            weatherTemp.innerText = temp + "°C";

                            var isRain = code >= 51 && code <= 99;
                            if (isRain) {
                                weatherText.innerText = "Trời mưa / Ẩm ướt - Nhu cầu đặt đồ ăn online tăng cao";
                                weatherRec.innerText = "⚡ Gợi ý: Đẩy mạnh các món nước (Phở, Bún, Cháo) & tăng nhân sự giao hàng.";
                            } else if (temp >= 33) {
                                weatherText.innerText = "Thời tiết nắng nóng - Khách hàng có xu hướng gọi đồ uống giải nhiệt";
                                weatherRec.innerText = "🧊 Gợi ý: Chạy ưu đãi Trà đào, Sinh tố, Nước ép & Salad tươi mát.";
                            } else {
                                weatherText.innerText = "Thời tiết mát mẻ lý tưởng cho các bữa ăn ngon miệng";
                                weatherRec.innerText = "🍱 Gợi ý: Quảng bá các Gói cơm văn phòng Combo tuần/tháng.";
                            }
                        }
                    })
                    .catch(function(err) {
                        weatherText.innerText = "Thời tiết lý tưởng cho hoạt động kinh doanh ẩm thực";
                        weatherRec.innerText = "🍱 Gợi ý: Đẩy mạnh các suất ăn trưa và combo dinh dưỡng.";
                        weatherTemp.innerText = "30°C";
                    });
            })();
        });
    </script>
@endsection

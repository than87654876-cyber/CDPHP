@extends('layouts.admin')

@section('title', 'Báo cáo doanh thu & Thống kê - FOODDAILY Admin')

@section('content')
<div class="space-y-8">
    <!-- Top Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thống Kê & Báo Cáo Doanh Thu FOODDAILY</h2>
            <p class="text-xs text-slate-500 mt-1">Theo dõi hoạt động kinh doanh, tăng trưởng và nguồn thu nhập</p>
        </div>
        <div class="relative group">
            <button class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-2 shadow-md transition-all">
                <i class="fas fa-download text-rose-400"></i> Xuất báo cáo (Excel / CSV)
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
                <a class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-rose-600" href="{{ route('baocao_xuat_refunds') }}">
                    <i class="fas fa-rotate-left mr-2 text-rose-500"></i>Xuất lịch sử hoàn tiền
                </a>
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

    <!-- Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Monthly Revenue -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-rose-200 transition-all">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Doanh thu (Tháng {{ now()->format('m/Y') }})</p>
                <h3 class="text-2xl font-extrabold text-slate-800 mt-2">{{ number_format($monthlyRevenue, 0, ',', '.') }}đ</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <!-- Yearly Revenue -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-emerald-200 transition-all">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Doanh thu (Năm {{ now()->format('Y') }})</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 mt-2">{{ number_format($yearlyRevenue, 0, ',', '.') }}đ</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-sack-dollar"></i>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-amber-200 transition-all">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tổng đơn hàng</p>
                <h3 class="text-2xl font-extrabold text-amber-500 mt-2">{{ number_format($orderCount) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-bag-shopping"></i>
            </div>
        </div>

        <!-- Active Packages -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex items-center justify-between group hover:border-blue-200 transition-all">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Gói dịch vụ đang chạy</p>
                <h3 class="text-2xl font-extrabold text-blue-600 mt-2">{{ number_format($activeSubscriptionCount) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition-transform">
                <i class="fas fa-box-archive"></i>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Area Chart (Revenue Trend) -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-rose-500"></i> Biểu đồ tổng quan doanh thu năm {{ now()->format('Y') }}
                </h3>
            </div>
            <div class="h-80 w-full relative">
                <canvas id="myAreaChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart (Revenue Breakdown) -->
        <div class="bg-white rounded-3xl p-6 shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-rose-500"></i> Nguồn doanh thu
                </h3>
            </div>
            <div class="h-64 w-full relative my-auto">
                <canvas id="myPieChart"></canvas>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 flex justify-around text-xs font-bold">
                <span class="flex items-center gap-2 text-slate-700">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span> Món lẻ ({{ $singlePercent }}%)
                </span>
                <span class="flex items-center gap-2 text-slate-700">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span> Gói Combo ({{ $subscriptionPercent }}%)
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('admin/vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        Chart.defaults.global.defaultFontFamily = 'Plus Jakarta Sans', sans-serif;
        Chart.defaults.global.defaultFontColor = '#64748b';

        function formatMoney(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + 'đ';
        }

        // Area Chart
        var ctxArea = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctxArea, {
          type: 'line',
          data: {
            labels: ["Thg 1", "Thg 2", "Thg 3", "Thg 4", "Thg 5", "Thg 6", "Thg 7", "Thg 8", "Thg 9", "Thg 10", "Thg 11", "Thg 12"],
            datasets: [{
              label: "Doanh thu",
              lineTension: 0.4,
              backgroundColor: "rgba(239, 68, 68, 0.08)",
              borderColor: "rgba(239, 68, 68, 1)",
              pointRadius: 4,
              pointBackgroundColor: "rgba(239, 68, 68, 1)",
              pointBorderColor: "#fff",
              pointHoverRadius: 6,
              pointHoverBackgroundColor: "rgba(239, 68, 68, 1)",
              pointHoverBorderColor: "#fff",
              pointHitRadius: 10,
              pointBorderWidth: 2,
              data: @json($chartAreaValues),
            }],
          },
          options: {
            maintainAspectRatio: false,
            scales: {
              xAxes: [{ gridLines: { display: false }, ticks: { maxTicksLimit: 12 } }],
              yAxes: [{
                ticks: {
                  maxTicksLimit: 5,
                  padding: 10,
                  callback: function(value) { return formatMoney(value); }
                },
                gridLines: { color: "#f1f5f9", zeroLineColor: "#f1f5f9", drawBorder: false }
              }],
            },
            legend: { display: false },
            tooltips: {
              backgroundColor: "#0f172a",
              bodyFontColor: "#fff",
              titleFontColor: '#94a3b8',
              cornerRadius: 12,
              xPadding: 12,
              yPadding: 12,
              displayColors: false,
              callbacks: {
                label: function(tooltipItem, chart) {
                  return 'Doanh thu: ' + formatMoney(tooltipItem.yLabel);
                }
              }
            }
          }
        });

        // Pie Chart
        var ctxPie = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctxPie, {
          type: 'doughnut',
          data: {
            labels: ["Món đơn lẻ", "Gói dịch vụ"],
            datasets: [{
              data: [{{ $singleRevenue }}, {{ $subscriptionRevenue }}],
              backgroundColor: ['#ef4444', '#10b981'],
              hoverBackgroundColor: ['#dc2626', '#059669'],
              hoverBorderColor: "#ffffff",
            }],
          },
          options: {
            maintainAspectRatio: false,
            legend: { display: false },
            cutoutPercentage: 75,
            tooltips: {
              backgroundColor: "#0f172a",
              cornerRadius: 12,
              xPadding: 12,
              yPadding: 12,
              callbacks: {
                label: function(tooltipItem, data) {
                  var label = data.labels[tooltipItem.index] || '';
                  var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                  return label + ': ' + formatMoney(value);
                }
              }
            }
          },
        });
    </script>

    <!-- Weather Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch('https://api.open-meteo.com/v1/forecast?latitude=10.823&longitude=106.6296&current_weather=true')
                .then(response => response.json())
                .then(data => {
                    if (data && data.current_weather) {
                        const temp = data.current_weather.temperature;
                        const code = data.current_weather.weathercode;
                        document.getElementById('weather-temp').innerText = temp + '°C';
                        
                        let desc = 'Nắng đẹp, trời quang';
                        let recommendation = '💡 Thời tiết lý tưởng để đề xuất các món tráng miệng giải nhiệt và combo trưa!';
                        
                        if (code >= 1 && code <= 3) {
                            desc = 'Mây rải rác';
                            recommendation = '💡 Mát mẻ, phù hợp đẩy mạnh quảng bá đơn hàng giao tận nơi.';
                        } else if (code >= 51 && code <= 67) {
                            desc = 'Mưa nhẹ';
                            recommendation = '💡 Nhu cầu đặt món giao tận nhà tăng 15-20%. Sẵn sàng đội ngũ shipper!';
                        } else if (code >= 71) {
                            desc = 'Mưa dông lớn';
                            recommendation = '⚠️ Mưa lớn! Đơn hàng online tăng đột biến. Ưu tiên điều phối giao hàng nhanh!';
                        }

                        document.getElementById('weather-text').innerText = 'Hiện tại: ' + desc + ' • Gió: ' + data.current_weather.windspeed + ' km/h';
                        document.getElementById('weather-recommendation').innerText = recommendation;
                    }
                })
                .catch(err => {
                    console.error('Weather load error:', err);
                    document.getElementById('weather-text').innerText = 'Dữ liệu thời tiết hiện không khả dụng.';
                });
        });
    </script>
@endsection

@extends('layouts.admin')

@section('title', 'Cấu hình Hệ thống - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Cấu hình Hệ thống & Doanh nghiệp</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Tùy chỉnh logo, thông tin thương hiệu và các chỉ số hiển thị trang chủ</p>
        </div>
    </div>

    <!-- Main Config Form -->
    <form action="#" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Branding Assets -->
            <div class="space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-6">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <i class="fas fa-image text-emerald-600"></i> Logo & Banner Thương hiệu
                    </h3>

                    <!-- Current Logo -->
                    <div class="text-center space-y-3">
                        <label class="text-xs font-bold text-slate-700 block uppercase tracking-wider">Logo hiển thị</label>
                        <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-emerald-500/30 mx-auto shadow-sm p-1 bg-slate-50">
                            <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-cover rounded-full">
                        </div>
                        <input type="file" name="logo" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>

                    <hr class="border-slate-100">

                    <!-- Current Banner -->
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-slate-700 block uppercase tracking-wider">Banner Hero Section</label>
                        <div class="aspect-16/9 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 p-2">
                            <img src="{{ asset('client/assets/img/hero-img.png') }}" alt="Hero Banner" class="w-full h-full object-contain">
                        </div>
                        <input type="file" name="hero_banner" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Stats -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Bio & Stats Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 space-y-6">
                    <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-4">
                        <i class="fas fa-chart-line text-emerald-600"></i> Giới thiệu & Thống kê
                    </h3>

                    <div>
                        <label for="company_bio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Giới thiệu Doanh nghiệp</label>
                        <textarea id="company_bio" name="bio" rows="4" class="w-full p-4 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none" required>Chào mừng bạn đến với FOODDAILY – nơi kết hợp hoàn hảo giữa hương vị ẩm thực tinh tế, nguyên liệu sạch và không gian dịch vụ hiện đại.</textarea>
                    </div>

                    <!-- Stats counters -->
                    <div class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Số liệu hiển thị trang chủ</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 block mb-1">Số Khách hàng</span>
                                <input type="number" name="stat_clients" value="232" min="0" class="w-full px-3 py-2 text-center text-xs font-extrabold text-slate-900 bg-slate-50 border border-slate-200 rounded-xl focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 block mb-1">Số Đơn thành công</span>
                                <input type="number" name="stat_projects" value="521" min="0" class="w-full px-3 py-2 text-center text-xs font-extrabold text-slate-900 bg-slate-50 border border-slate-200 rounded-xl focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 block mb-1">Giờ Hỗ trợ</span>
                                <input type="number" name="stat_hours" value="1453" min="0" class="w-full px-3 py-2 text-center text-xs font-extrabold text-slate-900 bg-slate-50 border border-slate-200 rounded-xl focus:border-emerald-500 outline-none">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 block mb-1">Đầu bếp / Nhân sự</span>
                                <input type="number" name="stat_workers" value="32" min="0" class="w-full px-3 py-2 text-center text-xs font-extrabold text-slate-900 bg-slate-50 border border-slate-200 rounded-xl focus:border-emerald-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="button" onclick="alert('Đã lưu cấu hình giao diện thành công!')" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition-all">
                            <i class="fas fa-save mr-1.5"></i> Lưu cấu hình
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
@endsection

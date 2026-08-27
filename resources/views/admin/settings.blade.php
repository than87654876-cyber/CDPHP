@extends('layouts.admin')

@section('title', 'Cấu hình Trang chủ - FOODDAILY Admin')

@section('content')
<div class="space-y-8">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Cấu Hình Trang Chủ & Thương Hiệu</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quản lý hình ảnh logo, banner, thông tin liên hệ và tích hợp bản đồ</p>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Quick Navigation Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-lg font-bold">
                <i class="fas fa-utensils"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-900 text-sm">Thực Đơn Món Ăn</h4>
                <p class="text-xs text-slate-400 font-medium">Quản lý danh mục & các món ăn đơn</p>
            </div>
            <div class="pt-2 flex gap-2">
                <a href="{{ route('quanly_monandon') }}" class="px-3.5 py-1.5 rounded-xl bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white text-xs font-bold transition-all">Món ăn</a>
                <a href="{{ route('quanly_danhmuc') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">Danh mục</a>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-3 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold">
                <i class="fas fa-ticket"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-slate-900 text-sm">Khuyến Mãi & Sự Kiện</h4>
                <p class="text-xs text-slate-400 font-medium">Tạo voucher & chương trình ưu đãi</p>
            </div>
            <div class="pt-2">
                <a href="{{ route('quanly_khuyenmai') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white text-xs font-bold transition-all">Chương trình khuyến mãi</a>
            </div>
        </div>
    </div>

    <!-- Main Settings Form Panel -->
    <form action="{{ route('quanly_cauhinh.post') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Column 1: Images Assets -->
            <div class="space-y-6">
                <!-- Logo Settings -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fas fa-image text-[#ee4d2d]"></i> 1. Ảnh Logo Cửa Hàng
                    </h3>
                    <div class="text-center space-y-3">
                        <div class="w-28 h-28 rounded-2xl overflow-hidden border-2 border-slate-200 p-1 mx-auto bg-slate-50 shadow-xs">
                            <img src="{{ isset($settings['logo_url']) ? (\Illuminate\Support\Str::startsWith($settings['logo_url'], 'http') ? $settings['logo_url'] : asset($settings['logo_url'])) : asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <input type="file" name="logo_image" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#ee4d2d] hover:file:bg-rose-100 transition-all cursor-pointer">
                        <p class="text-[10px] text-slate-400 font-medium">Hỗ trợ JPG, PNG, GIF (Tối đa 2MB)</p>
                    </div>
                </div>

                <!-- Banner Settings -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fas fa-[#ee4d2d] text-[#ee4d2d]"></i> 2. Ảnh Banner Hero
                    </h3>
                    <div class="space-y-3">
                        <div class="aspect-16/9 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 p-1">
                            <img src="{{ isset($settings['banner_image']) ? (\Illuminate\Support\Str::startsWith($settings['banner_image'], 'http') ? $settings['banner_image'] : asset($settings['banner_image'])) : asset('client/assets/img/hero-img.png') }}" alt="Hero Banner" class="w-full h-full object-cover rounded-xl">
                        </div>
                        <input type="file" name="banner_image" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#ee4d2d] hover:file:bg-rose-100 transition-all cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Column 2 & 3: Info & Google Maps -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Banner Title & Contact Info -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
                    <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-4">
                        <i class="fas fa-bullhorn text-[#ee4d2d]"></i> 3. Tiêu Đề Banner & Thông Tin Liên Hệ
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tiêu đề chính Banner <span class="text-[#ee4d2d]">*</span></label>
                            <input type="text" name="banner_title" value="{{ old('banner_title', $settings['banner_title'] ?? 'Bạn đã sẵn sàng để tận hưởng hương vị ngon miệng chưa?') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tiêu đề phụ Banner <span class="text-[#ee4d2d]">*</span></label>
                            <input type="text" name="banner_subtitle" value="{{ old('banner_subtitle', $settings['banner_subtitle'] ?? 'Đồng hành cùng bạn trên hành trình khám phá những bữa ăn dinh dưỡng') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Số điện thoại Hotline <span class="text-[#ee4d2d]">*</span></label>
                            <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '0912 345 678') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa chỉ Email <span class="text-[#ee4d2d]">*</span></label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? 'contact@fooddaily.vn') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Địa chỉ Cửa hàng <span class="text-[#ee4d2d]">*</span></label>
                        <input type="text" name="contact_address" value="{{ old('contact_address', $settings['contact_address'] ?? '123 Lê Hồng Phong, Quận 10, TP. Hồ Chí Minh') }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã / Link nhúng Google Maps <span class="text-[#ee4d2d]">*</span></label>
                        <textarea name="map_embed_url" rows="3" required placeholder="Dán mã <iframe...> hoặc đường link Google Maps..." class="w-full p-4 rounded-2xl border border-slate-200 text-xs font-mono text-slate-800 focus:border-[#ee4d2d] outline-none transition-all">{{ old('map_embed_url', $settings['map_original_url'] ?? $settings['map_embed_url'] ?? '') }}</textarea>
                    </div>

                    <!-- Map Preview -->
                    @if(isset($settings['map_embed_url']))
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider">Xem trước Bản đồ Google Maps</span>
                            <div class="aspect-16/9 rounded-2xl overflow-hidden border border-slate-200">
                                <iframe src="{{ $settings['map_embed_url'] }}" class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-8 py-3.5 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-lg shadow-rose-500/20 hover:scale-105 transition-all">
                            <i class="fas fa-save mr-2"></i> LƯU CẤU HÌNH TRANG CHỦ
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>
@endsection

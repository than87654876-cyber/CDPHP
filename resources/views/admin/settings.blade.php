@extends('layouts.admin')

@section('title', 'Cấu hình Hình nền Website - FOODDAILY Admin')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Cấu Hình Hình Nền Website</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Thay đổi linh hoạt ảnh nền khung Banner Hero và ảnh nền chính toàn bộ trang web</p>
        </div>
        <a href="{{ route('trangchu') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-eye text-[#ee4d2d]"></i> Xem giao diện website
        </a>
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

    <!-- Main Settings Form -->
    <form action="{{ route('quanly_cauhinh.post') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">
            
            <!-- 1. Cấu hình Ảnh Nền Khung Banner Hero -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 flex flex-col justify-between space-y-6 hover:shadow-md transition-all">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-black text-xs">1</span>
                            Ảnh Nền Khung Banner Hero
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] text-[10px] font-extrabold">Khung Hero Trái</span>
                    </div>

                    <p class="text-xs text-slate-500 font-medium">Hình ảnh nền hiển thị bên trong khung Hero góc trái trang chủ (chứa thanh tìm kiếm và nút danh mục):</p>

                    <!-- Preview Container -->
                    <div class="aspect-16/9 rounded-2xl overflow-hidden bg-slate-900 border-2 border-slate-200 p-1 relative shadow-sm group">
                        <img id="preview-banner" src="{{ isset($settings['banner_image']) && $settings['banner_image'] ? (\Illuminate\Support\Str::startsWith($settings['banner_image'], 'http') ? $settings['banner_image'] : asset($settings['banner_image'])) : asset('uploads/ve-dep-sai-gon-qua-ong-kinh-cua-nguoi-me-anh-ivivu-2.jpg') }}" alt="Hero Banner Preview" class="w-full h-full object-cover rounded-xl group-hover:scale-102 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-bold bg-black/60 px-3 py-1 rounded-full"><i class="fas fa-image mr-1"></i> Ảnh đang hiển thị</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tải lên ảnh mới cho Banner Hero:</label>
                    <input type="file" name="banner_image" accept="image/*" onchange="previewImage(this, 'preview-banner')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#ee4d2d] hover:file:bg-rose-100 transition-all cursor-pointer">
                    <p class="text-[10px] text-slate-400 font-medium">Hỗ trợ định dạng: JPG, PNG, GIF, WEBP (Tối đa 8MB)</p>
                </div>
            </div>

            <!-- 2. Cấu hình Ảnh Nền Chính Toàn Website -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 flex flex-col justify-between space-y-6 hover:shadow-md transition-all">
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs">2</span>
                            Ảnh Nền Chính Toàn Website
                        </h3>
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-extrabold">Nền Toàn Trang</span>
                    </div>

                    <p class="text-xs text-slate-500 font-medium">Hình ảnh nền bàn ăn bao phủ toàn bộ phía sau của trang web (hiển thị xuyên suốt các trang):</p>

                    <!-- Preview Container -->
                    <div class="aspect-16/9 rounded-2xl overflow-hidden bg-slate-900 border-2 border-slate-200 p-1 relative shadow-sm group">
                        <img id="preview-site-bg" src="{{ isset($settings['site_background']) && $settings['site_background'] ? (\Illuminate\Support\Str::startsWith($settings['site_background'], 'http') ? $settings['site_background'] : asset($settings['site_background'])) : asset('uploads/cf1a02d49dc2b801e809fcb9adefd77e.jpg') }}" alt="Site Background Preview" class="w-full h-full object-cover rounded-xl group-hover:scale-102 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-white text-xs font-bold bg-black/60 px-3 py-1 rounded-full"><i class="fas fa-layer-group mr-1"></i> Ảnh đang hiển thị</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-100">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tải lên ảnh mới cho Nền Website:</label>
                    <input type="file" name="site_background" accept="image/*" onchange="previewImage(this, 'preview-site-bg')" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition-all cursor-pointer">
                    <p class="text-[10px] text-slate-400 font-medium">Hỗ trợ định dạng: JPG, PNG, GIF, WEBP (Tối đa 8MB)</p>
                </div>
            </div>

        </div>

        <!-- Submit Button Bar -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 flex items-center justify-between">
            <span class="text-xs text-slate-500 font-medium">💡 Sau khi bấm lưu, hình ảnh mới sẽ được áp dụng ngay lập tức trên toàn hệ thống.</span>
            <button type="submit" class="px-8 py-3.5 bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs rounded-2xl transition-all shadow-md flex items-center gap-2 active:scale-98">
                <i class="fas fa-save text-sm"></i> LƯU VÀ ÁP DỤNG HÌNH NỀN
            </button>
        </div>

    </form>

</div>
@endsection

@section('scripts')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById(previewId);
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

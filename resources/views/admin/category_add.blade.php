@extends('layouts.admin')

@section('title', 'Thêm Danh mục Món ăn - FOODDAILY Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Thêm Danh Mục Mới</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Tạo nhóm phân loại thực đơn mới (Ví dụ: Cơm, Mì ý, Tráng miệng, Nước giải khát...)</p>
        </div>
        <a href="{{ route('quanly_danhmuc') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs space-y-1">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-2 font-medium"><i class="fas fa-circle-exclamation text-rose-500"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-200/80 space-y-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-base font-bold shadow-xs">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Thông tin danh mục món ăn</h3>
                <p class="text-[11px] text-slate-400 font-medium">Nhập tên danh mục và mô tả ngắn gọn cho phân loại này</p>
            </div>
        </div>

        <form action="{{ route('danhmuc_them.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="space-y-2">
                <label for="category_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Tên nhóm danh mục <span class="text-[#ee4d2d]">*</span>
                </label>
                <input type="text" id="category_name" name="category_name" value="{{ old('category_name') }}" placeholder="Ví dụ: Ăn sáng, Món chính, Món tráng miệng..." required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] focus:bg-white outline-none transition-all shadow-xs">
            </div>

            <div class="space-y-2">
                <label for="description" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Miêu tả chi tiết nhóm món
                </label>
                <textarea id="description" name="description" rows="4" placeholder="Nhập mô tả tóm tắt đặc điểm hoặc thực đơn của nhóm món này..." class="w-full p-4 rounded-2xl border border-slate-200 text-xs font-medium text-slate-900 focus:border-[#ee4d2d] focus:bg-white outline-none transition-all shadow-xs leading-relaxed">{{ old('description') }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('quanly_danhmuc') }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2 active:scale-98">
                    <i class="fas fa-save"></i> LƯU DANH MỤC
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

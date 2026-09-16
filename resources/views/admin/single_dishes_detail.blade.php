@extends('layouts.admin')

@section('title', 'Chi tiết Món ăn - FOODDAILY Admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span>Chi Tiết Món Ăn</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-[#ee4d2d] font-extrabold border border-rose-200">
                    #{{ $dish->id }}
                </span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Hồ sơ thông tin chi tiết, định giá và trạng thái phục vụ</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_monandon') }}" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-xs transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
            <a href="{{ route('monandon_chinhsua', $dish->id) }}" class="px-4 py-2.5 rounded-xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Chỉnh sửa món
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Dish Image Card -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-image"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Hình ảnh minh họa</h3>
            </div>
            
            <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 border border-slate-200/60 relative group">
                @if($dish->image_url)
                    <img src="{{ Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url) }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 gap-2">
                        <i class="fas fa-utensils text-3xl"></i>
                        <span class="text-xs font-semibold">Chưa có ảnh</span>
                    </div>
                @endif
                <div class="absolute top-3 right-3">
                    @if($dish->is_available)
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/90 backdrop-blur-xs text-white text-[10px] font-black shadow-xs flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Đang phục vụ
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full bg-slate-700/90 backdrop-blur-xs text-white text-[10px] font-black shadow-xs">
                            Tạm ngưng
                        </span>
                    @endif
                </div>
            </div>

            <div class="text-center pt-2">
                <p class="text-xs font-bold text-slate-400">Giá niêm yết</p>
                <p class="text-2xl font-black text-rose-600 mt-0.5">{{ number_format($dish->price, 0, ',', '.') }}đ</p>
            </div>
        </div>

        <!-- Dish Info Details Card -->
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-2 pb-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-list-check"></i>
                </div>
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Thông tin sản phẩm chi tiết</h3>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Tên món ăn</p>
                        <p class="text-sm font-black text-slate-900 mt-1">{{ $dish->dish_name }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Danh mục phân loại</p>
                        <p class="text-sm font-black text-[#ee4d2d] mt-1">{{ $dish->category->category_name ?? 'Chưa phân loại' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Mã ID món</p>
                        <p class="text-sm font-black text-slate-800 mt-1">#{{ $dish->id }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Trạng thái bán</p>
                        <div class="mt-1">
                            @if($dish->is_available)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Còn món / Đang bán
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-extrabold border border-slate-200">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span> Tạm ngưng phục vụ
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Mô tả món ăn</p>
                    <p class="text-xs font-medium text-slate-700 leading-relaxed">
                        {{ $dish->description ?? 'Chưa có mô tả chi tiết cho món ăn này.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

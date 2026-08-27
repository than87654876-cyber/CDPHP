@extends('layouts.admin')

@section('title', 'Quản lý Món ăn đơn - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                @if($categoryId == 1)
                    Danh sách Món Ăn Sáng
                @elseif($categoryId == 2)
                    Danh sách Món Tráng Miệng
                @else
                    Danh sách Tất Cả Món Ăn Đơn
                @endif
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Quản lý thông tin, hình ảnh, giá bán và trạng thái món ăn</p>
        </div>

        <a href="{{ route('monandon_them', ['category_id' => $categoryId]) }}" class="px-5 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white text-xs font-extrabold shadow-lg shadow-rose-500/20 transition-all flex items-center justify-center gap-2">
            <i class="fas fa-plus"></i> Thêm món ăn mới
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        <!-- Table Header & Search -->
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-utensils text-[#ee4d2d]"></i> Danh sách sản phẩm thực đơn
            </h3>

            <!-- Search Form -->
            <form method="GET" action="{{ route('quanly_monandon') }}" class="flex items-center gap-2 max-w-xs w-full">
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm tên món ăn..." class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#ee4d2d] outline-none">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-[#ee4d2d] text-white text-xs font-bold transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                        <th class="py-4 px-6">STT</th>
                        <th class="py-4 px-6">Hình ảnh</th>
                        <th class="py-4 px-6">Tên món ăn</th>
                        <th class="py-4 px-6">Danh mục</th>
                        <th class="py-4 px-6">Giá bán</th>
                        <th class="py-4 px-6">Trạng thái</th>
                        <th class="py-4 px-6 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dishes as $index => $dish)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-bold text-slate-400">#{{ $index + 1 }}</td>
                            <td class="py-4 px-6">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shadow-xs">
                                    @if($dish->image)
                                        <img src="{{ asset($dish->image) }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-utensils"></i></div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="font-extrabold text-slate-900 text-xs">{{ $dish->dish_name }}</p>
                                <p class="text-[11px] text-slate-400 line-clamp-1 font-medium">{{ $dish->description }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold">
                                    {{ $dish->category->category_name ?? 'Chưa phân loại' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-black text-slate-900">
                                {{ number_format($dish->price) }}đ
                            </td>
                            <td class="py-4 px-6">
                                @if($dish->is_available)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-extrabold">Đang bán</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-50 border border-rose-200 text-rose-700 text-[10px] font-extrabold">Tạm ngưng</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <a href="{{ route('monandon_chinhsua', ['id' => $dish->id]) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('monandon_xoa', ['id' => $dish->id]) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa món ăn này?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-colors">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 font-medium">Chưa có món ăn nào trong danh sách.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

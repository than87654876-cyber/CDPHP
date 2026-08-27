@extends('layouts.admin')

@section('title', 'Chi tiết Danh mục - FOODDAILY Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Chi Tiết Danh Mục: <span class="text-[#ee4d2d]">{{ $category->category_name }}</span></h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Xem thông tin nhóm và danh sách các món ăn thuộc danh mục này</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('danhmuc_chinhsua', $category->id) }}" class="px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-extrabold text-xs transition-colors flex items-center gap-2">
                <i class="fas fa-pen-to-square"></i> Chỉnh sửa
            </a>
            <a href="{{ route('quanly_danhmuc') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <!-- Left: Category Info Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-[#ee4d2d] flex items-center justify-center font-bold text-base">
                    <i class="fas fa-folder-open"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Thông tin danh mục</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Chi tiết phân loại</p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Tên danh mục:</span>
                    <span class="font-extrabold text-slate-900">{{ $category->category_name }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Số lượng món ăn:</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-black text-[11px] border border-emerald-200">
                        {{ $category->dishes->count() }} món
                    </span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Ngày tạo:</span>
                    <span class="font-semibold text-slate-700">{{ $category->created_at ? $category->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                    <span class="text-slate-500 font-bold">Cập nhật cuối:</span>
                    <span class="font-semibold text-slate-700">{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                </div>
                <div class="space-y-1.5 pt-1">
                    <span class="text-slate-500 font-bold block">Mô tả danh mục:</span>
                    <p class="text-slate-700 font-medium bg-slate-50 p-3 rounded-2xl border border-slate-100 leading-relaxed">
                        {{ $category->description ?? 'Chưa có mô tả cho danh mục này.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: Dishes in Category -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden space-y-0">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-utensils text-[#ee4d2d]"></i> Danh sách món ăn thuộc nhóm này ({{ $category->dishes->count() }})
                </h3>
                <a href="{{ route('monandon_them', ['category_id' => $category->id]) }}" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-[#ee4d2d] text-[#ee4d2d] hover:text-white font-extrabold text-xs transition-colors">
                    <i class="fas fa-plus mr-1"></i> Thêm món vào nhóm
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] text-slate-400 uppercase font-extrabold tracking-wider">
                            <th class="py-3.5 px-6">STT</th>
                            <th class="py-3.5 px-6">Hình ảnh</th>
                            <th class="py-3.5 px-6">Tên món ăn</th>
                            <th class="py-3.5 px-6">Đơn giá</th>
                            <th class="py-3.5 px-6">Trạng thái</th>
                            <th class="py-3.5 px-6 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($category->dishes as $index => $dish)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-slate-400">#{{ $index + 1 }}</td>
                                <td class="py-4 px-6">
                                    <div class="w-11 h-11 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shadow-2xs">
                                        <img src="{{ $dish->image_url ? (\Illuminate\Support\Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url)) : ($dish->image ? asset($dish->image) : asset('logo.jpg')) }}" alt="{{ $dish->dish_name }}" class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-extrabold text-slate-900 text-xs">{{ $dish->dish_name }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium line-clamp-1">{{ $dish->description ?? 'Không có mô tả' }}</p>
                                </td>
                                <td class="py-4 px-6 font-black text-[#ee4d2d]">
                                    {{ number_format($dish->price, 0, ',', '.') }}đ
                                </td>
                                <td class="py-4 px-6">
                                    @if($dish->is_available)
                                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-black border border-emerald-200">
                                            ĐANG BÁN
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black border border-slate-200">
                                            TẠM NGƯNG
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right space-x-1">
                                    <a href="{{ route('monandon_chinhsua', $dish->id) }}" title="Chỉnh sửa món ăn" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-colors">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    <a href="{{ route('monandon_xem', $dish->id) }}" title="Xem chi tiết" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <form action="{{ route('monandon_xoa', $dish->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa món ăn này khỏi thực đơn?');">
                                        @csrf
                                        <button type="submit" title="Xóa món" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 transition-colors">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-medium">Chưa có món ăn nào thuộc danh mục này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

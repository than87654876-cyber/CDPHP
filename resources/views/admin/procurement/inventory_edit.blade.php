@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Mặt Hàng - ' . $item->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_kho') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Chỉnh Sửa Mặt Hàng: {{ $item->name }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Mã mặt hàng: <span class="font-mono font-bold text-slate-800">{{ $item->item_code }}</span></p>
            </div>
        </div>

        <form method="POST" action="{{ route('kho_xoa', $item->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mặt hàng này khỏi kho? Lưu ý: Nếu đã có phiếu nhập liên quan thì không thể xóa!')">
            @csrf
            <button type="submit" class="px-3.5 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-bold transition-colors flex items-center gap-1.5">
                <i class="fas fa-trash-can"></i> Xóa mặt hàng
            </button>
        </form>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs font-semibold space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                <span>Có lỗi xảy ra, vui lòng kiểm tra lại:</span>
            </div>
            <ul class="list-disc list-inside text-rose-700 pl-4 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-xs">
        <form method="POST" action="{{ route('kho_chinhsua.post', $item->id) }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Mã mặt hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã mặt hàng (SKU / Code) *</label>
                    <input type="text" name="item_code" value="{{ old('item_code', $item->item_code) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                </div>

                <!-- Tên mặt hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tên mặt hàng / Nguyên liệu *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                </div>

                <!-- Danh mục phân loại -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phân loại danh mục</label>
                    <input type="text" name="category" value="{{ old('category', $item->category) }}" list="categoryList"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                    <datalist id="categoryList">
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>

                <!-- Đơn vị tính -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Đơn vị tính *</label>
                    <input type="text" name="unit" value="{{ old('unit', $item->unit) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                </div>

                <!-- Ngưỡng tồn kho tối thiểu (Min stock) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ngưỡng tối thiểu (Cảnh báo hết) *</label>
                    <input type="number" step="0.01" min="0" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-extrabold text-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                </div>

                <!-- Giá vốn nhập tham khảo -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Giá vốn nhập gần nhất (VNĐ)</label>
                    <input type="number" step="1" min="0" name="cost_price" value="{{ old('cost_price', $item->cost_price) }}"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-extrabold text-emerald-600 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                </div>

                <!-- Nhà cung cấp / Nguồn nhập ưu tiên -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nguồn Nhập Hàng Ưu Tiên (Nhà Cung Cấp)</label>
                    <select name="supplier_id" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                        <option value="">-- Chọn đơn vị cung cấp chính --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id', $item->supplier_id) == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} (SĐT: {{ $sup->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Ghi chú mặt hàng -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ghi chú thêm về mặt hàng</label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">{{ old('notes', $item->notes) }}</textarea>
            </div>

            <!-- Nút Lưu -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('quanly_kho') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i> Cập Nhật Mặt Hàng
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

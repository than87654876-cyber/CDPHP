@extends('layouts.admin')

@section('title', 'Thêm Mặt Hàng / Nguyên Liệu Mới - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('quanly_kho') }}" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">Thêm Mặt Hàng / Nguyên Liệu Mới Vào Kho</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Khai báo mặt hàng để quản lý tồn kho và thiết lập nguồn nhập đối tác</p>
            </div>
        </div>
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
        <form method="POST" action="{{ route('kho_them.post') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Mã mặt hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mã mặt hàng (SKU / Code) *</label>
                    <input type="text" name="item_code" value="{{ old('item_code', 'NL-' . strtoupper(Str::random(5))) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                    <span class="text-[10px] text-slate-400 mt-1 block">Ví dụ: NL-THITGA, SP-COCA...</span>
                </div>

                <!-- Tên mặt hàng -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tên mặt hàng / Nguyên liệu *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Thịt đùi gà tươi, Nước khoáng Lavie..." required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                </div>

                <!-- Danh mục phân loại -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phân loại danh mục</label>
                    <input type="text" name="category" value="{{ old('category') }}" list="categoryList" placeholder="Chọn hoặc nhập mới (VD: Thịt tươi sống, Đồ uống đóng chai...)"
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
                    <select name="unit" required class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="lon" {{ old('unit') == 'lon' ? 'selected' : '' }}>Lon (lon)</option>
                        <option value="chai" {{ old('unit') == 'chai' ? 'selected' : '' }}>Chai (chai)</option>
                        <option value="hộp" {{ old('unit') == 'hộp' ? 'selected' : '' }}>Hộp (hộp)</option>
                        <option value="gói" {{ old('unit') == 'gói' ? 'selected' : '' }}>Gói (gói)</option>
                        <option value="bao" {{ old('unit') == 'bao' ? 'selected' : '' }}>Bao (bao)</option>
                        <option value="quả" {{ old('unit') == 'quả' ? 'selected' : '' }}>Quả / Trái (quả)</option>
                        <option value="bó" {{ old('unit') == 'bó' ? 'selected' : '' }}>Bó (bó)</option>
                        <option value="lít" {{ old('unit') == 'lít' ? 'selected' : '' }}>Lít (lít)</option>
                    </select>
                </div>

                <!-- Số lượng tồn ban đầu -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Số lượng tồn kho ban đầu *</label>
                    <input type="number" step="0.01" min="0" name="current_stock" value="{{ old('current_stock', 0) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-extrabold text-blue-600 focus:outline-hidden focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500">
                </div>

                <!-- Ngưỡng tồn kho tối thiểu (Min stock) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ngưỡng tối thiểu (Cảnh báo hết) *</label>
                    <input type="number" step="0.01" min="0" name="min_stock" value="{{ old('min_stock', 10) }}" required
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-extrabold text-amber-600 focus:outline-hidden focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500">
                    <span class="text-[10px] text-slate-400 mt-1 block">Khi tồn kho nhỏ hơn mức này, hệ thống sẽ báo vàng/đỏ cần nhập hàng.</span>
                </div>

                <!-- Giá vốn nhập tham khảo -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Giá vốn nhập dự kiến (VNĐ)</label>
                    <input type="number" step="1" min="0" name="cost_price" value="{{ old('cost_price', 0) }}"
                           class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-extrabold text-emerald-600 focus:outline-hidden focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500">
                </div>

                <!-- Nhà cung cấp / Nguồn nhập ưu tiên -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nguồn Nhập Hàng (Nhà Cung Cấp)</label>
                    <select name="supplier_id" class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">
                        <option value="">-- Chọn đơn vị cung cấp chính --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} (SĐT: {{ $sup->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Ghi chú mặt hàng -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ghi chú thêm về mặt hàng</label>
                <textarea name="notes" rows="3" placeholder="Cách bảo quản, tiêu chuẩn chất lượng khi nhận hàng..."
                          class="w-full px-4 py-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-medium text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#ee4d2d]/30 focus:border-[#ee4d2d]">{{ old('notes') }}</textarea>
            </div>

            <!-- Nút Lưu -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('quanly_kho') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-6 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-[#d73211] text-white text-xs font-black shadow-lg shadow-rose-500/30 transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i> Lưu Mặt Hàng Vào Kho
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

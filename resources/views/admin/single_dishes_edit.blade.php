@extends('layouts.admin')

@section('title', 'Chỉnh sửa Món ăn - FOODDAILY Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Chỉnh Sửa Món Ăn</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Cập nhật thông tin món: <strong class="text-[#ee4d2d]">{{ $dish->dish_name }}</strong></p>
        </div>
        <a href="{{ route('danhmuc_xem', $dish->category_id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Quay lại danh mục
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
                <i class="fas fa-utensils"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Biểu mẫu thay đổi dữ liệu món ăn</h3>
                <p class="text-[11px] text-slate-400 font-medium">Danh mục: <strong class="text-slate-700">{{ $dish->category->category_name ?? 'Chưa phân loại' }}</strong></p>
            </div>
        </div>

        <form action="{{ route('monandon_chinhsua.post', $dish->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Tên món -->
                <div class="space-y-2">
                    <label for="dish_name" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Tên món ăn <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <input type="text" id="dish_name" name="dish_name" value="{{ old('dish_name', $dish->dish_name) }}" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                </div>

                <!-- Phân loại danh mục -->
                <div class="space-y-2">
                    <label for="category_id" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Danh mục phân loại <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <select id="category_id" name="category_id" required class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs bg-white">
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}" {{ $cat->id == $dish->category_id ? 'selected' : '' }}>
                                {{ $cat->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Giá bán -->
                <div class="space-y-2">
                    <label for="price" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Đơn giá bán (VNĐ) <span class="text-[#ee4d2d]">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" step="1000" id="price" name="price" value="{{ old('price', (int)$dish->price) }}" required min="0" class="w-full pl-4 pr-10 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-[#ee4d2d] focus:border-[#ee4d2d] outline-none transition-all shadow-xs">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-extrabold text-slate-400">đ</span>
                    </div>
                </div>

                <!-- Trạng thái phục vụ -->
                <div class="space-y-2">
                    <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                        Trạng thái mở bán
                    </label>
                    <div class="pt-2 flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="is_available" name="is_available" value="1" {{ (session()->hasOldInput() ? old('is_available') : $dish->is_available) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                            <span class="ml-3 text-xs font-bold text-slate-700 peer-checked:text-emerald-600">Đang phục vụ / Mở bán</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Hình ảnh món -->
            <div class="space-y-3 pt-2">
                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Hình ảnh món ăn
                </label>
                <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-xs flex-shrink-0">
                        <img id="dish-preview" src="{{ $dish->image_url ? (\Illuminate\Support\Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url)) : ($dish->image ? asset($dish->image) : asset('logo.jpg')) }}" alt="Dish image" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-2 flex-grow w-full">
                        <input type="file" name="image" accept="image/*" onchange="previewDishImage(this)" class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#ee4d2d] hover:file:bg-rose-100 transition-all cursor-pointer">
                        <p class="text-[10px] text-slate-400 font-medium">Tải lên hình ảnh mới chất lượng cao (JPG, PNG, WEBP tối đa 5MB)</p>
                    </div>
                </div>
            </div>

            <!-- Mô tả món -->
            <div class="space-y-2">
                <label for="description" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                    Mô tả / Hương vị món ăn
                </label>
                <textarea id="description" name="description" rows="3" placeholder="Nhập mô tả hương vị, nguyên liệu đặc trưng của món..." class="w-full p-4 rounded-2xl border border-slate-200 text-xs font-medium text-slate-900 focus:border-[#ee4d2d] outline-none transition-all shadow-xs leading-relaxed">{{ old('description', $dish->description) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('danhmuc_xem', $dish->category_id) }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="px-8 py-3 rounded-2xl bg-[#ee4d2d] hover:bg-red-600 text-white font-extrabold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-2 active:scale-98">
                    <i class="fas fa-save"></i> LƯU THAY ĐỔI MÓN
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function previewDishImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('dish-preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

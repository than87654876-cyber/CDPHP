@extends('layouts.admin')

@section('title', 'Chỉnh sửa Món ăn đơn - FOODDAILY')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa thông tin món ăn đơn lẻ</h1>
    <a href="{{ route('quanly_monandon', ['category_id' => $dish->category_id]) }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-undo fa-sm"></i> Hủy và Quay lại
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger py-2 small col-lg-8">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow mb-4 col-lg-8 text-dark px-0">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu thay đổi dữ liệu món ăn</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('monandon_chinhsua.post', $dish->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="dish_name" class="font-weight-bold text-dark">Tên món ăn <span class="text-danger">*</span></label>
                    <input type="text" class="form-control font-weight-bold" id="dish_name" name="dish_name"
                        value="{{ old('dish_name', $dish->dish_name) }}" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="category_id_display" class="font-weight-bold text-dark">Phân loại thực đơn <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light" id="category_id_display" value="{{ $dish->category->category_name ?? '' }}" readonly>
                    <input type="hidden" name="category_id" value="{{ $dish->category_id }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="price" class="font-weight-bold text-dark">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" step="1000" class="form-control" id="price" name="price" value="{{ old('price', (int)$dish->price) }}" required min="0">
                </div>

                <div class="form-group col-md-6">
                    <label for="is_available" class="font-weight-bold text-dark">Trạng thái phục vụ</label>
                    <div class="form-check pt-2">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" {{ (session()->hasOldInput() ? old('is_available') : $dish->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label text-success font-weight-bold" for="is_available">Đang bán (Available)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="image" class="font-weight-bold text-dark">Thay đổi hình ảnh mới</label>
                    <input type="file" class="form-control-file mb-2" id="image" name="image" accept="image/*">
                    @if($dish->image_url)
                        <small class="text-muted d-block">Ảnh đang dùng hiện tại:</small>
                        <div class="mt-1">
                            <img src="{{ Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url) }}" class="img-thumbnail" style="max-height: 80px;">
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="font-weight-bold text-dark">Mô tả món ăn</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $dish->description) }}</textarea>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-save fa-sm mr-1"></i> Lưu thay đổi
            </button>
            <a href="{{ route('quanly_monandon', ['category_id' => $dish->category_id]) }}" class="btn btn-secondary shadow-sm px-3">Hủy bỏ</a>
        </form>
    </div>
</div>
@endsection

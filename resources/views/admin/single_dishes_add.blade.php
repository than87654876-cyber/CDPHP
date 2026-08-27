@extends('layouts.admin')

@section('title', 'Thêm Món ăn đơn - FOODDAILY')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        @if($selectedCategoryId == 1)
            Thêm món ăn sáng mới
        @elseif($selectedCategoryId == 2)
            Thêm món tráng miệng mới
        @else
            Thêm món ăn đơn mới
        @endif
    </h1>
    <a href="{{ route('quanly_monandon', ['category_id' => $selectedCategoryId]) }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-undo fa-sm"></i> Quay lại
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
        <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu thêm dữ liệu món ăn mới</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('monandon_them.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="dish_name" class="font-weight-bold text-dark">Tên món ăn <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="dish_name" name="dish_name" value="{{ old('dish_name') }}" placeholder="Ví dụ: Phở bò, Cơm chiên..." required>
                </div>

                <div class="form-group col-md-6">
                    <label for="category_id_display" class="font-weight-bold text-dark">Phân loại thực đơn <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light" id="category_id_display" value="{{ $categories->firstWhere('id', $selectedCategoryId)->category_name ?? '' }}" readonly>
                    <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="price" class="font-weight-bold text-dark">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" step="1000" class="form-control" id="price" name="price" value="{{ old('price', '0') }}" required min="0">
                </div>

                <div class="form-group col-md-6">
                    <label for="is_available" class="font-weight-bold text-dark">Trạng thái phục vụ</label>
                    <div class="form-check pt-2">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" {{ (session()->hasOldInput() ? old('is_available') : true) ? 'checked' : '' }}>
                        <label class="form-check-label text-success font-weight-bold" for="is_available">Đang bán (Available)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="image" class="font-weight-bold text-dark">Hình ảnh món ăn</label>
                    <input type="file" class="form-control-file mb-2" id="image" name="image" accept="image/*">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="font-weight-bold text-dark">Mô tả món ăn</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Hương vị, thành phần nguyên liệu chính...">{{ old('description') }}</textarea>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-save fa-sm mr-1"></i> Lưu lại
            </button>
            <a href="{{ route('quanly_monandon', ['category_id' => $selectedCategoryId]) }}" class="btn btn-secondary shadow-sm px-3">Hủy bỏ</a>
        </form>
    </div>
</div>
@endsection

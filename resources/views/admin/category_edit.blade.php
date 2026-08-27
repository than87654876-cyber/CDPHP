@extends('layouts.admin')

@section('title', 'Chỉnh sửa Danh mục - FOODDAILY')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa danh mục món ăn</h1>
    <a href="{{ route('quanly_danhmuc') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-undo"></i> Hủy thay đổi
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
        <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu chỉnh sửa dữ liệu danh mục</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('danhmuc_chinhsua.post', $category->id) }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="category_name" class="font-weight-bold">Tên nhóm danh mục <span class="text-danger">*</span></label>
                <input type="text" class="form-control font-weight-bold" id="category_name"
                    name="category_name" value="{{ old('category_name', $category->category_name) }}" required>
            </div>

            <div class="form-group mb-4">
                <label for="description" class="font-weight-bold">Miêu tả danh mục</label>
                <textarea class="form-control" id="description"
                    name="description" rows="4"
                    placeholder="Nhập mô tả tóm tắt đặc điểm nhóm món này...">{{ old('description', $category->description) }}</textarea>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-save mr-1"></i> Lưu lại thay đổi
            </button>
        </form>
    </div>
</div>
@endsection

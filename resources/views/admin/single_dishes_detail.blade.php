@extends('layouts.admin')

@section('title', 'Chi tiết Món ăn đơn - FOODDAILY')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chi tiết món ăn</h1>
    <a href="{{ route('quanly_monandon') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
    </a>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hình ảnh minh họa</h6>
            </div>
            <div class="card-body text-center">
                @if($dish->image_url)
                    <img src="{{ Str::startsWith($dish->image_url, 'http') ? $dish->image_url : asset($dish->image_url) }}" alt="{{ $dish->dish_name }}" class="img-fluid rounded shadow-sm"
                        style="max-height: 250px; object-fit: cover;">
                @else
                    <div class="py-5 bg-light text-muted rounded">Không có ảnh minh họa</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin món ăn</h6>
                <a href="{{ route('monandon_chinhsua', $dish->id) }}" class="btn btn-sm btn-warning shadow-sm">
                    <i class="fas fa-edit fa-sm"></i> Chỉnh sửa món
                </a>
            </div>
            <div class="card-body">
                <table class="table table-bordered text-dark">
                    <tbody>
                        <tr>
                            <th style="width: 30%">Mã định danh (ID):</th>
                            <td>#{{ $dish->id }}</td>
                        </tr>
                        <tr>
                            <th>Tên món ăn:</th>
                            <td class="font-weight-bold text-primary">{{ $dish->dish_name }}</td>
                        </tr>
                        <tr>
                            <th>Danh mục phân loại:</th>
                            <td>
                                <span class="badge badge-info">{{ $dish->category->category_name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Giá bán:</th>
                            <td class="text-danger font-weight-bold">{{ number_format($dish->price, 0, ',', '.') }} đ</td>
                        </tr>
                        <tr>
                            <th>Trạng thái phục vụ:</th>
                            <td>
                                @if($dish->is_available)
                                    <span class="badge badge-success">Còn món / Đang bán</span>
                                @else
                                    <span class="badge badge-secondary">Tạm ngưng phục vụ</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Mô tả chi tiết:</th>
                            <td>{{ $dish->description ?? 'Không có mô tả chi tiết cho món ăn này.' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

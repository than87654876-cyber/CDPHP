@extends('layouts.admin')

@section('title', 'Thêm chương trình khuyến mãi - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm chương trình khuyến mãi mới</h1>
        <a href="{{ route('quanly_khuyenmai') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show text-dark" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Có lỗi xảy ra:</strong> {{ $errors->first() }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu thêm mới khuyến mãi</h6>
        </div>
        <div class="card-body text-dark">
            <form action="{{ route('khuyenmai_them.post') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="coupon_code" class="font-weight-bold">Mã khuyến mãi (Coupon Code) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}" required placeholder="Ví dụ: SUMMER2026, OFF10">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="discount_type" class="font-weight-bold">Loại giảm giá <span class="text-danger">*</span></label>
                        <select class="form-control" id="discount_type" name="discount_type" required>
                            <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Giảm tiền cố định (đ)</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="discount_value" class="font-weight-bold">Mức giảm giá <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control text-danger font-weight-bold" id="discount_value" name="discount_value" value="{{ old('discount_value', 10000) }}" min="0" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="min_order_value" class="font-weight-bold">Giá trị đơn tối thiểu áp dụng <span class="text-danger">*</span></label>
                        <input type="number" step="1" class="form-control" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', 0) }}" min="0" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="start_date" class="font-weight-bold">Ngày bắt đầu áp dụng <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="end_date" class="font-weight-bold">Ngày kết thúc áp dụng <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="usage_limit" class="font-weight-bold">Giới hạn số lần sử dụng</label>
                        <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Không điền = Vô hạn">
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary shadow-sm px-4 mr-2">
                        <i class="fas fa-save fa-sm mr-1"></i> Tạo khuyến mãi
                    </button>
                    <a href="{{ route('quanly_khuyenmai') }}" class="btn btn-secondary shadow-sm px-3">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
@endsection

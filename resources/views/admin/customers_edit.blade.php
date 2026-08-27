@extends('layouts.admin')

@section('title', 'Chỉnh sửa Khách hàng - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cập nhật hồ sơ khách hàng</h1>
        <a href="{{ route('quanly_khachhang') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-undo fa-sm"></i> Hủy thay đổi
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
            <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu điều chỉnh thông tin thành viên</h6>
        </div>
        <div class="card-body text-dark">
            <form action="{{ route('khachhang_chinhsua.post', $customer->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Mã khách hàng (Cố định)</label>
                        <input type="text" class="form-control bg-light" value="KH-{{ sprintf('%03d', $customer->id) }}" readonly>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="fullname" class="font-weight-bold">Họ và Tên khách hàng <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="{{ old('fullname', $customer->fullname) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="phone" class="font-weight-bold">Số điện thoại liên lạc <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email" class="font-weight-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="points" class="font-weight-bold">Điểm số tích lũy hệ thống</label>
                        <input type="number" class="form-control" id="points" name="points" value="{{ old('points', $customer->points) }}" min="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="membership" class="font-weight-bold">Phân hạng thành viên</label>
                        <select class="form-control font-weight-bold" id="membership" name="membership">
                            <option value="bronze" {{ old('membership', $customer->membership) === 'bronze' ? 'selected' : '' }}>Đồng (Bronze)</option>
                            <option value="silver" {{ old('membership', $customer->membership) === 'silver' ? 'selected' : '' }}>Bạc (Silver)</option>
                            <option value="gold" {{ old('membership', $customer->membership) === 'gold' ? 'selected' : '' }}>Vàng (Gold)</option>
                            <option value="diamond" {{ old('membership', $customer->membership) === 'diamond' ? 'selected' : '' }}>Kim Cương (Diamond)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="font-weight-bold">Ghi chú quản lý</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                </div>

                <hr>
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary shadow-sm px-4 mr-2">
                        <i class="fas fa-save fa-sm mr-1"></i> Lưu thông tin khách hàng
                    </button>
                    <a href="{{ route('quanly_khachhang') }}" class="btn btn-secondary shadow-sm px-3">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

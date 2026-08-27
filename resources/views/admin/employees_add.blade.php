@extends('layouts.admin')

@section('title', 'Thêm nhân viên mới - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thêm nhân viên mới</h1>
        <a href="{{ route('quanly_nhanvien') }}" class="btn btn-sm btn-secondary shadow-sm">
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
            <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu thêm tài khoản nhân viên mới</h6>
        </div>
        <div class="card-body text-dark">
            <form action="{{ route('nhanvien_them.post') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="fullname" class="font-weight-bold">Họ và Tên nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="{{ old('fullname') }}" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="phone" class="font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="Ví dụ: 0912345678">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="email" class="font-weight-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="Ví dụ: name@FOODDAILY.com">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password" class="font-weight-bold">Mật khẩu khởi tạo <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Tối thiểu 6 ký tự">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="role" class="font-weight-bold">Quyền hạn hệ thống <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold" id="role" name="role" required>
                            <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Nhân viên vận hành (Staff)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status" class="font-weight-bold">Trạng thái làm việc <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold" id="status" name="status" required>
                            <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Đang hoạt động (Active)</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Tạm đình chỉ (Suspended)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="font-weight-bold">Chức vụ cụ thể / Ghi chú mô tả công việc</label>
                    <input type="text" class="form-control" id="notes" name="notes" value="{{ old('notes') }}" placeholder="Ví dụ: Thu ngân, Giao hàng, Kế toán kho...">
                </div>

                <hr>
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary shadow-sm px-4 mr-2">
                        <i class="fas fa-save fa-sm mr-1"></i> Tạo tài khoản nhân viên
                    </button>
                    <a href="{{ route('quanly_nhanvien') }}" class="btn btn-secondary shadow-sm px-3">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
@endsection

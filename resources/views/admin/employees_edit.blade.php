@extends('layouts.admin')

@section('title', 'Chỉnh sửa tài khoản nhân viên - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa hồ sơ nhân viên</h1>
        <a href="{{ route('quanly_nhanvien') }}" class="btn btn-sm btn-secondary shadow-sm">
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
            <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu điều chỉnh thông tin nhân viên</h6>
        </div>
        <div class="card-body text-dark">
            <form action="{{ route('nhanvien_chinhsua.post', $employee->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Mã nhân viên (Cố định)</label>
                        <input type="text" class="form-control bg-light" value="NV-{{ sprintf('%03d', $employee->id) }}" readonly>
                    </div>
                    <div class="form-group col-md-8">
                        <label for="fullname" class="font-weight-bold">Họ và Tên nhân viên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="{{ old('fullname', $employee->fullname) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="phone" class="font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="email" class="font-weight-bold">Địa chỉ Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="password" class="font-weight-bold">Mật khẩu mới (Để trống nếu giữ nguyên)</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu mới nếu muốn thay đổi">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="notes" class="font-weight-bold">Chức vụ cụ thể / Ghi chú mô tả công việc</label>
                        <input type="text" class="form-control" id="notes" name="notes" value="{{ old('notes', $employee->notes) }}" placeholder="Ví dụ: Thu ngân, Giao hàng, Kế toán kho...">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="role" class="font-weight-bold">Quyền hạn hệ thống <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold" id="role" name="role" required>
                            <option value="staff" {{ old('role', $employee->role) === 'staff' ? 'selected' : '' }}>Nhân viên vận hành (Staff)</option>
                            <option value="admin" {{ old('role', $employee->role) === 'admin' ? 'selected' : '' }}>Quản trị viên (Admin)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status" class="font-weight-bold">Trạng thái làm việc <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold" id="status" name="status" required>
                            <option value="1" {{ old('status', $employee->status) == '1' ? 'selected' : '' }}>Đang hoạt động (Active)</option>
                            <option value="0" {{ old('status', $employee->status) == '0' ? 'selected' : '' }}>Tạm đình chỉ (Suspended)</option>
                        </select>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-warning text-dark font-weight-bold shadow-sm px-4 mr-2">
                        <i class="fas fa-save fa-sm mr-1"></i> Lưu thông tin nhân viên
                    </button>
                    <a href="{{ route('quanly_nhanvien') }}" class="btn btn-secondary shadow-sm px-3">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

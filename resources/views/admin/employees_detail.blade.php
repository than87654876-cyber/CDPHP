@extends('layouts.admin')

@section('title', 'Chi tiết nhân viên - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông tin chi tiết nhân viên</h1>
        <a href="{{ route('quanly_nhanvien') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thẻ nhân viên</h6>
                </div>
                <div class="card-body text-center text-dark">
                    <div class="mb-3">
                        <i class="fas fa-id-badge fa-7x text-gray-300"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ $employee->fullname }}</h5>
                    <p class="text-muted small mb-2">Mã nhân viên: <span class="font-weight-bold">NV-{{ sprintf('%03d', $employee->id) }}</span></p>
                    
                    @if($employee->role === 'admin')
                    <span class="badge badge-danger px-3 py-2 font-weight-bold">Administrator</span>
                    @else
                    <span class="badge badge-primary px-3 py-2 font-weight-bold">Staff</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin hồ sơ nhân sự</h6>
                    <a href="{{ route('nhanvien_chinhsua', $employee->id) }}" class="btn btn-sm btn-warning shadow-sm fw-bold text-dark">
                        <i class="fas fa-edit fa-sm"></i> Chỉnh sửa & phân quyền
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-dark mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%">Họ và Tên nhân viên:</th>
                                <td class="font-weight-bold text-primary">{{ $employee->fullname }}</td>
                            </tr>
                            <tr>
                                <th>Chức vụ / Mô tả:</th>
                                <td class="font-weight-bold">{{ $employee->notes ?? 'Chưa thiết lập chức vụ' }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại liên lạc:</th>
                                <td>{{ $employee->phone ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ Email:</th>
                                <td>{{ $employee->email }}</td>
                            </tr>
                            <tr>
                                <th>Trạng thái nhân sự:</th>
                                <td>
                                    @if($employee->status)
                                    <span class="badge badge-success px-3 py-1 font-weight-bold">Đang làm việc</span>
                                    @else
                                    <span class="badge badge-warning px-3 py-1 text-dark font-weight-bold">Tạm đình chỉ</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tuyển dụng:</th>
                                <td>{{ $employee->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Chi tiết khách hàng - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thông tin chi tiết hội viên</h1>
        <a href="{{ route('quanly_khachhang') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thẻ khách hàng</h6>
                </div>
                <div class="card-body text-center text-dark">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-7x text-gray-300"></i>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ $customer->fullname }}</h5>
                    <p class="text-muted small mb-2">Mã hội viên: <span class="font-weight-bold">KH-{{ sprintf('%03d', $customer->id) }}</span></p>
                    
                    @if($customer->membership === 'diamond')
                    <span class="badge badge-danger px-3 py-2 font-weight-bold">Kim cương</span>
                    @elseif($customer->membership === 'gold')
                    <span class="badge badge-warning px-3 py-2 text-dark font-weight-bold">Vàng</span>
                    @elseif($customer->membership === 'silver')
                    <span class="badge badge-secondary px-3 py-2 font-weight-bold">Bạc</span>
                    @else
                    <span class="badge badge-light border px-3 py-2 text-dark">Đồng</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin cá nhân & Lịch sử tích điểm</h6>
                    <a href="{{ route('khachhang_chinhsua', $customer->id) }}" class="btn btn-sm btn-warning shadow-sm fw-bold text-dark">
                        <i class="fas fa-edit fa-sm"></i> Thay đổi thông tin
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-dark mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%">Họ và Tên khách hàng:</th>
                                <td class="font-weight-bold text-primary">{{ $customer->fullname }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại liên lạc:</th>
                                <td>{{ $customer->phone ?? 'Chưa cập nhật' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ Email:</th>
                                <td>{{ $customer->email }}</td>
                            </tr>
                            <tr>
                                <th>Tổng điểm tích lũy:</th>
                                <td class="text-success font-weight-bold">{{ number_format($customer->points) }} Điểm</td>
                            </tr>
                            <tr>
                                <th>Ngày đăng ký tài khoản:</th>
                                <td>{{ $customer->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Ghi chú / Nhãn nội bộ:</th>
                                <td>{{ $customer->notes ?? 'Không có ghi chú.' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

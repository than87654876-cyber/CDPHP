@extends('layouts.admin')

@section('title', 'Chi tiết đăng ký gói - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết lịch trình sử dụng gói</h1>
        <a href="{{ route('quanly_goidangky') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-check-circle mr-1"></i> Thành công!</strong> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 text-dark" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-1"></i> Lỗi!</strong> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Thông tin tổng quan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle mr-1"></i>Thông tin tổng quan dịch vụ
            </h6>
            <a href="{{ route('goidangky_chinhsua', $subscription->id) }}" class="btn btn-warning btn-sm font-weight-bold text-dark">
                <i class="fas fa-edit"></i> Chỉnh sửa & phân bổ món
            </a>
        </div>
        <div class="card-body text-dark">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2"><strong>Tên gói dịch vụ:</strong> {{ $subscription->package->package_name }} ({{ $subscription->package->duration_days }} ngày)</p>
                    <p class="mb-2"><strong>Khách hàng:</strong> {{ $subscription->user->fullname }} (SĐT: {{ $subscription->user->phone ?? 'Chưa cập nhật' }})</p>
                    <p class="mb-2"><strong>Mã hóa đơn gốc:</strong> <a href="{{ route('donhang_xem', $subscription->order_id) }}" class="text-danger font-weight-bold">#FDL-{{ $subscription->order_id }}</a></p>
                    <p class="mb-0"><strong>Ghi chú đơn hàng:</strong> {{ $subscription->order->health_notes ?? 'Không có ghi chú' }}</p>
                </div>
                <div class="col-md-6 border-left">
                    <p class="mb-2"><strong>Ngày đăng ký kích hoạt:</strong> {{ $subscription->start_date->format('d/m/Y') }}</p>
                    <p class="mb-2"><strong>Ngày kết thúc dự kiến:</strong> {{ $subscription->end_date->format('d/m/Y') }}</p>
                    <p class="mb-2"><strong>Tiến độ hiện tại:</strong> Ngày thứ <span class="badge badge-primary font-weight-bold text-white px-2 py-1">{{ $subscription->dailySchedules()->where('delivery_status', 'delivered')->count() }} / {{ $subscription->dailySchedules()->count() }}</span></p>
                    <p class="mb-0"><strong>Trạng thái:</strong> 
                        @if($subscription->status === 'active')
                        <span class="badge badge-success font-weight-bold px-2 py-1">Đang hoạt động (Còn {{ $subscription->remaining_days }} ngày)</span>
                        @elseif($subscription->status === 'paused')
                        <span class="badge badge-warning text-dark font-weight-bold px-2 py-1">Tạm dừng (Còn {{ $subscription->remaining_days }} ngày)</span>
                        @elseif($subscription->status === 'expired')
                        <span class="badge badge-secondary font-weight-bold px-2 py-1">Hết hạn</span>
                        @elseif($subscription->status === 'pending')
                        <span class="badge badge-info font-weight-bold px-2 py-1">Chờ thanh toán (Còn {{ $subscription->remaining_days }} ngày)</span>
                        @else
                        <span class="badge badge-danger font-weight-bold px-2 py-1">Đã hủy</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách các món ăn chia theo lịch trình ngày -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-alt mr-1"></i>Nhật ký thực đơn & Trạng thái giao theo ngày
            </h6>
        </div>
        <div class="card-body text-dark">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-dark mb-0">
                    <thead class="bg-gradient-light text-center">
                        <tr>
                            <th style="width: 15%">Ngày thứ (Tiến độ)</th>
                            <th style="width: 20%">Ngày giao dự kiến</th>
                            <th style="width: 40%">Thực đơn chỉ định giao</th>
                            <th style="width: 25%">Trạng thái giao nhận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedules as $index => $sch)
                        <tr class="{{ $sch->delivery_date->isToday() ? 'table-primary font-weight-bold text-primary' : '' }}">
                            <td class="text-center font-weight-bold">Ngày {{ $index + 1 }}</td>
                            <td class="text-center">{{ $sch->delivery_date->format('d/m/Y') }}</td>
                            <td>
                                {{ $sch->dish->dish_name }}
                                @if($sch->delivery_notes)
                                <br><small class="text-muted"><i class="fas fa-comment-dots text-warning mr-1"></i>Ghi chú của khách: {{ $sch->delivery_notes }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($sch->delivery_status === 'delivered')
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>Đã hoàn thành</span>
                                @elseif($sch->delivery_status === 'pending')
                                <span class="badge badge-secondary px-2 py-1">Chưa đến ngày</span>
                                @elseif($sch->delivery_status === 'skipped')
                                <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-ban mr-1"></i>Đã bỏ qua</span>
                                @else
                                <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i>Thất bại</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

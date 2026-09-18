@extends('layouts.admin')

@section('title', 'Chỉnh sửa phân bổ gói - FOODELICIOUS')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Điều chỉnh & Phân bổ gói dịch vụ</h1>
        <a href="{{ route('quanly_goidangky') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-undo"></i> Quay lại
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

    <!-- THÀNH PHẦN 1: ĐIỀU CHỈNH TRẠNG THÁI CHUNG -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-toggle-on mr-1"></i>Trạng thái tổng thể gói dịch vụ</h6>
        </div>
        <div class="card-body text-dark">
            <form action="{{ route('goidangky_chinhsua.post', $subscription->id) }}" method="POST" id="subUpdateForm">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Trạng thái hoạt động</label>
                        <select class="form-control font-weight-bold" name="status">
                            <option value="pending" {{ $subscription->status === 'pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                            <option value="active" {{ $subscription->status === 'active' ? 'selected' : '' }}>Đang chạy dịch vụ (Active)</option>
                            <option value="paused" {{ $subscription->status === 'paused' ? 'selected' : '' }}>Tạm dừng giao (Paused)</option>
                            <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>Hủy ngang gói dịch vụ (Cancelled)</option>
                            <option value="expired" {{ $subscription->status === 'expired' ? 'selected' : '' }}>Gói đã hết hạn (Expired)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Trạng thái tài chính đơn gốc</label>
                        <select class="form-control font-weight-bold" name="payment_status">
                            <option value="paid" {{ ($subscription->order && $subscription->order->payment_status === 'paid') ? 'selected' : '' }}>Đã tất toán toàn bộ hóa đơn (Paid)</option>
                            <option value="pending" {{ ($subscription->order && $subscription->order->payment_status === 'pending') ? 'selected' : '' }}>Đang chờ thanh toán (Pending)</option>
                            <option value="failed" {{ ($subscription->order && $subscription->order->payment_status === 'failed') ? 'selected' : '' }}>Thanh toán thất bại (Failed)</option>
                            <option value="refunded" {{ ($subscription->order && $subscription->order->payment_status === 'refunded') ? 'selected' : '' }}>Đã hoàn tiền (Refunded)</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4 d-flex align-items-end">
                        <!-- Nút trigger tạo vận đơn hôm nay gửi POST riêng biệt -->
                        <button type="button" class="btn btn-success w-100 shadow-sm font-weight-bold" onclick="event.preventDefault(); document.getElementById('dailyOrderForm').submit();">
                            <i class="fas fa-shipping-fast mr-1"></i> Tạo vận đơn hôm nay
                        </button>
                    </div>
                </div>
        </div>
    </div>

    <!-- THÀNH PHẦN 2: THAY ĐỔI THỰC ĐƠN TỪNG NGÀY THEO YÊU CẦU CỦA KHÁCH -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-utensils mr-1"></i>Thay đổi chi tiết món ăn theo lịch trình</h6>
        </div>
        <div class="card-body text-dark">
            <p class="text-muted small">* Chỉ những ngày có trạng thái "Chưa giao" mới được phép chỉnh sửa món ăn.</p>

            @foreach($schedules as $index => $sch)
                @php
                    $deliveryDateFormatted = \Carbon\Carbon::parse($sch->delivery_date)->format('d/m/Y');
                @endphp
                @if(!$sch->is_locked && $sch->delivery_status === 'pending')
                <div class="row align-items-center bg-light p-2 rounded mb-3 border-left-primary">
                    <div class="col-md-2 font-weight-bold text-center">Ngày thứ {{ $index + 1 }}<br><small class="text-muted">({{ $deliveryDateFormatted }})</small></div>
                    <div class="col-md-7">
                        <label class="small font-weight-bold mb-1">Món ăn chỉ định giao:</label>
                        <input type="text" class="form-control form-control-sm font-weight-bold text-primary" name="menu_day[{{ $index + 1 }}]" value="{{ $sch->dish->dish_name ?? '' }}">
                    </div>
                    <div class="col-md-3 text-center">
                        <span class="badge badge-secondary py-2 px-3">Cho phép sửa</span>
                    </div>
                </div>
                @else
                <div class="row align-items-center bg-light p-2 rounded mb-3 border-left-secondary text-muted">
                    <div class="col-md-2 font-weight-bold text-center">Ngày thứ {{ $index + 1 }}<br><small class="text-muted">({{ $deliveryDateFormatted }})</small></div>
                    <div class="col-md-7">
                        <label class="small font-weight-bold mb-1">Món ăn đã giao (Khóa):</label>
                        <input type="text" class="form-control form-control-sm bg-white" value="{{ $sch->dish->dish_name ?? '' }}" disabled>
                    </div>
                    <div class="col-md-3 text-center">
                        <span class="badge badge-light border py-2 px-3 text-dark">Đã khóa</span>
                    </div>
                </div>
                @endif
            @endforeach

            <hr>
            <button type="submit" class="btn btn-primary shadow-sm px-4" onclick="document.getElementById('subUpdateForm').submit();">
                <i class="fas fa-save fa-sm mr-1"></i> Lưu lại toàn bộ cấu hình mới
            </button>
            </form>
        </div>
    </div>

    <!-- Hidden form for Daily Order Trigger -->
    <form action="{{ route('goidangky_tao_don', $subscription->id) }}" method="POST" id="dailyOrderForm" style="display: none;">
        @csrf
    </form>
@endsection

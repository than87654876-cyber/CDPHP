@extends('layouts.admin')

@section('title', 'Chỉnh sửa đơn hàng - FOODDAILY')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">Cập nhật tiến trình đơn hàng #FDL-{{ $order->id }}</h1>
        <a href="{{ route('quanly_donhang') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-undo fa-sm"></i> Hủy và Quay lại
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Biểu mẫu điều phối trạng thái đơn vận</h6>
        </div>
        <div class="card-body text-dark">
            <form action="{{ route('donhang_chinhsua.post', $order->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Mã số hóa đơn</label>
                        <input type="text" class="form-control bg-light" value="#FDL-{{ $order->id }}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Khách hàng đặt hàng</label>
                        <input type="text" class="form-control bg-light" value="{{ $order->user->fullname ?? 'Khách vãng lai' }}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-bold">Tổng tiền hóa đơn</label>
                        <input type="text" class="form-control bg-light text-danger font-weight-bold" value="{{ number_format($order->final_amount, 0, ',', '.') }} đ" readonly>
                    </div>
                </div>

                <hr class="my-3">

                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="order_status" class="font-weight-bold text-primary">
                            <i class="fas fa-tasks mr-1"></i>Trạng thái đơn hàng
                        </label>
                        <select class="form-control border-left-primary font-weight-bold" id="order_status" name="order_status">
                            <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Chờ xác nhận đơn (Pending)</option>
                            <option value="confirmed" {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>Đã xác nhận đơn (Confirmed)</option>
                            <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }}>Đang chuẩn bị món (Preparing)</option>
                            <option value="delivering" {{ $order->order_status === 'delivering' ? 'selected' : '' }}>Xác nhận giao - Đang giao hàng (Delivering)</option>
                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Đã hoàn thành - Thành công (Completed)</option>
                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }} class="text-danger">Hủy đơn hàng (Cancelled)</option>
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="payment_status" class="font-weight-bold text-success">
                            <i class="fas fa-money-check-alt mr-1"></i>Trạng thái thanh toán dòng tiền
                        </label>
                        <select class="form-control border-left-success font-weight-bold" id="payment_status" name="payment_status">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Xác nhận đã thanh toán thành công (Paid)</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại (Failed)</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }} class="text-secondary">Đã xử lý hoàn tiền (Refunded)</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="form-group col-md-12">
                        <label for="admin_notes" class="font-weight-bold">Ghi chú điều phối / Lý do hủy đơn (Nếu có)</label>
                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Nhập ghi chú cho nhân viên bếp hoặc shipper liên tỉnh nếu cần..."></textarea>
                    </div>
                </div>

                <hr class="my-4">
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="fas fa-save fa-sm mr-1"></i> Cập nhật trạng thái đơn
                </button>
            </form>
        </div>
    </div>
@endsection

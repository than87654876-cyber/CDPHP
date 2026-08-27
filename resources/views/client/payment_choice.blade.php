@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng - FOODDAILY')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="card-title mb-4">Thanh toán đơn hàng #FDL-{{ $order->id }}</h3>
                    <p class="text-muted">Chọn phương thức thanh toán và xác nhận giao dịch.</p>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Tổng tiền cần thanh toán</span>
                            <span class="fs-4 text-danger fw-bold">{{ number_format($order->final_amount, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="border rounded p-3 bg-light">
                            <div><strong>Phương thức hiện tại:</strong> {{ $order->payment_method === 'cash' ? 'Tiền mặt khi nhận' : ($order->payment_method === 'bank_transfer' ? 'Chuyển khoản ATM/VietQR' : 'Ví điện tử MoMo') }}</div>
                            <div class="small text-secondary mt-1">Điện thoại: {{ $order->user->phone ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <form action="{{ route('muahang.thanhtoan.select', $order->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <h5 class="fw-bold">Chọn lại phương thức thanh toán</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_cash" value="cash" {{ $order->payment_method === 'cash' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pay_cash">Tiền mặt khi nhận (COD)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_atm" value="bank_transfer" {{ $order->payment_method === 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label" for="pay_atm">Chuyển khoản ATM/VietQR</label>
                            </div>
                            <div class="form-check">
                                <!-- MoMo option removed: only bank transfer and cash available -->
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="submit" class="btn btn-primary">Lưu lựa chọn</button>
                        </div>
                    </form>

                    <div class="border-top pt-4">
                        <h5 class="fw-bold">Hướng dẫn thanh toán tiền mặt</h5>
                        <p class="small text-secondary">Nếu bạn chọn thanh toán tiền mặt, shipper sẽ thu tiền khi giao hàng. Đơn hàng sẽ được giữ ở trạng thái chờ và chỉ được xác nhận thanh toán khi shipper cập nhật hoàn tất.</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('giohang') }}" class="btn btn-secondary">Quay lại giỏ hàng</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

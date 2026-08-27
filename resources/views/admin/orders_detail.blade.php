@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng - FOODDAILY')

@section('content')
    @php
        $notes = $order->health_notes;
        $reqAmountText = null;
        if (preg_match('/Số tiền hoàn lại: ([^,\]]+)/', $notes, $matches)) {
            $reqAmountText = $matches[1];
        } elseif (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $notes, $matches)) {
            $reqAmountText = $matches[1];
        }
        
        $imageLink = '';
        if (preg_match('/Hình ảnh minh chứng: ([^,\]]+)/', $notes, $matches)) {
            $imageLink = trim($matches[1]);
            if ($imageLink === 'Không có') {
                $imageLink = '';
            }
        }
        
        $reasonText = '';
        if (preg_match('/Lý do: ([^,\]]+)/', $notes, $matches)) {
            $reasonText = $matches[1];
        }

        $methodText = '';
        if (preg_match('/Phương thức: ([^,\]]+)/', $notes, $matches)) {
            $methodVal = trim($matches[1]);
            if ($methodVal === 'bank') $methodText = 'Ngân hàng';
            elseif ($methodVal === 'momo') $methodText = 'MoMo';
            elseif ($methodVal === 'cash') $methodText = 'Tiền mặt';
            else $methodText = $methodVal;
        }
    @endphp
    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">Chi tiết hóa đơn #FDL-{{ $order->id }}</h1>
        <a href="{{ route('quanly_donhang') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-truck mr-2"></i>Thông tin giao nhận & Thanh toán
                    </h6>
                </div>
                <div class="card-body text-dark">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 35%">Khách hàng đặt:</th>
                                <td>{{ $order->user->fullname ?? 'Khách vãng lai' }} (Mã thành viên: KH-{{ $order->user_id ?? 'N/A' }})</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại:</th>
                                <td class="font-weight-bold">{{ $order->user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Địa chỉ giao hàng & Ghi chú:</th>
                                <td style="white-space: pre-line;">{{ $order->health_notes }}</td>
                            </tr>
                            <tr>
                                <th>Phương thức thanh toán:</th>
                                <td>
                                    @php
                                        $paymentMethodText = 'COD (Tiền mặt)';
                                        if ($order->payment_method === 'bank_transfer') {
                                            $paymentMethodText = 'Chuyển khoản ATM';
                                        } elseif ($order->payment_method === 'momo') {
                                            $paymentMethodText = 'Ví điện tử MoMo';
                                        }
                                    @endphp
                                    <span class="badge badge-primary">{{ $paymentMethodText }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày đặt hàng:</th>
                                <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-receipt mr-2"></i>Sản phẩm thực tế trong đơn
                    </h6>
                    <a href="{{ route('donhang_chinhsua', $order->id) }}" class="btn btn-sm btn-warning shadow-sm">
                        <i class="fas fa-edit fa-sm"></i> Sửa trạng thái
                    </a>
                </div>
                <div class="card-body text-dark">
                    <table class="table table-striped table-bordered mb-3">
                        <thead class="bg-light">
                            <tr>
                                <th>Tên món ăn</th>
                                <th>SL</th>
                                <th>Đơn giá lẻ</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>{{ $item->dish->dish_name ?? 'Món ăn không tồn tại' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                    <td class="font-weight-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-3 bg-light rounded text-right">
                        <div class="mb-1">Trạng thái xử lý: 
                            @if($order->order_status === 'pending')
                                <span class="badge bg-secondary text-white font-weight-bold">Chờ xác nhận</span>
                            @elseif($order->order_status === 'confirmed')
                                <span class="badge bg-info text-white font-weight-bold">Đã xác nhận</span>
                            @elseif($order->order_status === 'preparing')
                                <span class="badge bg-warning text-dark font-weight-bold">Đang chuẩn bị</span>
                            @elseif($order->order_status === 'delivering')
                                <span class="badge bg-primary text-white font-weight-bold">Đang giao hàng</span>
                            @elseif($order->order_status === 'completed')
                                <span class="badge bg-success text-white font-weight-bold">Đã hoàn thành</span>
                            @elseif($order->order_status === 'cancelled')
                                <span class="badge bg-danger text-white font-weight-bold">Đã hủy</span>
                            @endif
                        </div>
                        <div class="mb-2">Trạng thái dòng tiền: 
                            @if($order->payment_status === 'pending')
                                <span class="badge bg-warning text-dark font-weight-bold">Chờ thanh toán</span>
                            @elseif($order->payment_status === 'paid')
                                <span class="badge bg-success text-white font-weight-bold">Đã nhận tiền</span>
                            @elseif($order->payment_status === 'failed')
                                <span class="badge bg-danger text-white font-weight-bold">Thất bại</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="badge bg-dark text-white font-weight-bold">Đã hoàn tiền</span>
                            @endif
                        </div>
                        <h4 class="font-weight-bold text-danger mb-0">Tổng cộng: {{ number_format($order->final_amount, 0, ',', '.') }} đ</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phân hệ Hoàn tiền từ Admin -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-undo mr-2"></i>Thông tin & Nghiệp vụ Hoàn tiền
                    </h6>
                </div>
                <div class="card-body text-dark">
                    @if($order->payment_status === 'refunded')
                        <!-- Nếu đơn hàng ĐÃ ĐƯỢC HOÀN TIỀN -->
                        <div class="row align-items-center">
                            <div class="col-md-6 border-right">
                                <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-check-circle mr-1"></i>Đơn hàng đã được xử lý hoàn tiền</h6>
                                <p class="mb-2"><strong>Số tiền đã hoàn:</strong> <span class="text-danger font-weight-bold">{{ $reqAmountText ?? number_format($order->final_amount, 0, ',', '.') . ' đ' }}</span></p>
                                <p class="mb-2"><strong>Phương thức hoàn trả:</strong> {{ $methodText ?: 'N/A' }}</p>
                                <p class="mb-2"><strong>Lý do hoàn trả tiền:</strong> {{ $reasonText ?: 'Khác' }}</p>
                            </div>
                            <div class="col-md-6 text-center">
                                @if($imageLink)
                                    <div class="font-weight-bold mb-2">Ảnh minh chứng giao dịch hoàn tiền:</div>
                                    <a href="{{ $imageLink }}" target="_blank">
                                        <img src="{{ $imageLink }}" class="img-thumbnail" style="max-height: 180px; object-fit: contain;">
                                    </a>
                                @else
                                    <div class="text-muted font-italic">Không có hình ảnh minh chứng giao dịch.</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Nếu đơn hàng CHƯA ĐƯỢC HOÀN TIỀN -->
                        <div class="p-3 bg-gradient-light rounded border mb-3">
                            <h6 class="font-weight-bold mb-2"><i class="fas fa-info-circle mr-1"></i>Quy trình hoàn trả tài chính</h6>
                            <p class="mb-0 small text-muted">
                                Quản trị viên sử dụng công cụ dưới đây để ghi nhận giao dịch hoàn tiền thực tế cho khách hàng. Đơn hàng sau khi hoàn tiền sẽ tự động chuyển trạng thái dòng tiền sang <strong>Đã hoàn tiền (Refunded)</strong> và trạng thái xử lý đơn sang <strong>Đã hủy (Cancelled)</strong>.
                            </p>
                        </div>
                        
                        <form action="{{ route('donhang_refund', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 form-group mb-3">
                                    <label for="refund_amount" class="font-weight-bold small">Số tiền hoàn trả (đ) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control font-weight-bold text-danger" id="refund_amount" name="refund_amount" value="{{ $order->final_amount }}" max="{{ $order->final_amount }}" min="0" required>
                                    <small class="text-muted">Nhập số tiền hoàn trả (tối đa bằng tổng tiền đơn).</small>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="refund_method" class="font-weight-bold small">Phương thức hoàn tiền <span class="text-danger">*</span></label>
                                    <select class="form-control" id="refund_method" name="refund_method" required>
                                        <option value="bank" selected>Chuyển khoản Ngân hàng</option>
                                        <option value="cash">Tiền mặt</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group mb-3">
                                    <label for="refund_image" class="font-weight-bold small">Ảnh biên lai / hóa đơn chuyển khoản hoàn trả</label>
                                    <input type="file" class="form-control-file" id="refund_image" name="refund_image" accept="image/*">
                                    <small class="text-muted">Hỗ trợ định dạng hình ảnh: png, jpg, jpeg (tối đa 2MB).</small>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="refund_reason" class="font-weight-bold small">Lý do hoàn trả tiền <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="refund_reason" name="refund_reason" rows="2" placeholder="Nhập lý do chi tiết hoàn tiền..." required></textarea>
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="btn btn-danger font-weight-bold px-4 shadow-sm" onclick="return confirm('Xác nhận thực hiện hoàn tiền này cho đơn hàng #FDL-{{ $order->id }}?')">
                                    <i class="fas fa-check-double mr-1"></i>Xác nhận & Cập nhật Hoàn tiền
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

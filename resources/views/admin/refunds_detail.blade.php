@extends('layouts.admin')

@section('title', 'Thẩm định đơn hoàn tiền - FOODDAILY')

@section('content')
    @php
        $notes = $order->health_notes;
        
        $reason = '';
        if (preg_match('/Lý do: ([^,\]]+)/', $notes, $matches)) {
            $reason = $matches[1];
        }
        
        if ($reason === 'wrong_dish') {
            $reasonText = 'Giao sai món ăn / Nhầm lẫn thực đơn';
        } elseif ($reason === 'damaged_food') {
            $reasonText = 'Thực phẩm biến chất, rơi đổ do vận chuyển';
        } elseif ($reason === 'not_delivered') {
            $reasonText = 'Tài xế không giao hàng nhưng bấm hoàn thành';
        } else {
            $reasonText = $reason ?: 'Khác';
        }

        $method = 'bank';
        if (preg_match('/Phương thức: ([^, \]]+)/', $notes, $matches)) {
            $method = $matches[1];
        }

        $detail = '';
        if (preg_match('/Chi tiết: ([^\]]+)/', $notes, $matches)) {
            $detail = $matches[1];
        }

        // bank details
        $bankName = '';
        if (preg_match('/Ngân hàng: ([^,]+)/', $notes, $matches)) { $bankName = $matches[1]; }
        $bankAccount = '';
        if (preg_match('/STK: ([^,]+)/', $notes, $matches)) { $bankAccount = $matches[1]; }
        $bankUser = '';
        if (preg_match('/Chủ tài khoản: ([^\)]+)/', $notes, $matches)) { $bankUser = $matches[1]; }

        // momo details
        $momoPhone = '';
        if (preg_match('/SĐT MoMo: ([^,]+)/', $notes, $matches)) { $momoPhone = $matches[1]; }
        $momoUser = '';
        if (preg_match('/Chủ tài khoản MoMo: ([^\)]+)/', $notes, $matches)) { $momoUser = $matches[1]; }

        // Parse custom requested amount if present
        $reqAmount = null;
        if (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $notes, $matches)) {
            $reqAmount = $matches[1];
        } elseif (preg_match('/Số tiền hoàn lại ước tính: ([^,\]]+)/', $notes, $matches)) {
            $reqAmount = $matches[1];
        }

        // Parse custom requested image proof if present
        $imageLink = '';
        if (preg_match('/Hình ảnh minh chứng: ([^,\]]+)/', $notes, $matches)) {
            $imageLink = trim($matches[1]);
            if ($imageLink === 'Không có') {
                $imageLink = '';
            }
        }
    @endphp

    <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-3">
        <h1 class="h3 mb-0 text-gray-800">Hồ sơ kiểm định hoàn tiền chi tiết</h1>
        <a href="{{ route('quanly_yeucauhoan') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <!-- Cột hiển thị Lý do & Nội dung khiếu nại sự cố -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i>Nội dung khiếu nại đơn hàng
                    </h6>
                </div>
                <div class="card-body text-dark">
                    <p class="mb-2"><strong>Mã đơn hàng gốc liên kết:</strong> <a href="{{ route('donhang_xem', $order->id) }}"
                            class="font-weight-bold text-underline">#FDL-{{ $order->id }}</a></p>
                    <p class="mb-2"><strong>Khách hàng gửi đơn:</strong> {{ $order->user->fullname ?? 'Khách vãng lai' }} (Mã số: KH-{{ $order->user_id ?? 'N/A' }})</p>
                    <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
                    <p class="mb-3"><strong>Ngày gửi yêu cầu lên hệ thống:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}</p>

                    <div class="border-top pt-3">
                        <div class="text-muted small font-weight-bold mb-1">Lý do hoàn tiền phân loại:</div>
                        <h6 class="font-weight-bold text-danger"><i
                                class="fas fa-exclamation-circle me-1"></i>{{ $reasonText }}</h6>

                        <div class="text-muted small font-weight-bold mt-3 mb-1">Miêu tả chi tiết từ khách hàng:</div>
                        <div class="p-3 bg-light rounded text-dark font-italic mb-3">
                            "{{ $detail }}"
                        </div>

                        @if($imageLink)
                            <div class="text-muted small font-weight-bold mb-1">Hình ảnh minh chứng từ khách:</div>
                            <div class="p-2 bg-light rounded text-center border">
                                <a href="{{ $imageLink }}" target="_blank">
                                    <img src="{{ $imageLink }}" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột hiển thị Phương thức nhận tiền và Form thao tác xử lý của Admin -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-money-check-alt me-1"></i>Tài khoản đích nhận tiền hoàn
                        </h6>
                    </div>
                    <div class="card-body text-dark">
                        @if($method === 'momo')
                            <!-- MẪU HIỂN THỊ 1: NẾU KHÁCH CHỌN PHƯƠNG THỨC VÍ MOMO -->
                            <div class="p-3 rounded mb-3 border-left-info"
                                style="background-color: #fbf0f6; border-left: 5px solid #A50064 !important;">
                                <h6 class="font-weight-bold mb-2" style="color: #A50064;">
                                    <i class="fas fa-wallet me-1"></i>Phương thức chọn: Ví điện tử MoMo
                                </h6>
                                <p class="mb-1 small"><strong>Số điện thoại đăng ký ví MoMo:</strong> {{ $momoPhone }}</p>
                                <p class="mb-0 small"><strong>Tên chủ tài khoản ví:</strong> {{ $momoUser }}</p>
                            </div>
                        @else
                            <!-- MẪU HIỂN THỊ 2: NẾU KHÁCH CHỌN NGÂN HÀNG NỘI ĐỊA -->
                            <div class="p-3 bg-light rounded border-left-primary">
                                <h6 class="font-weight-bold text-primary mb-2">
                                    <i class="fas fa-university me-1"></i>Phương thức chọn: Ngân hàng nội địa
                                </h6>
                                <p class="mb-1 small"><strong>Tên ngân hàng:</strong> {{ $bankName }}</p>
                                <p class="mb-1 small"><strong>Số tài khoản ngân hàng:</strong> {{ $bankAccount }}</p>
                                <p class="mb-0 small"><strong>Tên chủ thẻ thụ hưởng:</strong> {{ $bankUser }}</p>
                            </div>
                        @endif

                        @if($bankName && $bankAccount)
                            <div class="mt-3 p-3 bg-white rounded border text-center">
                                <div class="fw-bold mb-2 text-danger small"><i class="fas fa-qrcode mr-1"></i>Quét mã QR để hoàn tiền nhanh:</div>
                                @php
                                    // Clean amount: remove non-digit characters
                                    $cleanAmount = $reqAmount ? preg_replace('/[^\d]/', '', $reqAmount) : $order->final_amount;
                                    // If cleanAmount is less than 1000 (meaning it is a minor currency like 49.95), convert it to mock VND
                                    if ($cleanAmount < 1000) {
                                        $cleanAmount = $cleanAmount * 100;
                                    }
                                    $qrUrl = "https://img.vietqr.io/image/" . urlencode(trim($bankName)) . "-" . urlencode(trim($bankAccount)) . "-compact2.jpg?amount=" . (int)$cleanAmount . "&addInfo=" . urlencode("Hoan tien FDL-" . $order->id) . "&accountName=" . urlencode(trim($bankUser));
                                @endphp
                                <img src="{{ $qrUrl }}" alt="Mã QR hoàn tiền" class="img-fluid rounded border shadow-sm" style="max-height: 220px; object-fit: contain;">
                                <div class="text-muted small mt-1 font-italic">Chủ tài khoản: {{ $bankUser }}</div>
                            </div>
                        @endif

                        <div class="mt-3 p-3 bg-gradient-light rounded border text-right text-dark">
                            @if($reqAmount)
                                <div class="text-muted small">Tổng giá trị đơn hàng gốc:</div>
                                <h5 class="font-weight-bold text-secondary mb-2">{{ number_format($order->final_amount, 0, ',', '.') }} đ</h5>
                                <div class="text-muted small">Số tiền khách hàng yêu cầu hoàn trả:</div>
                                <h3 class="font-weight-bold text-danger mb-0">
                                    @if(is_numeric($reqAmount))
                                        {{ number_format($reqAmount, 0, ',', '.') }} đ
                                    @else
                                        {{ $reqAmount }}
                                    @endif
                                </h3>
                            @else
                                <div class="text-muted small">Tổng giá trị thụ hưởng hoàn trả (Yêu cầu hoàn toàn bộ):</div>
                                <h3 class="font-weight-bold text-danger mb-0">{{ number_format($order->final_amount, 0, ',', '.') }} đ</h3>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- FORM THAO TÁC XÁC NHẬN HOÀN TIỀN CỦA QUẢN TRỊ VIÊN -->
                <div class="card-footer bg-white border-top-0 p-4">
                    <form action="{{ route('yeucauhoan_duyet', $order->id) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="action" class="font-weight-bold text-dark small">
                                <i class="fas fa-gavel me-1"></i>Quyết định phê duyệt hồ sơ:
                            </label>
                            <select class="form-control font-weight-bold text-primary" id="action" name="action" required>
                                <option value="approve" class="text-success">Phê duyệt khiếu nại - Xác nhận đã hoàn tiền</option>
                                <option value="reject" class="text-danger">Từ chối khiếu nại hoàn tiền</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="admin_response" class="font-weight-bold text-dark small">Ghi chú phản hồi cho khách hàng:</label>
                            <textarea class="form-control text-dark" id="admin_response" name="admin_response" rows="2"
                                placeholder="Hệ thống đã thực hiện hoàn tiền thành công..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm py-2">
                            <i class="fas fa-save"></i> Cập nhật & Đóng hồ sơ khiếu nại
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

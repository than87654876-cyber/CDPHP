<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Thanh toán Đơn hàng - FOODDAILY</title>

    <link href="{{ asset('logo.jpg') }}" rel="icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">

    <link href="{{ asset('client/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/css/main.css') }}" rel="stylesheet">

    <style>
        .momo-brand-color {
            color: #A50064;
        }

        .bank-brand-color {
            color: #004B87;
        }

        .nav-pills .nav-link {
            border: 2px solid #dee2e6;
            color: #495057;
            background-color: #fff;
            font-weight: bold;
            transition: all 0.2s ease-in-out;
        }

        .nav-pills .nav-link.active.tab-momo {
            background-color: #A50064 !important;
            border-color: #A50064 !important;
            color: white !important;
        }

        .nav-pills .nav-link.active.tab-bank {
            background-color: #004B87 !important;
            border-color: #004B87 !important;
            color: white !important;
        }

        .qr-box {
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            max-width: 280px;
            margin: 0 auto;
        }

        .qr-box.box-momo {
            border: 2px solid #A50064;
        }

        .qr-box.box-bank {
            border: 2px solid #004B87;
        }

        .countdown-timer {
            font-size: 22px;
            font-weight: bold;
            color: #ce1126;
            background: #fff5f5;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
        }
    </style>
</head>

<body class="bg-light text-dark">

    <header id="header" class="header d-flex align-items-center sticky-top bg-white shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('trangchu') }}" class="logo d-flex align-items-center me-auto" style="text-decoration: none;">
                <img src="{{ asset('logo.jpg') }}" alt="">
                <h1 class="sitename text-danger m-0" style="font-size: 28px;">FOODDAILY</h1>
            </a>
            <span class="navbar-text fw-bold text-muted"><i class="bi bi-shield-check text-success"></i> Hệ thống thanh toán mã QR giả lập</span>
        </div>
    </header>

    <main class="container my-5">
        @php
            $displayAmount = $amount < 1000 ? $amount * 100 : $amount;
        @endphp
        <div class="row justify-content-center">

            <div class="col-lg-6 col-md-10 mb-4">
                <div class="card shadow border-0 p-4 text-center">
                    <h5 class="fw-bold mb-4">Chọn phương thức quét mã QR</h5>

                    <div>
                        <div class="text-start bg-white p-3 rounded border mb-3 text-dark mx-auto" style="max-width: 320px;">
                            <div class="qr-box box-bank shadow-sm mb-3">
                                <img src="https://img.vietqr.io/image/BIDV-8899408675-compact2.jpg?amount={{ $displayAmount }}&addInfo=FDL-{{ $order_id }}&accountName=Tran%20Le%20Than"
                                    alt="Mã VietQR" class="img-fluid">
                            </div>
                            
                            <div class="row small mb-1">
                                <div class="row small mb-1">
                                    <div class="col-5 text-muted">Ngân hàng:</div>
                                    <div class="col-7 fw-bold">BIDV</div>
                                </div>
                                <div class="row small mb-1">
                                    <div class="col-5 text-muted">Chi nhánh:</div>
                                    <div class="col-7 fw-bold">CN TP Hồ Chí Minh</div>
                                </div>
                                <div class="row small mb-1">
                                    <div class="col-5 text-muted">Số tài khoản:</div>
                                    <div class="col-7 fw-bold text-primary">8899408675</div>
                                </div>
                                <div class="row small mb-1">
                                    <div class="col-5 text-muted">Chủ tài khoản:</div>
                                    <div class="col-7 fw-bold">TRAN LE THAN</div>
                                </div>
                                <div class="row small mb-1">
                                    <div class="col-5 text-muted">Số tiền:</div>
                                    <div class="col-7 fw-bold text-danger">{{ number_format($displayAmount, 0, ',', '.') }} đ</div>
                                </div>
                                <div class="row small mb-1">
                                    <div class="col-5 text-muted">Nội dung CK:</div>
                                    <div class="col-7 fw-bold text-success">FDL-{{ $order_id }}</div>
                                </div>
                            </div>

                        <a href="https://img.vietqr.io/image/BIDV-8899408675-compact2.jpg?amount={{ $displayAmount }}&addInfo=FDL-{{ $order_id }}&accountName=Tran%20Le%20Than" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary fw-bold mb-3 d-inline-block w-100" 
                           style="max-width: 320px;">
                            <i class="bi bi-qr-code-scan me-1"></i> Liên kết chuyển khoản nhanh VietQR
                        </a>

                        <button type="button" 
                                id="btn-confirm-payment" 
                                class="btn btn-success fw-bold mb-3 w-100" 
                                style="max-width: 320px;">
                            <i class="bi bi-check-circle-fill me-1"></i> Xác nhận đã chuyển khoản thành công
                        </button>

                        <p class="small text-muted mb-3"><i class="bi bi-phone-vibrate me-1"></i> Sử dụng
                            <strong>App Ngân hàng (Mobile Banking)</strong> để quét mã hoặc sử dụng thông tin trên để chuyển khoản.</p>
                    </div>

                    <div class="mb-2 mt-2 small text-secondary">Thời gian giữ mã giao dịch còn lại:</div>
                    <div class="countdown-timer mb-2 shadow-sm"><i class="bi bi-clock-history me-2"></i><span
                            id="timer">10:00</span></div>
                    <div id="payment-status" class="alert alert-info small mb-0">
                        <i class="bi bi-hourglass-split me-2"></i>⏳ Đang chờ xác nhận thanh toán...
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-10">
                <div class="card shadow border-0 p-4 text-dark">
                    <h5 class="fw-bold border-bottom pb-2 text-dark"><i class="bi bi-receipt me-2 text-danger"></i>Thông tin đơn hàng</h5>

                    <div class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Mã đơn hàng:</span>
                        <strong class="text-dark">#FDL-{{ $order_id }}</strong>
                    </div>
                    <div class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Nhà cung cấp:</span>
                        <span class="fw-bold">Hệ thống FOODDAILY</span>
                    </div>
                    <div class="py-2 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Số tiền cần thanh toán:</span>
                        <span class="fs-4 fw-bold text-danger">{{ number_format($displayAmount, 0, ',', '.') }} đ</span>
                    </div>

                    <div class="alert alert-warning small mt-3 border-0">
                        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Lưu ý đối soát hệ thống:</h6>
                        Mã QR đã tích hợp sẵn số tiền **{{ number_format($displayAmount, 0, ',', '.') }} đ** và nội dung chuyển khoản tự động. Vui lòng không tự ý thay đổi thông tin để đơn hàng được duyệt ngay lập tức.
                    </div>

                    <div class="mt-4 pt-2 border-top">
                        <div class="alert alert-info text-start mb-3">
                            <div class="fw-bold mb-2"><i class="bi bi-hourglass-split me-2"></i>Đang chờ hệ thống xác nhận giao dịch...</div>
                            <div class="small">Sau khi ngân hàng xác nhận giao dịch thành công, đơn hàng sẽ tự động được cập nhật.</div>
                        </div>
                        <a href="{{ route('trangchu_dangnhap') }}" class="btn btn-outline-secondary w-100 py-2.5 btn-sm">Quay lại trang chủ</a>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            var interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    display.textContent = "Hết hạn";
                    document.getElementById('payment-status').className = 'alert alert-danger small mb-0';
                    document.getElementById('payment-status').innerHTML = '<i class="bi bi-x-circle me-2"></i>❌ Mã QR đã hết hạn. Vui lòng tạo giao dịch mới.';
                }
            }, 1000);
        }

        function showSuccessModal() {
            const modal = document.createElement('div');
            modal.className = 'modal fade show';
            modal.style.display = 'block';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-3">
                        <div class="modal-body">
                            <div class="display-6 text-success mb-3"><i class="bi bi-check-circle-fill"></i></div>
                            <h5 class="fw-bold">✅ Thanh toán thành công</h5>
                            <p class="mb-2">Đơn hàng của bạn đã được thanh toán.</p>
                            <p class="mb-3">Mã đơn hàng: #FDL-{{ $order_id }}</p>
                            <p class="text-muted">Cảm ơn bạn đã sử dụng dịch vụ.</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('giohang') }}" class="btn btn-success">Xem đơn hàng</a>
                                <a href="{{ route('trangchu') }}" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
                            </div>
                        </div>
                    </div>
                </div>`;
            document.body.appendChild(modal);
            setTimeout(() => {
                window.location.href = '{{ route('giohang') }}';
            }, 3000);
        }

        window.onload = function () {
            var tenMinutes = 60 * 10,
                display = document.querySelector('#timer');
            startTimer(tenMinutes, display);

            const poll = () => {
                fetch('{{ route('api.orders.payment-status', ['id' => $order_id]) }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.payment_status === 'paid') {
                            document.getElementById('payment-status').className = 'alert alert-success small mb-0';
                            document.getElementById('payment-status').innerHTML = '<i class="bi bi-check-circle me-2"></i>✅ Thanh toán thành công';
                            showSuccessModal();
                            return;
                        }
                    })
                    .catch(() => {});
            };

            setInterval(poll, 4000);
            poll();

            document.getElementById('btn-confirm-payment').addEventListener('click', function() {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang xử lý...';
                
                fetch('{{ route('api.payments.bank-transfer.notify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: {{ $order_id }},
                        amount: {{ $displayAmount }},
                        content: 'FDL-{{ $order_id }}',
                        status: 'success',
                        transaction_id: 'MOCK_TX_' + Date.now(),
                        bank_reference: 'MOCK_REF_' + Date.now(),
                        bank_code: 'BIDV',
                        paid_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success) {
                        document.getElementById('payment-status').className = 'alert alert-success small mb-0';
                        document.getElementById('payment-status').innerHTML = '<i class="bi bi-check-circle me-2"></i>✅ Thanh toán thành công';
                        showSuccessModal();
                    } else {
                        alert('Lỗi xác nhận: ' + (data.message || 'Không thể xác nhận giao dịch.'));
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Xác nhận đã chuyển khoản thành công';
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi kết nối đến máy chủ.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Xác nhận đã chuyển khoản thành công';
                });
            });
        };
    </script>

    <script src="{{ asset('client/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Yêu cầu hoàn tiền - FOODDAILY</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <link href="logo.jpg" rel="icon">
    <link href="client/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Amatic+SC:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">

    <link href="client/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="client/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="client/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="client/assets/css/main.css" rel="stylesheet">

    <style>
        .cart-container {
            min-height: 70vh;
            padding: 40px 0;
        }

        .search-box {
            margin-bottom: 30px;
        }

        .search-box input {
            border-radius: 5px;
            border: 2px solid #ce1126;
            padding: 10px 15px;
        }

        .search-box button {
            background-color: #ce1126;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-box button:hover {
            background-color: #a00d20;
        }

        .refund-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s;
        }

        .refund-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .refund-header {
            border-bottom: 1px solid #f4f4f4;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .order-id {
            color: #ce1126;
            font-weight: bold;
            font-size: 16px;
        }

        .refund-date {
            color: #666;
            font-size: 14px;
        }

        .detail-item {
            font-size: 14px;
            padding: 4px 0;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #ce1126;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-btn:hover {
            color: #a00d20;
        }

        .section-title {
            color: #ce1126;
            margin-bottom: 30px;
            border-bottom: 3px solid #ce1126;
            padding-bottom: 15px;
        }
    </style>
</head>

<body class="index-page">
    <header id="header" class="header d-flex align-items-center sticky-top">
        <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="{{ route('trangchu') }}" class="logo d-flex align-items-center me-auto me-xl-0">
                <img src="{{ isset($settings['logo_url']) ? (\Illuminate\Support\Str::startsWith($settings['logo_url'], 'http') ? $settings['logo_url'] : asset($settings['logo_url'])) : asset('logo.jpg') }}" alt="" class="setting-logo-img">
                <h1 class="sitename">FOODDAILY</h1><span>.</span>
            </a>
    </header>

    <main class="main">
        <div class="cart-container">
            <div class="container">
                <a href="{{ route('trangchu_dangnhap') }}" class="back-btn"><i class="bi bi-arrow-left"></i> Quay lại
                    cửa hàng</a>
                <h1 class="section-title"><i class="bi bi-arrow-counterclockwise"></i> Danh sách yêu cầu hoàn tiền</h1>

                <!-- Search Box -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="search-box">
                            <form action="#" class="d-flex gap-2">
                                <input type="text" class="form-control"
                                    placeholder="Tìm kiếm theo mã đơn hàng hoặc nội dung...">
                                <button type="button" class="btn"><i class="bi bi-search"></i> Tìm</button>
                            </form>
                        </div>
                    </div>
                </div>

                @forelse($orders as $order)
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

                        $methodText = 'Chuyển khoản ATM';
                        if ($method === 'momo') {
                            $momoPhone = '';
                            if (preg_match('/SĐT MoMo: ([^,]+)/', $notes, $matches)) { $momoPhone = $matches[1]; }
                            $methodText = 'Ví điện tử MoMo (' . $momoPhone . ')';
                        } else {
                            $bankName = '';
                            if (preg_match('/Ngân hàng: ([^,]+)/', $notes, $matches)) { $bankName = $matches[1]; }
                            $methodText = 'Chuyển khoản Ngân hàng ' . $bankName;
                        }

                        // Parse Admin Response if any
                        $adminResponse = '';
                        if (preg_match('/\[Admin Phản hồi: ([^\]\)]+)/', $notes, $matches)) {
                            $adminResponse = $matches[1];
                        }

                        $reqAmountText = number_format($order->final_amount, 0, ',', '.') . ' đ';
                        if (preg_match('/Số tiền yêu cầu: ([^,\]]+)/', $notes, $matches)) {
                            $reqAmountText = $matches[1];
                        }
                        
                        $imageLink = '';
                        if (preg_match('/Hình ảnh minh chứng: ([^,\]]+)/', $notes, $matches)) {
                            $imageLink = trim($matches[1]);
                            if ($imageLink === 'Không có') {
                                $imageLink = '';
                            }
                        }
                    @endphp
                    <div class="refund-card text-dark">
                        <div class="refund-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="order-id"><i class="bi bi-receipt me-1"></i> Mã đơn hàng: #FDL-{{ $order->id }}</div>
                                    <div class="refund-date mt-1"><i class="bi bi-calendar-event me-1"></i> Ngày yêu cầu: {{ $order->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    @if($order->payment_status === 'refunded')
                                        <span class="badge bg-success shadow-sm"><i class="bi bi-check-circle-fill me-1"></i> Đã hoàn tiền</span>
                                    @elseif(strpos($order->health_notes, '[Admin Phản hồi:') !== false)
                                        <span class="badge bg-danger shadow-sm"><i class="bi bi-x-circle-fill me-1"></i> Từ chối</span>
                                    @else
                                        <span class="badge bg-warning text-dark shadow-sm"><i class="bi bi-clock-history me-1"></i> Đang chờ xác nhận</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="refund-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item"><strong>Số tiền yêu cầu:</strong> <span class="text-danger fw-bold">{{ $reqAmountText }}</span></div>
                                    <div class="detail-item"><strong>Lý do:</strong> {{ $reasonText }}</div>
                                    @if($imageLink)
                                        <div class="mt-2 small">
                                            <strong>Hình ảnh minh chứng:</strong><br>
                                            <a href="{{ $imageLink }}" target="_blank">
                                                <img src="{{ $imageLink }}" class="img-thumbnail mt-1" style="max-height: 80px; object-fit: cover;">
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item"><strong>Nhận tiền qua:</strong> {{ $methodText }}</div>
                                    <div class="detail-item text-muted font-italic"><strong>Ghi chú sự cố:</strong> "{{ $detail }}"</div>
                                    @if($adminResponse)
                                        <div class="detail-item text-primary font-weight-bold"><strong>Phản hồi từ Admin:</strong> "{{ $adminResponse }}"</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded border text-muted">
                        <i class="bi bi-journal-x fs-1"></i>
                        <p class="mt-2 mb-0">Bạn chưa gửi yêu cầu hoàn tiền nào.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer id="footer" class="footer dark-background">
        <div class="container copyright text-center mt-4">
            <p>© <span>2026</span> <strong class="px-1">FOODDAILY</strong>. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>

    <script src="client/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="client/assets/vendor/aos/aos.js"></script>
    
    <!-- Real-time settings and logo polling -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentLogoUrl = "{{ isset($settings['logo_url']) ? (\Illuminate\Support\Str::startsWith($settings['logo_url'], 'http') ? $settings['logo_url'] : asset($settings['logo_url'])) : asset('logo.jpg') }}";
            
            function pollRefundsSettings() {
                fetch("{{ route('api.settings.poll') }}")
                    .then(response => response.json())
                    .then(data => {
                        const newLogo = data.settings.logo_url;
                        if (newLogo && newLogo !== currentLogoUrl) {
                            currentLogoUrl = newLogo;
                            document.querySelectorAll('.setting-logo-img').forEach(img => {
                                img.src = newLogo;
                            });
                        }
                    })
                    .catch(err => console.error('Error polling settings in refunds view:', err));
            }
            setInterval(pollRefundsSettings, 2000);
        });
    </script>
</body>

</html>

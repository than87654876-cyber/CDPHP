<?php
error_reporting(E_ALL & ~E_DEPRECATED);

$fontPath = 'C:/Windows/Fonts/arialbd.ttf';
$fontRegular = 'C:/Windows/Fonts/arial.ttf';

function drawRoundedBox($img, $x, $y, $w, $h, $radius, $fillColor, $borderColor = null) {
    imagefilledrectangle($img, (int)($x + $radius), (int)$y, (int)($x + $w - $radius), (int)($y + $h), $fillColor);
    imagefilledrectangle($img, (int)$x, (int)($y + $radius), (int)($x + $w), (int)($y + $h - $radius), $fillColor);
    imagefilledellipse($img, (int)($x + $radius), (int)($y + $radius), (int)($radius * 2), (int)($radius * 2), $fillColor);
    imagefilledellipse($img, (int)($x + $w - $radius), (int)($y + $radius), (int)($radius * 2), (int)($radius * 2), $fillColor);
    imagefilledellipse($img, (int)($x + $radius), (int)($y + $h - $radius), (int)($radius * 2), (int)($radius * 2), $fillColor);
    imagefilledellipse($img, (int)($x + $w - $radius), (int)($y + $h - $radius), (int)($radius * 2), (int)($radius * 2), $fillColor);

    if ($borderColor) {
        imagerectangle($img, (int)$x, (int)$y, (int)($x + $w), (int)($y + $h), $borderColor);
    }
}

function generateRoleDiagram($filename, $rootTitle, $captionText, $modules, $colorsList) {
    global $fontPath, $fontRegular;
    $width = 1800;
    $height = 720;

    $image = imagecreatetruecolor($width, $height);
    imageantialias($image, true);

    $bg = imagecolorallocate($image, 248, 250, 252);
    $white = imagecolorallocate($image, 255, 255, 255);
    $border = imagecolorallocate($image, 226, 232, 240);
    $lineColor = imagecolorallocate($image, 148, 163, 184);
    $darkText = imagecolorallocate($image, 30, 41, 59);
    $rootBg = imagecolorallocate($image, 30, 58, 138);

    imagefilledrectangle($image, 0, 0, $width, $height, $bg);

    // Root
    $rootW = 750;
    $rootH = 70;
    $rootX = (int)(($width - $rootW) / 2);
    $rootY = 40;
    drawRoundedBox($image, $rootX, $rootY, $rootW, $rootH, 15, $rootBg);
    $bbox = imagettfbbox(17, 0, $fontPath, $rootTitle);
    $textW = $bbox[2] - $bbox[0];
    imagettftext($image, 17, 0, (int)($rootX + ($rootW - $textW) / 2), $rootY + 44, $white, $fontPath, $rootTitle);

    $numCols = count($modules);
    $cardW = ($numCols == 4) ? 405 : 330;
    $cardH = 430;
    $gapX = ($numCols == 4) ? 35 : 25;
    $startX = (int)(($width - ($cardW * $numCols + $gapX * ($numCols - 1))) / 2);
    $rowY = 190;
    $centerLineY = 145;

    imagesetthickness($image, 3);
    imageline($image, (int)($width / 2), $rootY + $rootH, (int)($width / 2), $centerLineY, $lineColor);
    imageline($image, (int)($startX + $cardW / 2), $centerLineY, (int)($startX + ($numCols - 1) * ($cardW + $gapX) + $cardW / 2), $centerLineY, $lineColor);

    for ($i = 0; $i < $numCols; $i++) {
        $x = $startX + $i * ($cardW + $gapX);
        $y = $rowY;

        imageline($image, (int)($x + $cardW / 2), $centerLineY, (int)($x + $cardW / 2), $y, $lineColor);

        $headerCol = imagecolorallocate($image, $colorsList[$i][0], $colorsList[$i][1], $colorsList[$i][2]);
        $subBg = imagecolorallocate($image, $colorsList[$i][3], $colorsList[$i][4], $colorsList[$i][5]);

        drawRoundedBox($image, $x, $y, $cardW, $cardH, 12, $white, $border);
        drawRoundedBox($image, $x, $y, $cardW, 56, 12, $headerCol);
        imagefilledrectangle($image, (int)$x, (int)($y + 44), (int)($x + $cardW), (int)($y + 56), $headerCol);

        imagettftext($image, 13, 0, $x + 16, $y + 36, $white, $fontPath, $modules[$i]['title']);

        $itemY = $y + 95;
        foreach ($modules[$i]['items'] as $item) {
            drawRoundedBox($image, $x + 12, $itemY - 22, $cardW - 24, 48, 6, $subBg);
            imagettftext($image, 11, 0, $x + 20, $itemY + 6, $darkText, $fontRegular, $item);
            $itemY += 62;
        }
    }

    $bboxCap = imagettfbbox(14, 0, $fontPath, $captionText);
    $capW = $bboxCap[2] - $bboxCap[0];
    imagettftext($image, 14, 0, (int)(($width - $capW) / 2), $height - 25, $darkText, $fontPath, $captionText);

    imagepng($image, $filename, 9);
    imagedestroy($image);
    echo "Generated: $filename (" . filesize($filename) . " bytes)\n";
}

// 2. Nhân viên quầy
generateRoleDiagram(
    'docs/images/so_do_chuc_nang_nhan_vien.png',
    'HỆ THỐNG BÀN LÀM VIỆC NHÂN VIÊN THU NGÂN (STAFF WORKSPACE)',
    'Sơ đồ 2.2: Sơ đồ chức năng của Nhân viên quầy / Thu ngân – FOODDAILY',
    [
        [
            'title' => '1. Bàn làm việc Workspace',
            'items' => [
                '• Xem danh sách đơn nấu xong từ Bếp',
                '• Giám sát trạng thái đơn hàng realtime',
                '• Nhận thông báo âm thanh khi có đơn mới',
                '• Quản lý danh sách bàn / khu vực'
            ]
        ],
        [
            'title' => '2. In hóa đơn thanh toán',
            'items' => [
                '• Thao tác In bill 1 chạm (Phím tắt F9)',
                '• Xuất hóa đơn nhiệt 80mm chuẩn POS',
                '• Thể hiện chi tiết món & ghi chú riêng',
                '• Xác nhận trạng thái VietQR hoặc COD'
            ]
        ],
        [
            'title' => '3. Đóng gói & Bàn giao',
            'items' => [
                '• Đối soát món ăn thực tế với hóa đơn',
                '• Dán niêm phong túi và kẹp bill tính tiền',
                '• Bàn giao túi đồ ăn cho đơn vị vận chuyển',
                '• Chuyển trạng thái đơn sang Delivering'
            ]
        ],
        [
            'title' => '4. Theo dõi & Cảnh báo kho',
            'items' => [
                '• Xem danh mục nguyên liệu tồn kho',
                '• Nhận cảnh báo nguyên liệu chạm ngưỡng đỏ',
                '• Báo cáo quản lý bổ sung nguyên liệu gấp',
                '• Tạm khóa món ăn hết nguyên liệu đột xuất'
            ]
        ]
    ],
    [
        [37, 99, 235, 239, 246, 255],
        [5, 150, 105, 236, 253, 245],
        [217, 119, 6, 255, 251, 235],
        [124, 58, 237, 245, 243, 255]
    ]
);

// 3. Đầu bếp
generateRoleDiagram(
    'docs/images/so_do_chuc_nang_dau_bep.png',
    'MÀN HÌNH BẾP ĐIỀU PHỐI CHẾ BIẾN FIFO (KITCHEN DISPLAY SYSTEM)',
    'Sơ đồ 2.3: Sơ đồ chức năng của Đầu bếp / Nhân viên Bếp – FOODDAILY',
    [
        [
            'title' => '1. Hàng đợi chế biến FIFO',
            'items' => [
                '• Xếp thứ tự ưu tiên theo giờ đặt hàng',
                '• Chuông báo âm thanh khi có đơn mới',
                '• Thẻ đơn đổi màu cảnh báo thời gian chờ',
                '• Xem chi tiết dặn dò khẩu vị của khách'
            ]
        ],
        [
            'title' => '2. Bảng gom số lượng (Batching)',
            'items' => [
                '• Tự động gom tổng số lượng từng món',
                '• Tối ưu hóa nấu hàng loạt cùng 1 mẻ',
                '• Tiết kiệm 60% thời gian chế biến món',
                '• Giảm áp lực nhà bếp vào giờ cao điểm'
            ]
        ],
        [
            'title' => '3. Điều phối trạng thái nấu',
            'items' => [
                '• Chạm nút "Bắt đầu nấu" -> Preparing',
                '• Chế biến theo đúng công thức chuẩn F&B',
                '• Chạm nút "Đã nấu xong" -> Cooked',
                '• Tự động đẩy đơn sang quầy thu ngân đóng gói'
            ]
        ],
        [
            'title' => '4. Báo cáo nguyên liệu bếp',
            'items' => [
                '• Kiểm soát lượng suất ăn còn lại trong ngày',
                '• Báo quầy tắt món khi hết nguyên liệu tươi',
                '• Thống kê số lượng phần ăn đã chế biến',
                '• Đảm bảo vệ sinh an toàn thực phẩm'
            ]
        ]
    ],
    [
        [225, 29, 72, 255, 241, 242],
        [14, 165, 233, 240, 249, 255],
        [13, 148, 136, 240, 253, 250],
        [217, 119, 6, 255, 251, 235]
    ]
);

// 4. Khách hàng
generateRoleDiagram(
    'docs/images/so_do_chuc_nang_khach_hang.png',
    'PHÂN HỆ NGƯỜI DÙNG & ĐẶT MÓN TRỰC TUYẾN (CUSTOMER)',
    'Sơ đồ 2.4: Sơ đồ chức năng của Khách hàng – FOODDAILY',
    [
        [
            'title' => '1. Khám phá & AI',
            'items' => [
                '• Duyệt thực đơn theo danh mục',
                '• Tìm kiếm & Bộ lọc nâng cao',
                '• Vòng quay "Hôm nay ăn gì?"',
                '• Hỏi Trợ lý AI Gemini tư vấn'
            ]
        ],
        [
            'title' => '2. Đặt nhóm Realtime',
            'items' => [
                '• Tạo phòng cấp PIN & QR Code',
                '• Mời đồng nghiệp tham gia',
                '• Đồng bộ giỏ hàng thời gian thực',
                '• Bảng chia tiền minh bạch'
            ]
        ],
        [
            'title' => '3. Thanh toán VietQR',
            'items' => [
                '• Nhập thông tin & chọn địa chỉ',
                '• Áp mã giảm giá từ Ví Voucher',
                '• Quét mã VietQR PayOS 1-2 giây',
                '• Thanh toán tiền mặt COD'
            ]
        ],
        [
            'title' => '4. Gói ăn định kỳ',
            'items' => [
                '• Đăng ký gói tuần / tháng',
                '• Chọn thực đơn đổi món hằng ngày',
                '• Chọn khung giờ nhận ăn cố định',
                '• Tạm dừng / Gia hạn gói ăn'
            ]
        ],
        [
            'title' => '5. Tra cứu & Đánh giá',
            'items' => [
                '• Tra cứu đơn 5 bước Realtime',
                '• Nhận hàng tận tay từ Shipper',
                '• Tích lũy điểm thưởng Loyalty',
                '• Đánh giá sao & Nhận xét món'
            ]
        ]
    ],
    [
        [37, 99, 235, 239, 246, 255],
        [124, 58, 237, 245, 243, 255],
        [5, 150, 105, 236, 253, 245],
        [217, 119, 6, 255, 251, 235],
        [13, 148, 136, 240, 253, 250]
    ]
);

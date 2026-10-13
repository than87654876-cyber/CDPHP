<?php
error_reporting(E_ALL & ~E_DEPRECATED);

$fontPath = 'C:/Windows/Fonts/timesbd.ttf';
$fontRegular = 'C:/Windows/Fonts/times.ttf';
if (!file_exists($fontPath)) {
    $fontPath = 'C:/Windows/Fonts/arialbd.ttf';
    $fontRegular = 'C:/Windows/Fonts/arial.ttf';
}

function wrapTextToLines($text, $maxChars = 14) {
    $words = explode(' ', trim($text));
    $lines = [];
    $currentLine = '';

    foreach ($words as $word) {
        if ($currentLine === '') {
            $currentLine = $word;
        } else if (mb_strlen($currentLine . ' ' . $word) <= $maxChars) {
            $currentLine .= ' ' . $word;
        } else {
            $lines[] = $currentLine;
            $currentLine = $word;
        }
    }
    if ($currentLine !== '') {
        $lines[] = $currentLine;
    }
    return $lines;
}

function drawAcademicBox($img, $x, $y, $w, $h, $text, $isBold = false, $fontSize = 11) {
    global $fontPath, $fontRegular;
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    $font = $isBold ? $fontPath : $fontRegular;

    imagefilledrectangle($img, (int)$x, (int)$y, (int)($x + $w), (int)($y + $h), $white);
    imagesetthickness($img, 2);
    imagerectangle($img, (int)$x, (int)$y, (int)($x + $w), (int)($y + $h), $black);

    $lines = wrapTextToLines($text, 14);
    $lineHeight = (int)($fontSize * 1.45);
    $totalTextH = count($lines) * $lineHeight;
    $startY = $y + (int)(($h - $totalTextH) / 2) + $fontSize;

    foreach ($lines as $i => $line) {
        $bbox = imagettfbbox($fontSize, 0, $font, $line);
        $textW = $bbox[2] - $bbox[0];
        $textX = $x + (int)(($w - $textW) / 2);
        $textY = $startY + $i * $lineHeight;
        imagettftext($img, $fontSize, 0, (int)$textX, (int)$textY, $black, $font, $line);
    }
}

function generateAcademicBFD($outPath, $rootTitle, $columns, $options = []) {
    $colW = $options['colW'] ?? 145;
    $boxH = $options['boxH'] ?? 54;
    $gapX = $options['gapX'] ?? 24;
    $gapY = $options['gapY'] ?? 16;
    $margin = $options['margin'] ?? 36;

    $numCols = count($columns);
    $totalColsW = $numCols * $colW + ($numCols - 1) * $gapX;
    $width = $totalColsW + $margin * 2 + 60;

    $maxChildren = 0;
    foreach ($columns as $col) {
        $count = count($col['items']);
        if ($count > $maxChildren) $maxChildren = $count;
    }

    $rootH = 50;
    $rootW = 160;
    $busY = $margin + $rootH + 35;
    $parentY = $busY + 35;
    $childrenStartY = $parentY + $boxH + $gapY;

    $height = $childrenStartY + $maxChildren * ($boxH + $gapY) + $margin + 20;

    $image = imagecreatetruecolor($width, $height);
    imageantialias($image, true);

    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);

    imagefilledrectangle($image, 0, 0, $width, $height, $white);
    imagesetthickness($image, 2);
    imagerectangle($image, (int)($margin / 2), (int)($margin / 2), (int)($width - $margin / 2), (int)($height - $margin / 2), $black);

    // Root
    $rootX = (int)(($width - $rootW) / 2);
    $rootY = $margin + 10;
    drawAcademicBox($image, $rootX, $rootY, $rootW, $rootH, $rootTitle, true, 14);

    // Vertical line to bus
    imagesetthickness($image, 2);
    imageline($image, (int)($width / 2), $rootY + $rootH, (int)($width / 2), $busY, $black);

    // Horizontal Bus
    $firstColCenterX = $margin + 30 + (int)($colW / 2);
    $lastColCenterX = $margin + 30 + ($numCols - 1) * ($colW + $gapX) + (int)($colW / 2);
    imageline($image, $firstColCenterX, $busY, $lastColCenterX, $busY, $black);

    // Columns
    for ($i = 0; $i < $numCols; $i++) {
        $col = $columns[$i];
        $colX = $margin + 30 + $i * ($colW + $gapX);
        $centerX = $colX + (int)($colW / 2);

        imageline($image, $centerX, $busY, $centerX, $parentY, $black);
        $isStandalone = empty($col['items']);
        drawAcademicBox($image, $colX, $parentY, $colW, $boxH, $col['title'], true, 11);

        if (!$isStandalone) {
            $currY = $parentY + $boxH;
            foreach ($col['items'] as $j => $itemText) {
                $childY = $childrenStartY + $j * ($boxH + $gapY);
                imageline($image, $centerX, $currY, $centerX, $childY, $black);
                drawAcademicBox($image, $colX, $childY, $colW, $boxH, $itemText, false, 10);
                $currY = $childY + $boxH;
            }
        }
    }

    imagepng($image, $outPath, 9);
    imagedestroy($image);
    echo "Generated: $outPath ({$width}x{$height})\n";
}

// 1. ADMIN
$adminCols = [
    ['title' => 'Đăng nhập', 'items' => []],
    ['title' => 'Đăng xuất', 'items' => []],
    ['title' => 'Đổi mật khẩu', 'items' => []],
    [
        'title' => 'Quản lý món ăn',
        'items' => [
            'Thêm món ăn mới',
            'Cập nhật món ăn',
            'Xóa món ăn',
            'Cập nhật giá bán',
            'Cập nhật trạng thái',
            'Tìm kiếm món ăn'
        ]
    ],
    [
        'title' => 'Quản lý đơn hàng',
        'items' => [
            'Xem danh sách đơn',
            'Cập nhật trạng thái',
            'Hủy đơn đặt hàng',
            'Đối soát VietQR',
            'Xuất đơn đặt hàng',
            'Tìm kiếm đơn hàng'
        ]
    ],
    [
        'title' => 'Quản lý khách hàng',
        'items' => [
            'Xem hồ sơ khách',
            'Cập nhật thông tin',
            'Quản lý điểm tích lũy',
            'Khóa / Mở tài khoản',
            'Tìm kiếm khách hàng'
        ]
    ],
    [
        'title' => 'Quản lý kho',
        'items' => [
            'Thêm nguyên liệu',
            'Cập nhật nguyên liệu',
            'Lập phiếu nhập kho',
            'Cảnh báo tồn tối thiểu',
            'Tìm kiếm nguyên liệu'
        ]
    ],
    [
        'title' => 'Quản lý nhà cung cấp',
        'items' => [
            'Thêm nhà cung cấp',
            'Cập nhật thông tin',
            'Xóa nhà cung cấp',
            'Tìm kiếm nhà cung cấp'
        ]
    ],
    [
        'title' => 'Quản lý voucher',
        'items' => [
            'Tạo mã voucher',
            'Cập nhật voucher',
            'Phát voucher vào ví',
            'Xóa mã voucher',
            'Tìm kiếm voucher'
        ]
    ],
    [
        'title' => 'Gói ăn định kỳ',
        'items' => [
            'Tạo gói dịch vụ ăn',
            'Cập nhật gói ăn',
            'Điều phối lịch giao',
            'Tìm kiếm gói ăn'
        ]
    ],
    [
        'title' => 'Thống kê báo cáo',
        'items' => [
            'Thống kê doanh thu',
            'Thống kê đơn hàng',
            'Top món bán chạy',
            'Xuất báo cáo Excel'
        ]
    ]
];
generateAcademicBFD('docs/images/so_do_chuc_nang_admin.png', 'Admin', $adminCols);

// 2. NHÂN VIÊN QUẦY
$staffCols = [
    ['title' => 'Đăng nhập', 'items' => []],
    ['title' => 'Đăng xuất', 'items' => []],
    ['title' => 'Đổi mật khẩu', 'items' => []],
    [
        'title' => 'Bàn làm việc Workspace',
        'items' => [
            'Xem đơn nấu xong',
            'Giám sát tiến độ đơn',
            'Tìm kiếm đơn hàng'
        ]
    ],
    [
        'title' => 'In hóa đơn thanh toán',
        'items' => [
            'In bill 1 chạm (F9)',
            'Xuất hóa đơn 80mm',
            'Kiểm tra tiền VietQR',
            'Kiểm tra tiền COD'
        ]
    ],
    [
        'title' => 'Đóng gói & Giao vận',
        'items' => [
            'Đối soát món thực tế',
            'Bàn giao cho Shipper',
            'Cập nhật trạng thái'
        ]
    ],
    [
        'title' => 'Giám sát kho',
        'items' => [
            'Xem tồn kho nguyên liệu',
            'Xem cảnh báo đỏ',
            'Báo quản lý nhập hàng'
        ]
    ]
];
generateAcademicBFD('docs/images/so_do_chuc_nang_nhan_vien.png', 'Nhân viên quầy', $staffCols);

// 3. ĐẦU BẾP
$chefCols = [
    ['title' => 'Đăng nhập', 'items' => []],
    ['title' => 'Đăng xuất', 'items' => []],
    ['title' => 'Đổi mật khẩu', 'items' => []],
    [
        'title' => 'Hàng đợi Bếp FIFO',
        'items' => [
            'Xem đơn theo thứ tự',
            'Xem cảnh báo đổi màu',
            'Xem ghi chú khẩu vị'
        ]
    ],
    [
        'title' => 'Gom món nấu (Batching)',
        'items' => [
            'Xem bảng gộp số lượng',
            'Nấu hàng loạt cùng mẻ'
        ]
    ],
    [
        'title' => 'Điều phối nấu',
        'items' => [
            'Cập nhật Bắt đầu nấu',
            'Cập nhật Đã nấu xong',
            'Chuyển đơn sang quầy'
        ]
    ],
    [
        'title' => 'Báo cáo nguyên liệu',
        'items' => [
            'Kiểm tra suất ăn ngày',
            'Báo hết nguyên liệu món'
        ]
    ]
];
generateAcademicBFD('docs/images/so_do_chuc_nang_dau_bep.png', 'Đầu bếp', $chefCols);

// 4. KHÁCH HÀNG
$custCols = [
    ['title' => 'Đăng ký tài khoản', 'items' => []],
    ['title' => 'Đăng nhập', 'items' => []],
    ['title' => 'Đổi mật khẩu', 'items' => []],
    [
        'title' => 'Xem thực đơn & Chọn món',
        'items' => [
            'Duyệt danh mục món',
            'Tìm kiếm & Lọc giá',
            'Vòng quay Hôm nay ăn gì',
            'Hỏi Trợ lý AI Gemini'
        ]
    ],
    [
        'title' => 'Đặt nhóm Realtime',
        'items' => [
            'Tạo phòng cấp PIN / QR',
            'Mời thành viên vào phòng',
            'Tự chọn món máy riêng',
            'Đồng bộ giỏ hàng nhóm',
            'Xem bảng chia chi phí'
        ]
    ],
    [
        'title' => 'Giỏ hàng & Thanh toán',
        'items' => [
            'Quản lý giỏ hàng',
            'Áp dụng voucher ví',
            'Quét mã VietQR PayOS',
            'Thanh toán tiền mặt COD'
        ]
    ],
    [
        'title' => 'Gói ăn định kỳ',
        'items' => [
            'Đăng ký gói tuần / tháng',
            'Chọn thực đơn đổi món',
            'Chọn khung giờ giao'
        ]
    ],
    [
        'title' => 'Tra cứu & Đánh giá',
        'items' => [
            'Tra cứu đơn Realtime',
            'Tích điểm thành viên',
            'Đánh giá số sao món ăn'
        ]
    ]
];
generateAcademicBFD('docs/images/so_do_chuc_nang_khach_hang.png', 'Khách hàng', $custCols);

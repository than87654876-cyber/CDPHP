<?php
error_reporting(E_ALL & ~E_DEPRECATED);

$fontPath = 'C:/Windows/Fonts/timesbd.ttf';
$fontRegular = 'C:/Windows/Fonts/times.ttf';
if (!file_exists($fontPath)) {
    $fontPath = 'C:/Windows/Fonts/arialbd.ttf';
    $fontRegular = 'C:/Windows/Fonts/arial.ttf';
}

/**
 * Helper to wrap text into multiple lines based on max character width
 */
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

/**
 * Draw a crisp academic box with centered multi-line text
 */
function drawAcademicBox($img, $x, $y, $w, $h, $text, $isBold = false, $fontSize = 11) {
    global $fontPath, $fontRegular;
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    $font = $isBold ? $fontPath : $fontRegular;

    // Fill white
    imagefilledrectangle($img, (int)$x, (int)$y, (int)($x + $w), (int)($y + $h), $white);
    // Draw black border
    imagesetthickness($img, 2);
    imagerectangle($img, (int)$x, (int)$y, (int)($x + $w), (int)($y + $h), $black);

    // Wrap text
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

/**
 * Build the Academic BFD diagram
 */
function generateAcademicBFD($outPath, $rootTitle, $columns, $options = []) {
    global $fontPath;

    $colW = $options['colW'] ?? 140;
    $boxH = $options['boxH'] ?? 55;
    $gapX = $options['gapX'] ?? 24;
    $gapY = $options['gapY'] ?? 16;
    $margin = $options['margin'] ?? 40;

    $numCols = count($columns);
    $totalColsW = $numCols * $colW + ($numCols - 1) * $gapX;
    $width = $totalColsW + $margin * 2 + 80;

    // Find max children in any column
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

    // White background
    imagefilledrectangle($image, 0, 0, $width, $height, $white);

    // Outer academic page border
    imagesetthickness($image, 2);
    imagerectangle($image, $margin / 2, $margin / 2, $width - $margin / 2, $height - $margin / 2, $black);

    // 1. Draw Root Box
    $rootX = (int)(($width - $rootW) / 2);
    $rootY = $margin + 10;
    drawAcademicBox($image, $rootX, $rootY, $rootW, $rootH, $rootTitle, true, 14);

    // Vertical line from Root down to Bus line
    imagesetthickness($image, 2);
    imageline($image, (int)($width / 2), $rootY + $rootH, (int)($width / 2), $busY, $black);

    // 2. Horizontal Bus Line
    $firstColCenterX = $margin + 40 + (int)($colW / 2);
    $lastColCenterX = $margin + 40 + ($numCols - 1) * ($colW + $gapX) + (int)($colW / 2);
    imageline($image, $firstColCenterX, $busY, $lastColCenterX, $busY, $black);

    // 3. Draw Each Column
    for ($i = 0; $i < $numCols; $i++) {
        $col = $columns[$i];
        $colX = $margin + 40 + $i * ($colW + $gapX);
        $centerX = $colX + (int)($colW / 2);

        // Vertical drop line from bus to module box
        imageline($image, $centerX, $busY, $centerX, $parentY, $black);

        // Draw Module Box
        $isStandalone = empty($col['items']);
        drawAcademicBox($image, $colX, $parentY, $colW, $boxH, $col['title'], true, 11);

        // If has children, draw children cascade
        if (!$isStandalone) {
            $currY = $parentY + $boxH;
            foreach ($col['items'] as $j => $itemText) {
                $childY = $childrenStartY + $j * ($boxH + $gapY);
                // Line connecting from previous box bottom to this box top
                imageline($image, $centerX, $currY, $centerX, $childY, $black);
                // Draw child box
                drawAcademicBox($image, $colX, $childY, $colW, $boxH, $itemText, false, 10);
                $currY = $childY + $boxH;
            }
        }
    }

    imagepng($image, $outPath, 9);
    imagedestroy($image);
    echo "Generated academic BFD: $outPath (" . filesize($outPath) . " bytes, {$width}x{$height})\n";
}

// TEST: Admin Diagram with 11 columns
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

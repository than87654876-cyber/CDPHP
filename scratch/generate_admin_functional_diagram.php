<?php
error_reporting(E_ALL & ~E_DEPRECATED);
// Script to generate high-resolution Functional Decomposition Diagram for Admin
$fontPath = 'C:/Windows/Fonts/arialbd.ttf';
$fontRegular = 'C:/Windows/Fonts/arial.ttf';

$width = 1800;
$height = 1150;

$image = imagecreatetruecolor($width, $height);
imageantialias($image, true);

// Colors
$bg = imagecolorallocate($image, 248, 250, 252); // #f8fafc
$white = imagecolorallocate($image, 255, 255, 255);
$border = imagecolorallocate($image, 226, 232, 240); // #e2e8f0
$lineColor = imagecolorallocate($image, 148, 163, 184); // #94a3b8
$darkText = imagecolorallocate($image, 30, 41, 59); // #1e293b
$subText = imagecolorallocate($image, 71, 85, 105); // #475569

// Primary root colors
$rootBg = imagecolorallocate($image, 30, 58, 138); // #1e3a8a deep navy
$rootText = imagecolorallocate($image, 255, 255, 255);

// Module Header Colors (8 modules)
$colors = [
    ['header' => imagecolorallocate($image, 37, 99, 235), 'bg' => imagecolorallocate($image, 239, 246, 255)], // Blue
    ['header' => imagecolorallocate($image, 5, 150, 105), 'bg' => imagecolorallocate($image, 236, 253, 245)], // Emerald
    ['header' => imagecolorallocate($image, 217, 119, 6), 'bg' => imagecolorallocate($image, 255, 251, 235)], // Amber
    ['header' => imagecolorallocate($image, 124, 58, 237), 'bg' => imagecolorallocate($image, 245, 243, 255)], // Purple
    ['header' => imagecolorallocate($image, 14, 165, 233), 'bg' => imagecolorallocate($image, 240, 249, 255)], // Sky
    ['header' => imagecolorallocate($image, 225, 29, 72), 'bg' => imagecolorallocate($image, 255, 241, 242)], // Rose
    ['header' => imagecolorallocate($image, 13, 148, 136), 'bg' => imagecolorallocate($image, 240, 253, 250)], // Teal
    ['header' => imagecolorallocate($image, 79, 70, 229), 'bg' => imagecolorallocate($image, 238, 242, 255)], // Indigo
];

// Fill Background
imagefilledrectangle($image, 0, 0, $width, $height, $bg);

// Helper function to draw rounded box
function drawRoundedBox($img, $x, $y, $w, $h, $radius, $fillColor, $borderColor = null) {
    imagefilledrectangle($img, $x + $radius, $y, $x + $w - $radius, $y + $h, $fillColor);
    imagefilledrectangle($img, $x, $y + $radius, $x + $w, $y + $h - $radius, $fillColor);
    imagefilledellipse($img, $x + $radius, $y + $radius, $radius * 2, $radius * 2, $fillColor);
    imagefilledellipse($img, $x + $w - $radius, $y + $radius, $radius * 2, $radius * 2, $fillColor);
    imagefilledellipse($img, $x + $radius, $y + $h - $radius, $radius * 2, $radius * 2, $fillColor);
    imagefilledellipse($img, $x + $w - $radius, $y + $h - $radius, $radius * 2, $radius * 2, $fillColor);

    if ($borderColor) {
        imagerectangle($img, $x, $y, $x + $w, $y + $h, $borderColor);
    }
}

// 1. Draw Root Box
$rootW = 750;
$rootH = 75;
$rootX = ($width - $rootW) / 2;
$rootY = 45;
drawRoundedBox($image, $rootX, $rootY, $rootW, $rootH, 15, $rootBg);
$rootTitle = "HỆ THỐNG QUẢN LÝ & ĐIỀU HÀNH FOODDAILY (ADMIN)";
$bbox = imagettfbbox(18, 0, $fontPath, $rootTitle);
$textW = $bbox[2] - $bbox[0];
imagettftext($image, 18, 0, $rootX + ($rootW - $textW) / 2, $rootY + 46, $rootText, $fontPath, $rootTitle);

// 2. Modules Data (8 modules)
$modules = [
    // ROW 1
    [
        'title' => '1. Quản lý Thống kê & Doanh thu',
        'items' => [
            '• Thống kê doanh thu theo ngày / tuần / tháng',
            '• Thống kê sản lượng đơn hàng & trạng thái',
            '• Biểu đồ tỷ lệ thanh toán VietQR vs COD',
            '• Top 5 món bán chạy & Khách hàng thân thiết',
            '• Xuất báo cáo tài chính ra file Excel'
        ]
    ],
    [
        'title' => '2. Quản lý Thực đơn & Món ăn',
        'items' => [
            '• Phân loại danh mục món ăn (Cơm, Bún, Nước...)',
            '• Thêm mới, chỉnh sửa, xóa món ăn',
            '• Cập nhật giá bán niêm yết & giá khuyến mãi',
            '• Quản lý suất ăn phục vụ trong ngày (Stock)',
            '• Bật / Tắt trạng thái kinh doanh phục vụ'
        ]
    ],
    [
        'title' => '3. Quản lý Đơn đặt hàng',
        'items' => [
            '• Xem danh sách đơn hàng toàn hệ thống',
            '• Lọc đơn theo 7 trạng thái chuẩn F&B',
            '• Xem chi tiết đơn: người nhận, món, ghi chú',
            '• Đối soát mã giao dịch VietQR PayOS',
            '• Cập nhật trạng thái hoặc hủy đơn ghi lý do'
        ]
    ],
    [
        'title' => '4. Quản lý Khách hàng & Thành viên',
        'items' => [
            '• Hồ sơ khách hàng (Tên, Email, SĐT, Sổ địa chỉ)',
            '• Lịch sử các đơn đặt hàng cá nhân & nhóm',
            '• Tích lũy điểm thưởng thành viên (Loyalty)',
            '• Quản lý phân hạng (Đồng, Bạc, Vàng, Kim cương)',
            '• Khóa / Mở khóa tài khoản khách hàng'
        ]
    ],
    // ROW 2
    [
        'title' => '5. Quản lý Kho & Nhà cung cấp',
        'items' => [
            '• Danh bạ đối tác nhà cung ứng thực phẩm',
            '• Danh mục nguyên vật liệu & đơn vị tính',
            '• Cảnh báo tự động nguyên liệu chạm mức tối thiểu',
            '• Lập và phê duyệt phiếu nhập kho (Goods Receipts)',
            '• Cập nhật tồn kho thực tế & tính giá vốn bình quân'
        ]
    ],
    [
        'title' => '6. Quản lý Khuyến mãi & Voucher',
        'items' => [
            '• Tạo mã Voucher ưu đãi (% hoặc tiền cố định)',
            '• Thiết lập điều kiện: Đơn tối thiểu, hạn dùng',
            '• Phát voucher tự động vào Ví Voucher người dùng',
            '• Kiểm soát giới hạn lượt sử dụng mã',
            '• Báo cáo hiệu quả chiến dịch Marketing'
        ]
    ],
    [
        'title' => '7. Quản lý Gói ăn định kỳ',
        'items' => [
            '• Cấu hình các gói dịch vụ tuần / tháng',
            '• Quản lý danh sách khách hàng đăng ký thuê bao',
            '• Lập lịch điều phối thực đơn giao hằng ngày',
            '• Tiếp nhận & xử lý yêu cầu đổi món của khách',
            '• Theo dõi tiến độ gói ăn & gia hạn'
        ]
    ],
    [
        'title' => '8. Hệ thống & Phân quyền RBAC',
        'items' => [
            '• Quản lý tài khoản nhân viên (Thu ngân, Bếp)',
            '• Phân quyền vai trò chi tiết (Spatie RBAC)',
            '• Cấu hình thông tin nhà hàng & Cổng thanh toán',
            '• Cấu hình API Google Gemini AI & WebSockets',
            '• Sao lưu dữ liệu tự động & Phục hồi hệ thống'
        ]
    ]
];

// Card dimensions
$cardW = 405;
$cardH = 390;
$gapX = 35;
$startX = ($width - ($cardW * 4 + $gapX * 3)) / 2;

$row1Y = 190;
$row2Y = 640;

// Draw Central Distribution Line
$centerLineY = 145;
imagesetthickness($image, 3);
imageline($image, $width / 2, $rootY + $rootH, $width / 2, $centerLineY, $lineColor);
imageline($image, $startX + $cardW / 2, $centerLineY, $startX + 3 * ($cardW + $gapX) + $cardW / 2, $centerLineY, $lineColor);

for ($i = 0; $i < 8; $i++) {
    $row = ($i < 4) ? 1 : 2;
    $col = $i % 4;
    $x = $startX + $col * ($cardW + $gapX);
    $y = ($row == 1) ? $row1Y : $row2Y;

    // Line from distribution line to card
    if ($row == 1) {
        imageline($image, $x + $cardW / 2, $centerLineY, $x + $cardW / 2, $y, $lineColor);
    } else {
        // Connect to row 2
        imageline($image, $x + $cardW / 2, $row1Y + $cardH, $x + $cardW / 2, $y, $lineColor);
    }

    $c = $colors[$i];

    // Card Body Box
    drawRoundedBox($image, $x, $y, $cardW, $cardH, 12, $white, $border);

    // Card Header Box
    drawRoundedBox($image, $x, $y, $cardW, 56, 12, $c['header']);
    imagefilledrectangle($image, $x, $y + 44, $x + $cardW, $y + 56, $c['header']); // square bottom corners of header

    // Card Title
    imagettftext($image, 13, 0, $x + 16, $y + 36, $white, $fontPath, $modules[$i]['title']);

    // Sub-items
    $itemY = $y + 92;
    foreach ($modules[$i]['items'] as $item) {
        // Subtle bullet background
        drawRoundedBox($image, $x + 12, $itemY - 22, $cardW - 24, 46, 6, $c['bg']);
        imagettftext($image, 11, 0, $x + 22, $itemY + 6, $darkText, $fontRegular, $item);
        $itemY += 58;
    }
}

// Caption at bottom
$caption = "Sơ đồ 2.1: Sơ đồ chức năng hệ thống dành cho Quản trị viên (Admin) – FOODDAILY";
$bboxCap = imagettfbbox(14, 0, $fontPath, $caption);
$capW = $bboxCap[2] - $bboxCap[0];
imagettftext($image, 14, 0, ($width - $capW) / 2, $height - 25, $darkText, $fontPath, $caption);

// Save Image
$outPath = 'docs/images/so_do_chuc_nang_admin.png';
imagepng($image, $outPath, 9);
imagedestroy($image);

echo "Successfully generated: " . $outPath . " (" . filesize($outPath) . " bytes)\n";

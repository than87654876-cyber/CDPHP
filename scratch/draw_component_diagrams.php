<?php
require_once __DIR__ . '/diagram_core.php';
require_once __DIR__ . '/component_helper.php';

// Helper for Database Cylinder
function drawDatabaseCylinder($im, $cx, $cy, $w, $h, $label = "CSDL\nMySQL") {
    global $fontTimesBd;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    
    $rx = $w / 2;
    $ry = 14;
    $yTop = $cy - ($h / 2) + $ry;
    $yBottom = $cy + ($h / 2) - $ry;
    
    // Body rectangle
    imagefilledrectangle($im, $cx - $rx, $yTop, $cx + $rx, $yBottom, $white);
    
    // Bottom arc
    imagefilledellipse($im, $cx, $yBottom, $w, $ry * 2, $white);
    imagesetthickness($im, 2);
    imagearc($im, $cx, $yBottom, $w, $ry * 2, 0, 180, $black);
    
    // Side lines
    imageline($im, $cx - $rx, $yTop, $cx - $rx, $yBottom, $black);
    imageline($im, $cx + $rx, $yTop, $cx + $rx, $yBottom, $black);
    
    // Top ellipse
    imagefilledellipse($im, $cx, $yTop, $w, $ry * 2, $white);
    imageellipse($im, $cx, $yTop, $w, $ry * 2, $black);
    imagesetthickness($im, 1);
    
    // Label
    $lines = explode("\n", $label);
    $lineH = 15;
    $startY = $cy - (count($lines) * $lineH / 2) + 12;
    foreach ($lines as $i => $line) {
        $bbox = imagettfbbox(10, 0, $fontTimesBd, $line);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 10, 0, (int)($cx - ($tw / 2)), (int)($startY + $i * $lineH), $black, $fontTimesBd, $line);
    }
}

// Helper for 3D Deployment Node Cube
function draw3DNode($im, $cx, $cy, $w, $h, $depth, $lines) {
    global $fontTimesBd;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    $grayTop = imagecolorallocate($im, 238, 238, 238);
    $graySide = imagecolorallocate($im, 220, 220, 220);
    
    $x1 = (int)($cx - ($w / 2));
    $y1 = (int)($cy - ($h / 2));
    $x2 = (int)($cx + ($w / 2));
    $y2 = (int)($cy + ($h / 2));
    
    $dx = (int)($depth * 0.7);
    $dy = (int)($depth * 0.5);
    
    // Top face
    $topPts = [
        $x1, $y1,
        $x1 + $dx, $y1 - $dy,
        $x2 + $dx, $y1 - $dy,
        $x2, $y1
    ];
    imagefilledpolygon($im, $topPts, $grayTop);
    imagesetthickness($im, 2);
    imagepolygon($im, $topPts, $black);
    
    // Right face
    $sidePts = [
        $x2, $y1,
        $x2 + $dx, $y1 - $dy,
        $x2 + $dx, $y2 - $dy,
        $x2, $y2
    ];
    imagefilledpolygon($im, $sidePts, $graySide);
    imagepolygon($im, $sidePts, $black);
    
    // Front face
    imagefilledrectangle($im, $x1, $y1, $x2, $y2, $white);
    imagerectangle($im, $x1, $y1, $x2, $y2, $black);
    imagesetthickness($im, 1);
    
    // Text
    if (is_string($lines)) $lines = [$lines];
    $lineH = 16;
    $startY = $cy - (count($lines) * $lineH / 2) + 12;
    foreach ($lines as $i => $line) {
        $bbox = imagettfbbox(10, 0, $fontTimesBd, $line);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 10, 0, (int)($cx - ($tw / 2)), (int)($startY + $i * $lineH), $black, $fontTimesBd, $line);
    }
}

// 1. Biểu đồ 2.1: Biểu đồ thành phần tổng quan hệ thống (EXACTLY MATCHING USER'S PHOTO!)
function drawOverviewComponentDiagram() {
    $w = 640;
    $h = 300;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);
    
    // Left: Website
    drawComponentBox($im, 130, 150, 120, 60, "Website");
    
    // Right: Giao diện Admin & Giao diện khách hàng (plus Bếp & Thu ngân for FOODDAILY)
    drawComponentBox($im, 490, 80, 150, 52, "Giao diện\nAdmin");
    drawComponentBox($im, 490, 220, 150, 52, "Giao diện\nkhách hàng");
    
    // Dashed dependency arrows
    drawDependencyArrow($im, 190, 140, 415, 80);
    drawDependencyArrow($im, 190, 160, 415, 220);
    
    imagepng($im, 'docs/images/bieu_do_2_1_thanh_phan_tong_quan.png');
    imagedestroy($im);
    echo "Generated Overview Component Diagram (Biểu đồ 2.1)\n";
}

// 2. Biểu đồ 2.2: Biểu đồ thành phần quản trị viên
function drawAdminComponentDiagram() {
    $w = 860;
    $h = 720;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);
    
    // Left: Giao diện Admin
    drawComponentBox($im, 85, 360, 110, 60, "Giao diện\nAdmin");
    
    // Middle components (3 columns or 2 columns of sub-components)
    $comps = [
        ["Quản lý thống kê\n& Doanh thu", 270, 70],
        ["Quản lý thực đơn\n& Món ăn", 270, 150],
        ["Quản lý đơn hàng\n& Trạng thái", 270, 230],
        ["Quản lý khách hàng\n& Tích điểm", 270, 310],
        ["Quản lý kho hàng\n& Phiếu nhập", 270, 390],
        ["Quản lý đối tác\nNhà cung cấp", 270, 470],
        ["Quản lý voucher\nKhuyến mãi", 270, 550],
        ["Phân quyền RBAC\n& Nhân viên", 270, 630],
        
        ["Màn hình Bếp\nFIFO (KDS)", 470, 150],
        ["Quầy thu ngân\n& In bill 1 chạm", 470, 250],
        ["Gói ăn định kỳ\nMeal Subscription", 470, 350],
        ["Cấu hình hệ thống\n& PayOS Webhook", 470, 450],
        ["Đổi mật khẩu\n& Đăng xuất", 470, 550]
    ];
    
    foreach ($comps as $c) {
        drawComponentBox($im, $c[1], $c[2], 135, 48, explode("\n", $c[0]));
        // Arrow from Admin to comp
        drawDependencyArrow($im, 140, 360, $c[1] - 68, $c[2]);
    }
    
    // Right: Lưu trữ dữ liệu & bảo mật, Kết nối, CSDL
    drawComponentBox($im, 670, 350, 140, 60, ["Lưu trữ dữ liệu", "& Bảo mật"]);
    drawComponentBox($im, 780, 200, 95, 48, "Kết nối");
    drawDatabaseCylinder($im, 780, 90, 80, 70, "CSDL\nMySQL");
    
    // Arrows from middle comps to Storage
    foreach ($comps as $c) {
        drawDependencyArrow($im, $c[1] + 68, $c[2], 600, 350);
    }
    
    // Arrow Storage -> Connection -> DB
    drawDependencyArrow($im, 730, 320, 780, 224);
    drawDependencyArrow($im, 780, 176, 780, 125);
    
    imagepng($im, 'docs/images/bieu_do_2_2_thanh_phan_admin.png');
    imagedestroy($im);
    echo "Generated Admin Component Diagram (Biểu đồ 2.2)\n";
}

// 3. Biểu đồ 2.3: Biểu đồ thành phần người dùng
function drawUserComponentDiagram() {
    $w = 680;
    $h = 320;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);
    
    drawComponentBox($im, 130, 160, 140, 60, "Giao diện\nkhách hàng");
    
    $userComps = [
        ["Tìm kiếm món ăn &\nTrợ lý AI Gemini", 480, 65],
        ["Đặt món theo nhóm\nRealtime (WebSockets)", 480, 130],
        ["Thanh toán tự động\nVietQR (PayOS)", 480, 195],
        ["Quản lý giỏ hàng &\nTra cứu đơn hàng", 480, 260]
    ];
    
    foreach ($userComps as $uc) {
        drawComponentBox($im, $uc[1], $uc[2], 180, 48, explode("\n", $uc[0]));
        drawDependencyArrow($im, 200, 160, $uc[1] - 90, $uc[2]);
    }
    
    imagepng($im, 'docs/images/bieu_do_2_3_thanh_phan_nguoi_dung.png');
    imagedestroy($im);
    echo "Generated User Component Diagram (Biểu đồ 2.3)\n";
}

// 4. Biểu đồ 2.4: Biểu đồ triển khai (3D Node Cubes)
function drawDeploymentDiagram() {
    $w = 780;
    $h = 300;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);
    
    // 3 Nodes: Web Server, Application Server, Database Server
    draw3DNode($im, 140, 150, 140, 90, 25, ["Web Server", "(Nginx /", "Laragon)"]);
    draw3DNode($im, 400, 150, 150, 90, 25, ["Application Server", "(Laravel 13 &", "PHP 8.3 / Reverb)"]);
    draw3DNode($im, 660, 150, 140, 90, 25, ["Database Server", "(MySQL 8.0 &", "Storage)"]);
    
    // Connections between nodes
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 2);
    // Line 1: Web Server to App Server
    imageline($im, 225, 145, 315, 145, $black);
    // Line 2: App Server to DB Server
    imageline($im, 490, 145, 580, 145, $black);
    imagesetthickness($im, 1);
    
    // Labels for protocols
    global $fontTimes;
    imagettftext($im, 9, 0, 235, 135, $black, $fontTimes, "HTTP/FastCGI");
    imagettftext($im, 9, 0, 505, 135, $black, $fontTimes, "TCP Port 3306");
    
    imagepng($im, 'docs/images/bieu_do_2_4_trien_khai.png');
    imagedestroy($im);
    echo "Generated Deployment Diagram (Biểu đồ 2.4)\n";
}

// 5. Hình 2.2: Sơ đồ Cơ sở dữ liệu (ERD)
function drawDatabaseErdDiagram() {
    $w = 1100;
    $h = 680;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);
    
    global $fontTimesBd, $fontTimes;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    $headerBg = imagecolorallocate($im, 240, 240, 240);
    $gold = imagecolorallocate($im, 218, 165, 32);
    
    function drawErdTable($im, $x, $y, $w, $h, $name, $cols) {
        global $fontTimesBd, $fontTimes;
        $black = imagecolorallocate($im, 0, 0, 0);
        $white = imagecolorallocate($im, 255, 255, 255);
        $headerBg = imagecolorallocate($im, 235, 235, 235);
        $gold = imagecolorallocate($im, 218, 165, 32);
        
        // Box
        imagefilledrectangle($im, $x, $y, $x + $w, $y + $h, $white);
        imagesetthickness($im, 2);
        imagerectangle($im, $x, $y, $x + $w, $y + $h, $black);
        
        // Header
        imagefilledrectangle($im, $x, $y, $x + $w, $y + 24, $headerBg);
        imageline($im, $x, $y + 24, $x + $w, $y + 24, $black);
        imagesetthickness($im, 1);
        
        // Table name
        imagettftext($im, 10, 0, $x + 10, $y + 17, $black, $fontTimesBd, $name);
        
        // Columns
        foreach ($cols as $idx => $col) {
            $cy = $y + 40 + ($idx * 16);
            if ($col[0] === 'PK') {
                imagefilledellipse($im, $x + 12, $cy - 4, 7, 7, $gold);
                imagettftext($im, 8.5, 0, $x + 22, $cy, $black, $fontTimesBd, $col[1]);
            } elseif ($col[0] === 'FK') {
                imagettftext($im, 8, 0, $x + 8, $cy, $gold, $fontTimesBd, "FK");
                imagettftext($im, 8.5, 0, $x + 25, $cy, $black, $fontTimes, $col[1]);
            } else {
                imagettftext($im, 8.5, 0, $x + 22, $cy, $black, $fontTimes, $col[1]);
            }
        }
    }
    
    // Tables layout
    $tables = [
        ["Users", 40, 40, 150, 140, [["PK", "id"], ["-", "name"], ["-", "email"], ["-", "password"], ["-", "role"], ["-", "points"]]],
        ["Categories", 240, 40, 140, 110, [["PK", "id"], ["-", "name"], ["-", "slug"], ["-", "is_active"]]],
        ["Dishes", 430, 40, 150, 140, [["PK", "id"], ["FK", "category_id"], ["-", "name"], ["-", "price"], ["-", "image"], ["-", "stock"]]],
        ["Orders", 630, 40, 160, 160, [["PK", "id"], ["-", "order_code"], ["FK", "user_id"], ["-", "total_amount"], ["-", "payment_status"], ["-", "order_status"]]],
        ["Order_Items", 840, 40, 150, 130, [["PK", "id"], ["FK", "order_id"], ["FK", "dish_id"], ["-", "quantity"], ["-", "price"]]],
        
        ["Group_Orders", 40, 260, 150, 120, [["PK", "id"], ["-", "room_code"], ["FK", "host_id"], ["-", "status"]]],
        ["Group_Items", 240, 260, 150, 130, [["PK", "id"], ["FK", "group_order_id"], ["FK", "dish_id"], ["-", "user_name"], ["-", "quantity"]]],
        ["Coupons", 440, 260, 140, 120, [["PK", "id"], ["-", "code"], ["-", "discount_val"], ["-", "min_order"]]],
        ["User_Coupons", 630, 260, 150, 120, [["PK", "id"], ["FK", "user_id"], ["FK", "coupon_id"], ["-", "is_used"]]],
        
        ["Suppliers", 40, 480, 150, 120, [["PK", "id"], ["-", "name"], ["-", "phone"], ["-", "address"]]],
        ["Inventory_Items", 240, 480, 160, 140, [["PK", "id"], ["-", "item_code"], ["-", "name"], ["-", "unit"], ["-", "current_stock"], ["FK", "supplier_id"]]],
        ["Goods_Receipts", 450, 480, 160, 130, [["PK", "id"], ["-", "receipt_code"], ["FK", "supplier_id"], ["-", "total_amount"]]],
        ["Receipt_Items", 660, 480, 160, 130, [["PK", "id"], ["FK", "receipt_id"], ["FK", "item_id"], ["-", "quantity"], ["-", "unit_price"]]],
        ["Subscriptions", 870, 260, 160, 140, [["PK", "id"], ["FK", "user_id"], ["-", "package_name"], ["-", "start_date"], ["-", "status"]]]
    ];
    
    foreach ($tables as $t) {
        drawErdTable($im, $t[1], $t[2], $t[3], $t[4], $t[0], $t[5]);
    }
    
    // Relationship lines with keys / crows foot
    imagesetthickness($im, 1.5);
    // Categories -> Dishes
    imageline($im, 380, 80, 430, 80, $black);
    // Users -> Orders
    imageline($im, 190, 80, 240, 80, $black); // to Categories
    imageline($im, 190, 110, 630, 110, $black); // Users to Orders
    // Orders -> Order_Items
    imageline($im, 790, 80, 840, 80, $black);
    // Dishes -> Order_Items
    imageline($im, 580, 110, 840, 110, $black);
    // Group_Orders -> Group_Items
    imageline($im, 190, 310, 240, 310, $black);
    // Users -> Subscriptions
    imageline($im, 115, 180, 115, 230, $black);
    imageline($im, 115, 230, 870, 230, $black);
    imageline($im, 870, 230, 870, 260, $black);
    // Suppliers -> Inventory_Items
    imageline($im, 190, 530, 240, 530, $black);
    // Suppliers -> Goods_Receipts
    imageline($im, 190, 560, 450, 560, $black);
    // Goods_Receipts -> Receipt_Items
    imageline($im, 610, 530, 660, 530, $black);
    imagesetthickness($im, 1);
    
    imagepng($im, 'docs/images/hinh_2_2_co_so_du_lieu.png');
    imagedestroy($im);
    echo "Generated Database ERD Diagram (Hình 2.2)\n";
}

// Run all diagram generators
drawOverviewComponentDiagram();
drawAdminComponentDiagram();
drawUserComponentDiagram();
drawDeploymentDiagram();
drawDatabaseErdDiagram();

echo "All Component & Deployment & ERD Diagrams generated successfully!\n";

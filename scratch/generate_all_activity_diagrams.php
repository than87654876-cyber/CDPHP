<?php
require_once __DIR__ . '/diagram_core.php';

// 1. Reset Password Activity Diagram (EXACTLY matching Image 3)
function drawResetPasswordDiagram() {
    $w = 620;
    $h = 750;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    // Initial node
    drawStartNode($im, 310, 45);

    // Action 1: Chọn reset password
    drawRoundedRect($im, 310, 115, 140, 48, ["Chọn reset", "password"]);

    // Arrow down to Action 2
    drawOrthogonalArrow($im, [[310, 139], [310, 195]]);

    // Action 2: Nhập thông tin username / email
    drawRoundedRect($im, 310, 225, 140, 48, ["Nhập thông tin", "username"]);

    // Decision 1: Kiểm tra username
    drawDecisionDiamond($im, 480, 225, 95, 55, "Kiểm tra");
    drawOrthogonalArrow($im, [[380, 225], [432, 225]]);

    // Error 1: Thông báo sai thông tin
    drawRoundedRect($im, 125, 225, 135, 48, ["Thông báo sai", "thông tin"]);
    drawOrthogonalArrow($im, [[240, 225], [192, 225]], "Sai thông tin");

    // Loop back from Error 1 to Action 1
    drawOrthogonalArrow($im, [[125, 201], [125, 115], [240, 115]], "Sai thông tin");

    // Success from Decision 1 to Action 3
    drawOrthogonalArrow($im, [[480, 252], [480, 320], [380, 320]], "Thông tin chính xác");

    // Loop if decision 1 fail
    drawOrthogonalArrow($im, [[480, 197], [480, 175], [310, 175], [310, 201]], "Sai thông tin");

    // Action 3: Nhập password mới
    drawRoundedRect($im, 310, 320, 140, 48, ["Nhập", "password mới"]);

    // Decision 2: Kiểm tra password mới
    drawDecisionDiamond($im, 310, 440, 95, 55, "Kiểm tra");
    drawOrthogonalArrow($im, [[310, 344], [310, 412]], "Thông tin chính xác");

    // Error 2: Thông báo sai thông tin
    drawRoundedRect($im, 125, 440, 135, 48, ["Thông báo sai", "thông tin"]);
    drawOrthogonalArrow($im, [[262, 440], [192, 440]], "Sai thông tin");

    // Loop from Error 2 back to Action 3
    drawOrthogonalArrow($im, [[125, 416], [125, 335], [240, 335]]);

    // Action 4: Lưu thông tin
    drawRoundedRect($im, 310, 560, 140, 48, ["Lưu thông tin"]);
    drawOrthogonalArrow($im, [[310, 467], [310, 536]], "Thông tin chính xác");

    // Final Node
    drawEndNode($im, 170, 560);
    drawOrthogonalArrow($im, [[240, 560], [185, 560]]);

    imagepng($im, 'docs/images/luoc_do_2_3_reset_password.png');
    imagedestroy($im);
    echo "Generated Reset Password Activity Diagram\n";
}

// 2. Login Activity Diagram
function drawLoginDiagram() {
    $w = 580;
    $h = 700;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 170, 48, ["Chọn chức năng", "đăng nhập"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 200]]);

    drawRoundedRect($im, 290, 230, 170, 48, ["Nhập Email", "& Mật khẩu"]);
    drawOrthogonalArrow($im, [[290, 254], [290, 320]]);

    drawDecisionDiamond($im, 290, 350, 110, 60, "Kiểm tra");

    drawRoundedRect($im, 110, 350, 140, 48, ["Thông báo sai", "thông tin"]);
    drawOrthogonalArrow($im, [[235, 350], [180, 350]], "Sai thông tin");
    drawOrthogonalArrow($im, [[110, 326], [110, 230], [205, 230]]);

    drawRoundedRect($im, 290, 470, 170, 48, ["Khởi tạo Session", "& Phân quyền"]);
    drawOrthogonalArrow($im, [[290, 380], [290, 446]], "Thông tin hợp lệ");

    drawRoundedRect($im, 290, 580, 170, 48, ["Chuyển hướng", "vào không gian làm việc"]);
    drawOrthogonalArrow($im, [[290, 494], [290, 556]]);

    drawEndNode($im, 290, 650);
    drawOrthogonalArrow($im, [[290, 604], [290, 635]]);

    imagepng($im, 'docs/images/luoc_do_2_2_dang_nhap.png');
    imagedestroy($im);
    echo "Generated Login Activity Diagram\n";
}

// 3. Search & AI Food Recommendation Diagram
function drawSearchAiDiagram() {
    $w = 580;
    $h = 720;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Nhập từ khóa /", "Chat với Gemini AI"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 200]]);

    drawDecisionDiamond($im, 290, 230, 120, 60, "Có kết quả?");
    
    drawRoundedRect($im, 100, 230, 130, 48, ["Gợi ý món", "tương tự / hot"]);
    drawOrthogonalArrow($im, [[230, 230], [165, 230]], "Không tìm thấy");
    drawOrthogonalArrow($im, [[100, 206], [100, 120], [200, 120]]);

    drawRoundedRect($im, 290, 350, 180, 48, ["Hiển thị danh sách món", "& Đánh giá sao"]);
    drawOrthogonalArrow($im, [[290, 260], [290, 326]], "Tìm thấy món");

    drawRoundedRect($im, 290, 470, 180, 48, ["Xem chi tiết món &", "Ghi chú món ăn"]);
    drawOrthogonalArrow($im, [[290, 374], [290, 446]]);

    drawRoundedRect($im, 290, 580, 180, 48, ["Thêm vào giỏ hàng", "cá nhân / nhóm"]);
    drawOrthogonalArrow($im, [[290, 494], [290, 556]]);

    drawEndNode($im, 290, 660);
    drawOrthogonalArrow($im, [[290, 604], [290, 645]]);

    imagepng($im, 'docs/images/luoc_do_2_4_tim_kiem.png');
    imagedestroy($im);
    echo "Generated Search Activity Diagram\n";
}

// 4. Realtime Group Order Diagram
function drawGroupOrderDiagram() {
    $w = 640;
    $h = 740;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 320, 45);

    drawRoundedRect($im, 320, 120, 180, 48, ["Chủ phòng bấm", "Tạo phòng đặt nhóm"]);
    drawOrthogonalArrow($im, [[320, 144], [320, 200]]);

    drawRoundedRect($im, 320, 225, 180, 48, ["Hệ thống sinh", "Room Code & QR Code"]);
    drawOrthogonalArrow($im, [[320, 249], [320, 305]]);

    drawRoundedRect($im, 320, 330, 190, 48, ["Thành viên tham gia", "& Chọn món riêng"]);
    drawOrthogonalArrow($im, [[320, 354], [320, 410]]);

    drawRoundedRect($im, 320, 435, 190, 48, ["Broadcast WebSockets", "Đồng bộ giỏ Realtime"]);
    drawOrthogonalArrow($im, [[320, 459], [320, 515]]);

    drawDecisionDiamond($im, 320, 545, 130, 60, "Chốt đơn?");
    drawOrthogonalArrow($im, [[385, 545], [480, 545], [480, 330], [415, 330]], "Chưa chốt");

    drawRoundedRect($im, 320, 645, 180, 48, ["Khóa phòng & Chuyển", "sang Thanh toán"]);
    drawOrthogonalArrow($im, [[320, 575], [320, 621]], "Chủ phòng chốt");

    drawEndNode($im, 140, 645);
    drawOrthogonalArrow($im, [[230, 645], [155, 645]]);

    imagepng($im, 'docs/images/luoc_do_2_5_dat_mon_nhom.png');
    imagedestroy($im);
    echo "Generated Group Order Activity Diagram\n";
}

// 5. PayOS VietQR Payment Diagram
function drawPaymentPayosDiagram() {
    $w = 600;
    $h = 750;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 300, 45);

    drawRoundedRect($im, 300, 120, 180, 48, ["Chọn phương thức", "VietQR (PayOS)"]);
    drawOrthogonalArrow($im, [[300, 144], [300, 200]]);

    drawRoundedRect($im, 300, 225, 180, 48, ["Sinh mã QR VietQR", "động hiển thị màn hình"]);
    drawOrthogonalArrow($im, [[300, 249], [300, 305]]);

    drawRoundedRect($im, 300, 330, 180, 48, ["Khách quét mã chuyển tiền", "& PayOS bắn Webhook"]);
    drawOrthogonalArrow($im, [[300, 354], [300, 415]]);

    drawDecisionDiamond($im, 300, 445, 120, 60, "Check HMAC?");

    drawRoundedRect($im, 100, 445, 130, 48, ["Từ chối Webhook", "(HTTP 400)"]);
    drawOrthogonalArrow($im, [[240, 445], [165, 445]], "Sai chữ ký");

    drawRoundedRect($im, 300, 560, 180, 48, ["Đổi đơn sang 'paid' &", "Kích hoạt chuông Bếp"]);
    drawOrthogonalArrow($im, [[300, 475], [300, 536]], "Chữ ký hợp lệ");

    drawRoundedRect($im, 300, 660, 180, 42, ["Hiển thị 'Thành công'"]);
    drawOrthogonalArrow($im, [[300, 584], [300, 639]]);

    drawEndNode($im, 130, 660);
    drawOrthogonalArrow($im, [[210, 660], [145, 660]]);

    imagepng($im, 'docs/images/luoc_do_2_6_thanh_toan_payos.png');
    imagedestroy($im);
    echo "Generated Payment Activity Diagram\n";
}

// 6. FIFO Kitchen Display Diagram
function drawKitchenFifoDiagram() {
    $w = 580;
    $h = 720;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Nhận đơn tự động &", "Sắp xếp hàng đợi FIFO"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 205]]);

    drawRoundedRect($im, 290, 230, 180, 48, ["Xem bảng gom số lượng", "nấu theo mẻ (Batch)"]);
    drawOrthogonalArrow($im, [[290, 254], [290, 315]]);

    drawRoundedRect($im, 290, 340, 180, 48, ["Bấm 'Bắt đầu nấu'", "(preparing)"]);
    drawOrthogonalArrow($im, [[290, 364], [290, 425]]);

    drawDecisionDiamond($im, 290, 455, 120, 60, "Nấu xong?");
    drawOrthogonalArrow($im, [[350, 455], [440, 455], [440, 340], [380, 340]], "Đang nấu");

    drawRoundedRect($im, 290, 570, 180, 48, ["Bấm 'Đã nấu xong' &", "Đẩy sang Thu ngân"]);
    drawOrthogonalArrow($im, [[290, 485], [290, 546]], "Đã xong");

    drawEndNode($im, 290, 660);
    drawOrthogonalArrow($im, [[290, 594], [290, 645]]);

    imagepng($im, 'docs/images/luoc_do_2_7_dieu_phoi_bep.png');
    imagedestroy($im);
    echo "Generated Kitchen Activity Diagram\n";
}

// 7. POS Workspace & Print Bill Diagram
function drawPosPrintBillDiagram() {
    $w = 580;
    $h = 700;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Thu ngân nhận đơn", "đã nấu xong từ Bếp"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 205]]);

    drawRoundedRect($im, 290, 230, 180, 48, ["Thao tác In bill 1 chạm", "(Phím tắt F9)"]);
    drawOrthogonalArrow($im, [[290, 254], [290, 315]]);

    drawRoundedRect($im, 290, 340, 180, 48, ["Máy in xuất phiếu nhiệt", "80mm kèm mã đơn"]);
    drawOrthogonalArrow($im, [[290, 364], [290, 425]]);

    drawRoundedRect($im, 290, 450, 180, 48, ["Đóng gói đồ ăn &", "Kẹp bill niêm phong"]);
    drawOrthogonalArrow($im, [[290, 474], [290, 535]]);

    drawRoundedRect($im, 290, 560, 180, 48, ["Bàn giao Shipper &", "Đổi sang Delivering"]);
    drawOrthogonalArrow($im, [[290, 584], [290, 635]]);

    drawEndNode($im, 290, 655);

    imagepng($im, 'docs/images/luoc_do_2_8_in_hoa_don.png');
    imagedestroy($im);
    echo "Generated POS Print Bill Activity Diagram\n";
}

// 8. Customer Management Diagram
function drawCustomerMgmtDiagram() {
    $w = 580;
    $h = 680;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Xem danh sách khách hàng", "& Điểm tích lũy"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 205]]);

    drawDecisionDiamond($im, 290, 235, 120, 60, "Thao tác?");

    drawRoundedRect($im, 110, 340, 140, 48, ["Khóa / Mở khóa", "tài khoản vi phạm"]);
    drawOrthogonalArrow($im, [[230, 235], [110, 235], [110, 316]], "Cảnh báo");

    drawRoundedRect($im, 290, 340, 150, 48, ["Xem lịch sử đơn hàng", "& Sổ địa chỉ"]);
    drawOrthogonalArrow($im, [[290, 265], [290, 316]], "Chi tiết");

    drawRoundedRect($im, 470, 340, 130, 48, ["Tặng voucher ví", "hội viên tri ân"]);
    drawOrthogonalArrow($im, [[350, 235], [470, 235], [470, 316]], "Chăm sóc");

    drawRoundedRect($im, 290, 470, 180, 48, ["Cập nhật cơ sở dữ liệu", "& Gửi thông báo"]);
    drawOrthogonalArrow($im, [[110, 364], [110, 470], [200, 470]]);
    drawOrthogonalArrow($im, [[290, 364], [290, 446]]);
    drawOrthogonalArrow($im, [[470, 364], [470, 470], [380, 470]]);

    drawEndNode($im, 290, 580);
    drawOrthogonalArrow($im, [[290, 494], [290, 565]]);

    imagepng($im, 'docs/images/luoc_do_2_9_quan_ly_khach_hang.png');
    imagedestroy($im);
    echo "Generated Customer Management Activity Diagram\n";
}

// 9. Dish & Menu Management Diagram
function drawDishMgmtDiagram() {
    $w = 580;
    $h = 700;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Vào Quản lý món ăn &", "Chọn Thêm / Sửa"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 205]]);

    drawRoundedRect($im, 290, 230, 180, 48, ["Nhập tên, giá, danh mục", "và chọn ảnh món"]);
    drawOrthogonalArrow($im, [[290, 254], [290, 315]]);

    drawDecisionDiamond($im, 290, 345, 120, 60, "Hợp lệ?");
    drawRoundedRect($im, 100, 345, 120, 48, ["Báo lỗi thiếu tin"]);
    drawOrthogonalArrow($im, [[230, 345], [160, 345]], "Lỗi");
    drawOrthogonalArrow($im, [[100, 321], [100, 230], [200, 230]]);

    drawRoundedRect($im, 290, 460, 180, 48, ["Upload ảnh Cloudinary &", "Tối ưu hóa WebP"]);
    drawOrthogonalArrow($im, [[290, 375], [290, 436]], "Hợp lệ");

    drawRoundedRect($im, 290, 570, 180, 48, ["Lưu bảng Dishes &", "Cập nhật Menu"]);
    drawOrthogonalArrow($im, [[290, 484], [290, 546]]);

    drawEndNode($im, 290, 650);
    drawOrthogonalArrow($im, [[290, 594], [290, 635]]);

    imagepng($im, 'docs/images/luoc_do_2_10_quan_ly_mon_an.png');
    imagedestroy($im);
    echo "Generated Dish Management Activity Diagram\n";
}

// 10. Supplier Management Diagram
function drawSupplierMgmtDiagram() {
    $w = 560;
    $h = 660;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 280, 45);

    drawRoundedRect($im, 280, 120, 180, 48, ["Mở danh bạ Nhà cung cấp"]);
    drawOrthogonalArrow($im, [[280, 144], [280, 205]]);

    drawRoundedRect($im, 280, 230, 180, 48, ["Nhập tên doanh nghiệp,", "MST, SĐT & Địa chỉ"]);
    drawOrthogonalArrow($im, [[280, 254], [280, 315]]);

    drawDecisionDiamond($im, 280, 345, 110, 60, "Kiểm tra");
    drawOrthogonalArrow($im, [[280, 254], [280, 315]]);

    drawRoundedRect($im, 280, 460, 180, 48, ["Lưu bảng Suppliers &", "Liên kết nguyên liệu"]);
    drawOrthogonalArrow($im, [[280, 375], [280, 436]], "Hợp lệ");

    drawEndNode($im, 280, 570);
    drawOrthogonalArrow($im, [[280, 484], [280, 555]]);

    imagepng($im, 'docs/images/luoc_do_2_11_quan_ly_nha_cung_cap.png');
    imagedestroy($im);
    echo "Generated Supplier Management Activity Diagram\n";
}

// 11. Inventory & Goods Receipts Diagram
function drawInventoryReceiptDiagram() {
    $w = 580;
    $h = 720;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Hệ thống cảnh báo đỏ", "nguyên liệu sắp hết"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 205]]);

    drawRoundedRect($im, 290, 230, 180, 48, ["Lập Phiếu nhập kho", "chọn Nhà cung cấp"]);
    drawOrthogonalArrow($im, [[290, 254], [290, 315]]);

    drawRoundedRect($im, 290, 340, 180, 48, ["Nhập số lượng thực tế", "& Đơn giá nhập"]);
    drawOrthogonalArrow($im, [[290, 364], [290, 425]]);

    drawDecisionDiamond($im, 290, 455, 120, 60, "Phê duyệt?");
    drawOrthogonalArrow($im, [[350, 455], [440, 455], [440, 230], [380, 230]], "Chỉnh sửa");

    drawRoundedRect($im, 290, 570, 180, 48, ["Cộng dồn tồn kho &", "Tính giá vốn bình quân"]);
    drawOrthogonalArrow($im, [[290, 485], [290, 546]], "Duyệt phiếu");

    drawEndNode($im, 290, 660);
    drawOrthogonalArrow($im, [[290, 594], [290, 645]]);

    imagepng($im, 'docs/images/luoc_do_2_12_nhap_kho_nguyen_lieu.png');
    imagedestroy($im);
    echo "Generated Inventory Receipt Activity Diagram\n";
}

// 12. Staff & RBAC Diagram
function drawRbacDiagram() {
    $w = 580;
    $h = 680;
    $im = createBaseImage($w, $h);
    drawOuterBorder($im, $w, $h);

    drawStartNode($im, 290, 45);

    drawRoundedRect($im, 290, 120, 180, 48, ["Admin vào Phân quyền", "nhân viên hệ thống"]);
    drawOrthogonalArrow($im, [[290, 144], [290, 205]]);

    drawRoundedRect($im, 290, 230, 180, 48, ["Chọn tài khoản & Gán", "vai trò (Staff, Kitchen)"]);
    drawOrthogonalArrow($im, [[290, 254], [290, 315]]);

    drawRoundedRect($im, 290, 340, 180, 48, ["Đồng bộ Spatie Permission", "& Phân quyền chi tiết"]);
    drawOrthogonalArrow($im, [[290, 364], [290, 425]]);

    drawRoundedRect($im, 290, 450, 180, 48, ["Cập nhật Middleware", "chặn truy cập trái phép"]);
    drawOrthogonalArrow($im, [[290, 474], [290, 535]]);

    drawEndNode($im, 290, 580);
    drawOrthogonalArrow($im, [[290, 498], [290, 565]]);

    imagepng($im, 'docs/images/luoc_do_2_13_quan_ly_nhan_vien.png');
    imagedestroy($im);
    echo "Generated RBAC Activity Diagram\n";
}

// Execute all generators
drawResetPasswordDiagram();
drawLoginDiagram();
drawSearchAiDiagram();
drawGroupOrderDiagram();
drawPaymentPayosDiagram();
drawKitchenFifoDiagram();
drawPosPrintBillDiagram();
drawCustomerMgmtDiagram();
drawDishMgmtDiagram();
drawSupplierMgmtDiagram();
drawInventoryReceiptDiagram();
drawRbacDiagram();

echo "All 12 Activity Diagrams successfully generated!\n";

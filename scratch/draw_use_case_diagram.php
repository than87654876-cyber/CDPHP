<?php
require_once __DIR__ . '/diagram_core.php';

$w = 1200;
$h = 850;
$im = createBaseImage($w, $h);
drawOuterBorder($im, $w, $h);

// System Boundary
$black = imagecolorallocate($im, 0, 0, 0);
imagesetthickness($im, 1.5);
imagerectangle($im, 170, 35, 1030, 815, $black);
imagesetthickness($im, 1);

// Title of boundary
imagettftext($im, 11, 0, 420, 25, $black, $fontTimesBd, "HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN FOODDAILY");

// Actors
drawActor($im, 85, 150, "Quản trị viên (Admin)");
drawActor($im, 85, 520, "Khách hàng");
drawActor($im, 1115, 260, "Nhân viên quầy");
drawActor($im, 1115, 600, "Đầu bếp");

// Use cases - Left column (Admin & Auth)
$ucLogin = [320, 75];
$ucReset = [320, 140];
$ucStats = [320, 220];
$ucDish = [320, 320];
$ucOrder = [320, 430];
$ucInv = [320, 540];
$ucPromo = [320, 640];
$ucRbac = [320, 730];

drawUseCaseEllipse($im, $ucLogin[0], $ucLogin[1], 150, 42, "Đăng nhập hệ thống");
drawUseCaseEllipse($im, $ucReset[0], $ucReset[1], 150, 42, "Khôi phục mật khẩu");
drawUseCaseEllipse($im, $ucStats[0], $ucStats[1], 160, 45, ["Quản lý thống kê", "& Doanh thu"]);
drawUseCaseEllipse($im, $ucDish[0], $ucDish[1], 160, 45, ["Quản lý thực đơn", "& Món ăn"]);
drawUseCaseEllipse($im, $ucOrder[0], $ucOrder[1], 160, 45, "Quản lý đơn đặt hàng");
drawUseCaseEllipse($im, $ucInv[0], $ucInv[1], 160, 45, ["Quản lý kho nguyên liệu", "& Nhà cung cấp"]);
drawUseCaseEllipse($im, $ucPromo[0], $ucPromo[1], 160, 42, "Quản lý voucher ví");
drawUseCaseEllipse($im, $ucRbac[0], $ucRbac[1], 160, 42, "Phân quyền RBAC");

// Connections for Admin
drawSolidLine($im, 120, 150, $ucLogin[0] - 75, $ucLogin[1]);
drawSolidLine($im, 120, 150, $ucStats[0] - 80, $ucStats[1]);
drawSolidLine($im, 120, 150, $ucDish[0] - 80, $ucDish[1]);
drawSolidLine($im, 120, 150, $ucOrder[0] - 80, $ucOrder[1]);
drawSolidLine($im, 120, 150, $ucInv[0] - 80, $ucInv[1]);
drawSolidLine($im, 120, 150, $ucPromo[0] - 80, $ucPromo[1]);
drawSolidLine($im, 120, 150, $ucRbac[0] - 80, $ucRbac[1]);

// Admin Extend Sub-use cases
drawUseCaseEllipse($im, 490, 190, 120, 36, "Xuất Excel báo cáo");
drawDashedArrow($im, 430, 195, $ucStats[0] + 65, $ucStats[1] - 5, "<<Extend>>");

drawUseCaseEllipse($im, 490, 250, 120, 36, "Thêm / Sửa / Xóa món");
drawDashedArrow($im, 430, 260, $ucDish[0] + 70, $ucDish[1] - 10, "<<Extend>>");

drawUseCaseEllipse($im, 490, 310, 120, 36, "Upload ảnh Cloudinary");
drawDashedArrow($im, 430, 315, $ucDish[0] + 75, $ucDish[1], "<<Extend>>");

drawUseCaseEllipse($im, 490, 410, 120, 36, "Đối soát mã PayOS");
drawDashedArrow($im, 430, 415, $ucOrder[0] + 75, $ucOrder[1], "<<Extend>>");

drawUseCaseEllipse($im, 490, 520, 125, 36, "Lập phiếu nhập kho");
drawDashedArrow($im, 430, 525, $ucInv[0] + 75, $ucInv[1], "<<Extend>>");

drawUseCaseEllipse($im, 490, 575, 125, 36, "Cảnh báo tồn kho đỏ");
drawDashedArrow($im, 430, 570, $ucInv[0] + 75, $ucInv[1] + 10, "<<Extend>>");

// Use cases - Middle/Right column (Customer)
$ucSearch = [690, 75];
$ucAi = [690, 160];
$ucGroup = [690, 260];
$ucPay = [690, 370];
$ucSub = [690, 480];
$ucTrack = [690, 580];
$ucLoyalty = [690, 675];

drawUseCaseEllipse($im, $ucSearch[0], $ucSearch[1], 160, 42, "Tìm kiếm món ăn");
drawUseCaseEllipse($im, $ucAi[0], $ucAi[1], 160, 45, ["Trợ lý AI Gemini", "tư vấn món"]);
drawUseCaseEllipse($im, $ucGroup[0], $ucGroup[1], 165, 45, ["Đặt món theo nhóm", "Realtime"]);
drawUseCaseEllipse($im, $ucPay[0], $ucPay[1], 165, 45, ["Thanh toán VietQR", "PayOS tự động"]);
drawUseCaseEllipse($im, $ucSub[0], $ucSub[1], 160, 42, "Gói ăn định kỳ tuần/tháng");
drawUseCaseEllipse($im, $ucTrack[0], $ucTrack[1], 160, 42, "Tra cứu tiến độ đơn");
drawUseCaseEllipse($im, $ucLoyalty[0], $ucLoyalty[1], 160, 42, "Tích điểm thưởng & Đổi quà");

// Connections for Customer
drawSolidLine($im, 120, 520, $ucLogin[0] - 75, $ucLogin[1]);
drawSolidLine($im, 120, 520, $ucReset[0] - 75, $ucReset[1]);
drawSolidLine($im, 120, 520, $ucSearch[0] - 80, $ucSearch[1]);
drawSolidLine($im, 120, 520, $ucAi[0] - 80, $ucAi[1]);
drawSolidLine($im, 120, 520, $ucGroup[0] - 80, $ucGroup[1]);
drawSolidLine($im, 120, 520, $ucPay[0] - 80, $ucPay[1]);
drawSolidLine($im, 120, 520, $ucSub[0] - 80, $ucSub[1]);
drawSolidLine($im, 120, 520, $ucTrack[0] - 80, $ucTrack[1]);
drawSolidLine($im, 120, 520, $ucLoyalty[0] - 80, $ucLoyalty[1]);

// Customer Extend Sub-use cases
drawUseCaseEllipse($im, 530, 75, 110, 34, "Lọc danh mục / giá");
drawDashedArrow($im, 585, 75, $ucSearch[0] - 80, $ucSearch[1], "<<Extend>>");

drawUseCaseEllipse($im, 540, 230, 115, 34, "Tạo phòng cấp PIN");
drawDashedArrow($im, 595, 240, $ucGroup[0] - 70, $ucGroup[1] - 5, "<<Extend>>");

drawUseCaseEllipse($im, 540, 280, 115, 34, "Đồng bộ giỏ Realtime");
drawDashedArrow($im, 595, 275, $ucGroup[0] - 75, $ucGroup[1] + 5, "<<Extend>>");

drawUseCaseEllipse($im, 540, 370, 115, 34, "Webhook khớp lệnh");
drawDashedArrow($im, 595, 370, $ucPay[0] - 80, $ucPay[1], "<<Extend>>");

// Use cases - Staff & Kitchen
$ucWork = [910, 190];
$ucBill = [910, 270];
$ucPack = [910, 350];

drawUseCaseEllipse($im, $ucWork[0], $ucWork[1], 155, 42, "Bàn làm việc thu ngân");
drawUseCaseEllipse($im, $ucBill[0], $ucBill[1], 155, 42, "In hóa đơn nhiệt 80mm");
drawUseCaseEllipse($im, $ucPack[0], $ucPack[1], 155, 42, "Đóng gói & Bàn giao");

drawSolidLine($im, 1080, 260, $ucLogin[0] + 75, $ucLogin[1]);
drawSolidLine($im, 1080, 260, $ucWork[0] + 75, $ucWork[1]);
drawSolidLine($im, 1080, 260, $ucBill[0] + 75, $ucBill[1]);
drawSolidLine($im, 1080, 260, $ucPack[0] + 75, $ucPack[1]);

$ucKds = [910, 520];
$ucBatch = [910, 600];
$ucCook = [910, 690];

drawUseCaseEllipse($im, $ucKds[0], $ucKds[1], 155, 45, ["Màn hình Bếp FIFO", "(KDS Display)"]);
drawUseCaseEllipse($im, $ucBatch[0], $ucBatch[1], 155, 42, "Bảng gom số lượng món");
drawUseCaseEllipse($im, $ucCook[0], $ucCook[1], 155, 42, "Cập nhật nấu xong món");

drawSolidLine($im, 1080, 600, $ucLogin[0] + 75, $ucLogin[1]);
drawSolidLine($im, 1080, 600, $ucKds[0] + 75, $ucKds[1]);
drawSolidLine($im, 1080, 600, $ucBatch[0] + 75, $ucBatch[1]);
drawSolidLine($im, 1080, 600, $ucCook[0] + 75, $ucCook[1]);

// Output PNG
$outPath = 'docs/images/luoc_do_2_1_use_case.png';
imagepng($im, $outPath);
imagedestroy($im);

echo "Generated UML Use Case Diagram at: $outPath\n";

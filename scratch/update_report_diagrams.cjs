const fs = require('fs');
const path = require('path');

let code = fs.readFileSync('scratch/build_comprehensive_report.cjs', 'utf8');

// 1. In 2.2.1 Lược đồ Use case, insert the use case diagram image
const targetUseCase = `bodyChildren.push(pHeading3("2.2.1\\tLược đồ Use case"));
    bodyChildren.push(pBody("Lược đồ 2.1: Lược đồ use case tổng quát toàn hệ thống FOODDAILY biểu diễn các mối quan hệ tương tác giữa 4 tác nhân chính (Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên Admin) với toàn bộ các ca sử dụng trong hệ thống:"));`;

const replaceUseCase = `bodyChildren.push(pHeading3("2.2.1\\tLược đồ Use case"));
    bodyChildren.push(pBody("Lược đồ 2.1: Lược đồ use case tổng quát toàn hệ thống FOODDAILY biểu diễn các mối quan hệ tương tác giữa 4 tác nhân chính (Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên Admin) với toàn bộ các ca sử dụng trong hệ thống:"));
    bodyChildren.push(...makeImageBlock("luoc_do_2_1_use_case.png", "Lược đồ 2.1 Lược đồ use case.", 560, 395));`;

code = code.replace(targetUseCase, replaceUseCase);

// 2. Add table captions under each of the 11 Use Case tables
// Let's check how the 11 tables are added
const ucCaptions = [
    ["2.2.2.1\\tĐặc tả use case đăng nhập vào hệ thống.", "Bảng 2.1 Luồng sự kiện chính use case đăng nhập."],
    ["2.2.2.2\\tĐặc tả use case đăng xuất vào hệ thống.", "Bảng 2.2 Luồng sự kiện chính use case đăng xuất."],
    ["2.2.2.3\\tĐặc tả use case tìm kiếm và xem chi tiết món ăn.", "Bảng 2.3 Luồng sự kiện chính use case tìm kiếm và xem món ăn."],
    ["2.2.2.4\\tĐặc tả use case đặt món theo nhóm Realtime.", "Bảng 2.4 Luồng sự kiện chính use case đặt món theo nhóm Realtime."],
    ["2.2.2.5\\tĐặc tả use case thanh toán tự động VietQR PayOS.", "Bảng 2.5 Luồng sự kiện chính use case thanh toán tự động VietQR PayOS."],
    ["2.2.2.6\\tĐặc tả use case Trợ lý AI Gemini tư vấn món ăn.", "Bảng 2.6 Luồng sự kiện chính use case Trợ lý AI Gemini tư vấn món."],
    ["2.2.2.7\\tĐặc tả use case điều phối chế biến tại Màn hình Bếp FIFO.", "Bảng 2.7 Luồng sự kiện chính use case điều phối chế biến Bếp FIFO."],
    ["2.2.2.8\\tĐặc tả use case đăng ký và quản lý gói ăn định kỳ.", "Bảng 2.8 Luồng sự kiện chính use case đăng ký gói ăn định kỳ."],
    ["2.2.2.9\\tĐặc tả use case quản lý thực đơn món ăn.", "Bảng 2.9 Luồng sự kiện chính use case quản lý thực đơn món ăn."],
    ["2.2.2.10\\tĐặc tả use case quản lý kho nguyên liệu và nhà cung cấp.", "Bảng 2.10 Luồng sự kiện chính use case quản lý kho và nhà cung cấp."],
    ["2.2.2.11\\tĐặc tả use case quản lý khuyến mãi và cấp voucher ví.", "Bảng 2.11 Luồng sự kiện chính use case quản lý khuyến mãi và voucher."]
];

// 3. In 2.2.2 Lược đồ Activity, insert the images
const targetActivityLoop = `    activities.forEach(act => {
        bodyChildren.push(pHeading4(act[0]));
        bodyChildren.push(pBullet("Tên lược đồ", act[1]));
        bodyChildren.push(pBullet("Mô tả luồng hoạt động", act[2]));
    });`;

const actImages = [
    "luoc_do_2_2_dang_nhap.png",
    "luoc_do_2_3_reset_password.png",
    "luoc_do_2_4_tim_kiem.png",
    "luoc_do_2_5_dat_mon_nhom.png",
    "luoc_do_2_6_thanh_toan_payos.png",
    "luoc_do_2_7_dieu_phoi_bep.png",
    "luoc_do_2_8_in_hoa_don.png",
    "luoc_do_2_9_quan_ly_khach_hang.png",
    "luoc_do_2_10_quan_ly_mon_an.png",
    "luoc_do_2_11_quan_ly_nha_cung_cap.png",
    "luoc_do_2_12_nhap_kho_nguyen_lieu.png",
    "luoc_do_2_13_quan_ly_nhan_vien.png"
];

const replaceActivityLoop = `    const actImages = ${JSON.stringify(actImages)};
    activities.forEach((act, idx) => {
        bodyChildren.push(pHeading4(act[0]));
        bodyChildren.push(pBullet("Tên lược đồ", act[1]));
        bodyChildren.push(pBullet("Mô tả luồng hoạt động", act[2]));
        if (actImages[idx]) {
            bodyChildren.push(...makeImageBlock(actImages[idx], act[1] + ".", 340, 420));
        }
    });`;

code = code.replace(targetActivityLoop, replaceActivityLoop);

fs.writeFileSync('scratch/build_comprehensive_report.cjs', code, 'utf8');
console.log('Updated build_comprehensive_report.cjs successfully');

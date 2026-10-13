const fs = require('fs');

let code = fs.readFileSync('scratch/build_comprehensive_report.cjs', 'utf8');

const startMarker = '    // 2.2.3 BIỂU ĐỒ THÀNH PHẦN';
const endMarker = '    // 2.3 CƠ SỞ DỮ LIỆU';

const pStart = code.indexOf(startMarker);
const pEnd = code.indexOf(endMarker);

if (pStart !== -1 && pEnd !== -1) {
    const newSection = `    // 2.2.3 BIỂU ĐỒ THÀNH PHẦN
    bodyChildren.push(pHeading3("2.2.3\\tBiểu đồ thành phần"));
    bodyChildren.push(pBody("Biểu đồ thành phần (Component Diagram) thể hiện cấu trúc mô-đun hóa của hệ thống FOODDAILY, sự phân chia trách nhiệm và các giao diện giao tiếp giữa các thành phần phần mềm trong hệ thống:"));

    // 2.2.4.1 Biểu đồ thành phần hệ thống
    bodyChildren.push(pHeading4("2.2.4.1\\tBiểu đồ thành phần hệ thống"));
    bodyChildren.push(pBody("Biểu đồ thành phần hệ thống tổng quan thể hiện cấu trúc phân rã giữa ứng dụng Website cốt lõi với hai phân hệ chính: Giao diện quản trị viên (Admin) và Giao diện người dùng (Khách hàng):"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_1_thanh_phan_tong_quan.png", "Biểu đồ 2.1 Biểu đồ thành phần tổng quan hệ thống.", 520, 240));

    // 2.2.4.2 Biểu đồ thành phần quản trị viên
    bodyChildren.push(pHeading4("2.2.4.2\\tBiểu đồ thành phần quản trị viên"));
    bodyChildren.push(pBody("Biểu đồ thành phần phân hệ quản trị viên mô tả mối liên kết giữa Giao diện Admin với toàn bộ các mô-đun quản lý nghiệp vụ, dịch vụ lưu trữ dữ liệu, bảo mật và kết nối cơ sở dữ liệu MySQL:"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_2_thanh_phan_admin.png", "Biểu đồ 2.2 Biểu đồ thành phần quản trị viên.", 540, 440));

    // 2.2.4.4 Biểu đồ thành phần người dùng
    bodyChildren.push(pHeading4("2.2.4.4\\tBiểu đồ thành phần người dùng"));
    bodyChildren.push(pBody("Biểu đồ thành phần người dùng mô tả các mô-đun chức năng giao tiếp trực tiếp với khách hàng: tìm kiếm món ăn kết hợp AI, đặt món theo nhóm Realtime, thanh toán VietQR và giỏ hàng:"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_3_thanh_phan_nguoi_dung.png", "Biểu đồ 2.3 Biểu đồ thành phần người dùng.", 540, 250));

    // 2.2.4 BIỂU ĐỒ TRIỂN KHAI
    bodyChildren.push(pHeading3("2.2.4\\tBiểu đồ triển khai"));
    bodyChildren.push(pBody("Biểu đồ triển khai (Deployment Diagram) thể hiện kiến trúc vật lý và các nút phần cứng, phần mềm máy chủ phục vụ vận hành hệ thống FOODDAILY:"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_4_trien_khai.png", "Biểu đồ 2.4 Biểu đồ triển khai.", 540, 210));
    bodyChildren.push(pBullet("Nút Web Server", "Máy chủ web Nginx / Laragon tiếp nhận các truy vấn HTTP/HTTPS từ phía Client trên Port 80/443 và phân phối yêu cầu xử lý."));
    bodyChildren.push(pBullet("Nút Application Server", "Máy chủ ứng dụng chạy PHP 8.3 FPM thực thi mã nguồn Laravel 13, kết hợp tiến trình WebSocket Laravel Reverb chạy độc lập trên Port 8080 xử lý thông báo thời gian thực."));
    bodyChildren.push(pBullet("Nút Database Server", "Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0 lưu trữ an toàn toàn bộ dữ liệu nghiệp vụ, cấu hình Storage Engine InnoDB và cơ chế Transaction ACID."));
    bodyChildren.push(pBullet("Nút External Cloud Services", "Kết nối qua API HTTPS bảo mật đến: Cổng thanh toán VietQR PayOS, Dịch vụ Trí tuệ Nhân tạo Google Gemini AI và Kho lưu trữ hình ảnh Cloudinary CDN."));\n\n`;

    code = code.substring(0, pStart) + newSection + code.substring(pEnd);
    fs.writeFileSync('scratch/build_comprehensive_report.cjs', code, 'utf8');
    console.log("Successfully replaced component and deployment sections!");
} else {
    console.log("Markers not found:", pStart, pEnd);
}

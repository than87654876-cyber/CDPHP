const fs = require('fs');

let code = fs.readFileSync('scratch/build_comprehensive_report.cjs', 'utf8');

// Target 2.2.3 and 2.2.4
const targetSection = `    // 2.2.3 BIỂU ĐỒ THÀNH PHẦN
    bodyChildren.push(pHeading3("2.2.3\\tBiểu đồ thành phần"));
    bodyChildren.push(pBody("Biểu đồ thành phần (Component Diagram) thể hiện cấu trúc mô-đun hóa của hệ thống FOODDAILY, sự phân chia trách nhiệm và các giao diện giao tiếp giữa các thành phần phần mềm:"));
    bodyChildren.push(pBullet("Biểu đồ 2.1 Biểu đồ thành phần hệ thống tổng thể", "Hệ thống tổ chức theo mô hình kiến trúc đa tầng (Multi-tier Architecture): Tầng giao diện Client (Blade Templates, Tailwind CSS, JavaScript ES6), Tầng điều khiển HTTP Routing & Controller Layer (Laravel 13), Tầng nghiệp vụ Service Layer (PaymentService, ReverbWebSocketService, GeminiAIService), Tầng dữ liệu Eloquent ORM và Tầng cơ sở dữ liệu MySQL 8.0 kết nối các cổng dịch vụ bên ngoài (PayOS VietQR API, Google Gemini AI API, Cloudinary Storage)."));
    bodyChildren.push(pBullet("Biểu đồ 2.2 Biểu đồ thành phần phân hệ quản trị viên", "Bao gồm các Controller chuyên trách: AdminDashboardController (thống kê), DishManagementController (thực đơn), OrderManagementController (đơn hàng), InventoryController (kho nguyên liệu), CouponController (khuyến mãi) và RolePermissionController (phân quyền Spatie RBAC)."));
    bodyChildren.push(pBullet("Biểu đồ 2.3 Biểu đồ thành phần phân hệ người dùng", "Bao gồm các Controller phục vụ khách hàng: HomeController, MenuController, CartController, GroupOrderController (phòng nhóm realtime), CheckoutController (thanh toán PayOS), SubscriptionController (gói ăn định kỳ) và OrderTrackingController (tra cứu tiến độ)."));

    // 2.2.4 BIỂU ĐỒ TRIỂN KHAI
    bodyChildren.push(pHeading3("2.2.4\\tBiểu đồ triển khai"));
    bodyChildren.push(pBody("Biểu đồ 2.4: Biểu đồ triển khai hệ thống FOODDAILY thể hiện cấu hình kiến trúc phần cứng, phần mềm máy chủ và hạ tầng mạng vận hành:"));
    bodyChildren.push(pBullet("Nút Client Devices", "Bao gồm máy tính cá nhân (Desktop/Laptop), máy tính bảng và điện thoại di động thông minh (iOS/Android) của khách hàng, nhân viên quầy và đầu bếp. Giao tiếp với máy chủ thông qua giao thức HTTPS bảo mật (Port 443) và kênh WebSocket an toàn WSS (Port 8080)."));
    bodyChildren.push(pBullet("Nút Web Server & Application Server", "Máy chủ cài đặt hệ điều hành Windows Server / Linux với môi trường Laragon / Nginx Web Server. Chạy phiên bản PHP 8.3 FPM xử lý mã nguồn Laravel 13 Framework."));
    bodyChildren.push(pBullet("Nút WebSocket Server (Laravel Reverb)", "Tiến trình máy chủ WebSocket độc lập chạy trên Port 8080 chịu trách nhiệm lắng nghe và phát tín hiệu Realtime cho phân hệ đặt nhóm và Màn hình Bếp FIFO."));
    bodyChildren.push(pBullet("Nút Database Server (MySQL 8.0)", "Lưu trữ tập trung dữ liệu toàn hệ thống, cấu hình InnoDB Engine với cơ chế Transaction ACID bảo vệ giao dịch tài chính PayOS."));`;

const replaceSection = `    // 2.2.3 BIỂU ĐỒ THÀNH PHẦN
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
    bodyChildren.push(pBullet("Nút Database Server", "Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0 lưu trữ an toàn toàn bộ dữ liệu nghiệp vụ, cấu hình Storage Engine InnoDB và cơ chế Transaction ACID."));`;

if (code.includes(targetSection)) {
    code = code.replace(targetSection, replaceSection);
    console.log("Replaced component and deployment diagram sections.");
} else {
    console.log("Could not find exact targetSection, check string matching.");
}

// Target 2.3 Database ERD image
const targetErd = `    bodyChildren.push(pBody("Hình 2.1: Lược đồ quan hệ cơ sở dữ liệu (Database Schema ERD) thể hiện đầy đủ các mối quan hệ 1-Nhiều (1-n) và Nhiều-Nhiều (n-n) giữa các thực thể cốt lõi trong hệ thống."));
    bodyChildren.push(pBody("Dưới đây là bảng đặc tả cấu trúc chi tiết của 12 bảng cơ sở dữ liệu chính:"));`;

const replaceErd = `    bodyChildren.push(pBody("Hình 2.2: Sơ đồ Cơ sở dữ liệu (Database Schema ERD) thể hiện đầy đủ các mối quan hệ 1-Nhiều (1-n) và Nhiều-Nhiều (n-n) giữa 12 thực thể cốt lõi trong hệ thống FOODDAILY:"));
    bodyChildren.push(...makeImageBlock("hinh_2_2_co_so_du_lieu.png", "Hình 2.2 Cơ sở dữ liệu.", 560, 340));
    bodyChildren.push(pBody("Dưới đây là bảng đặc tả cấu trúc chi tiết của 12 bảng cơ sở dữ liệu chính:"));`;

if (code.includes(targetErd)) {
    code = code.replace(targetErd, replaceErd);
    console.log("Replaced ERD image block.");
} else {
    console.log("Could not find exact targetErd.");
}

fs.writeFileSync('scratch/build_comprehensive_report.cjs', code, 'utf8');
console.log('Finished updating build_comprehensive_report.cjs');

const fs = require('fs');
const path = require('path');

let md = fs.readFileSync('docs/BAO_CAO_DU_AN_FOODDAILY.md', 'utf8');

// Target 2.2.3 in MD
const targetMd223 = `### 2.2.3 Biểu đồ thành phần

#### 2.2.4.1 Biểu đồ thành phần hệ thống:
Hệ thống gồm 3 tầng: Tầng Trình duyệt (Client), Tầng Ứng dụng Backend (Laravel 13), và Tầng Dữ liệu & Dịch vụ ngoài (MySQL, Reverb, PayOS, Gemini AI, Cloudinary).

#### 2.2.4.2 Biểu đồ thành phần quản trị viên:
Bao gồm các Controller quản trị: \`AdminDashboardController\`, \`AdminDishController\`, \`AdminOrderController\`, \`AdminProcurementController\`, \`AdminPromotionController\`, \`AdminRoleController\` kết nối với các Service tương ứng.

#### 2.2.4.4 Biểu đồ thành phần người dùng:
Bao gồm các Controller phía khách hàng: \`ShopController\`, \`CartController\`, \`GroupOrderController\`, \`SubscriptionController\`, \`AuthController\` tương tác với giao diện Blade Engine và JavaScript Fetch API.

---

### 2.2.4 Biểu đồ triển khai
Hệ thống triển khai trên nền tảng Nginx / Laragon Web Server kết hợp PHP-FPM 8.3, cơ sở dữ liệu MySQL 8.0, máy chủ WebSockets Laravel Reverb (Port 8080) và kết nối với các API đám mây qua giao thức HTTPS bảo mật.`;

const replaceMd223 = `### 2.2.3 Biểu đồ thành phần

Biểu đồ thành phần (Component Diagram) thể hiện cấu trúc mô-đun hóa của hệ thống FOODDAILY, sự phân chia trách nhiệm và các giao diện giao tiếp giữa các thành phần phần mềm:

#### 2.2.4.1 Biểu đồ thành phần hệ thống
Biểu đồ thành phần hệ thống tổng quan thể hiện cấu trúc phân rã giữa ứng dụng Website cốt lõi với hai phân hệ chính: Giao diện quản trị viên (Admin) và Giao diện người dùng (Khách hàng):

![Biểu đồ 2.1 Biểu đồ thành phần tổng quan hệ thống](images/bieu_do_2_1_thanh_phan_tong_quan.png)

*Biểu đồ 2.1: Biểu đồ thành phần tổng quan hệ thống.*

#### 2.2.4.2 Biểu đồ thành phần quản trị viên
Biểu đồ thành phần phân hệ quản trị viên mô tả mối liên kết giữa Giao diện Admin với toàn bộ các mô-đun quản lý nghiệp vụ, dịch vụ lưu trữ dữ liệu, bảo mật và kết nối cơ sở dữ liệu MySQL:

![Biểu đồ 2.2 Biểu đồ thành phần quản trị viên](images/bieu_do_2_2_thanh_phan_admin.png)

*Biểu đồ 2.2: Biểu đồ thành phần quản trị viên.*

#### 2.2.4.4 Biểu đồ thành phần người dùng
Biểu đồ thành phần người dùng mô tả các mô-đun chức năng giao tiếp trực tiếp với khách hàng: tìm kiếm món ăn kết hợp AI, đặt món theo nhóm Realtime, thanh toán VietQR và giỏ hàng:

![Biểu đồ 2.3 Biểu đồ thành phần người dùng](images/bieu_do_2_3_thanh_phan_nguoi_dung.png)

*Biểu đồ 2.3: Biểu đồ thành phần người dùng.*

---

### 2.2.4 Biểu đồ triển khai
Biểu đồ triển khai (Deployment Diagram) thể hiện kiến trúc vật lý và các nút phần cứng, phần mềm máy chủ phục vụ vận hành hệ thống FOODDAILY:

![Biểu đồ 2.4 Biểu đồ triển khai](images/bieu_do_2_4_trien_khai.png)

*Biểu đồ 2.4: Biểu đồ triển khai.*

- **Nút Web Server:** Máy chủ web Nginx / Laragon tiếp nhận các truy vấn HTTP/HTTPS từ phía Client trên Port 80/443 và phân phối yêu cầu xử lý.
- **Nút Application Server:** Máy chủ ứng dụng chạy PHP 8.3 FPM thực thi mã nguồn Laravel 13, kết hợp tiến trình WebSocket Laravel Reverb chạy độc lập trên Port 8080 xử lý thông báo thời gian thực.
- **Nút Database Server:** Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0 lưu trữ an toàn toàn bộ dữ liệu nghiệp vụ, cấu hình Storage Engine InnoDB và cơ chế Transaction ACID.
- **Nút External Cloud Services:** Kết nối qua API HTTPS bảo mật đến: Cổng thanh toán VietQR PayOS, Dịch vụ Trí tuệ Nhân tạo Google Gemini AI và Kho lưu trữ hình ảnh Cloudinary CDN.`;

if (md.includes(targetMd223)) {
    md = md.replace(targetMd223, replaceMd223);
    console.log("Updated 2.2.3 and 2.2.4 in MD.");
} else {
    console.log("Could not find targetMd223 in MD.");
}

// Target ERD in MD
const targetErdMd = `## 2.3 Cơ sở dữ liệu

### Tóm tắt ý nghĩa các bảng dữ liệu:`;

const replaceErdMd = `## 2.3 Cơ sở dữ liệu

Hình 2.2: Sơ đồ Cơ sở dữ liệu (Database Schema ERD) thể hiện đầy đủ các mối quan hệ 1-Nhiều (1-n) và Nhiều-Nhiều (n-n) giữa 12 thực thể cốt lõi trong hệ thống FOODDAILY:

![Hình 2.2 Cơ sở dữ liệu](images/hinh_2_2_co_so_du_lieu.png)

*Hình 2.2: Sơ đồ Cơ sở dữ liệu.*

### Tóm tắt ý nghĩa các bảng dữ liệu:`;

if (md.includes(targetErdMd)) {
    md = md.replace(targetErdMd, replaceErdMd);
    console.log("Updated ERD in MD.");
} else {
    console.log("Could not find targetErdMd in MD.");
}

fs.writeFileSync('docs/BAO_CAO_DU_AN_FOODDAILY.md', md, 'utf8');

// Also sync to brain artifact
const artifactPath = 'C:/Users/Acer/.gemini/antigravity-ide/brain/dba20c26-96e2-41df-b710-97b3cbacfe87/bao_cao_du_an_fooddaily.md';
if (fs.existsSync(path.dirname(artifactPath))) {
    fs.writeFileSync(artifactPath, md, 'utf8');
}

console.log("Synced MD and brain artifact successfully!");

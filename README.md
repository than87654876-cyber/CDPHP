# 🍽️ FOODDAILY - Nền tảng Đặt & Giao Đồ Ăn Trực Tuyến Tích Hợp AI & Realtime

<p align="center">
  <img src="public/logo.jpg" alt="FOODDAILY Logo" width="120" style="border-radius: 50%;">
</p>

FOODDAILY là hệ sinh thái thương mại điện tử F&B toàn diện được xây dựng trên nền tảng **Laravel 13**, kết hợp công nghệ thời gian thực (**Laravel Reverb / WebSockets**), trí tuệ nhân tạo tư vấn thực đơn (**Google Gemini AI**), cổng thanh toán tự động đối soát (**PayOS VietQR**), và phân hệ quản lý kho vận - nhà cung cấp - điều phối bếp thông minh.

---

## 🚀 Tính năng cốt lõi

### 1. Phân hệ Khách hàng (Client Portal)
- **Đặt món ăn linh hoạt:** Tìm kiếm theo danh mục, mức giá, gợi ý món hay ăn theo thói quen.
- **Vòng quay may mắn:** "Hôm nay mình ăn gì?" gợi ý ngẫu nhiên các món theo ngân sách cài đặt.
- **Trợ lý AI Gemini:** Tư vấn món ăn thông minh dựa theo tâm trạng, sở thích và thực đơn có sẵn.
- **Đặt đơn theo nhóm (Group Order):** Tạo phòng đặt món chung qua mã phòng/QR code, tự động tổng hợp đơn hàng.
- **Gói ăn định kỳ (Meal Subscriptions):** Mua combo theo tuần/tháng, tùy chọn đổi món từng ngày trước giờ nấu.
- **Thanh toán VietQR PayOS & MoMo:** Thanh toán quét mã QR tự động xác nhận qua webhook thời gian thực.
- **Theo dõi đơn hàng Realtime:** Tra cứu đơn hàng cho cả thành viên và khách vãng lai.

### 2. Phân hệ Vận hành Bếp & Bàn làm việc (Kitchen & Staff Portal)
- **Điều phối Bếp FIFO:** Hàng đợi chế biến ưu tiên đơn đặt trước làm trước, gom nhóm số lượng theo từng món.
- **Bàn làm việc nhân viên (Staff Workspace):** Thống kê nhanh đơn chờ xử lý, lịch gói ăn trong ngày, cảnh báo nguyên liệu tồn kho thấp.

### 3. Phân hệ Quản trị Cấp cao (Admin Portal)
- **Phân quyền đa tầng (RBAC):** Super Admin, Admin, Staff, Kitchen, Shipper quản lý qua Spatie Permission.
- **Quản lý Kho & Nhập hàng (Procurement & Inventory):** Theo dõi nguyên liệu, tồn kho, nhà cung cấp và lập phiếu nhập kho.
- **Chiến dịch Khuyến mãi & Ví Voucher:** Phát hành mã giảm giá, tự động lưu vào ví thành viên và gửi email thông báo.
- **Sao lưu & Phục hồi dữ liệu (Customer Backup):** Xuất file zip sao lưu định kỳ, hỗ trợ khôi phục an toàn.
- **Thông báo Telegram Bot:** Tự động gửi cảnh báo đơn hàng mới, thanh toán thành công và doanh thu đến nhóm Telegram.

---

## 🛠️ Công nghệ sử dụng

- **Backend:** PHP 8.3+, Laravel 13, Eloquent ORM, Spatie Laravel Permission
- **Frontend:** Laravel Blade Template Engine, Tailwind CSS v4, JavaScript Modern Fetch API
- **Realtime:** Laravel Reverb / WebSockets
- **Database:** MySQL 8.0+ / SQLite (Testing)
- **AI & Third-party Services:**
  - Google Gemini 1.5 Flash (Trợ lý ảo tư vấn)
  - PayOS (Cổng thanh toán tự động VietQR)
  - Cloudinary (Lưu trữ hình ảnh Cloud)
  - Telegram Bot API (Thông báo nội bộ)

---

## ⚙️ Hướng dẫn cài đặt & Triển khai

### 1. Yêu cầu hệ thống
- PHP >= 8.3 (với các extensions: `pdo_mysql`, `gd`, `zip`, `fileinfo`, `curl`)
- MySQL >= 8.0 hoặc MariaDB
- Composer 2.x
- Node.js >= 20.x & npm

### 2. Các bước cài đặt

```bash
# 1. Clone repository hoặc giải nén mã nguồn vào thư mục web server (Laragon, XAMPP, v.v.)
cd c:\laragon\www\CDPHP

# 2. Cài đặt các gói phụ thuộc PHP
composer install

# 3. Tạo file cấu hình môi trường
copy .env.example .env
php artisan key:generate

# 4. Cấu hình thông tin Database trong file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=CDPHP
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Chạy migration và seed dữ liệu mẫu
php artisan migrate --seed

# 6. Cài đặt và build assets giao diện
npm install
npm run build
```

### 3. Khởi động môi trường phát triển (Development)

Bạn có thể chạy toàn bộ hệ thống cùng lúc thông qua Composer script:
```bash
composer run dev
```
Hoặc chạy thủ công các tiến trình:
```bash
# Web Server
php artisan serve

# Queue Worker (gửi mail, thông báo)
php artisan queue:listen --tries=1

# Realtime WebSocket Server (Laravel Reverb)
php artisan reverb:start
```

---

## ⏰ Các lệnh Artisan tự động (Cronjobs & Schedule)

Hệ thống đã cấu hình sẵn trong `routes/console.php`:

| Lệnh | Tần suất | Chức năng |
| :--- | :--- | :--- |
| `php artisan orders:cancel-overdue` | Mỗi 1 phút | Tự động hủy đơn hàng quá 15 phút chưa xác nhận/chưa thanh toán |
| `php artisan subscription:dispatch-daily` | 05:00 sáng | Chuẩn bị lịch giao món ăn gói combo của các hội viên trong ngày |
| `php artisan backup:customers` | 00:00 đêm | Tự động tạo bản sao lưu dữ liệu khách hàng |

Để kích hoạt hệ thống chạy tự động, cấu hình Cron / Task Scheduler gọi:
```bash
* * * * * php c:\laragon\www\CDPHP\artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Kiểm thử (Testing)

Dự án có đầy đủ bộ kiểm thử Feature & Unit:
```bash
php artisan test
```

---
*Phát triển bởi Trần Lê Thân - Đồ án tốt nghiệp Công nghệ Thông tin.*

# 🎓 SƯỜN BÁO CÁO ĐỒ ÁN TỐT NGHIỆP / DỰ ÁN CÔNG NGHỆ (CHUẨN 4 CHƯƠNG)
# ĐỀ TÀI: HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY

---

## 📌 THÔNG TIN ĐỀ TÀI
* **Tên đề tài:** Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo và đặt món theo nhóm – FOODDAILY
* **Sinh viên thực hiện:** Trần Lê Thân
* **Nền tảng công nghệ:** Laravel 13, PHP 8.3, MySQL, Blade Template Engine, TailwindCSS, Google Gemini AI, PayOS VietQR
* **Bố cục đồ án:** **4 Chương chuẩn** theo quy định báo cáo Đồ án Công nghệ phần mềm / Tốt nghiệp.

---

## 📑 BỐ CỤC CHI TIẾT SƯỜN BÁO CÁO (4 CHƯƠNG)

### LỜI MỞ ĐẦU
1. **Lý do chọn đề tài:**
   * Nhu cầu đặt đồ ăn trực tuyến ngày càng cao của giới văn phòng, học sinh - sinh viên.
   * Những bất cập khi đặt đồ ăn chung cho tập thể (gom đơn thủ công, dễ nhầm món, tính sót tiền).
   * Vấn đề phân vân "hôm nay ăn gì" và nhu cầu tư vấn món ăn cá nhân hóa bằng AI.
2. **Mục đích nghiên cứu:**
   * Xây dựng hệ thống thương mại điện tử F&B khép kín từ Khách hàng $\rightarrow$ Bếp $\rightarrow$ Ban Quản trị.
   * Tích hợp Trí tuệ nhân tạo Google Gemini AI làm trợ lý ẩm thực thông minh.
   * Phát triển tính năng Đặt món theo nhóm thời gian thực (Realtime Group Order) qua mã QR / liên kết phòng.
   * Tự động hóa thanh toán quét mã QR qua cổng PayOS VietQR.
3. **Phạm vi và phương pháp nghiên cứu:**
   * Phạm vi: Ứng dụng đặt món và quản trị cho nhà hàng, chuỗi quán ăn giao tận nơi & gói ăn định kỳ.
   * Phương pháp: Mô hình Agile/Scrum, thiết kế hướng module MVC phân tầng trên Laravel 13.
4. **Kết cấu của đồ án:** Tóm tắt nội dung chính của 4 chương.

---

### CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI VÀ CƠ SỞ CÔNG NGHỆ
* **1.1. Bối cảnh thực tiễn và phát biểu bài toán:**
  * Khái quát về mô hình kinh doanh F&B trực tuyến hiện đại.
  * Phân tích những khó khăn thực tế:
    * Khó khăn khi gom đơn tập thể phòng ban (tốn thời gian, dễ nhầm lẫn).
    * Khách hàng phân vân khi chọn món do thiếu công cụ gợi ý ngữ cảnh.
    * Đứt gãy luồng thông tin giữa nhân viên phục vụ và nhà bếp gây trễ đơn.
    * Rủi ro và sự chậm trễ khi đối soát chuyển khoản ngân hàng thủ công.
* **1.2. Mục tiêu và giải pháp của hệ thống FOODDAILY:**
  * Giải pháp phòng ăn ảo Realtime cho nhóm qua mã QR / PIN.
  * Trợ lý ảo Google Gemini AI đọc hiểu thực đơn và tư vấn món ăn thông minh.
  * Thanh toán tự động VietQR khớp lệnh tức thì qua Webhook trong 1-2 giây.
* **1.3. Cơ sở lý thuyết và công nghệ nền tảng sử dụng:**
  * Framework Laravel 13 & Ngôn ngữ lập trình PHP 8.3 (MVC Architecture).
  * Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0.
  * Giao diện người dùng: Blade Template Engine, TailwindCSS, JavaScript Modern Fetch API.
  * Công nghệ đồng bộ dữ liệu thời gian thực (Realtime Long-Polling / WebSockets).
  * Dịch vụ bên thứ ba: Google Gemini 1.5 Flash API, Cổng PayOS VietQR, OpenStreetMap Nominatim.

---

### CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG
* **2.1. Xác định yêu cầu và phân định vai trò người dùng (Actors):**
  * *Khách hàng (Customer):* Xem thực đơn, chat AI, đặt món cá nhân/nhóm, thanh toán VietQR, tra cứu đơn, đánh giá.
  * *Nhân viên phục vụ & Thu ngân (Staff):* Duyệt đơn 1 chạm, thu tiền, in hóa đơn.
  * *Đầu bếp (Kitchen Staff):* Nhận đơn thời gian thực, nấu món theo hàng đợi ưu tiên FIFO.
  * *Ban Quản trị (Super Admin & Admin):* Quản lý thực đơn, tồn kho nguyên liệu, phân quyền nhân sự, báo cáo doanh thu.
* **2.2. Sơ đồ Use Case hệ thống:**
  * Sơ đồ Use Case tổng quát toàn hệ thống.
  * Các biểu đồ Use Case phân rã: Đặt món nhóm, Xử lý đơn hàng nhà bếp, Quản lý kho & Nhập hàng.
* **2.3. Sơ đồ Hoạt động (Activity Diagram):**
  * Luồng nghiệp vụ Đặt hàng & Thanh toán tự động qua PayOS VietQR.
  * Luồng nghiệp vụ Đặt đơn theo nhóm Realtime (Tạo phòng $\rightarrow$ Mời bạn bè $\rightarrow$ Chọn món $\rightarrow$ Chốt đơn).
  * Luồng nghiệp vụ Tiếp nhận và Chế biến đơn hàng tại Màn hình Bếp (Kitchen Display).
* **2.4. Thiết kế Cơ sở dữ liệu (Database Schema / ERD):**
  * Sơ đồ quan hệ thực thể (ERD) toàn hệ thống.
  * Chi tiết cấu trúc các bảng dữ liệu lõi trên MySQL: `users`, `dishes`, `categories`, `orders`, `order_items`, `group_orders`, `group_order_items`, `inventory`, `suppliers`, `purchase_orders`, `meal_subscriptions`, `vouchers`, `reviews`.
  * Các ràng buộc toàn vẹn dữ liệu (Khóa chính, Khóa ngoại, Chỉ mục tối ưu truy vấn).

---

### CHƯƠNG 3: CÀI ĐẶT VÀ HIỆN THỰC HÓA HỆ THỐNG
* **3.1. Kiến trúc phần mềm và bảo mật hệ thống:**
  * Kiến trúc phân tầng Laravel 13 (Controller $\rightarrow$ Service Layer $\rightarrow$ Model).
  * Cơ chế bảo mật phân quyền đa tầng RBAC (Super Admin, Admin, Staff).
  * *Điểm nhấn bảo mật:* Middleware `PreventBackHistory` triệt tiêu lỗi Back-Forward Cache (BFCache), chống xem lén dữ liệu quản trị sau khi đăng xuất.
* **3.2. Hiện thực hóa các phân hệ chức năng:**
  * *3.2.1. Phân hệ Khách hàng (Client Portal):*
    * Khám phá thực đơn, tìm kiếm thông minh, vòng quay ngẫu nhiên *"Hôm nay ăn gì?"*.
    * Giỏ hàng cá nhân, tự động tính phí ship Geocoding, tra cứu đơn hàng Realtime.
    * Đăng ký gói ăn định kỳ (Meal Subscriptions) theo tuần/tháng tự đổi món theo ngày.
  * *3.2.2. Phân hệ Đặt món theo nhóm Realtime (Group Order):*
    * Cơ chế phòng ăn ảo: Sinh mã PIN và mã QR chia sẻ link cho bạn bè.
    * Đồng bộ giỏ hàng nhóm hai chiều theo thời gian thực không cần tải lại trang.
  * *3.2.3. Phân hệ Vận hành Bếp & Bàn làm việc (Kitchen & Staff Workspace):*
    * Màn hình Bếp (KDS): Hàng đợi chế biến món theo nguyên tắc FIFO (vào trước làm trước).
    * Bàn làm việc thu ngân: Thao tác 1 chạm duyệt đơn, thu tiền và in hóa đơn thanh toán.
  * *3.2.4. Phân hệ Quản trị Kho & Nhập hàng (Procurement & Inventory):*
    * Theo dõi tồn kho nguyên liệu, cảnh báo mức tồn an toàn.
    * Quản lý nhà cung cấp và lập phiếu nhập kho nguyên vật liệu.
  * *3.2.5. Phân hệ Ban Quản trị Cấp cao (Admin Dashboard):*
    * Biểu đồ doanh thu trực quan, món ăn bán chạy nhất theo ngày/tháng.
    * Quản lý chiến dịch Khuyến mãi & Gửi Email Marketing hàng loạt đến Gmail toàn bộ khách hàng.
    * Công cụ sao lưu và phục hồi dữ liệu khách hàng (Customer Backup).
* **3.3. Tích hợp Trí tuệ nhân tạo (Google Gemini AI) và Cổng thanh toán VietQR:**
  * *Trợ lý ẩm thực AI:* Nạp ngữ cảnh thực đơn vào Gemini API; tư vấn món theo tâm trạng, thời tiết, ngân sách; hiển thị gợi ý kèm hình ảnh trực quan.
  * *Cổng thanh toán PayOS VietQR:* Sinh mã VietQR động chuẩn Napas; Webhook xác thực chữ ký HMAC SHA256 tự động khớp lệnh thanh toán tức thì.

---

### CHƯƠNG 4: KIỂM THỬ HỆ THỐNG VÀ ĐÁNH GIÁ KẾT QUẢ
* **4.1. Môi trường và chiến lược kiểm thử:**
  * Thiết lập môi trường Automation Testing với PHPUnit trên Laravel 13.
  * Phương pháp kiểm thử chức năng (Feature Testing) và kiểm thử đơn vị (Unit Testing).
* **4.2. Kịch bản và kết quả kiểm thử tự động:**
  * Thực thi bộ kịch bản kiểm thử toàn diện (**41/41 test cases đạt 100% Passed**):
    * Kiểm thử luồng Đặt hàng và tính tiền giỏ hàng (`CartTest`, `OrderTest`).
    * Kiểm thử luồng Đặt đơn nhóm và khóa phòng (`GroupOrderTest`).
    * Kiểm thử Webhook thanh toán VietQR PayOS (`PaymentTest`).
    * Kiểm thử Bảo mật phân quyền và chặn BFCache (`AuthSecurityTest`).
* **4.3. Đánh giá kết quả đạt được:**
  * Mức độ hoàn thiện sản phẩm so với mục tiêu đề ra ban đầu (đạt 100%).
  * Đánh giá hiệu năng và tính ổn định (thời gian phản hồi dưới 1 giây, dữ liệu realtime mượt mà).

---

### KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN
* **1. Kết luận:** Tổng kết các kết quả nghiên cứu và sản phẩm thực tế đã hoàn thành.
* **2. Hạn chế của hệ thống:** Chưa có cổng ứng dụng riêng cho shipper; kho nguyên liệu chưa tự trừ theo công thức định lượng món.
* **3. Hướng phát triển trong 2 tuần tiếp theo:**
  * *Tuần 1 (Kho, Bếp & Quản trị):* Định lượng món tự trừ kho (Recipe/BOM), chuông báo âm thanh bếp, tích điểm thăng hạng thành viên.
  * *Tuần 2 (Vận chuyển & Khách hàng):* Cổng ứng dụng cho Tài xế (Driver Portal), định vị Live GPS Tracking, thông báo đẩy trạng thái đơn (Web Push / Zalo ZNS).

---

### PHỤ LỤC & TÀI LIỆU THAM KHẢO
* **Phụ lục 1:** Mã nguồn Middleware bảo mật chống BFCache (`PreventBackHistory`).
* **Phụ lục 2:** Mã nguồn Service kết nối Google Gemini AI (`GeminiService.php`).
* **Phụ lục 3:** Mã nguồn xử lý Webhook thanh toán VietQR (`PayOSService.php`).
* **Phụ lục 4:** Bảng danh mục kết quả chạy Automation Testing (PHPUnit Results).
* **Tài liệu tham khảo:** Laravel Framework Documentation, Google Gemini API, PayOS VietQR API, Spatie Permission, TailwindCSS.

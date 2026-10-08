# 🎓 SƯỜN BÁO CÁO ĐỒ ÁN TỐT NGHIỆP / DỰ ÁN CÔNG NGHỆ (CHUẨN 4 CHƯƠNG)
# ĐỀ TÀI: HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY

---

## 📌 THÔNG TIN ĐỀ TÀI
* **Tên đề tài:** Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo và đặt món theo nhóm – FOODDAILY
* **Sinh viên thực hiện:** Trần Lê Thân
* **Nền tảng công nghệ:** Laravel 13, PHP 8.3, MySQL, Blade Template Engine, TailwindCSS, Google Gemini AI, PayOS VietQR
* **Bố cục đồ án:** **4 Chương chuẩn** (Đã bổ sung đầy đủ phương pháp nghiên cứu, quy trình xây dựng, kiến trúc module theo cấu trúc chuẩn tham khảo từ Nhóm 12).

---

## 📑 BỐ CỤC CHI TIẾT SƯỜN BÁO CÁO

### LỜI MỞ ĐẦU
1. **Lý do chọn đề tài:**
   * Nhu cầu đặt đồ ăn trực tuyến ngày càng cao của giới văn phòng, học sinh - sinh viên.
   * Những bất cập khi đặt đồ ăn chung cho tập thể (gom đơn thủ công, dễ nhầm món, tính sót tiền).
   * Vấn đề phân vân "hôm nay ăn gì" và nhu cầu tư vấn món ăn cá nhân hóa bằng AI.
2. **Mục đích nghiên cứu:**
   * Xây dựng hệ thống thương mại điện tử F&B khép kín từ Khách hàng $\rightarrow$ Bếp $\rightarrow$ Ban Quản trị.
   * Tích hợp Trí tuệ nhân tạo Google Gemini AI làm trợ lý ẩm thực thông minh 24/7.
   * Phát triển tính năng Đặt món theo nhóm thời gian thực (Realtime Group Order) qua mã QR / liên kết phòng.
   * Tự động hóa thanh toán quét mã QR qua cổng PayOS VietQR.
3. **Phạm vi và phương pháp nghiên cứu:**
   * Phạm vi: Ứng dụng đặt món và quản trị cho nhà hàng, chuỗi quán ăn giao tận nơi & gói ăn định kỳ.
   * Phương pháp: Mô hình Agile/Scrum, thiết kế hướng module MVC phân tầng trên Laravel 13.
4. **Kết cấu của đồ án:** Tóm tắt nội dung chính của 4 chương.

---

### CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI VÀ BÀI TOÁN THỰC TẾ
* **1.1. Bối cảnh thực tiễn và giới thiệu đề tài:**
  * Khái quát về mô hình kinh doanh F&B trực tuyến hiện đại.
  * Sự chuyển dịch từ gọi món truyền thống sang đặt món qua nền tảng số.
* **1.2. Phân tích bài toán và những khó khăn thực tế:**
  * *Khó khăn gom đơn tập thể:* Đặt đồ ăn theo nhóm văn phòng/học sinh tốn nhiều thời gian tổng hợp, dễ sót món và tính nhầm tiền.
  * *Tâm lý phân vân của khách hàng:* Thiếu công cụ gợi ý món ăn theo ngân sách, sở thích hoặc thời tiết.
  * *Đứt gãy luồng thông tin quầy - bếp:* Nhân viên phục vụ ghi chép thủ công dễ trễ đơn, nhầm thứ tự ưu tiên chế biến.
  * *Rủi ro đối soát chuyển khoản:* Kiểm tra sao kê thủ công tốn thời gian, dễ gây trễ nải đơn hàng.
* **1.3. Mục tiêu và phạm vi của đề tài:**
  * Xây dựng website đặt món trực tuyến với giao diện thân thiện, hiện đại, tốc độ phản hồi nhanh.
  * Hoàn thiện các module: Thực đơn, Giỏ hàng, Đặt món nhóm, Thanh toán, Vận hành bếp và Quản trị hệ thống.
  * Tích hợp Trí tuệ nhân tạo (Google Gemini AI) hỗ trợ khách hàng ra quyết định chọn món.
* **1.4. Đối tượng và phạm vi ứng dụng:**
  * Khách hàng cá nhân và nhóm khách hàng văn phòng/tập thể.
  * Nhân viên thu ngân, nhân viên phục vụ và đội ngũ đầu bếp nhà hàng.
  * Ban Quản trị nhà hàng và chuỗi cơ sở F&B.

---

### CHƯƠNG 2: PHƯƠNG PHÁP THỰC HIỆN VÀ THIẾT KẾ HỆ THỐNG *(Tham khảo cấu trúc chuyên sâu từ Nhóm 12)*
* **2.1. Phương pháp nghiên cứu và tiếp cận:**
  * Khảo sát và phân tích các hệ thống đặt đồ ăn và thương mại điện tử hiện có (ShopeeFood, GrabFood).
  * Phân tích các yêu cầu nghiệp vụ đặc thù của ngành F&B (tính tức thời, đồng bộ đơn hàng với bếp).
  * Tiếp cận quy trình phát triển phần mềm theo mô hình Agile/Scrum.
  * Lập trình và thiết kế hệ thống theo mô hình MVC phân tầng trong Laravel.
* **2.2. Công nghệ và công cụ sử dụng (Tech Stack & Tooling):**
  * *Backend:* PHP 8.3, Framework Laravel 13, Eloquent ORM.
  * *Frontend:* Blade Template Engine, TailwindCSS v4, Vite, JavaScript Modern Fetch API.
  * *Database:* Hệ quản trị cơ sở dữ liệu MySQL 8.0 (hỗ trợ SQLite môi trường testing).
  * *Realtime:* Công nghệ đồng bộ dữ liệu thời gian thực (Long-Polling / WebSockets Laravel Reverb).
  * *Payment Gateway:* Cổng thanh toán trực tuyến PayOS VietQR tự động khớp lệnh qua Webhook.
  * *Trí tuệ nhân tạo (AI):* Google Gemini 1.5 Flash API (Prompt Context Injection).
  * *Quản lý Dependency & Môi trường:* Composer, npm, Git, Laragon / Postman.
* **2.3. Quy trình xây dựng hệ thống (Quy trình 8 bước chuẩn):**
  * *Bước 1:* Khảo sát yêu cầu và phân tích chức năng hệ thống F&B.
  * *Bước 2:* Thiết kế cơ sở dữ liệu và xây dựng quan hệ thực thể (ERD).
  * *Bước 3:* Xây dựng hệ thống định tuyến (Routes), Controllers, Models và Service Layer.
  * *Bước 4:* Thiết kế giao diện người dùng cho Khách hàng và Admin Dashboard.
  * *Bước 5:* Tích hợp giỏ hàng thông minh, đặt món nhóm và thanh toán VietQR PayOS.
  * *Bước 6:* Tích hợp Trợ lý ẩm thực AI Gemini, Màn hình Bếp FIFO và Vòng quay may mắn.
  * *Bước 7:* Kiểm thử hệ thống (Automation Testing với PHPUnit) và tối ưu bảo mật (RBAC, BFCache).
  * *Bước 8:* Đánh giá hiệu năng, sửa lỗi phát sinh và đóng gói triển khai.
* **2.4. Thiết kế Kiến trúc hệ thống (System Architecture):**
  * *2.4.1. Kiến trúc phân tầng phần mềm:* Tách biệt rõ ràng Controller $\rightarrow$ Service Layer $\rightarrow$ Model $\rightarrow$ Database.
  * *2.4.2. Các module chức năng cốt lõi của hệ thống:*
    * Module Món ăn & Danh mục (Dishes & Categories)
    * Module Giỏ hàng & Đặt hàng cá nhân (Cart & Checkout)
    * Module Đặt món theo nhóm Realtime (Group Order System)
    * Module Vận hành Bếp & Bàn làm việc (Kitchen Display & Staff Workspace)
    * Module Quản lý Kho & Nhà cung cấp (Procurement & Inventory)
    * Module Khuyến mãi & Voucher (Coupons & Marketing)
    * Module Đăng ký gói ăn định kỳ (Meal Subscriptions)
    * Module Đánh giá & Phản hồi khách hàng (Customer Reviews)
    * Module Trợ lý ảo AI ẩm thực (Gemini AI Assistant)
    * Module Quản trị, Phân quyền & Báo cáo (Admin Dashboard & RBAC)
* **2.5. Phân định vai trò người dùng (Actors) & Biểu đồ Use Case:**
  * Xác định 4 tác nhân: Khách hàng (Customer), Thu ngân/Phục vụ (Staff), Bếp (Kitchen Staff), Quản trị viên (Super Admin & Admin).
  * Sơ đồ Use Case tổng quát toàn hệ thống.
  * Biểu đồ Use Case phân rã cho các phân hệ: Đặt món nhóm, Màn hình Bếp, Quản lý kho.
* **2.6. Biểu đồ Hoạt động (Activity Diagrams) cho các luồng nghiệp vụ chính:**
  * Luồng Đặt hàng và Thanh toán tự động qua PayOS VietQR.
  * Luồng Đặt đơn theo nhóm Realtime (Tạo phòng $\rightarrow$ Mời bạn bè $\rightarrow$ Chọn món $\rightarrow$ Chốt đơn).
  * Luồng Tiếp nhận đơn và Chế biến tại Màn hình Bếp (Kitchen Display).
* **2.7. Thiết kế Mô hình Dữ liệu (Database Schema / ERD):**
  * Sơ đồ quan hệ thực thể (ERD) toàn hệ thống.
  * Chi tiết cấu trúc các bảng dữ liệu lõi:
    * `users`: Quản lý tài khoản khách hàng, nhân viên và phân quyền vai trò.
    * `dishes`, `categories`: Quản lý thực đơn món ăn và nhóm món.
    * `orders`, `order_items`: Lưu thông tin đơn đặt hàng và chi tiết món.
    * `group_orders`, `group_order_items`: Lưu phòng ăn nhóm và giỏ hàng nhóm.
    * `inventory`, `suppliers`, `purchase_orders`: Quản lý kho và nhà cung cấp.
    * `meal_subscriptions`: Quản lý gói ăn định kỳ tuần/tháng.
    * `vouchers`, `saved_coupons`: Quản lý mã giảm giá và ví voucher.
    * `reviews`: Đánh giá sao và phản hồi món ăn.

---

### CHƯƠNG 3: CÀI ĐẶT, THỰC NGHIỆM VÀ KẾT QUẢ TRIỂN KHAI
* **3.1. Môi trường triển khai thực nghiệm:**
  * Hệ điều hành: Windows / Web Server Laragon.
  * Phiên bản phần mềm: PHP 8.3, Laravel 13, MySQL 8.0, Node.js 24, Vite 8.
* **3.2. Kết quả giao diện và chức năng đạt được:**
  * *3.2.1. Phân hệ Khách hàng (Client Portal):* Giao diện trang chủ, tìm kiếm, giỏ hàng, đặt món nhóm realtime, vòng quay may mắn, tra cứu đơn hàng realtime.
  * *3.2.2. Phân hệ Vận hành Bếp & Bàn làm việc (Kitchen & Staff):* Màn hình bếp FIFO tự động nhận đơn, bàn làm việc thu ngân duyệt đơn 1 chạm và in bill.
  * *3.2.3. Phân hệ Quản trị Cấp cao (Admin Portal):* Dashboard biểu đồ doanh thu, quản lý món ăn, quản lý kho nguyên liệu, phân quyền RBAC và gửi email marketing hàng loạt.
  * *3.2.4. Trợ lý ẩm thực Google Gemini AI:* Giao diện chatbot thông minh tư vấn món kèm hình ảnh trực quan.
  * *3.2.5. Cổng thanh toán VietQR PayOS:* Quét mã QR thanh toán và tự động nhận lệnh qua Webhook trong 1-2 giây.
* **3.3. Các kịch bản kiểm thử tự động (Automation Testing):**
  * Thiết lập môi trường PHPUnit Testing trên Laravel 13.
  * Danh sách các ca kiểm thử: Kiểm tra thêm giỏ hàng, kiểm tra tính toán tiền, kiểm tra đặt món nhóm, kiểm tra webhook thanh toán, kiểm tra phân quyền bảo mật và chống BFCache.
  * Kết quả kiểm thử: **41/41 test cases đạt 100% Passed**.
* **3.4. Đánh giá hiệu quả sau thực nghiệm:**
  * *Ưu điểm:* Giao diện chỉn chu, tốc độ phản hồi nhanh dưới 1 giây, dữ liệu đồng bộ thời gian thực mượt mà, bảo mật phân quyền chặt chẽ.
  * *Hạn chế:* Logic trừ kho chưa tự động theo định lượng món, chưa có app chuyên biệt cho shipper di chuyển ngoài đường.

---

### CHƯƠNG 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN
* **4.1. Tổng kết đề tài:**
  * Hệ thống FOODDAILY đã hoàn thiện các mục tiêu cốt lõi của một nền tảng F&B trực tuyến hiện đại.
  * Ứng dụng thành công các công nghệ tiên tiến (Google Gemini AI, Realtime Group Order, PayOS VietQR).
* **4.2. Đánh giá chung:**
  * Sản phẩm chạy thực tế ổn định, cấu trúc mã nguồn rõ ràng, dễ bảo trì và mở rộng.
* **4.3. Hướng phát triển tiếp theo (Lộ trình 2 tuần):**
  * *Tuần 1 (Kho, Bếp & Quản trị):* Định lượng món tự động trừ kho (Recipe/BOM), chuông báo âm thanh bếp, tích điểm thăng hạng thành viên.
  * *Tuần 2 (Vận chuyển & Khách hàng):* Cổng ứng dụng cho Tài xế (Driver Portal), định vị Live GPS Tracking, thông báo đẩy trạng thái đơn (Web Push / Zalo ZNS).
* **4.4. Kết luận cuối cùng:** Khẳng định giá trị thực tiễn và triển vọng áp dụng của đề tài.

---

### PHỤ LỤC & TÀI LIỆU THAM KHẢO
* **Phụ lục:** Mã nguồn Middleware chống BFCache (`PreventBackHistory`), Service Gemini AI, Service Webhook PayOS, Bảng kết quả chạy Automation Testing.
* **Tài liệu tham khảo:** Laravel Framework Documentation, Google Gemini API, PayOS VietQR API, Spatie Permission, TailwindCSS.

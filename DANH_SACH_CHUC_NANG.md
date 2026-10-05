# TỔNG HỢP DANH SÁCH CHỨC NĂNG HỆ THỐNG FOODDAILY
> **Hệ thống Website Đặt Món & Quản Trị Nhà Hàng Toàn Diện**  
> *Được biên soạn đầy đủ, ngắn gọn, dễ hiểu từ Trang chủ (Khách hàng) đến Quản trị (Admin & Vận hành).*

---

## I. PHÂN HỆ KHÁCH HÀNG (Trang chủ & Mua hàng)

### 1. Khám phá & Tìm kiếm thực đơn
* **Xem thực đơn:** Xem các món ăn theo từng danh mục, hiển thị hình ảnh món, giá bán, thành phần và đánh giá sao.
* **Tìm kiếm thông minh:** Tìm nhanh món ăn theo từ khóa tên món.
* **Vòng quay ngẫu nhiên ("Hôm nay ăn gì?"):** Vòng quay gợi ý món ăn tự động cho khách hàng khi phân vân chưa biết ăn gì.
* **Món đã xem gần đây:** Tự động lưu vết các món ăn khách vừa xem để xem lại nhanh; hỗ trợ xóa lịch sử xem.

### 2. Đặt hàng & Giỏ hàng thông minh
* **Giỏ hàng cá nhân:** Thêm món, điều chỉnh số lượng, thêm ghi chú riêng cho từng món (ít cay, không hành...).
* **Đặt đơn theo nhóm (Group Order):** Tạo phòng ăn chung, chia sẻ đường link để bạn bè/đồng nghiệp cùng chọn món vào chung một giỏ hàng, sau đó chủ phòng bấm chốt đơn.
* **Ví Voucher & Khuyến mãi:** Tự động đề xuất mã giảm giá tốt nhất cho đơn hàng; cho phép khách bấm lưu mã khuyến mãi vào ví cá nhân để dùng dần.

### 3. Quy trình Thanh toán & Vận chuyển
* **Tính tiền ship tự động (Geocoding):** Tự động nhận diện khoảng cách từ quán tới vị trí khách để tính phí giao hàng chính xác.
* **Đa dạng kênh thanh toán:**
  * Tiền mặt khi nhận hàng (COD).
  * Chuyển khoản ngân hàng quét mã QR (tự động nhận diện khớp lệnh).
  * Cổng thanh toán trực tuyến PayOS.
  * Ví điện tử MoMo.
  * Thẻ ATM nội địa.
* **Tra cứu đơn hàng Realtime:** Theo dõi tiến độ đơn hàng theo thời gian thực (Chờ xác nhận $\rightarrow$ Đang nấu $\rightarrow$ Đang giao $\rightarrow$ Hoàn tất). Hỗ trợ tra cứu cho cả khách đã đăng nhập và khách vãng lai (không có tài khoản).

### 4. Dịch vụ sau bán hàng & Đặt gói định kỳ
* **Hủy đơn linh hoạt:** Khách hàng được quyền tự hủy đơn nếu quán chưa tiến hành nấu món.
* **Đánh giá & Phản hồi:** Chấm điểm số sao (1-5 sao) và để lại nhận xét sau khi nhận và thưởng thức món ăn.
* **Gói ăn định kỳ (Subscription):** Đăng ký các gói ăn theo tuần hoặc theo tháng (quán tự nấu và giao mỗi ngày):
  * Khách được quyền tự đổi món ăn từng ngày theo sở thích.
  * Tạm ngưng giao hàng khi bận hoặc hủy gói bất kỳ lúc nào.

### 5. Tài khoản, Bảo mật & Trợ lý ảo AI
* **Đăng ký & Đăng nhập:** Hỗ trợ đăng nhập tài khoản thường và **Đăng nhập nhanh 1 chạm bằng Google**.
* **Quên mật khẩu an toàn:** Cấp lại mật khẩu qua mã xác thực OTP gửi thẳng về hòm thư Gmail của khách.
* **Chatbot AI (Google Gemini):** Trợ lý ảo tư vấn chọn món, giải đáp thông tin nhà hàng và hỗ trợ khách hàng 24/7.

---

## II. PHÂN HỆ VẬN HÀNH (Nhân viên & Bếp)

* **Bàn làm việc nhân viên (Staff Workspace):** Giao diện thao tác 1 chạm dành riêng cho thu ngân/nhân viên phục vụ: duyệt đơn nhanh, cập nhật trạng thái đơn, thu tiền và in hóa đơn thanh toán.
* **Màn hình Bếp (Kitchen Display / Queue):** Màn hình tiếp nhận đơn mới theo thời gian thực (Realtime), tự động sắp xếp danh sách các món cần nấu để đầu bếp chế biến đúng thứ tự.

---

## III. PHÂN HỆ QUẢN TRỊ VIÊN (Admin Dashboard)

### 1. Quản lý Bán hàng & Dịch vụ
* **Bảng điều khiển (Dashboard):** Biểu đồ doanh thu trực quan theo ngày/tháng, tổng số đơn đặt, lượng khách hàng mới và danh sách các món bán chạy nhất.
* **Quản lý Danh mục:** Thêm, xem, chỉnh sửa hoặc xóa các danh mục món (Cơm, Bún/Phở, Đồ uống, Món ăn vặt...).
* **Quản lý Món ăn:** Thêm món mới, cập nhật giá tiền, hình ảnh mô tả, bật/tắt trạng thái món ăn (còn hàng / hết hàng).
* **Quản lý Đơn hàng:** Xem chi tiết từng hóa đơn, thay đổi trạng thái đơn hàng, chỉnh sửa thông tin người nhận hoặc hủy đơn khi có sự cố.
* **Quản lý Gói dịch vụ (Combo Packages):** Thiết lập các gói combo tuần/tháng, chọn trước thực đơn cho từng ngày trong tuần.
* **Quản lý Gói khách đăng ký:** Theo dõi danh sách các gói khách hàng đang sử dụng, hỗ trợ đổi món và tự động kích hoạt tạo đơn giao hàng mỗi ngày.
* **Quản lý Đánh giá (Reviews):** Theo dõi toàn bộ nhận xét, đánh giá sao từ khách; cho phép kiểm duyệt hoặc xóa các đánh giá spam, phản cảm.

### 2. Quản lý Kho & Nhập hàng (Procurement)
* **Quản lý Tồn kho:** Theo dõi số lượng tồn thực tế của từng loại nguyên vật liệu, điều chỉnh số lượng và tự động cảnh báo khi nguyên liệu sắp hết.
* **Quản lý Nhà cung cấp:** Lưu danh bạ thông tin các đối tác cung ứng thực phẩm, rau củ, gia vị.
* **Quản lý Nhập hàng:** Lập phiếu nhập kho nguyên vật liệu từ nhà cung cấp, kiểm tra đối soát tổng tiền hàng và in phiếu nhập kho.

### 3. Khách hàng & Tiếp thị (Marketing)
* **Quản lý Khách hàng & Khách vãng lai:** Xem danh sách khách, số điện thoại, địa chỉ, lịch sử đơn hàng và tổng số tiền khách đã chi tiêu.
* **Sao lưu dữ liệu khách hàng (Customer Backup):** Tạo bản sao lưu an toàn cơ sở dữ liệu khách hàng, tải file về máy tính cá nhân hoặc phục hồi lại dữ liệu khi cần.
* **Quản lý Khuyến mãi & Gửi Email hàng loạt:** 
  * Tạo các mã voucher khuyến mãi (giảm theo % hoặc trừ tiền trực tiếp).
  * Tính năng **gửi email marketing hàng loạt**: Bấm 1 nút là hệ thống tự động gửi thư thông báo mã khuyến mãi vào Gmail của toàn bộ khách hàng kèm theo nút bấm dẫn thẳng về trang web đặt món.

### 4. Hệ thống, Nhân sự & Báo cáo
* **Quản lý Nhân viên:** Thêm tài khoản nhân viên mới, cấp tài khoản, đổi mật khẩu và quản lý danh sách nhân sự.
* **Phân quyền vai trò (RBAC):** Tạo các vai trò riêng biệt (Admin, Quản lý kho, Thu ngân, Nhân viên phục vụ) và phân quyền chi tiết cho từng trang chức năng.
* **Báo cáo & Xuất dữ liệu:** Hỗ trợ xuất dữ liệu ra file phục vụ báo cáo:
  * Xuất danh sách Đơn hàng (CSV).
  * Xuất danh sách Món ăn (CSV).
  * Xuất danh sách Khách hàng (Excel).
* **Cài đặt Website (Settings):** Tùy chỉnh thông tin website ngay trong trang admin: thay đổi logo, banner khuyến mãi, số hotline, địa chỉ quán và thông tin bản quyền chân trang.

---
*Tài liệu được khởi tạo tự động phục vụ báo cáo đồ án / thuyết minh hệ thống FOODDAILY.*

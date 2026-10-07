# CÁC CHỨC NĂNG THẬT SỰ CẦN THIẾT - HỆ THỐNG FOODDAILY
> Bảng tổng hợp các chức năng quan trọng nhất cần bổ sung để hoàn thiện chu trình vận hành thực tế.

| STT | Chức năng | Mô tả chi tiết & Lý do vì sao bắt buộc phải có |
| :---: | :--- | :--- |
| **1** | **Quản lý Định lượng Món ăn & Tự động trừ kho (Recipe / BOM)** | **Mô tả:** Thiết lập công thức chế biến cho từng món (VD: 1 tô Phở bò = 150g phở + 100g thịt bò + 500ml nước dùng). Khi bếp nấu xong đơn, hệ thống tự động trừ lùi số lượng nguyên liệu thô trong kho.<br><br>👉 **Vì sao cần thiết:** Hiện tại hệ thống đã có bảng kho nguyên liệu và nhập hàng, nhưng bán món lại không trừ nguyên liệu. Nếu không có chức năng này, quản lý kho chỉ là "hình thức", không thể tính giá vốn (COGS) và không kiểm soát được thất thoát. |
| **2** | **Cổng điều phối & Phân hệ dành riêng cho Tài xế (Driver Portal)** | **Mô tả:** Giao diện cho nhân viên giao hàng/shipper: nhận thông báo có đơn đã nấu xong, xem địa chỉ giao, số điện thoại khách, bấm nhận giao và chụp ảnh xác nhận đã giao hàng thành công.<br><br>👉 **Vì sao cần thiết:** Quy trình hiện tại dừng lại ở "Bếp nấu xong" là đứt gãy. Cần phân hệ tài xế để hoàn thiện chu trình: Khách đặt $\rightarrow$ Bếp nấu $\rightarrow$ Shipper giao $\rightarrow$ Hoàn tất. |
| **3** | **Định vị tài xế di chuyển thời gian thực (Live GPS Tracking)** | **Mô tả:** Truyền tọa độ GPS của shipper qua WebSocket (Laravel Reverb) lên bản đồ trang tra cứu đơn của khách hàng, kèm thời gian dự kiến giao hàng (ETA).<br><br>👉 **Vì sao cần thiết:** Đây là tính năng tiêu chuẩn của mọi app giao đồ ăn (Grab/ShopeeFood). Giúp giải tỏa tâm lý chờ đợi của khách, tránh việc khách gọi hotline hối đơn hoặc hủy đơn ngang. |
| **4** | **Chuông báo âm thanh & Đếm giờ nấu cho Màn hình Bếp (Kitchen Audio & Timer)** | **Mô tả:** Chuông reo tự động khi có đơn mới chuyển đến bếp; đồng hồ đếm ngược thời gian nấu từng món, đổi màu cảnh báo (Xanh $\rightarrow$ Vàng $\rightarrow$ Đỏ) khi đơn bị trễ giờ.<br><br>👉 **Vì sao cần thiết:** Đầu bếp luôn tay nấu nướng trong môi trường ồn ào và nóng bức, không thể dán mắt vào màn hình. Chuông báo giúp không bao giờ bị sót đơn. |
| **5** | **Thông báo đẩy trạng thái đơn (Web Push / Zalo ZNS)** | **Mô tả:** Tự động gửi thông báo trực tiếp đến điện thoại/máy tính của khách khi đơn được duyệt, bắt đầu nấu, tài xế đang giao và khi tài xế đã đến nơi.<br><br>👉 **Vì sao cần thiết:** Khách hàng đặt xong thường tắt tab trình duyệt. Nếu không có thông báo đẩy, khách sẽ không biết khi nào đồ ăn đến để xuống nhận. |
| **6** | **Hệ thống Hạng thành viên & Tích điểm thưởng (Loyalty Points & Tiers)** | **Mô tả:** Tích điểm theo % giá trị đơn hàng, thăng hạng (Đồng, Bạc, Vàng, Kim Cương) để nhận voucher độc quyền, giảm giá trực tiếp và freeship.<br><br>👉 **Vì sao cần thiết:** Doanh thu F&B phụ thuộc lớn vào khách hàng quen quay lại. Tích điểm là công cụ giữ chân khách hàng (Retention) bắt buộc phải có cho thương hiệu bán lẻ. |
| **7** | **Nhật ký kiểm toán thao tác hệ thống (Audit Log & Activity Tracking)** | **Mô tả:** Ghi vết tự động mọi hành vi nhạy cảm: ai đã sửa giá món, ai đã xóa đơn, ai đã sửa số lượng tồn kho, kèm tài khoản, IP và mốc thời gian chi tiết.<br><br>👉 **Vì sao cần thiết:** Ngăn chặn tuyệt đối tình trạng nhân viên thu ngân hoặc quản trị viên gian lận nội bộ, sửa tiền hoặc xóa đơn bỏ túi riêng. |

---

## 📅 LỘ TRÌNH PHÁT TRIỂN TRONG 2 TUẦN (14 NGÀY)
> Kế hoạch triển khai cụ thể theo từng ngày, tập trung hoàn thiện 7 chức năng cấp thiết trên.

### TUẦN 1: BẾP, KHO & QUẢN TRỊ NỘI BỘ (Ngày 1 - Ngày 7)

| Thời gian | Chức năng thực hiện | Chi tiết công việc cần làm |
| :---: | :--- | :--- |
| **Ngày 1** | Cài đặt công thức món ăn | Tạo bảng nhập định lượng nguyên liệu cho từng món (1 tô phở = bánh phở + thịt bò + nước dùng). |
| **Ngày 2** | Tự động trừ kho nguyên liệu | Viết chức năng khi bếp bấm nấu xong đơn thì hệ thống tự động trừ số lượng nguyên liệu trong kho. |
| **Ngày 3** | Chuông báo cho nhà bếp | Tích hợp âm thanh chuông reo tự động trên màn hình bếp mỗi khi có đơn hàng mới chuyển đến. |
| **Ngày 4** | Hẹn giờ & Cảnh báo trễ món | Gắn đồng hồ đếm ngược thời gian nấu từng món, đổi màu cảnh báo (đỏ) khi bếp làm trễ giờ. |
| **Ngày 5** | Tích điểm cho khách mua hàng | Cài đặt tự động cộng điểm cho khách khi hoàn thành đơn, hiển thị số điểm trong tài khoản khách. |
| **Ngày 6** | Hạng thành viên & Đổi voucher | Phân hạng Đồng - Bạc - Vàng và cho phép khách dùng điểm để đổi mã giảm giá khi thanh toán. |
| **Ngày 7** | Nhật ký theo dõi nhân viên | Lưu lại lịch sử ai đã sửa giá món, ai hủy đơn, ai sửa kho để chủ quán kiểm tra chống gian lận. |

---

### TUẦN 2: GIAO HÀNG & THÔNG BÁO CHO KHÁCH (Ngày 8 - Ngày 14)

| Thời gian | Chức năng thực hiện | Chi tiết công việc cần làm |
| :---: | :--- | :--- |
| **Ngày 8** | Giao diện nhận đơn cho Shipper | Làm trang riêng cho người giao hàng: xem danh sách đơn đã nấu xong, xem số điện thoại và địa chỉ khách. |
| **Ngày 9** | Nút nhận đơn & Chụp ảnh giao hàng | Thêm nút bấm nhận đơn cho shipper và tính năng chụp ảnh gửi lên hệ thống khi đã giao hàng xong. |
| **Ngày 10** | Định vị vị trí Shipper | Lấy tọa độ GPS của người giao hàng khi đang chạy trên đường để truyền về hệ thống. |
| **Ngày 11** | Xem Shipper chạy trên bản đồ | Hiện vị trí xe của shipper trên bản đồ trang tra cứu của khách và báo còn mấy phút nữa tới nơi. |
| **Ngày 12** | Gửi thông báo đơn hàng cho khách | Cài đặt thông báo tự động báo cho khách khi đơn bắt đầu nấu, shipper đang giao và đã tới nơi. |
| **Ngày 13** | Kiểm tra liên thông toàn bộ hệ thống | Chạy thử toàn bộ chu trình thực tế từ Khách đặt -> Bếp nấu -> Shipper giao -> Trừ kho để kiểm tra lỗi. |
| **Ngày 14** | Chỉnh sửa giao diện & Hoàn thiện | Sửa các lỗi phát sinh, làm mượt giao diện và đóng gói hoàn tất sản phẩm để bàn giao/báo cáo. |

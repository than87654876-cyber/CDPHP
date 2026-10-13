const fs = require('fs');
const path = require('path');

// 1. UPDATE BAO_CAO_DU_AN_FOODDAILY.md
const mdPath = path.join(__dirname, '..', 'docs', 'BAO_CAO_DU_AN_FOODDAILY.md');
let mdContent = fs.readFileSync(mdPath, 'utf8');

const detailed212Markdown = `### 2.1.2 Thông tin yêu cầu
Để hệ thống FOODDAILY vận hành trơn tru, chính xác và đồng bộ giữa các phân hệ (Khách hàng, Nhà bếp, Thu ngân, Shipper và Quản trị viên), hệ thống thu thập, xử lý và quản lý chặt chẽ các luồng thực thể thông tin nghiệp vụ cốt lõi sau:

#### 1. Thông tin khách hàng (Customer Information)
- **Mục đích quản lý:** Quản lý danh tính số của người dùng, xác thực đăng nhập bảo mật, phân quyền theo vai trò (RBAC), theo dõi lịch sử đặt món, lưu trữ sổ địa chỉ nhận hàng cá nhân hóa, tích lũy điểm thưởng thành viên và chăm sóc khách hàng tự động.
- **Chi tiết các thuộc tính thông tin:**
  - \`ID (Mã khách hàng)\`: Số nguyên tự tăng (\`BIGINT UNSIGNED\`), khóa chính (\`PRIMARY KEY\`), định danh duy nhất của mỗi khách hàng trên toàn hệ thống.
  - \`Họ và tên (fullname)\`: Chuỗi ký tự Unicode (\`VARCHAR(150)\`, NOT NULL), họ tên đầy đủ của khách hàng để hiển thị lời chào trên giao diện cá nhân và in trên hóa đơn thanh toán.
  - \`Email (Địa chỉ thư điện tử)\`: Chuỗi ký tự (\`VARCHAR(191)\`, UNIQUE, NOT NULL), tài khoản đăng nhập chính của khách hàng, nhận thông báo hóa đơn điện tử, thông báo đơn hàng và liên kết đặt lại mật khẩu an toàn.
  - \`Mật khẩu mã hóa (password)\`: Chuỗi ký tự băm an toàn (\`VARCHAR(255)\`, NOT NULL), sử dụng thuật toán băm một chiều tiên tiến \`Bcrypt\` (chuẩn công nghiệp cost=12, độ dài 60 ký tự), tuyệt đối không lưu mật khẩu dạng rõ (plaintext), đảm bảo an toàn ngay cả khi cơ sở dữ liệu bị truy cập trái phép.
  - \`Số điện thoại (phone)\`: Chuỗi ký tự số (\`VARCHAR(20)\`, NULLABLE), số điện thoại liên lạc chính để nhân viên giao hàng (Shipper) liên hệ giao đồ ăn và nhận mã xác minh OTP khi cần thiết.
  - \`Địa chỉ nhận hàng (address)\`: Văn bản chi tiết (\`TEXT\` hoặc \`VARCHAR(255)\`, NULLABLE), lưu địa chỉ giao hàng mặc định gồm: Số nhà, ngõ/ngách, tên đường, phường/xã, quận/huyện. Hệ thống hỗ trợ lưu thêm \`company_address\` (địa chỉ công ty) và \`other_address\` (địa chỉ khác) kèm cờ \`default_address_type\` để khách linh hoạt chuyển đổi khi đặt món giao tận nơi.
  - \`Điểm tích lũy (points / loyalty_points)\`: Số nguyên không âm (\`INT UNSIGNED\`, DEFAULT 0), điểm thưởng tích lũy được cộng tự động sau mỗi đơn hàng giao thành công (10.000 VNĐ = 1 điểm). Điểm này dùng để xếp hạng thành viên (\`Bronze\`, \`Silver\`, \`Gold\`, \`Diamond\`) và quy đổi sang các voucher giảm giá trong Ví Voucher.
  - \`Các thuộc tính bổ trợ:\` Ảnh đại diện (\`avatar\`), Giới tính (\`gender\`), Ngày sinh nhật (\`birthday\` - phục vụ tặng voucher sinh nhật tự động), Trạng thái tài khoản (\`status\`: Active/Blocked) và Trạng thái xác thực 2 lớp (\`two_factor_enabled\`).
- **Quy tắc nghiệp vụ & Bảo mật:**
  - Email phải tuân thủ chuẩn RFC 5322 và duy nhất trong toàn hệ thống.
  - Mật khẩu đăng ký bắt buộc tối thiểu 8 ký tự, có cơ chế Rate Limiting chống tấn công vét cạn (Brute-force).
  - Khách hàng vãng lai có thể đặt hàng nhanh mà không bắt buộc tạo tài khoản, nhưng khi tạo tài khoản sẽ được kích hoạt quyền tích điểm và lưu sổ địa chỉ.

#### 2. Thông tin món ăn & Thực đơn (Dishes & Categories Information)
- **Mục đích quản lý:** Số hóa toàn bộ thực đơn ẩm thực của nhà hàng; phân nhóm khoa học theo danh mục; quản lý giá niêm yết, giá khuyến mãi, hình ảnh trực quan; kiểm soát lượng suất ăn phục vụ trong ngày nhằm tránh tình trạng khách đặt vượt quá năng lực phục vụ của nhà bếp.
- **Chi tiết các thuộc tính thông tin:**
  - \`ID món (Mã món ăn)\`: Số nguyên tự tăng (\`BIGINT UNSIGNED\`), khóa chính (\`PRIMARY KEY\`), định danh duy nhất cho từng món ăn.
  - \`ID danh mục (category_id)\`: Khóa ngoại (\`BIGINT UNSIGNED\`), liên kết tới bảng \`categories\` (Món chính: cơm/bún/phở, Món ăn nhanh, Đồ uống thanh mát, Tráng miệng, Combo tiết kiệm).
  - \`Tên món ăn (dish_name / name)\`: Chuỗi ký tự Unicode (\`VARCHAR(255)\`, NOT NULL), tên thương phẩm món ăn (ví dụ: "Cơm Tấm Sườn Bì Chả Đặc Biệt", "Trà Đào Cam Sả Tươi").
  - \`Đường dẫn tĩnh (slug)\`: Chuỗi ký tự không dấu (\`VARCHAR(255)\`, UNIQUE), định danh URL thân thiện cho SEO (ví dụ: \`com-tam-suon-bi-cha-dac-biet\`), hỗ trợ chia sẻ mạng xã hội.
  - \`Đơn giá bán (price)\`: Kiểu số thập phân chính xác cao (\`DECIMAL(10,2)\`, NOT NULL), giá bán thực tế mà khách hàng thanh toán cho một suất ăn.
  - \`Giá gốc niêm yết (original_price)\`: Kiểu số thập phân (\`DECIMAL(10,2)\`, NULLABLE), dùng hiển thị giá gạch ngang để kích thích nhu cầu mua sắm khi có chương trình giảm giá.
  - \`Ảnh đại diện Cloudinary (image_url / image)\`: Chuỗi URL (\`VARCHAR(500)\`), liên kết ảnh món ăn chất lượng cao lưu trữ trên nền tảng đám mây Cloudinary hoặc máy chủ, hỗ trợ định dạng nén tối ưu WebP tải siêu tốc.
  - \`Mô tả chi tiết (description)\`: Đoạn văn bản (\`TEXT\`), miêu tả thành phần, hương vị đặc trưng, hàm lượng calo và khuyến cáo dị ứng thực phẩm.
  - \`Số suất bán trong ngày (stock)\`: Số nguyên không âm (\`INT UNSIGNED\`, DEFAULT 0), số lượng suất ăn nhà bếp có thể chuẩn bị trong ngày; hệ thống tự động trừ lùi số lượng khi có đơn đặt thành công.
  - \`Trạng thái kinh doanh (is_available / is_active)\`: Boolean / TINYINT (\`1\` - Đang phục vụ, \`0\` - Tạm ngưng kinh doanh), cho phép quản trị viên hoặc thu ngân chủ động tắt món khi hết nguyên liệu đột xuất.
  - \`Thuộc tính nổi bật (is_featured)\`: Boolean, đánh dấu các món đặc sản tiêu biểu (Best Seller) để ưu tiên hiển thị ở vị trí trang trọng trên trang chủ.
- **Quy tắc nghiệp vụ:**
  - Tên món ăn và danh mục không được để trống; đơn giá bán bắt buộc $\\ge 0$ và $\\le$ giá gốc.
  - Khi \`stock = 0\` hoặc \`is_available = 0\`, hệ thống tự động gắn nhãn "Tạm hết hàng" và khóa thao tác thêm vào giỏ trên giao diện khách hàng.

#### 3. Thông tin đơn đặt hàng & Thanh toán (Orders & Payments Information)
- **Mục đích quản lý:** Lưu trữ và quản lý xuyên suốt vòng đời của mọi giao dịch mua bán đồ ăn từ khâu khởi tạo, áp voucher, thanh toán trực tuyến VietQR/COD, chuyển bếp nấu, in hóa đơn đến khi shipper giao tận tay khách hàng; phục vụ công tác đối soát kế toán và giải quyết khiếu nại.
- **Chi tiết các thuộc tính thông tin:**
  - \`Mã đơn hàng (order_code)\`: Chuỗi ký tự định danh duy nhất (\`VARCHAR(50)\`, UNIQUE, ví dụ: \`FD-20261012-0089\`), dùng cho khách tra cứu trực tuyến và in trên hóa đơn bán lẻ. Đồng thời lưu mã số nguyên đối soát cho Cổng PayOS.
  - \`Tên người nhận (customer_name / delivery_name)\`: Chuỗi ký tự (\`VARCHAR(150)\`, NOT NULL), họ tên người trực tiếp nhận đồ ăn.
  - \`Số điện thoại nhận hàng (delivery_phone)\`: Chuỗi ký tự số (\`VARCHAR(20)\`, NOT NULL), số điện thoại để Shipper gọi khi giao hàng.
  - \`Địa chỉ giao hàng (delivery_address)\`: Văn bản chi tiết (\`TEXT\`, NOT NULL), ghi rõ số nhà, ngõ, tên đường, phường/xã, quận/huyện để Shipper định vị tuyến đường di chuyển.
  - \`Tổng tiền món ăn (total_amount)\`: Kiểu số thập phân (\`DECIMAL(12,2)\`), tổng giá trị của tất cả các món ăn trong đơn trước khi giảm trừ voucher.
  - \`Tổng tiền thanh toán cuối cùng (final_amount)\`: Kiểu số thập phân (\`DECIMAL(12,2)\`), số tiền thực tế khách hàng phải chi trả sau khi cộng phí vận chuyển và trừ tiền giảm giá voucher:
    $$\\text{final\\_amount} = \\text{total\\_amount} + \\text{shipping\\_fee} - \\text{discount\\_amount}$$
  - \`Phương thức thanh toán (payment_method)\`: ENUM (\`cod\` - Tiền mặt khi giao hàng, \`payos\` - Chuyển khoản VietQR tự động qua Cổng PayOS, \`vnpay\`, \`momo\`).
  - \`Trạng thái thanh toán (payment_status)\`: ENUM (\`pending\` - Chờ thanh toán, \`paid\` - Đã thanh toán thành công, \`failed\` - Thất bại, \`refunded\` - Đã hoàn tiền).
  - \`Tiến độ xử lý đơn hàng (order_status)\`: ENUM 7 trạng thái chuẩn F&B (\`pending\` $\\rightarrow$ \`confirmed\` $\\rightarrow$ \`preparing\` $\\rightarrow$ \`cooked\` $\\rightarrow$ \`delivering\` $\\rightarrow$ \`completed\` $\\rightarrow$ \`cancelled\`).
  - \`Mã giao dịch ngân hàng PayOS (payment_transaction_id)\`: Chuỗi ký tự (\`VARCHAR(100)\`, NULLABLE), mã tham chiếu giao dịch ngân hàng do PayOS trả về khi khớp lệnh Webhook thành công.
  - \`Các thuộc tính giao dịch chi tiết:\` Ngân hàng thụ hưởng (\`payment_bank\`), Mã tham chiếu ngân hàng (\`payment_bank_reference\`), Thời điểm thanh toán thành công (\`payment_paid_at\`), Thời điểm giao hoàn tất (\`delivered_at\`).
  - \`Danh sách món ăn chi tiết trong đơn (order_items):\` Bảng liên kết gồm \`dish_id\`, \`quantity\` (số lượng), \`price\` (đơn giá tại thời điểm mua), \`item_notes\` (ghi chú riêng cho từng món: không hành, ít đá, thêm ớt).
- **Quy tắc nghiệp vụ & Tính toàn vẹn:**
  - Không được phép sửa đổi danh sách món ăn khi đơn đã chuyển sang trạng thái \`preparing\` (đang nấu) hoặc \`delivering\` (đang giao).
  - Đơn thanh toán VietQR chỉ được chuyển sang \`paid\` khi chữ ký HMAC-SHA256 của Webhook hợp lệ 100%.

#### 4. Thông tin nhà cung cấp, Kho nguyên liệu & Phiếu nhập kho (Suppliers, Inventory & Goods Receipts)
- **Mục đích quản lý:** Quản trị chuỗi cung ứng thực phẩm đầu vào của nhà hàng; kiểm soát tồn kho định lượng nguyên vật liệu (thịt, rau, gia vị, gạo, bao bì); tự động phát hiện và cảnh báo các mặt hàng chạm ngưỡng tồn kho tối thiểu nhằm tránh tình trạng thiếu hụt nguyên liệu phục vụ; quản lý lịch sử nhập kho và giá vốn hàng bán (COGS).
- **Chi tiết các thuộc tính thông tin:**
  - **Nhà cung cấp (Bảng \`suppliers\`):**
    - \`Tên nhà cung cấp (name)\`: Chuỗi ký tự (\`VARCHAR(255)\`, NOT NULL), tên doanh nghiệp/đối tác cung ứng (ví dụ: "Công ty Thực phẩm Sạch CP Việt Nam", "Nông trại Rau sạch Đà Lạt Farm").
    - \`Người đại diện liên hệ (contact_name)\`: Chuỗi ký tự (\`VARCHAR(150)\`), họ tên nhân viên kinh doanh của đối tác.
    - \`Số điện thoại (phone)\`: Chuỗi ký tự số (\`VARCHAR(20)\`, NOT NULL), dùng liên hệ đặt hàng gấp.
    - \`Địa chỉ (address)\`: Chuỗi văn bản (\`TEXT\`), địa chỉ kho hàng đối tác để nhận hàng hoặc đối soát.
    - \`Mã số thuế (tax_code)\` & \`Email (email)\`: Phục vụ xuất hóa đơn GTGT và gửi đơn đặt hàng.
  - **Kho nguyên liệu tồn kho (Bảng \`inventory_items\`):**
    - \`Mã nguyên liệu (item_code)\`: Chuỗi ký tự duy nhất (\`VARCHAR(50)\`, UNIQUE, ví dụ: \`NL-THITHEO-01\`, \`NL-GAO-02\`, \`NL-HOPXOP-03\`).
    - \`Tên nguyên liệu (name)\`: Chuỗi ký tự (\`VARCHAR(255)\`, NOT NULL), ví dụ: "Thịt sườn heo tươi", "Gạo tấm thơm lài", "Hộp xốp đựng cơm thân thiện môi trường".
    - \`Phân loại nhóm (category)\`: Phân loại theo nhóm: Thực phẩm tươi sống, Rau củ quả tươi, Gia vị & Phụ gia, Đồ uống đóng chai, Bao bì đóng gói.
    - \`Đơn vị tính (unit)\`: Chuỗi ký tự (\`VARCHAR(50)\`, NOT NULL), ví dụ: kg, gam, lít, chai, thùng, hộp, cái.
    - \`Số lượng tồn kho (current_stock)\`: Số thập phân (\`DECIMAL(10,2)\`, DEFAULT 0), số lượng hàng hóa còn trong kho thực tế, được cộng khi nhập kho và trừ đi khi xuất kho chế biến.
    - \`Ngưỡng báo nhập tối thiểu (min_stock)\`: Số thập phân (\`DECIMAL(10,2)\`, DEFAULT 5.00), ngưỡng giới hạn an toàn. Khi \`current_stock <= min_stock\`, hệ thống lập tức hiển thị cảnh báo đỏ trên Dashboard quản trị viên.
    - \`Đơn giá vốn nhập (cost_price)\`: Số thập phân (\`DECIMAL(12,2)\`, DEFAULT 0), giá mua vào trung bình của một đơn vị nguyên liệu để tính chi phí nguyên vật liệu món ăn.
    - \`Nhà cung cấp liên kết (supplier_id)\`: Khóa ngoại liên kết bảng \`suppliers\`.
    - \`Trạng thái tồn kho (status)\`: ENUM (\`in_stock\` - Còn hàng, \`low_stock\` - Sắp hết hàng chạm ngưỡng, \`out_of_stock\` - Đã hết hàng).
  - **Phiếu nhập kho nguyên liệu (Bảng \`goods_receipts\` & \`goods_receipt_items\`):**
    - \`Mã phiếu nhập (receipt_code)\`: Chuỗi ký tự duy nhất (ví dụ: \`PNK-20261012-001\`).
    - \`Ngày giờ nhập kho (receipt_date)\`: \`DATETIME\`, thời điểm hàng hóa thực tế được nhập vào kho.
    - \`Tổng tiền nhập kho (total_amount)\`: Số thập phân (\`DECIMAL(14,2)\`).
    - \`Trạng thái phiếu (status)\`: ENUM (\`draft\` - Bản nháp, \`completed\` - Đã nhập kho chính thức, \`cancelled\` - Đã hủy).
    - \`Chi tiết từng mặt hàng nhập (goods_receipt_items):\` Mã nguyên liệu (\`inventory_item_id\`), số lượng nhập thực tế (\`quantity\`), đơn giá nhập (\`unit_price\`), thành tiền (\`total_price\`), hạn sử dụng của lô nguyên liệu (\`expiry_date\`).
- **Quy tắc nghiệp vụ:**
  - Số lượng nhập và tồn kho luôn phải $\\ge 0$.
  - Khi phê duyệt phiếu nhập (\`status = 'completed'\`), hệ thống tự động chạy Database Transaction để cộng dồn tồn kho \`current_stock\` và tính lại \`cost_price\` theo phương pháp bình quân gia quyền.

#### 5. Thông tin phòng đặt nhóm, Gói ăn định kỳ & Voucher ví
- **Phòng đặt món nhóm Realtime (\`group_orders\`, \`group_order_items\`):** Quản lý mã phòng \`room_code\` (mã PIN 6 ký tự), người tạo phòng \`host_user_id\`, trạng thái phòng \`status\` (\`open\`, \`locked\`), danh sách thành viên \`member_name\`, chi tiết món ăn từng thành viên chọn kèm đơn giá và ghi chú riêng.
- **Ví Voucher khuyến mãi (\`coupons\`, \`user_coupons\`):** Quản lý mã voucher \`code\`, loại giảm giá \`discount_type\` (% hoặc số tiền), giá trị giảm \`discount_value\`, giá trị đơn tối thiểu \`min_order_value\`, hạn dùng \`start_date\`/\`end_date\` và trạng thái đã sử dụng \`is_used\`.
- **Gói ăn định kỳ Meal Subscription (\`service_packages\`, \`subscriptions\`):** Quản lý tên gói ăn, số ngày phục vụ (1 tuần, 1 tháng), số bữa/ngày, ngày bắt đầu/kết thúc, thời gian giao hàng mong muốn hằng ngày và danh sách thực đơn đổi món hằng ngày của khách.`;

const startIdx = mdContent.indexOf('### 2.1.2 Thông tin yêu cầu');
const endIdx = mdContent.indexOf('### 2.1.3 Sơ đồ chức năng của hệ thống');

if (startIdx !== -1 && endIdx !== -1) {
    mdContent = mdContent.substring(0, startIdx) + detailed212Markdown + '\n\n---\n\n' + mdContent.substring(endIdx);
    fs.writeFileSync(mdPath, mdContent, 'utf8');
    console.log('Successfully updated Section 2.1.2 in BAO_CAO_DU_AN_FOODDAILY.md');
} else {
    console.error('Could not find markers in BAO_CAO_DU_AN_FOODDAILY.md');
}

const fs = require('fs');
const path = require('path');

let md = fs.readFileSync('docs/BAO_CAO_DU_AN_FOODDAILY.md', 'utf8');

// 1. Embed Use case image in 2.2.1
const targetUc = `### 2.2.1 Lược đồ Use case

\`\`\`mermaid`;

const replaceUc = `### 2.2.1 Lược đồ Use case

![Lược đồ 2.1 Lược đồ use case](images/luoc_do_2_1_use_case.png)

*Lược đồ 2.1: Lược đồ use case – Hệ thống FOODDAILY*

\`\`\`mermaid`;

if (md.includes(targetUc)) {
    md = md.replace(targetUc, replaceUc);
}

// 2. Add captions to 11 Use Case tables
const captions = [
    { target: '| 4. Nhấn nút Đăng nhập gửi thông tin. | - |\n| - | 5. Kiểm tra thông tin đăng nhập, nếu đúng thì khởi tạo Session, phân quyền truy cập theo vai trò và chuyển hướng; nếu sai thì hiển thị thông báo sai thông tin đăng nhập. |', caption: '*Bảng 2.1 Luồng sự kiện chính use case đăng nhập.*' },
    { target: '| 1. Chọn chức năng đăng xuất trên thanh điều hướng. | - |\n| - | 2. Hệ thống hủy phiên làm việc (Session Flush), xóa các Cookie xác thực, vô hiệu hóa bộ nhớ đệm BFCache và chuyển hướng về trang chủ. |', caption: '*Bảng 2.2 Luồng sự kiện chính use case đăng xuất.*' },
    { target: '| 3. Nhấp vào một món ăn cụ thể. | - |\n| - | 4. Hiển thị trang chi tiết món ăn gồm hình ảnh, giá, mô tả và các đánh giá sao của khách hàng trước. |', caption: '*Bảng 2.3 Luồng sự kiện chính use case tìm kiếm và xem chi tiết món ăn.*' },
    { target: '| 7. Chủ phòng nhấn "Chốt đơn nhóm". | 8. Khóa phòng và chuyển toàn bộ dữ liệu sang màn hình thanh toán đơn hàng. |', caption: '*Bảng 2.4 Luồng sự kiện chính use case đặt món theo nhóm Realtime.*' },
    { target: '| - | 6. Màn hình khách hàng tự động chuyển sang thông báo "Thanh toán thành công". |', caption: '*Bảng 2.5 Luồng sự kiện chính use case thanh toán tự động VietQR PayOS.*' },
    { target: '| - | 4. Hiển thị tin nhắn tư vấn kèm các thẻ món ăn có nút bấm mua nhanh. |', caption: '*Bảng 2.6 Luồng sự kiện chính use case Trợ lý AI Gemini tư vấn món ăn.*' },
    { target: '| 5. Nấu xong nhấn "Đã nấu xong". | 6. Chuyển đơn sang trạng thái \'cooked\' và đẩy sang màn hình thu ngân đóng gói. |', caption: '*Bảng 2.7 Luồng sự kiện chính use case điều phối chế biến tại Màn hình Bếp FIFO.*' },
    { target: '| 3. Khách hàng xem lịch và chọn đổi món trước giờ cắt đơn. | 4. Cập nhật món ăn mới cho ngày tương ứng. |', caption: '*Bảng 2.8 Luồng sự kiện chính use case đăng ký và quản lý gói ăn định kỳ.*' },
    { target: '| 3. Nhập tên, danh mục, giá, tải ảnh lên và nhấn Lưu. | 4. Tải ảnh lên Cloudinary, lưu dữ liệu vào bảng Dishes và thông báo thành công. |', caption: '*Bảng 2.9 Luồng sự kiện chính use case quản lý thực đơn món ăn.*' },
    { target: '| 2. Nhấn "Hoàn tất phiếu nhập". | 3. Tạo bản ghi Goods_Receipt và tự động cộng dồn số lượng vào bảng tồn kho Inventory_Items. |', caption: '*Bảng 2.10 Luồng sự kiện chính use case quản lý kho nguyên liệu và nhà cung cấp.*' },
    { target: '| 2. Nhấn "Phát hành và cấp vào ví hội viên". | 3. Lưu vào bảng Coupons, tự động insert vào bảng User_Coupons cho toàn bộ thành viên và gửi email thông báo kích cầu. |', caption: '*Bảng 2.11 Luồng sự kiện chính use case quản lý khuyến mãi và cấp voucher ví.*' }
];

captions.forEach(item => {
    if (md.includes(item.target) && !md.includes(item.caption)) {
        md = md.replace(item.target, item.target + '\n\n' + item.caption);
    }
});

// 3. Add images to 12 Activity subsections
const actImages = [
    { title: '#### 2.2.2.1 Lược đồ chức năng đăng nhập', img: 'images/luoc_do_2_2_dang_nhap.png', cap: '*Lược đồ 2.2 Lược đồ chức năng đăng nhập.*' },
    { title: '#### 2.2.2.3 Lược đồ chức năng reset password', img: 'images/luoc_do_2_3_reset_password.png', cap: '*Lược đồ 2.3 Lược đồ chức năng reset password.*' },
    { title: '#### 2.2.2.4 Lược đồ tìm kiếm', img: 'images/luoc_do_2_4_tim_kiem.png', cap: '*Lược đồ 2.4 Lược đồ tìm kiếm.*' },
    { title: '#### 2.2.2.5 Lược đồ đặt món theo nhóm Realtime', img: 'images/luoc_do_2_5_dat_mon_nhom.png', cap: '*Lược đồ 2.5 Lược đồ đặt món theo nhóm Realtime.*' },
    { title: '#### 2.2.2.6 Lược đồ thanh toán PayOS VietQR', img: 'images/luoc_do_2_6_thanh_toan_payos.png', cap: '*Lược đồ 2.6 Lược đồ thanh toán PayOS VietQR.*' },
    { title: '#### 2.2.2.7 Lược đồ điều phối Bếp FIFO', img: 'images/luoc_do_2_7_dieu_phoi_bep.png', cap: '*Lược đồ 2.7 Lược đồ điều phối Bếp FIFO.*' },
    { title: '#### 2.2.2.8 Lược đồ quản lý thông tin hóa đơn và in bill', img: 'images/luoc_do_2_8_in_hoa_don.png', cap: '*Lược đồ 2.8 Lược đồ quản lý thông tin hóa đơn và in bill.*' },
    { title: '#### 2.2.2.9 Lược đồ quản lý thông tin khách hàng', img: 'images/luoc_do_2_9_quan_ly_khach_hang.png', cap: '*Lược đồ 2.9 Lược đồ quản lý thông tin khách hàng.*' },
    { title: '#### 2.2.2.10 Lược đồ quản lý thông tin món ăn', img: 'images/luoc_do_2_10_quan_ly_mon_an.png', cap: '*Lược đồ 2.10 Lược đồ quản lý thông tin món ăn.*' },
    { title: '#### 2.2.2.11 Lược đồ quản lý thông tin nhà cung cấp', img: 'images/luoc_do_2_11_quan_ly_nha_cung_cap.png', cap: '*Lược đồ 2.11 Lược đồ quản lý thông tin nhà cung cấp.*' },
    { title: '#### 2.2.2.12 Lược đồ quản lý kho nguyên liệu và nhập kho', img: 'images/luoc_do_2_12_nhap_kho_nguyen_lieu.png', cap: '*Lược đồ 2.12 Lược đồ quản lý kho nguyên liệu và nhập kho.*' },
    { title: '#### 2.2.2.13 Lược đồ quản lý nhân viên và phân quyền RBAC', img: 'images/luoc_do_2_13_quan_ly_nhan_vien.png', cap: '*Lược đồ 2.13 Lược đồ quản lý nhân viên và phân quyền RBAC.*' }
];

actImages.forEach(item => {
    if (md.includes(item.title) && !md.includes(item.img)) {
        md = md.replace(item.title, item.title + '\n\n![' + item.cap + '](' + item.img + ')\n\n' + item.cap);
    }
});

fs.writeFileSync('docs/BAO_CAO_DU_AN_FOODDAILY.md', md, 'utf8');

// Also sync to brain artifact
const artifactPath = 'C:/Users/Acer/.gemini/antigravity-ide/brain/dba20c26-96e2-41df-b710-97b3cbacfe87/bao_cao_du_an_fooddaily.md';
if (fs.existsSync(path.dirname(artifactPath))) {
    fs.writeFileSync(artifactPath, md, 'utf8');
}

console.log('Successfully synced markdown and brain artifact!');

const fs = require('fs');
const path = require('path');

// 1. UPDATE BAO_CAO_DU_AN_FOODDAILY.md
const mdPath = path.join(__dirname, '..', 'docs', 'BAO_CAO_DU_AN_FOODDAILY.md');
let mdContent = fs.readFileSync(mdPath, 'utf8');

const detailed213Markdown = `### 2.1.3 Sơ đồ chức năng của hệ thống

Với yêu cầu nghiệp vụ và thông tin đặt ra đối với hệ thống bán và giao đồ ăn trực tuyến FOODDAILY, ta xây dựng được sơ đồ phân rã chức năng (Functional Decomposition Diagram) của hệ thống tương ứng với các tác nhân tham gia:

#### 1. Sơ đồ chức năng hệ thống dành cho Quản trị viên (Admin)

Quản trị viên là tác nhân có quyền hạn cao nhất trong hệ thống, chịu trách nhiệm điều hành toàn bộ hoạt động kinh doanh, giám sát tài chính và cấu hình hệ thống:

![Sơ đồ 2.1 Sơ đồ chức năng của Admin](images/so_do_chuc_nang_admin.png)

*Sơ đồ 2.1: Sơ đồ chức năng hệ thống dành cho Quản trị viên (Admin) – FOODDAILY*

\`\`\`mermaid
graph TD
    ADMIN["HỆ THỐNG QUẢN LÝ & ĐIỀU HÀNH FOODDAILY (ADMIN)"]

    ADMIN --> M1["1. Quản lý Thống kê & Doanh thu"]
    M1 --> M1_1["Thống kê doanh thu theo ngày / tuần / tháng"]
    M1 --> M1_2["Thống kê sản lượng đơn hàng & trạng thái"]
    M1 --> M1_3["Biểu đồ tỷ lệ thanh toán VietQR vs COD"]
    M1 --> M1_4["Top 5 món bán chạy & Khách hàng thân thiết"]
    M1 --> M1_5["Xuất báo cáo tài chính ra file Excel"]

    ADMIN --> M2["2. Quản lý Thực đơn & Món ăn"]
    M2 --> M2_1["Phân loại danh mục món ăn (Cơm, Bún, Nước...)"]
    M2 --> M2_2["Thêm mới, chỉnh sửa, xóa món ăn"]
    M2 --> M2_3["Cập nhật giá bán niêm yết & giá khuyến mãi"]
    M2 --> M2_4["Quản lý suất ăn phục vụ trong ngày (Stock)"]
    M2 --> M2_5["Bật / Tắt trạng thái kinh doanh phục vụ"]

    ADMIN --> M3["3. Quản lý Đơn đặt hàng"]
    M3 --> M3_1["Xem danh sách đơn hàng toàn hệ thống"]
    M3 --> M3_2["Lọc đơn theo 7 trạng thái chuẩn F&B"]
    M3 --> M3_3["Xem chi tiết đơn: người nhận, món, ghi chú"]
    M3 --> M3_4["Đối soát mã giao dịch VietQR PayOS"]
    M3 --> M3_5["Cập nhật trạng thái hoặc hủy đơn ghi lý do"]

    ADMIN --> M4["4. Quản lý Khách hàng & Thành viên"]
    M4 --> M4_1["Hồ sơ khách hàng (Tên, Email, SĐT, Sổ địa chỉ)"]
    M4 --> M4_2["Lịch sử các đơn đặt hàng cá nhân & nhóm"]
    M4 --> M4_3["Tích lũy điểm thưởng thành viên (Loyalty)"]
    M4 --> M4_4["Quản lý phân hạng (Đồng, Bạc, Vàng, Kim cương)"]
    M4 --> M4_5["Khóa / Mở khóa tài khoản khách hàng"]

    ADMIN --> M5["5. Quản lý Kho & Nhà cung cấp"]
    M5 --> M5_1["Danh bạ đối tác nhà cung ứng thực phẩm"]
    M5 --> M5_2["Danh mục nguyên vật liệu & đơn vị tính"]
    M5 --> M5_3["Cảnh báo tự động nguyên liệu chạm mức tối thiểu"]
    M5 --> M5_4["Lập và phê duyệt phiếu nhập kho (Goods Receipts)"]
    M5 --> M5_5["Cập nhật tồn kho thực tế & tính giá vốn bình quân"]

    ADMIN --> M6["6. Quản lý Khuyến mãi & Voucher"]
    M6 --> M6_1["Tạo mã Voucher ưu đãi (% hoặc tiền cố định)"]
    M6 --> M6_2["Thiết lập điều kiện: Đơn tối thiểu, hạn dùng"]
    M6 --> M6_3["Phát voucher tự động vào Ví Voucher người dùng"]
    M6 --> M6_4["Kiểm soát giới hạn lượt sử dụng mã"]
    M6 --> M6_5["Báo cáo hiệu quả chiến dịch Marketing"]

    ADMIN --> M7["7. Quản lý Gói ăn định kỳ"]
    M7 --> M7_1["Cấu hình các gói dịch vụ tuần / tháng"]
    M7 --> M7_2["Quản lý danh sách khách hàng đăng ký thuê bao"]
    M7 --> M7_3["Lập lịch điều phối thực đơn giao hằng ngày"]
    M7 --> M7_4["Tiếp nhận & xử lý yêu cầu đổi món của khách"]
    M7 --> M7_5["Theo dõi tiến độ gói ăn & gia hạn"]

    ADMIN --> M8["8. Hệ thống & Phân quyền RBAC"]
    M8 --> M8_1["Quản lý tài khoản nhân viên (Thu ngân, Bếp)"]
    M8 --> M8_2["Phân quyền vai trò chi tiết (Spatie RBAC)"]
    M8 --> M8_3["Cấu hình thông tin nhà hàng & Cổng thanh toán"]
    M8 --> M8_4["Cấu hình API Google Gemini AI & WebSockets"]
    M8 --> M8_5["Sao lưu dữ liệu tự động & Phục hồi hệ thống"]
\`\`\`

#### 2. Sơ đồ chức năng hệ thống dành cho Nhân viên quầy / Thu ngân

![Sơ đồ 2.2 Sơ đồ chức năng nhân viên](images/so_do_chuc_nang_nhan_vien.png)

*Sơ đồ 2.2: Sơ đồ chức năng của Nhân viên quầy / Thu ngân – FOODDAILY*

- **Bàn làm việc Workspace:** Xem danh sách đơn nấu xong từ Bếp, giám sát trạng thái đơn hàng realtime, nhận thông báo âm thanh khi có đơn mới.
- **In hóa đơn thanh toán:** Thao tác In bill 1 chạm (Phím tắt F9), xuất hóa đơn nhiệt 80mm chuẩn POS thể hiện chi tiết món, ghi chú riêng và trạng thái thanh toán VietQR / COD.
- **Đóng gói & Bàn giao:** Đối soát món ăn thực tế với hóa đơn, dán niêm phong túi, kẹp bill và bàn giao cho tài xế giao hàng (chuyển trạng thái sang Delivering).
- **Theo dõi & Cảnh báo kho:** Xem danh mục nguyên liệu tồn kho, nhận cảnh báo nguyên liệu chạm ngưỡng đỏ, tạm tắt món ăn khi hết nguyên liệu đột xuất.

#### 3. Sơ đồ chức năng hệ thống dành cho Đầu bếp (Kitchen)

![Sơ đồ 2.3 Sơ đồ chức năng đầu bếp](images/so_do_chuc_nang_dau_bep.png)

*Sơ đồ 2.3: Sơ đồ chức năng của Đầu bếp / Nhân viên Bếp – FOODDAILY*

- **Hàng đợi chế biến FIFO:** Xếp thứ tự ưu tiên theo giờ đặt hàng (vào trước làm trước), chuông báo âm thanh khi có đơn mới, thẻ đơn đổi màu theo thời gian chờ (Xanh/Vàng/Đỏ).
- **Bảng gom số lượng (Batching):** Tự động gom tổng số lượng từng món để đầu bếp chế biến hàng loạt cùng một mẻ, tiết kiệm 60% thời gian nấu nướng.
- **Điều phối trạng thái nấu:** Chạm nút "Bắt đầu nấu" (Preparing) và "Đã nấu xong" (Cooked), tự động đẩy đơn sang quầy thu ngân đóng gói.
- **Báo cáo nguyên liệu bếp:** Kiểm soát lượng suất ăn còn lại trong ngày, báo quầy tắt món khi hết nguyên liệu tươi.

#### 4. Sơ đồ chức năng hệ thống dành cho Khách hàng

![Sơ đồ 2.4 Sơ đồ chức năng khách hàng](images/so_do_chuc_nang_khach_hang.png)

*Sơ đồ 2.4: Sơ đồ chức năng của Khách hàng – FOODDAILY*

- **Khám phá & Trợ lý AI:** Duyệt thực đơn theo danh mục, tìm kiếm, lọc giá, quay vòng quay may mắn "Hôm nay ăn gì?", hỏi đáp Trợ lý AI Gemini tư vấn món theo tâm trạng và ngân sách.
- **Đặt nhóm Realtime:** Tạo phòng cấp mã PIN và QR Code, mời bạn bè cùng chọn món trên máy riêng, đồng bộ giỏ hàng thời gian thực và phân tách chi phí minh bạch.
- **Giỏ hàng & Thanh toán:** Nhập địa chỉ, áp mã voucher từ ví, quét mã VietQR PayOS khớp lệnh tự động trong 1-2 giây hoặc chọn COD.
- **Gói ăn định kỳ:** Đăng ký gói ăn tuần/tháng, đổi món linh hoạt hằng ngày, chọn khung giờ giao ăn cố định.
- **Tra cứu Realtime & Đánh giá:** Tra cứu tiến trình 5 chặng theo thời gian thực không cần F5, nhận hàng tận tay, tích lũy điểm thưởng thành viên và chấm điểm số sao review món ăn.`;

const startIdx = mdContent.indexOf('### 2.1.3 Sơ đồ chức năng của hệ thống');
const endIdx = mdContent.indexOf('## 2.2 Các biểu đồ thiết kế hệ thống');

if (startIdx !== -1 && endIdx !== -1) {
    mdContent = mdContent.substring(0, startIdx) + detailed213Markdown + '\n\n---\n\n' + mdContent.substring(endIdx);
    fs.writeFileSync(mdPath, mdContent, 'utf8');
    console.log('Successfully updated Section 2.1.3 in docs/BAO_CAO_DU_AN_FOODDAILY.md');
} else {
    console.error('Could not find start or end index for 2.1.3 in markdown');
}

// 2. UPDATE build_final_report_docx.cjs
const buildScriptPath = path.join(__dirname, 'build_final_report_docx.cjs');
let buildCode = fs.readFileSync(buildScriptPath, 'utf8');

// Ensure ImageRun is imported
if (!buildCode.includes('ImageRun')) {
    buildCode = buildCode.replace('const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell, AlignmentType, WidthType, BorderStyle, HeadingLevel, PageNumber, Header, Footer } = require("docx");',
        'const { Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell, AlignmentType, WidthType, BorderStyle, HeadingLevel, PageNumber, Header, Footer, ImageRun } = require("docx");');
}

const detailed213Docx = `    bodyChildren.push(pHeading3("2.1.3\\tSơ đồ chức năng của hệ thống"));
    bodyChildren.push(pBody("Với yêu cầu nghiệp vụ và thông tin đặt ra đối với hệ thống bán và giao đồ ăn trực tuyến FOODDAILY, ta xây dựng được sơ đồ phân rã chức năng (Functional Decomposition Diagram) của hệ thống tương ứng với 4 tác nhân chính:"));

    // Sơ đồ 2.1 Admin
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho Admin."));
    const imgAdminPath = path.join(__dirname, '..', 'docs', 'images', 'so_do_chuc_nang_admin.png');
    if (fs.existsSync(imgAdminPath)) {
        bodyChildren.push(new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 120, after: 80 },
            children: [
                new ImageRun({
                    data: fs.readFileSync(imgAdminPath),
                    transformation: { width: 560, height: 357 }
                })
            ]
        }));
    }
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 180 },
        children: [
            new TextRun({ text: "Sơ đồ 2.1 Sơ đồ chức năng của Admin.", italics: true, size: 24, font: FONT_NAME })
        ]
    }));

    // Sơ đồ 2.2 Nhân viên
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho nhân viên."));
    const imgStaffPath = path.join(__dirname, '..', 'docs', 'images', 'so_do_chuc_nang_nhan_vien.png');
    if (fs.existsSync(imgStaffPath)) {
        bodyChildren.push(new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 120, after: 80 },
            children: [
                new ImageRun({
                    data: fs.readFileSync(imgStaffPath),
                    transformation: { width: 560, height: 224 }
                })
            ]
        }));
    }
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 180 },
        children: [
            new TextRun({ text: "Sơ đồ 2.2 Sơ đồ chức năng nhân viên.", italics: true, size: 24, font: FONT_NAME })
        ]
    }));

    // Sơ đồ 2.3 Đầu bếp
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho đầu bếp."));
    const imgChefPath = path.join(__dirname, '..', 'docs', 'images', 'so_do_chuc_nang_dau_bep.png');
    if (fs.existsSync(imgChefPath)) {
        bodyChildren.push(new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 120, after: 80 },
            children: [
                new ImageRun({
                    data: fs.readFileSync(imgChefPath),
                    transformation: { width: 560, height: 224 }
                })
            ]
        }));
    }
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 180 },
        children: [
            new TextRun({ text: "Sơ đồ 2.3 Sơ đồ chức năng đầu bếp.", italics: true, size: 24, font: FONT_NAME })
        ]
    }));

    // Sơ đồ 2.4 Khách hàng
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho khách hàng."));
    const imgCustPath = path.join(__dirname, '..', 'docs', 'images', 'so_do_chuc_nang_khach_hang.png');
    if (fs.existsSync(imgCustPath)) {
        bodyChildren.push(new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 120, after: 80 },
            children: [
                new ImageRun({
                    data: fs.readFileSync(imgCustPath),
                    transformation: { width: 560, height: 224 }
                })
            ]
        }));
    }
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 200 },
        children: [
            new TextRun({ text: "Sơ đồ 2.4 Sơ đồ chức năng khách hàng.", italics: true, size: 24, font: FONT_NAME })
        ]
    }));`;

const docxTargetStart = '    bodyChildren.push(pHeading3("2.1.3\\tSơ đồ chức năng của hệ thống"));';
const docxTargetEnd = '    bodyChildren.push(pHeading2("2.2\\tCác biểu đồ thiết kế hệ thống"));';

const dStart = buildCode.indexOf(docxTargetStart);
const dEnd = buildCode.indexOf(docxTargetEnd);

if (dStart !== -1 && dEnd !== -1) {
    buildCode = buildCode.substring(0, dStart) + detailed213Docx + '\n\n' + buildCode.substring(dEnd);
    fs.writeFileSync(buildScriptPath, buildCode, 'utf8');
    console.log('Successfully updated Section 2.1.3 in build_final_report_docx.cjs');
} else {
    console.error('Could not find start or end index in build_final_report_docx.cjs');
}

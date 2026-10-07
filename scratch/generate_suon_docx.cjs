const fs = require('fs');
const path = require('path');
const {
    Document,
    Packer,
    Paragraph,
    TextRun,
    AlignmentType
} = require('docx');

function createDocx() {
    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: {
                        font: "Times New Roman",
                        size: 26, // 13pt
                        color: "000000",
                    },
                    paragraph: {
                        spacing: { line: 360, before: 60, after: 60 }, // 1.5 line spacing
                    },
                },
            },
        },
        sections: [
            {
                properties: {
                    page: {
                        margin: {
                            top: 1440,
                            right: 1134,
                            bottom: 1440,
                            left: 1700,
                        },
                    },
                },
                children: [
                    // Title
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 100, after: 80 },
                        children: [
                            new TextRun({
                                text: "BÁO CÁO ĐỒ ÁN TỐT NGHIỆP / DỰ ÁN CÔNG NGHỆ PHẦN MỀM",
                                bold: true,
                                size: 28,
                                color: "1E3A8A",
                            }),
                        ],
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 0, after: 100 },
                        children: [
                            new TextRun({
                                text: "ĐỀ TÀI: XÂY DỰNG HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY",
                                bold: true,
                                size: 28,
                                color: "DC2626",
                            }),
                        ],
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 0, after: 240 },
                        children: [
                            new TextRun({
                                text: "Sinh viên thực hiện: Trần Lê Thân | Nền tảng: Laravel 13, PHP 8.3, MySQL, Gemini AI, PayOS",
                                italics: true,
                                size: 22,
                                color: "475569",
                            }),
                        ],
                    }),

                    // Divider
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 0, after: 200 },
                        children: [
                            new TextRun({
                                text: "─────────────────────────────────────────────────────────────",
                                color: "CBD5E1",
                            }),
                        ],
                    }),

                    // LỜI MỞ ĐẦU
                    new Paragraph({
                        spacing: { before: 200, after: 80 },
                        children: [
                            new TextRun({ text: "LỜI MỞ ĐẦU", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "• Lý do chọn đề tài:", bold: true }), new TextRun({ text: " Nhu cầu đặt đồ ăn trực tuyến bùng nổ; giải quyết bất cập gom đơn nhóm tập thể bằng phương pháp thủ công (ghi chép dễ nhầm món, sót tiền); ứng dụng AI tư vấn món ăn thông minh giải quyết tình trạng 'hôm nay ăn gì'." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Mục đích nghiên cứu:", bold: true }), new TextRun({ text: " Xây dựng hệ thống giao đồ ăn khép kín từ Khách hàng đến Nhà bếp và Quản trị viên; tích hợp Google Gemini AI và thanh toán tự động VietQR PayOS; xây dựng tính năng Đặt món theo nhóm thời gian thực (Realtime Group Order)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phạm vi và phương pháp nghiên cứu:", bold: true }), new TextRun({ text: " Phạm vi ứng dụng cho nhà hàng, chuỗi quán ăn giao tận nơi & gói ăn định kỳ; phương pháp tiếp cận Agile/Scrum trên nền tảng Laravel 13 MVC." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bố cục đồ án:", bold: true }), new TextRun({ text: " Bố cục gồm 4 chương chuẩn theo quy định báo cáo công nghệ phần mềm." })] }),

                    // CHƯƠNG 1
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI VÀ CƠ SỞ CÔNG NGHỆ", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "1.1. Bối cảnh thực tiễn và phát biểu bài toán", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Khái quát về mô hình kinh doanh F&B trực tuyến hiện đại." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân tích những khó khăn của doanh nghiệp và khách hàng: Khó khăn gom đơn nhóm tập thể, tâm lý phân vân khi chọn món, đứt gãy luồng thông tin giữa quầy và bếp, rủi ro đối soát chuyển khoản ngân hàng thủ công." })] }),
                    new Paragraph({ children: [new TextRun({ text: "1.2. Mục tiêu và giải pháp của hệ thống FOODDAILY", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Giải pháp phòng ăn ảo Realtime cho nhóm qua mã PIN / quét mã QR." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Trợ lý ảo Google Gemini AI đọc hiểu thực đơn và tư vấn món ăn thông minh." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Cổng thanh toán VietQR tự động đối soát giao dịch qua Webhook trong 1-2 giây." })] }),
                    new Paragraph({ children: [new TextRun({ text: "1.3. Cơ sở lý thuyết và công nghệ nền tảng sử dụng", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Framework Laravel 13, Ngôn ngữ PHP 8.3, Hệ quản trị cơ sở dữ liệu MySQL 8.0." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Giao diện Blade Template Engine, TailwindCSS, JavaScript Modern Fetch API." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Công nghệ đồng bộ dữ liệu Realtime Long-Polling / WebSockets (Laravel Reverb)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Dịch vụ tích hợp: Google Gemini 1.5 Flash API, Cổng PayOS VietQR, OpenStreetMap Nominatim." })] }),

                    // CHƯƠNG 2
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "2.1. Xác định yêu cầu và phân định vai trò người dùng (Actors)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Khách hàng (Customer): Xem thực đơn, chat AI, đặt món cá nhân/nhóm, thanh toán VietQR, tra cứu đơn, đánh giá." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Nhân viên thu ngân & phục vụ (Staff): Duyệt đơn 1 chạm, thu tiền mặt, in hóa đơn thanh toán." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Đầu bếp (Kitchen Staff): Nhận đơn thời gian thực, chế biến món theo hàng đợi ưu tiên FIFO." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Ban Quản trị (Super Admin & Admin): Quản lý món ăn, kho nguyên liệu, phân quyền nhân sự, báo cáo doanh thu." })] }),
                    new Paragraph({ children: [new TextRun({ text: "2.2. Sơ đồ Use Case hệ thống", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Sơ đồ Use Case tổng quát toàn hệ thống." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Biểu đồ Use Case phân rã: Đặt món nhóm, Xử lý đơn hàng nhà bếp, Quản trị Kho & Nhập hàng." })] }),
                    new Paragraph({ children: [new TextRun({ text: "2.3. Sơ đồ Hoạt động (Activity Diagram)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân tích luồng Đặt hàng & Thanh toán tự động qua PayOS VietQR." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân tích luồng Đặt đơn theo nhóm Realtime (Tạo phòng → Chia sẻ link → Chọn món → Chốt đơn)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân tích luồng tiếp nhận đơn hàng tại Màn hình Bếp (Kitchen Display)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "2.4. Thiết kế Cơ sở dữ liệu (Database Schema / ERD)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Sơ đồ quan hệ thực thể (ERD) toàn hệ thống." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Cấu trúc các bảng dữ liệu lõi MySQL: users, dishes, categories, orders, order_items, group_orders, group_order_items, inventory, suppliers, subscriptions, vouchers, reviews." })] }),

                    // CHƯƠNG 3
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 3: CÀI ĐẶT VÀ HIỆN THỰC HÓA HỆ THỐNG", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "3.1. Kiến trúc phần mềm và bảo mật hệ thống", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Kiến trúc phân tầng Laravel 13 (Controller → Service Layer → Model)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Cơ chế bảo mật phân quyền đa tầng RBAC (Super Admin, Admin, Staff)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Triển khai PreventBackHistory Middleware triệt tiêu lỗi Back-Forward Cache (BFCache) trên trình duyệt." })] }),
                    new Paragraph({ children: [new TextRun({ text: "3.2. Hiện thực hóa các phân hệ chức năng", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Khách hàng: Menu, tìm kiếm thông minh, vòng quay 'Hôm nay ăn gì?', giỏ hàng cá nhân, tính ship Geocoding, tra cứu đơn Realtime, gói ăn định kỳ (Meal Subscriptions)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Đặt món theo nhóm Realtime: Tạo phòng ăn mã PIN/QR, đồng bộ giỏ hàng nhóm hai chiều không cần F5." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Vận hành Bếp & Bàn làm việc: Màn hình bếp FIFO, bàn làm việc thu ngân duyệt đơn 1 chạm, in hóa đơn." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Quản trị Kho & Nhập hàng: Tồn kho nguyên liệu, cảnh báo tồn an toàn, quản lý nhà cung cấp, lập phiếu nhập kho." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Ban Quản trị Cấp cao: Dashboard doanh thu trực quan, phát hành voucher, gửi Email Marketing hàng loạt, sao lưu cơ sở dữ liệu khách hàng." })] }),
                    new Paragraph({ children: [new TextRun({ text: "3.3. Tích hợp Trí tuệ nhân tạo (AI) và Cổng thanh toán VietQR", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Trợ lý ẩm thực Google Gemini AI: Nạp ngữ cảnh thực đơn vào Prompt Context, tư vấn món theo tâm trạng/thời tiết kèm gợi ý hình ảnh." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Cổng thanh toán PayOS VietQR: Sinh mã VietQR động chuẩn Napas, Webhook HMAC SHA256 tự động khớp lệnh thanh toán tức thì." })] }),

                    // CHƯƠNG 4
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 4: KIỂM THỬ HỆ THỐNG VÀ ĐÁNH GIÁ KẾT QUẢ", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "4.1. Môi trường và chiến lược kiểm thử", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Thiết lập môi trường Automation Testing với PHPUnit trên Laravel 13 (Feature Testing & Unit Testing)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "4.2. Kịch bản và kết quả kiểm thử tự động", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Thực thi bộ kiểm thử toàn diện với 41/41 test case đạt 100% Passed: CartTest, OrderTest, GroupOrderTest, PaymentTest, AuthSecurityTest." })] }),
                    new Paragraph({ children: [new TextRun({ text: "4.3. Đánh giá kết quả đạt được và hiệu năng hệ thống", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Mức độ hoàn thiện chức năng so với mục tiêu đề ra ban đầu (đạt 100%)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Đánh giá hiệu năng và tính ổn định: Thời gian phản hồi trang dưới 1 giây, dữ liệu đồng bộ thời gian thực mượt mà." })] }),

                    // KẾT LUẬN
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "1. Kết luận:", bold: true }), new TextRun({ text: " Hoàn thành nền tảng F&B trực tuyến khép kín, hoạt động ổn định, ứng dụng công nghệ hiện đại (Google AI, PayOS QR, Realtime Group Order)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "2. Hạn chế còn tồn đọng:", bold: true }), new TextRun({ text: " Chưa có cổng ứng dụng chuyên biệt cho shipper và kho nguyên liệu chưa tự động trừ theo định lượng món." })] }),
                    new Paragraph({ children: [new TextRun({ text: "3. Hướng phát triển trong 2 tuần tiếp theo:", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tuần 1: Định lượng món tự động trừ kho (Recipe/BOM), chuông báo âm thanh bếp, tích điểm thăng hạng thành viên." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tuần 2: Cổng ứng dụng cho Tài xế (Driver Portal), định vị Live GPS Tracking, thông báo đẩy trạng thái đơn (Web Push / Zalo ZNS)." })] }),

                    // PHỤ LỤC & TÀI LIỆU THAM KHẢO
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "PHỤ LỤC & TÀI LIỆU THAM KHẢO", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "• Phụ lục: Mã nguồn Middleware chống BFCache, Service Gemini AI, Service Webhook PayOS, Bảng kết quả chạy Automation Testing (PHPUnit Results)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tài liệu tham khảo: Laravel Framework Documentation, Google Gemini API, PayOS VietQR API, Spatie Permission, TailwindCSS." })] }),
                ],
            },
        ],
    });

    const outputPath = path.join(__dirname, '..', 'docs', 'SUON_BAI_BAO_CAO_FOODDAILY.docx');
    Packer.toBuffer(doc).then(buffer => {
        fs.writeFileSync(outputPath, buffer);
        console.log('Successfully created:', outputPath);
    }).catch(err => {
        console.error('Error generating docx:', err);
    });
}

createDocx();

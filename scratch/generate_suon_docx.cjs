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
                        spacing: { line: 360, before: 60, after: 60 },
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
                            new TextRun({ text: "CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI VÀ BÀI TOÁN THỰC TẾ", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "1.1. Bối cảnh thực tiễn và giới thiệu đề tài", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Khái quát về mô hình kinh doanh F&B trực tuyến hiện đại và xu hướng chuyển đổi số trong ngành ẩm thực." })] }),
                    new Paragraph({ children: [new TextRun({ text: "1.2. Phân tích bài toán và những khó khăn thực tế", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Khó khăn gom đơn nhóm tập thể văn phòng/học sinh tốn thời gian, dễ nhầm món và sót tiền." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tâm lý phân vân của người dùng khi chọn món do thiếu công cụ gợi ý theo ngữ cảnh." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Đứt gãy luồng thông tin quầy - bếp gây trễ đơn và nhầm thứ tự ưu tiên làm đồ ăn." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Rủi ro và sự chậm trễ khi đối soát chuyển khoản ngân hàng bằng phương pháp thủ công." })] }),
                    new Paragraph({ children: [new TextRun({ text: "1.3. Mục tiêu và phạm vi của đề tài", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Xây dựng website đặt món trực tuyến với giao diện thân thiện, hiện đại, tốc độ phản hồi nhanh." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Hoàn thiện các module: Thực đơn, Giỏ hàng, Đặt món nhóm, Thanh toán, Vận hành bếp và Quản trị hệ thống." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tích hợp Trí tuệ nhân tạo (Google Gemini AI) hỗ trợ khách hàng ra quyết định chọn món." })] }),
                    new Paragraph({ children: [new TextRun({ text: "1.4. Đối tượng và phạm vi ứng dụng", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Khách hàng cá nhân, nhóm khách hàng tập thể; nhân viên phục vụ, thu ngân, đầu bếp và Ban quản trị nhà hàng." })] }),

                    // CHƯƠNG 2
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 2: PHƯƠNG PHÁP THỰC HIỆN VÀ THIẾT KẾ HỆ THỐNG", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "2.1. Phương pháp nghiên cứu và tiếp cận", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Khảo sát và phân tích các hệ thống đặt đồ ăn và thương mại điện tử hiện có (ShopeeFood, GrabFood)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân tích các yêu cầu nghiệp vụ đặc thù của ngành F&B (tính tức thời, đồng bộ đơn hàng với bếp)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tiếp cận quy trình phát triển phần mềm theo mô hình Agile/Scrum." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Lập trình và thiết kế hệ thống theo mô hình MVC phân tầng trong Laravel." })] }),

                    new Paragraph({ children: [new TextRun({ text: "2.2. Công nghệ và công cụ sử dụng (Tech Stack & Tooling)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Backend: PHP 8.3, Framework Laravel 13, Eloquent ORM." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Frontend: Blade Template Engine, TailwindCSS v4, Vite, JavaScript Modern Fetch API." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Database: Hệ quản trị cơ sở dữ liệu MySQL 8.0 (hỗ trợ SQLite môi trường testing)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Realtime: Công nghệ đồng bộ dữ liệu thời gian thực (Long-Polling / WebSockets Laravel Reverb)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Payment: Cổng thanh toán trực tuyến PayOS VietQR tự động khớp lệnh qua Webhook." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Trí tuệ nhân tạo (AI): Google Gemini 1.5 Flash API (Prompt Context Injection)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Quản lý Dependency & Môi trường: Composer, npm, Git, Laragon / Postman." })] }),

                    new Paragraph({ children: [new TextRun({ text: "2.3. Quy trình xây dựng hệ thống (Quy trình 8 bước chuẩn)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 1: Khảo sát yêu cầu và phân tích chức năng hệ thống F&B." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 2: Thiết kế cơ sở dữ liệu và xây dựng quan hệ thực thể (ERD)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 3: Xây dựng hệ thống định tuyến (Routes), Controllers, Models và Service Layer." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 4: Thiết kế giao diện người dùng cho Khách hàng và Admin Dashboard." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 5: Tích hợp giỏ hàng thông minh, đặt món nhóm và thanh toán VietQR PayOS." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 6: Tích hợp Trợ lý ẩm thực AI Gemini, Màn hình Bếp FIFO và Vòng quay may mắn." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 7: Kiểm thử hệ thống (Automation Testing với PHPUnit) và tối ưu bảo mật (RBAC, BFCache)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Bước 8: Đánh giá hiệu năng, sửa lỗi phát sinh và đóng gói triển khai." })] }),

                    new Paragraph({ children: [new TextRun({ text: "2.4. Thiết kế Kiến trúc hệ thống & Các Module chính", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• 2.4.1. Kiến trúc phân tầng: Tách biệt rõ ràng Controller → Service Layer → Model → Database." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• 2.4.2. Các module chức năng cốt lõi: Module Món ăn & Danh mục, Module Giỏ hàng & Thanh toán, Module Đặt món nhóm Realtime, Module Vận hành Bếp & Bàn làm việc, Module Quản lý Kho & Nhà cung cấp, Module Khuyến mãi & Voucher, Module Đăng ký gói ăn định kỳ, Module Đánh giá khách hàng, Module Trợ lý ảo AI, Module Quản trị & RBAC." })] }),

                    new Paragraph({ children: [new TextRun({ text: "2.5. Phân định vai trò người dùng (Actors) & Sơ đồ Use Case", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Xác định 4 tác nhân: Khách hàng (Customer), Thu ngân/Phục vụ (Staff), Bếp (Kitchen), Quản trị viên (Super Admin & Admin)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Sơ đồ Use Case tổng quát toàn hệ thống và biểu đồ Use Case phân rã: Đặt món nhóm, Màn hình Bếp, Quản lý kho." })] }),

                    new Paragraph({ children: [new TextRun({ text: "2.6. Biểu đồ Hoạt động (Activity Diagrams) cho các luồng nghiệp vụ chính", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Luồng Đặt hàng và Thanh toán tự động qua PayOS VietQR." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Luồng Đặt đơn theo nhóm Realtime (Tạo phòng → Mời bạn bè → Chọn món → Chốt đơn)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Luồng Tiếp nhận đơn và Chế biến tại Màn hình Bếp (Kitchen Display)." })] }),

                    new Paragraph({ children: [new TextRun({ text: "2.7. Thiết kế Mô hình Dữ liệu (Database Schema / ERD)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Sơ đồ ERD toàn hệ thống và cấu trúc các bảng dữ liệu lõi: users, dishes, categories, orders, order_items, group_orders, group_order_items, inventory, suppliers, meal_subscriptions, vouchers, reviews." })] }),

                    // CHƯƠNG 3
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 3: CÀI ĐẶT, THỰC NGHIỆM VÀ KẾT QUẢ TRIỂN KHAI", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "3.1. Môi trường triển khai thực nghiệm", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Hệ điều hành: Windows / Web Server Laragon; PHP 8.3, Laravel 13, MySQL 8.0, Node.js 24, Vite 8." })] }),
                    new Paragraph({ children: [new TextRun({ text: "3.2. Kết quả giao diện và chức năng đạt được", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Khách hàng (Menu, tìm kiếm, giỏ hàng, đặt món nhóm realtime, vòng quay may mắn, tra cứu đơn realtime)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Vận hành Bếp & Bàn làm việc (Màn hình bếp FIFO tự động nhận đơn, bàn làm việc thu ngân duyệt đơn 1 chạm, in bill)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Phân hệ Quản trị Cấp cao (Dashboard doanh thu, quản lý món ăn, quản lý kho nguyên liệu, phân quyền RBAC, email marketing)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Trợ lý ẩm thực Google Gemini AI & Cổng thanh toán VietQR PayOS hoạt động chính xác qua Webhook." })] }),
                    new Paragraph({ children: [new TextRun({ text: "3.3. Các kịch bản kiểm thử tự động (Automation Testing)", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Thực thi bộ kiểm thử PHPUnit toàn diện với 41/41 test case đạt 100% Passed (CartTest, OrderTest, GroupOrderTest, PaymentTest, AuthSecurityTest)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "3.4. Đánh giá hiệu quả sau thực nghiệm", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Ưu điểm: Giao diện chỉn chu, phản hồi dưới 1 giây, realtime mượt mà, bảo mật phân quyền chặt chẽ." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Hạn chế: Logic trừ kho chưa tự động theo định lượng món, chưa có app chuyên biệt cho shipper." })] }),

                    // CHƯƠNG 4
                    new Paragraph({
                        spacing: { before: 240, after: 80 },
                        children: [
                            new TextRun({ text: "CHƯƠNG 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN", bold: true, size: 26, color: "1E3A8A" }),
                        ],
                    }),
                    new Paragraph({ children: [new TextRun({ text: "4.1. Tổng kết đề tài:", bold: true }), new TextRun({ text: " Hoàn thiện nền tảng F&B trực tuyến khép kín, hoạt động ổn định, ứng dụng công nghệ hiện đại (Google AI, PayOS QR, Realtime Group Order)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "4.2. Đánh giá chung:", bold: true }), new TextRun({ text: " Sản phẩm đạt 100% mục tiêu ban đầu, cấu trúc mã nguồn phân tầng rõ ràng, dễ bảo trì và mở rộng." })] }),
                    new Paragraph({ children: [new TextRun({ text: "4.3. Hướng phát triển trong 2 tuần tiếp theo:", bold: true })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tuần 1: Định lượng món tự động trừ kho (Recipe/BOM), chuông báo âm thanh bếp, tích điểm thăng hạng thành viên." })] }),
                    new Paragraph({ children: [new TextRun({ text: "• Tuần 2: Cổng ứng dụng cho Tài xế (Driver Portal), định vị Live GPS Tracking, thông báo đẩy trạng thái đơn (Web Push / Zalo ZNS)." })] }),
                    new Paragraph({ children: [new TextRun({ text: "4.4. Kết luận cuối cùng:", bold: true }), new TextRun({ text: " Khẳng định tính khả thi, giá trị thực tế và tiềm năng ứng dụng rộng rãi của hệ thống FOODDAILY." })] }),

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

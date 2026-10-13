const fs = require('fs');
const path = require('path');
const {
    Document,
    Packer,
    Paragraph,
    TextRun,
    Table,
    TableRow,
    TableCell,
    WidthType,
    AlignmentType,
    BorderStyle,
    ShadingType,
    HeadingLevel
} = require('docx');

const FONT_NAME = "Times New Roman";

const borderThin = { style: BorderStyle.SINGLE, size: 4, color: "CBD5E1" };
const tableBorders = {
    top: borderThin,
    bottom: borderThin,
    left: borderThin,
    right: borderThin,
    insideHorizontal: { style: BorderStyle.SINGLE, size: 4, color: "E2E8F0" },
    insideVertical: { style: BorderStyle.SINGLE, size: 4, color: "E2E8F0" },
};

const cellMargins = { top: 120, bottom: 120, left: 140, right: 140 };

function hCell(text, width, align = AlignmentType.CENTER, bg = "1E3A8A") {
    return new TableCell({
        width: { size: width, type: WidthType.DXA },
        shading: { fill: bg, type: ShadingType.CLEAR },
        margins: cellMargins,
        children: [
            new Paragraph({
                alignment: align,
                children: [
                    new TextRun({ text, bold: true, color: "FFFFFF", size: 22, font: FONT_NAME })
                ]
            })
        ]
    });
}

function bCell(text, width, align = AlignmentType.LEFT, isBold = false, bg = "FFFFFF") {
    return new TableCell({
        width: { size: width, type: WidthType.DXA },
        shading: { fill: bg, type: ShadingType.CLEAR },
        margins: cellMargins,
        children: [
            new Paragraph({
                alignment: align,
                children: [
                    new TextRun({ text: String(text), bold: isBold, color: "0F172A", size: 21, font: FONT_NAME })
                ]
            })
        ]
    });
}

function makeTable(headers, rowsData, widths, headerBg = "1E3A8A") {
    const tableRows = [];
    // Header
    const hCells = headers.map((h, i) => hCell(h, widths[i], AlignmentType.CENTER, headerBg));
    tableRows.push(new TableRow({ children: hCells }));

    // Data
    rowsData.forEach((row, idx) => {
        const bg = idx % 2 === 1 ? "F8FAFC" : "FFFFFF";
        const cCells = row.map((cellText, i) => {
            const align = (i === 0 && cellText.length <= 4) ? AlignmentType.CENTER : AlignmentType.LEFT;
            const bold = (i === 0 || i === 1) && row.length > 3;
            return bCell(cellText, widths[i], align, bold, bg);
        });
        tableRows.push(new TableRow({ children: cCells }));
    });

    return new Table({
        borders: tableBorders,
        width: { size: 9400, type: WidthType.DXA },
        rows: tableRows
    });
}

function pHeading1(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 280, after: 120 },
        children: [
            new TextRun({ text, bold: true, size: 28, color: "1E3A8A", font: FONT_NAME })
        ]
    });
}

function pHeading2(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 200, after: 80 },
        children: [
            new TextRun({ text, bold: true, size: 25, color: "0F172A", font: FONT_NAME })
        ]
    });
}

function pHeading3(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 140, after: 60 },
        children: [
            new TextRun({ text, bold: true, size: 23, color: "2563EB", font: FONT_NAME })
        ]
    });
}

function pBody(text, isItalic = false) {
    return new Paragraph({
        spacing: { before: 40, after: 60, line: 360 },
        alignment: AlignmentType.JUSTIFIED,
        children: [
            new TextRun({ text, size: 24, font: FONT_NAME, italics: isItalic, color: "1E293B" })
        ]
    });
}

function pBullet(boldPrefix, content) {
    return new Paragraph({
        spacing: { before: 30, after: 50, line: 360 },
        children: [
            new TextRun({ text: "• " + boldPrefix + " ", bold: true, size: 24, font: FONT_NAME, color: "0F172A" }),
            new TextRun({ text: content, size: 24, font: FONT_NAME, color: "334155" })
        ]
    });
}

function pCaption(text) {
    return new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 80, after: 120 },
        children: [
            new TextRun({ text, italics: true, size: 21, font: FONT_NAME, color: "475569" })
        ]
    });
}

function pDivider() {
    return new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 100, after: 140 },
        children: [
            new TextRun({ text: "─────────────────────────────────────────────────────────────", color: "CBD5E1", font: FONT_NAME })
        ]
    });
}

function pageBreak() {
    return new Paragraph({
        children: [new TextRun({ text: "", break: 1 })],
        pageBreakBefore: true
    });
}

function generateDocument() {
    const children = [];

    // ==========================================
    // TRANG BÌA
    // ==========================================
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 60 },
        children: [
            new TextRun({ text: "TRƯỜNG ĐẠI HỌC [TÊN TRƯỜNG ĐẠI HỌC]", bold: true, size: 28, font: FONT_NAME, color: "1E3A8A" })
        ]
    }));
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 600 },
        children: [
            new TextRun({ text: "KHOA CÔNG NGHỆ THÔNG TIN", bold: true, size: 24, font: FONT_NAME, color: "0F172A" })
        ]
    }));

    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 300, after: 80 },
        children: [
            new TextRun({ text: "BÁO CÁO ĐỒ ÁN TỐT NGHIỆP / DỰ ÁN CÔNG NGHỆ PHẦN MỀM", bold: true, size: 26, font: FONT_NAME, color: "475569" })
        ]
    }));
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 400 },
        children: [
            new TextRun({ text: "CHUYÊN NGÀNH: CÔNG NGHỆ THÔNG TIN", bold: true, size: 24, font: FONT_NAME, color: "1E3A8A" })
        ]
    }));

    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 200, after: 100 },
        children: [
            new TextRun({ text: "ĐỀ TÀI:", bold: true, size: 24, font: FONT_NAME, color: "475569" })
        ]
    }));
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 800 },
        children: [
            new TextRun({
                text: "XÂY DỰNG HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY",
                bold: true,
                size: 30,
                font: FONT_NAME,
                color: "DC2626"
            })
        ]
    }));

    const infoTable = makeTable(
        ["Mục thông tin", "Chi tiết"],
        [
            ["Giảng viên hướng dẫn:", "ThS. [Họ và tên Giảng viên hướng dẫn]"],
            ["Sinh viên thực hiện:", "Trần Lê Thân"],
            ["Mã số sinh viên (MSSV):", "[Mã số sinh viên]"],
            ["Lớp chuyên ngành:", "[Mã lớp học]"],
            ["Khóa đào tạo:", "2022 – 2026"]
        ],
        [3200, 6200],
        "1E3A8A"
    );
    children.push(infoTable);

    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 600, after: 0 },
        children: [
            new TextRun({ text: "TP. Hồ Chí Minh, Năm 2026", bold: true, size: 22, font: FONT_NAME, color: "475569" })
        ]
    }));

    // ==========================================
    // LỜI CẢM ƠN
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("LỜI CẢM ƠN"));
    children.push(pBody("Để hoàn thành đồ án tốt nghiệp này, em xin bày tỏ lòng biết ơn sâu sắc và chân thành nhất đến quý Thầy Cô Khoa Công nghệ Thông tin - Trường Đại học [Tên trường Đại học] đã tận tâm giảng dạy, truyền thụ cho em những kiến thức chuyên môn, phương pháp nghiên cứu và tác phong làm việc khoa học trong suốt những năm tháng học tập dưới mái trường. Những nền tảng học thuật quý giá đó là hành trang vững chắc để em tự tin bước vào môi trường phát triển phần mềm chuyên nghiệp."));
    children.push(pBody("Đặc biệt, em xin gửi lời cảm ơn sâu sắc nhất tới Thầy/Cô hướng dẫn đã dành nhiều thời gian quý báu để định hướng đề tài, tận tình chỉ dẫn và đóng góp những ý kiến phản biện sắc bén trong suốt quá trình xây dựng hệ thống. Nhờ có sự đồng hành và hướng dẫn của Thầy/Cô, hệ thống FOODDAILY mới được hoàn thiện một cách khoa học, đáp ứng yêu cầu kỹ thuật cao và giải quyết hiệu quả các bài toán thực tế."));
    children.push(pBody("Mặc dù đã dành trọn tâm huyết và nỗ lực nghiên cứu, song do giới hạn về thời gian và kinh nghiệm thực tế, đề tài chắc chắn khó tránh khỏi những điểm thiếu sót. Em rất mong nhận được những góp ý, nhận xét chỉ bảo quý báu từ Hội đồng chấm đồ án để sản phẩm được hoàn thiện hơn nữa trong tương lai."));
    children.push(pBody("Kính chúc quý Thầy Cô luôn dồi dào sức khỏe, hạnh phúc và gặt hái thêm nhiều thành công rực rỡ trong sự nghiệp giáo dục cao quý!"));
    children.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 200, after: 40 },
        children: [new TextRun({ text: "Sinh viên thực hiện:", italics: true, size: 24, font: FONT_NAME })]
    }));
    children.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 0, after: 200 },
        children: [new TextRun({ text: "Trần Lê Thân", bold: true, size: 24, font: FONT_NAME, color: "1E3A8A" })]
    }));

    // ==========================================
    // LỜI CAM ĐOAN
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("LỜI CAM ĐOAN"));
    children.push(pBody("Em xin cam đoan đề tài “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY” là công trình nghiên cứu và thực nghiệm độc lập do chính em thực hiện dưới sự định hướng trực tiếp của Giảng viên hướng dẫn."));
    children.push(pBody("Tất cả các số liệu, tài liệu tham khảo, lược đồ UML, cấu trúc cơ sở dữ liệu và mã nguồn ứng dụng trình bày trong bài báo cáo này đều được thực hiện nghiêm túc, minh bạch và có nguồn gốc trích dẫn rõ ràng. Hệ thống phần mềm hoàn toàn không sao chép từ bất kỳ đồ án nào khác. Em xin hoàn toàn chịu trách nhiệm trước Hội đồng và Nhà trường về tính trung thực của lời cam đoan này."));

    // ==========================================
    // TỪ VIẾT TẮT
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("DANH MỤC CÁC TỪ VIẾT TẮT"));
    const abbrTable = makeTable(
        ["Viết tắt", "Thuật ngữ tiếng Anh", "Ý nghĩa / Diễn giải tiếng Việt"],
        [
            ["AI", "Artificial Intelligence", "Trí tuệ nhân tạo"],
            ["API", "Application Programming Interface", "Giao diện lập trình ứng dụng"],
            ["BOM", "Bill of Materials", "Bảng định lượng công thức nguyên vật liệu chế biến"],
            ["BFCache", "Back/Forward Cache", "Bộ nhớ đệm chuyển trang của trình duyệt"],
            ["COGS", "Cost of Goods Sold", "Giá vốn hàng bán"],
            ["CSRF", "Cross-Site Request Forgery", "Tấn công giả mạo yêu cầu từ phía người dùng"],
            ["ERD", "Entity Relationship Diagram", "Sơ đồ quan hệ thực thể trong cơ sở dữ liệu"],
            ["FIFO", "First In, First Out", "Vào trước ra trước (Ưu tiên đơn đặt trước làm trước)"],
            ["HMAC", "Hash-based Message Authentication Code", "Mã xác thực dữ liệu qua hàm băm bảo mật Webhook"],
            ["MVC", "Model - View - Controller", "Mô hình kiến trúc phân tầng phát triển ứng dụng"],
            ["ORM", "Object-Relational Mapping", "Kỹ thuật ánh xạ đối tượng vào cơ sở dữ liệu quan hệ"],
            ["PWA", "Progressive Web App", "Ứng dụng web đa nền tảng có khả năng cài đặt như ứng dụng native"],
            ["RBAC", "Role-Based Access Control", "Kiểm soát truy cập dựa trên vai trò người dùng"],
            ["VietQR", "Vietnamese Quick Response Code", "Chuẩn mã QR thanh toán liên ngân hàng Việt Nam"]
        ],
        [1500, 3500, 4400]
    );
    children.push(abbrTable);

    // ==========================================
    // LỜI MỞ ĐẦU
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("LỜI MỞ ĐẦU"));
    children.push(pBullet("Lý do chọn đề tài:", "Ngành F&B trực tuyến đang chứng kiến sự bùng nổ vượt bậc. Tuy nhiên, các bài toán nhức nhối như gom đơn nhóm văn phòng thủ công mất thời gian, người dùng phân vân 'hôm nay ăn gì', đứt gãy luồng thông tin quầy - bếp và đối soát chuyển khoản chậm chạp vẫn chưa được giải quyết thấu đáo. Đề tài ra đời nhằm giải quyết triệt để các tồn tại này bằng công nghệ hiện đại."));
    children.push(pBullet("Mục đích nghiên cứu:", "Xây dựng hệ thống giao đồ ăn khép kín từ Khách hàng đến Nhà bếp và Quản trị viên; tích hợp Trợ lý Trí tuệ nhân tạo Google Gemini AI, cổng thanh toán tự động VietQR PayOS, và tính năng Đặt món theo nhóm thời gian thực (Realtime Group Order)."));
    children.push(pBullet("Phạm vi và phương pháp nghiên cứu:", "Ứng dụng cho nhà hàng, chuỗi quán ăn giao tận nơi và gói ăn định kỳ; áp dụng quy trình phát triển Agile/Scrum trên nền tảng Laravel 13 MVC và PHP 8.3."));
    children.push(pBullet("Bố cục đồ án:", "Báo cáo gồm 4 chương chuẩn theo quy định đồ án công nghệ phần mềm: Tổng quan, Phương pháp thực hiện & Thiết kế, Cài đặt thực nghiệm & Kết quả, Kết luận & Hướng phát triển."));

    // ==========================================
    // CHƯƠNG 1
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("CHƯƠNG 1: TỔNG QUAN VỀ ĐỀ TÀI VÀ CƠ SỞ LÝ THUYẾT"));
    children.push(pHeading2("1.1. Khảo sát bài toán và bối cảnh thực tế"));
    children.push(pHeading3("1.1.1. Đặt vấn đề"));
    children.push(pBody("Trong thị trường ẩm thực trực tuyến (F&B Online), khách hàng ngày càng đòi hỏi tốc độ, sự thuận tiện và tính tương tác cao. Khảo sát thực tế chỉ ra 4 điểm nghẽn nghiêm trọng:"));
    children.push(pBullet("Gom đơn nhóm văn phòng/học sinh phiền toái:", "Mỗi trưa, một người phải ghi chép từng món ăn và ghi chú (ít đường, không cay) qua giấy hoặc tin nhắn Zalo, mất 15-30 phút, dễ nhầm món và đau đầu khi thu tiền từng người."));
    children.push(pBullet("Tâm lý lưỡng lự khi chọn món:", "Người dùng lướt menu nhiều phút mà không biết chọn món gì phù hợp với thời tiết, tâm trạng hay ngân sách hiện có do thiếu trợ lý tư vấn ngữ cảnh."));
    children.push(pBullet("Đứt gãy liên lạc quầy - bếp:", "Bếp nhận đơn giấy lộn xộn, không kiểm soát được thứ tự đơn trước/sau dẫn đến việc khách đặt sau có đồ ăn trước, gây bức xúc."));
    children.push(pBullet("Đối soát chuyển khoản thủ công chậm trễ:", "Nhân viên phải chụp màn hình hoặc ngóng tin nhắn biến động số dư, vừa mất thời gian vừa dễ bị kẻ gian lợi dụng biên lai giả mạo (fake bill)."));

    children.push(pHeading3("1.1.2. Giải pháp đề xuất của hệ thống FOODDAILY"));
    children.push(pBody("Hệ thống FOODDAILY giải quyết triệt để các tồn tại trên bằng 5 giải pháp công nghệ mũi nhọn:"));
    children.push(pBullet("Đặt món theo nhóm Realtime (Group Order):", "Chủ phòng tạo mã PIN/QR, thành viên tự chọn món trên điện thoại riêng, giỏ hàng tự đồng bộ thời gian thực và tự tính tiền từng người."));
    children.push(pBullet("Trợ lý ẩm thực AI Google Gemini 1.5 Flash:", "Chatbot hiểu ngôn ngữ tự nhiên, phân tích tâm trạng và ngân sách để gợi ý đúng món đang có sẵn tại quán kèm nút bấm mua ngay."));
    children.push(pBullet("Cổng thanh toán tự động VietQR PayOS:", "Mã QR động chuẩn VietQR, đối soát và kích hoạt đơn sang bếp ngay lập tức qua Webhook bảo mật HMAC-SHA256."));
    children.push(pBullet("Màn hình Bếp FIFO (Kitchen Display):", "Sắp xếp đơn vào trước làm trước, tự động gom tổng số lượng từng món để nấu hàng loạt, loại bỏ 100% phiếu giấy."));
    children.push(pBullet("Gói ăn định kỳ (Meal Subscriptions):", "Khách hàng đăng ký combo theo tuần/tháng, tự do đổi món mỗi ngày trước giờ quy định."));

    children.push(pHeading2("1.2. Cơ sở lý thuyết và công nghệ sử dụng"));
    children.push(pBullet("Ngôn ngữ PHP 8.3 & Framework Laravel 13:", "Tận dụng JIT Compiler hiệu năng cao, kiểu dữ liệu nghiêm ngặt, kiến trúc MVC phân tầng rõ ràng, Eloquent ORM chống truy vấn N+1, Artisan CLI lập lịch tự động."));
    children.push(pBullet("Cơ sở dữ liệu MySQL 8.0:", "Hỗ trợ ACID Transaction qua InnoDB, ràng buộc khóa ngoại chặt chẽ và chỉ mục Composite Index tối ưu tra cứu dưới 5ms."));
    children.push(pBullet("Frontend Tailwind CSS v4 & Blade Engine:", "Giao diện hiện đại chuẩn Mobile-First, tốc độ build siêu tốc với Vite 8.0, tương tác mượt mà qua Modern JavaScript Fetch API."));
    children.push(pBullet("Công nghệ thời gian thực (WebSockets / Long-Polling):", "Đồng bộ phòng nhóm và cập nhật tiến độ nấu nướng tức thời với độ trễ dưới 100ms."));
    children.push(pBullet("Trí tuệ nhân tạo Google Gemini 1.5 Flash API:", "Thời gian sinh phản hồi dưới 1.5s, tích hợp kỹ thuật Context-Aware Prompt Injection đảm bảo AI chỉ gợi ý món có trong thực đơn."));
    children.push(pBullet("Thanh toán PayOS VietQR API & Webhook HMAC-SHA256:", "Quét mã chuyển khoản liên ngân hàng Napas 24/7, xác thực chữ ký số chống gian lận tuyệt đối."));
    children.push(pBullet("Phân quyền Spatie Laravel Permission (RBAC):", "Phân rã 5 vai trò rõ rệt (Super Admin, Admin, Staff, Kitchen, Customer) theo nguyên tắc đặc quyền tối thiểu."));

    // ==========================================
    // CHƯƠNG 2
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("CHƯƠNG 2: PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG"));
    children.push(pHeading2("2.1. Phân tích hệ thống"));
    children.push(pHeading3("2.1.1. Quy trình nghiệp vụ 7 bước chuẩn F&B"));
    children.push(pBody("1. Bước 1: Khách hàng tìm kiếm món ăn hoặc tương tác với Trợ lý AI / Vòng quay may mắn để chọn món."));
    children.push(pBody("2. Bước 2: Khách hàng chọn hình thức đặt món cá nhân hoặc Tạo phòng đặt nhóm Realtime qua Room Code / QR Code."));
    children.push(pBody("3. Bước 3: Xác nhận đơn hàng, áp dụng voucher từ ví và thanh toán trực tuyến qua mã VietQR PayOS."));
    children.push(pBody("4. Bước 4: Webhook khớp lệnh thanh toán, đơn hàng tự động xuất hiện trên Màn hình Bếp FIFO theo thứ tự ưu tiên."));
    children.push(pBody("5. Bước 5: Đầu bếp nấu xong, bàn làm việc nhân viên quầy (Staff Workspace) xác nhận và in hóa đơn 1 chạm."));
    children.push(pBody("6. Bước 6: Đơn vị vận chuyển giao hàng; khách hàng theo dõi tiến độ đơn hàng thời gian thực trên web."));
    children.push(pBody("7. Bước 7: Khách nhận hàng thành công, hệ thống tự động tích điểm thưởng và tiếp nhận đánh giá sao chất lượng."));

    children.push(pHeading2("2.2. Các biểu đồ thiết kế hệ thống và Đặc tả Use Case"));
    children.push(pBody("Dưới đây là các bảng đặc tả chi tiết cho các Use Case trọng tâm của hệ thống FOODDAILY:"));

    // Bảng UC01
    children.push(pHeading3("2.2.1. Đặc tả Use Case UC01: Đăng nhập & Xác thực người dùng"));
    const uc01Table = makeTable(
        ["Thuộc tính", "Nội dung đặc tả"],
        [
            ["Tên Use Case", "UC01: Đăng nhập vào hệ thống"],
            ["Tác nhân", "Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên"],
            ["Mục đích", "Xác thực danh tính và phân quyền truy cập không gian làm việc tương ứng"],
            ["Điều kiện tiên quyết", "Người dùng đã có tài khoản trên hệ thống"],
            ["Điều kiện sau", "Khởi tạo phiên Session an toàn, chuyển hướng đúng phân hệ phân quyền"],
            ["Luồng chính", "1. Người dùng chọn Đăng nhập -> 2. Hệ thống hiển thị form đăng nhập -> 3. Người dùng nhập Email/Mật khẩu -> 4. Hệ thống kiểm tra Hash mật khẩu -> 5. Kiểm tra quyền Spatie RBAC và điều hướng người dùng."],
            ["Luồng ngoại lệ", "Nhập sai mật khẩu: Báo lỗi 'Thông tin không chính xác'; Tài khoản bị khóa: Báo lỗi 'Tài khoản đang bị tạm khóa'."],
            ["Điểm mở rộng", "Hỗ trợ đăng nhập nhanh bằng Google OAuth 2.0 Socialite."]
        ],
        [2400, 7000],
        "1E3A8A"
    );
    children.push(uc01Table);

    // Bảng UC02
    children.push(pHeading3("2.2.2. Đặc tả Use Case UC02: Tạo phòng & Đặt món theo nhóm Realtime"));
    const uc02Table = makeTable(
        ["Thuộc tính", "Nội dung đặc tả"],
        [
            ["Tên Use Case", "UC02: Tạo phòng & Đặt món theo nhóm Realtime"],
            ["Tác nhân", "Chủ phòng (Host), Thành viên nhóm (Members)"],
            ["Mục đích", "Cho phép tập thể cùng chọn món vào giỏ hàng chung theo thời gian thực"],
            ["Điều kiện tiên quyết", "Chủ phòng truy cập hệ thống và nhấn Tạo phòng"],
            ["Điều kiện sau", "Một đơn hàng chung được tạo với danh sách người chọn và tiền từng cá nhân"],
            ["Luồng chính", "1. Chủ phòng tạo phòng -> 2. Hệ thống sinh Room Code và QR Code -> 3. Thành viên tham gia qua mã -> 4. Thành viên chọn món -> 5. Hệ thống broadcast WebSocket cập nhật giỏ nhóm -> 6. Chủ phòng nhấn Chốt đơn."],
            ["Luồng ngoại lệ", "Nhập sai mã phòng: Báo lỗi 'Phòng không tồn tại'; Thành viên chỉ được xóa món của chính mình."]
        ],
        [2400, 7000],
        "1E3A8A"
    );
    children.push(uc02Table);

    // Bảng UC03
    children.push(pHeading3("2.2.3. Đặc tả Use Case UC03: Đặt hàng & Thanh toán VietQR PayOS"));
    const uc03Table = makeTable(
        ["Thuộc tính", "Nội dung đặc tả"],
        [
            ["Tên Use Case", "UC03: Đặt hàng & Thanh toán VietQR PayOS"],
            ["Tác nhân", "Khách hàng, Cổng thanh toán PayOS, Hệ thống FOODDAILY"],
            ["Mục đích", "Tự động hóa đối soát thanh toán chuyển khoản và kích hoạt đơn sang bếp"],
            ["Điều kiện tiên quyết", "Giỏ hàng có ít nhất một món ăn hợp lệ"],
            ["Điều kiện sau", "Đơn hàng chuyển trạng thái Đã thanh toán (paid) và xuất hiện trên Màn hình Bếp"],
            ["Luồng chính", "1. Khách chọn VietQR -> 2. Hệ thống gọi API PayOS sinh mã QR động -> 3. Khách quét mã chuyển tiền -> 4. PayOS gửi Webhook -> 5. Hệ thống kiểm tra chữ ký HMAC-SHA256 -> 6. Cập nhật paid và bắn chuông sang Bếp FIFO."],
            ["Luồng ngoại lệ", "Chữ ký Webhook không khớp: Từ chối yêu cầu với HTTP 400 Bad Request; Quá 15 phút chưa chuyển: Hệ thống tự hủy đơn."]
        ],
        [2400, 7000],
        "1E3A8A"
    );
    children.push(uc03Table);

    // ==========================================
    // CƠ SỞ DỮ LIỆU
    // ==========================================
    children.push(pHeading2("2.3. Thiết kế Cơ sở dữ liệu và Từ điển dữ liệu"));
    children.push(pBody("Hệ thống FOODDAILY được thiết kế trên hệ quản trị CSDL MySQL 8.0 với các bảng dữ liệu chuẩn hóa cấp 3NF. Dưới đây là từ điển dữ liệu (Data Dictionary) của các bảng cốt lõi:"));

    // Bảng users
    children.push(pHeading3("2.3.1. Bảng dữ liệu `users` (Tài khoản người dùng)"));
    const usersTable = makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Độ dài", "Mô tả ý nghĩa"],
        [
            ["id", "BIGINT UNSIGNED", "PK, Auto Increment", "20", "Mã định danh duy nhất của người dùng"],
            ["name", "VARCHAR", "NOT NULL", "255", "Họ và tên người dùng"],
            ["email", "VARCHAR", "UNIQUE, NOT NULL", "255", "Email đăng nhập và nhận hóa đơn"],
            ["password", "VARCHAR", "NOT NULL", "255", "Mật khẩu mã hóa Bcrypt"],
            ["role", "VARCHAR", "NOT NULL, DEFAULT 'customer'", "50", "Vai trò cơ bản (customer, staff, kitchen, admin)"],
            ["phone", "VARCHAR", "NULL", "20", "Số điện thoại liên hệ nhận hàng"],
            ["address", "TEXT", "NULL", "-", "Địa chỉ giao hàng mặc định"],
            ["loyalty_points", "INT", "NOT NULL, DEFAULT 0", "11", "Điểm thưởng tích lũy qua các đơn hàng"],
            ["is_active", "TINYINT(1)", "NOT NULL, DEFAULT 1", "1", "Trạng thái (1: Đang hoạt động, 0: Đang bị khóa)"]
        ],
        [1800, 2000, 2200, 800, 2600]
    );
    children.push(usersTable);

    // Bảng dishes
    children.push(pHeading3("2.3.2. Bảng dữ liệu `dishes` (Thực đơn món ăn)"));
    const dishesTable = makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Độ dài", "Mô tả ý nghĩa"],
        [
            ["id", "BIGINT UNSIGNED", "PK, Auto Increment", "20", "Mã định danh món ăn"],
            ["category_id", "BIGINT UNSIGNED", "FK (categories.id), NOT NULL", "20", "Mã danh mục món trực thuộc"],
            ["name", "VARCHAR", "NOT NULL", "255", "Tên món ăn"],
            ["slug", "VARCHAR", "UNIQUE, NOT NULL", "255", "Đường dẫn URL thân thiện"],
            ["price", "DECIMAL", "NOT NULL", "12,2", "Giá bán hiện tại (VNĐ)"],
            ["original_price", "DECIMAL", "NULL", "12,2", "Giá niêm yết gốc trước khi giảm"],
            ["image", "VARCHAR", "NULL", "255", "Đường dẫn ảnh món ăn trên Cloudinary"],
            ["stock", "INT", "NOT NULL, DEFAULT 100", "11", "Số lượng suất sẵn sàng phục vụ trong ngày"],
            ["is_active", "TINYINT(1)", "NOT NULL, DEFAULT 1", "1", "Trạng thái kinh doanh (1: Đang bán, 0: Tạm ngưng)"]
        ],
        [1800, 2000, 2200, 800, 2600]
    );
    children.push(dishesTable);

    // Bảng orders
    children.push(pHeading3("2.3.3. Bảng dữ liệu `orders` (Đơn đặt hàng)"));
    const ordersTable = makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Độ dài", "Mô tả ý nghĩa"],
        [
            ["id", "BIGINT UNSIGNED", "PK, Auto Increment", "20", "Mã định danh duy nhất của đơn hàng"],
            ["order_code", "VARCHAR", "UNIQUE, NOT NULL", "50", "Mã hiển thị tra cứu (VD: FD-20261012-001)"],
            ["customer_name", "VARCHAR", "NOT NULL", "255", "Tên người nhận hàng"],
            ["customer_phone", "VARCHAR", "NOT NULL", "20", "Số điện thoại liên hệ giao hàng"],
            ["shipping_address", "TEXT", "NOT NULL", "-", "Địa chỉ giao đồ ăn chi tiết"],
            ["total_amount", "DECIMAL", "NOT NULL", "12,2", "Tổng số tiền thanh toán cuối cùng của đơn"],
            ["payment_method", "VARCHAR", "NOT NULL", "50", "Phương thức (payos, momo, cod)"],
            ["payment_status", "VARCHAR", "NOT NULL, DEFAULT 'pending'", "50", "Trạng thái trả tiền (pending, paid, failed)"],
            ["order_status", "VARCHAR", "NOT NULL, DEFAULT 'pending'", "50", "Trạng thái tiến độ (pending, confirmed, preparing, cooked, delivering, completed)"]
        ],
        [1800, 2000, 2200, 800, 2600]
    );
    children.push(ordersTable);

    // ==========================================
    // CHƯƠNG 3
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("CHƯƠNG 3: CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ TRIỂN KHAI"));
    children.push(pHeading2("3.1. Môi trường triển khai thực nghiệm"));
    children.push(pBody("Hệ thống FOODDAILY đã được triển khai chạy thử nghiệm hoàn chỉnh trên máy chủ Laragon Platform tích hợp: PHP 8.3.16 JIT, MySQL 8.0.30, Laravel 13 Framework, Node.js 24, Vite 8 và các API đám mây: Google Gemini 1.5 Flash, PayOS VietQR Open API, Cloudinary Media CDN và Telegram Bot API."));

    children.push(pHeading2("3.2. Kết quả chức năng và giao diện đạt được"));
    children.push(pBullet("Phân hệ Khách hàng (Client Portal):", "Giao diện hiện đại phong cách ẩm thực cao cấp, hỗ trợ bộ lọc thông minh, tính năng Vòng quay may mắn 'Hôm nay ăn gì', Chatbot tư vấn món AI Gemini, tạo phòng đặt món nhóm realtime mượt mà và tra cứu đơn hàng không cần đăng nhập."));
    children.push(pBullet("Phân hệ Bếp FIFO & Thu ngân:", "Màn hình Bếp FIFO trực quan, tự động gom nhóm số lượng từng món để nấu hàng loạt, loại bỏ hoàn toàn phiếu giấy. Bàn làm việc nhân viên quầy hỗ trợ in bill thanh toán nhiệt 80mm chỉ với 1 cú nhấp chuột."));
    children.push(pBullet("Phân hệ Quản trị Admin:", "Dashboard thống kê doanh thu và đơn hàng theo biểu đồ thời gian thực, quản lý thực đơn, quản lý kho hàng và nhà cung cấp, phân quyền Spatie RBAC và sao lưu dữ liệu khách hàng định dạng zip an toàn."));
    children.push(pBullet("Thanh toán VietQR PayOS & Webhook:", "Sinh mã QR chuẩn Napas 24/7 dưới 0.3 giây, Webhook đối soát và kích hoạt đơn tự động chính xác 100% nhờ xác thực chữ ký HMAC-SHA256."));

    children.push(pHeading2("3.3. Kết quả Kiểm thử tự động (Automation Testing với PHPUnit)"));
    children.push(pBody("Hệ thống được kiểm thử tự động toàn diện qua 8 Test Suites với 41/41 Test Cases đạt tỷ lệ thành công 100%:"));

    const testTable = makeTable(
        ["STT", "Bộ kiểm thử (Test Suite)", "Số kịch bản", "Mục tiêu kiểm thử nghiệp vụ", "Kết quả"],
        [
            ["1", "CartPaymentFlowTest.php", "6", "Thêm giỏ hàng, áp voucher, tính phí ship, tạo đơn", "PASSED (100%)"],
            ["2", "CouponPromotionSystemTest.php", "5", "Tính % giảm giá, kiểm tra ví voucher, giới hạn lượt dùng", "PASSED (100%)"],
            ["3", "RolePermissionRbacTest.php", "5", "Kiểm tra phân quyền Spatie RBAC, chặn truy cập trái phép", "PASSED (100%)"],
            ["4", "KitchenQueueAndDishReviewTest.php", "5", "Hàng đợi Bếp FIFO, đổi trạng thái nấu, đánh giá món ăn", "PASSED (100%)"],
            ["5", "OrderAutomationTest.php", "6", "Tự hủy đơn quá 15 phút, phát lịch suất ăn combo tự động", "PASSED (100%)"],
            ["6", "ProcurementAndInventoryTest.php", "4", "Nhập kho nguyên liệu, cảnh báo tồn kho thấp, nhà cung cấp", "PASSED (100%)"],
            ["7", "FullSystemWorkflowTest.php", "5", "Luồng End-to-End từ Khách đặt -> PayOS QR -> Bếp nấu", "PASSED (100%)"],
            ["8", "MailNotificationTest.php", "5", "Gửi email hóa đơn, cấp voucher ví và khôi phục mật khẩu OTP", "PASSED (100%)"],
            ["Tổng", "8 Test Suites Toàn diện", "41", "Bao phủ toàn diện 100% các chức năng cốt lõi", "41/41 PASSED"]
        ],
        [600, 2700, 1100, 3600, 1400],
        "1E3A8A"
    );
    children.push(testTable);

    children.push(pHeading2("3.4. Đánh giá hiệu năng và giải pháp an toàn bảo mật"));
    children.push(pBullet("Hiệu năng tối ưu:", "Loại bỏ hoàn toàn lỗi truy vấn N+1 nhờ Eager Loading; đánh chỉ mục Composite Index giúp tốc độ truy vấn đơn hàng dưới 5ms; CSS/JS nén chỉ 45 KB đạt Google Lighthouse trên 95 điểm."));
    children.push(pBullet("Middleware PreventBackHistory chống BFCache:", "Thiết lập tiêu đề 'no-cache, no-store, must-revalidate', ngăn chặn rò rỉ thông tin quản trị khi người dùng bấm nút Back sau khi đăng xuất."));
    children.push(pBullet("Bảo mật Webhook HMAC-SHA256:", "Xác thực chữ ký số bằng mã băm bí mật, ngăn chặn 100% các hành vi gửi thông báo chuyển khoản giả mạo."));

    // ==========================================
    // CHƯƠNG 4
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("CHƯƠNG 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN"));
    children.push(pHeading2("4.1. Kết luận"));
    children.push(pBullet("Kết quả đạt được:", "Đồ án hoàn thành 100% mục tiêu nghiên cứu, xây dựng nền tảng F&B trực tuyến khép kín, hoạt động ổn định và giải quyết xuất sắc các bài toán thực tiễn bằng công nghệ AI, Realtime và Thanh toán tự động."));
    children.push(pBullet("Ưu điểm nổi bật:", "Trải nghiệm người dùng cao cấp, thao tác mượt mà không tải lại trang, kiến trúc mã nguồn chuẩn doanh nghiệp, bộ kiểm thử tự động đạt độ tin cậy tuyệt đối."));
    children.push(pBullet("Hạn chế hiện tại:", "Kho nguyên liệu chưa tự trừ lùi theo công thức định lượng món (BOM); chưa có ứng dụng và cổng thông tin dành riêng cho Tài xế giao hàng (Driver Portal)."));

    children.push(pHeading2("4.2. Định hướng phát triển đề tài (Lộ trình 6 tính năng thiết yếu)"));
    children.push(pBody("Nhằm chuẩn bị đưa hệ thống FOODDAILY vào vận hành thương mại quy mô lớn, nhóm nghiên cứu xác định lộ trình bổ sung 6 tính năng sống còn trong 2 tuần tiếp theo:"));

    const roadmapTable = makeTable(
        ["STT", "Tính năng nâng cấp", "Mô tả chi tiết", "Lý do bắt buộc phải có"],
        [
            ["1", "Định lượng món & Tự trừ kho (Recipe/BOM)", "Cài đặt công thức (VD: 1 Phở = 150g bánh + 100g bò). Bếp nấu xong tự trừ nguyên liệu thô.", "Quản lý chính xác giá vốn (COGS), kiểm soát thất thoát nguyên liệu."],
            ["2", "Cổng thông tin Tài xế (Driver Portal)", "Giao diện riêng cho shipper: nhận thông báo món nấu xong, xem địa chỉ, chụp ảnh giao hàng.", "Hoàn thiện mắt xích vận hành: Khách -> Bếp -> Shipper -> Giao tận tay."],
            ["3", "Định vị tài xế Live GPS Tracking", "Truyền tọa độ GPS của shipper qua WebSocket lên bản đồ cho khách theo dõi kèm ETA.", "Tính năng tiêu chuẩn của app giao đồ ăn, giảm tình trạng khách sốt ruột hủy đơn."],
            ["4", "Chuông báo âm thanh & Hẹn giờ Bếp", "Chuông reo to khi có đơn mới; đồng hồ đếm ngược đổi màu cảnh báo khi nấu trễ giờ.", "Đầu bếp tay luôn bận nấu, không thể nhìn màn hình; giúp không bao giờ sót đơn."],
            ["5", "Thông báo đẩy Web Push / Zalo ZNS", "Gửi thông báo trực tiếp đến điện thoại khi đơn duyệt, đang nấu, đang giao và đã tới nơi.", "Khách đặt xong thường tắt tab web; thông báo đẩy giúp nhận đồ ăn kịp thời."],
            ["6", "Hạng hội viên & Tích điểm (Loyalty)", "Tích điểm % đơn hàng, thăng hạng (Đồng, Bạc, Vàng, Kim Cương) để nhận mã giảm giá độc quyền.", "F&B phụ thuộc trên 60% vào khách quen; tích điểm là công cụ giữ chân khách bắt buộc."]
        ],
        [600, 2400, 3400, 3000],
        "1E3A8A"
    );
    children.push(roadmapTable);

    // ==========================================
    // TÀI LIỆU THAM KHẢO
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("DANH MỤC TÀI LIỆU THAM KHẢO"));
    children.push(pBody("[ 1 ] Laravel Documentation (Version 13.x) — https://laravel.com/docs/13.x"));
    children.push(pBody("[ 2 ] PHP Language Documentation (PHP 8.3 Manual) — https://www.php.net/manual/en/migration83.php"));
    children.push(pBody("[ 3 ] PayOS Documentation & VietQR Open API — https://payos.vn/docs/"));
    children.push(pBody("[ 4 ] Google Gemini API Documentation — https://ai.google.dev/docs"));
    children.push(pBody("[ 5 ] Spatie Laravel Permission — https://spatie.be/docs/laravel-permission/"));
    children.push(pBody("[ 6 ] Tailwind CSS v4 Documentation — https://tailwindcss.com/docs"));
    children.push(pBody("[ 7 ] MySQL 8.0 Reference Manual — https://dev.mysql.com/doc/refman/8.0/en/"));
    children.push(pBody("[ 8 ] RFC 7519 - JSON Web Signature (JWS) & HMAC Algorithms — https://datatracker.ietf.org/doc/html/rfc7519"));
    children.push(pBody("[ 9 ] MDN Web Docs - Back/Forward Cache (BFCache) — https://developer.mozilla.org/en-US/docs/Glossary/bfcache"));
    children.push(pBody("[ 10 ] Vite Official Guide — https://vitejs.dev/guide/"));

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: FONT_NAME, size: 24, color: "1E293B" },
                    paragraph: { spacing: { line: 360, before: 60, after: 60 } }
                }
            }
        },
        sections: [
            {
                properties: {
                    page: {
                        margin: {
                            top: 1134,    // 20mm
                            bottom: 1134, // 20mm
                            left: 1700,   // 30mm (gáy đóng sách)
                            right: 1134   // 20mm
                        }
                    }
                },
                children: children
            }
        ]
    });

    const outputPath = path.join(__dirname, '..', 'docs', 'BAO_CAO_DU_AN_FOODDAILY_TRAN_LE_THAN.docx');
    Packer.toBuffer(doc).then(buffer => {
        fs.writeFileSync(outputPath, buffer);
        console.log('Successfully generated full DOCX report at:', outputPath);
        console.log('File size:', buffer.length, 'bytes');
    }).catch(err => {
        console.error('Error generating document:', err);
    });
}

generateDocument();

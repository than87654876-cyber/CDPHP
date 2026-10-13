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
    Header,
    Footer,
    PageNumber,
    ImageRun
} = require('docx');

const FONT_NAME = "Times New Roman";
const CODE_FONT = "Consolas";

// Standard borders
const borderThin = { style: BorderStyle.SINGLE, size: 4, color: "000000" };
const borderGray = { style: BorderStyle.SINGLE, size: 4, color: "CBD5E1" };
const tableBorders = {
    top: borderThin,
    bottom: borderThin,
    left: borderThin,
    right: borderThin,
    insideHorizontal: { style: BorderStyle.SINGLE, size: 4, color: "D1D5DB" },
    insideVertical: { style: BorderStyle.SINGLE, size: 4, color: "D1D5DB" },
};

const cellMargins = { top: 120, bottom: 120, left: 140, right: 140 };

function hCell(text, width, align = AlignmentType.CENTER, bg = "E2E8F0") {
    return new TableCell({
        width: { size: width, type: WidthType.DXA },
        shading: { fill: bg, type: ShadingType.CLEAR },
        margins: cellMargins,
        children: [
            new Paragraph({
                alignment: align,
                children: [
                    new TextRun({ text, bold: true, color: "000000", size: 24, font: FONT_NAME })
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
                    new TextRun({ text: String(text), bold: isBold, color: "000000", size: 23, font: FONT_NAME })
                ]
            })
        ]
    });
}

function makeTable(headers, rowsData, widths, headerBg = "E2E8F0") {
    const tableRows = [];
    const hCells = headers.map((h, i) => hCell(h, widths[i], AlignmentType.CENTER, headerBg));
    tableRows.push(new TableRow({ children: hCells }));

    rowsData.forEach((row, idx) => {
        const bg = idx % 2 === 1 ? "F8FAFC" : "FFFFFF";
        const cCells = row.map((cellText, i) => {
            const align = (i === 0 && cellText.length <= 4) ? AlignmentType.CENTER : AlignmentType.LEFT;
            const bold = (i === 0) && row.length > 2;
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
        spacing: { before: 320, after: 140 },
        children: [
            new TextRun({ text, bold: true, size: 28, font: FONT_NAME, color: "000000" })
        ]
    });
}

function pHeading2(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 220, after: 100 },
        children: [
            new TextRun({ text, bold: true, size: 26, font: FONT_NAME, color: "000000" })
        ]
    });
}

function pHeading3(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 160, after: 80 },
        children: [
            new TextRun({ text, bold: true, size: 25, font: FONT_NAME, color: "000000" })
        ]
    });
}

function pHeading4(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 120, after: 60 },
        children: [
            new TextRun({ text, bold: true, italics: true, size: 24, font: FONT_NAME, color: "000000" })
        ]
    });
}

function pBody(text, isItalic = false) {
    return new Paragraph({
        spacing: { before: 40, after: 60, line: 360 }, // 1.5 line spacing
        alignment: AlignmentType.JUSTIFIED,
        children: [
            new TextRun({ text, size: 25, font: FONT_NAME, italics: isItalic, color: "000000" })
        ]
    });
}

function pBullet(boldPrefix, content) {
    return new Paragraph({
        spacing: { before: 30, after: 50, line: 360 },
        alignment: AlignmentType.JUSTIFIED,
        children: [
            new TextRun({ text: boldPrefix ? (boldPrefix + ": ") : "• ", bold: !!boldPrefix, size: 25, font: FONT_NAME, color: "000000" }),
            new TextRun({ text: content, size: 25, font: FONT_NAME, color: "000000" })
        ]
    });
}

function pCode(codeString) {
    const lines = codeString.split("\n");
    return lines.map(line => new Paragraph({
        spacing: { before: 10, after: 10, line: 240 },
        children: [
            new TextRun({ text: line, size: 19, font: CODE_FONT, color: "1E293B" })
        ]
    }));
}

function pageBreak() {
    return new Paragraph({
        children: [new TextRun({ text: "", break: 1 })],
        pageBreakBefore: true
    });
}

function buildDocx() {
    // SECTION 1: TRANG BÌA (Cover page)
    const coverChildren = [
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 200, after: 60 },
            children: [new TextRun({ text: "TRƯỜNG ĐẠI HỌC [TÊN TRƯỜNG ĐẠI HỌC]", bold: true, size: 28, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 600 },
            children: [new TextRun({ text: "KHOA CÔNG NGHỆ THÔNG TIN", bold: true, size: 26, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 400, after: 80 },
            children: [new TextRun({ text: "ĐỒ ÁN TỐT NGHIỆP", bold: true, size: 30, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 500 },
            children: [new TextRun({ text: "CHUYÊN NGÀNH: CÔNG NGHỆ THÔNG TIN", bold: true, size: 24, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 300, after: 80 },
            children: [new TextRun({ text: "ĐỀ TÀI:", bold: true, size: 24, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 800 },
            children: [
                new TextRun({
                    text: "XÂY DỰNG HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY",
                    bold: true,
                    size: 28,
                    font: FONT_NAME
                })
            ]
        }),
        new Paragraph({ spacing: { before: 600, after: 40 }, children: [new TextRun({ text: "Giảng viên hướng dẫn:\tThS. [Họ và tên Giảng viên hướng dẫn]", size: 24, font: FONT_NAME })] }),
        new Paragraph({ spacing: { before: 0, after: 40 }, children: [new TextRun({ text: "Sinh viên thực hiện:\tTrần Lê Thân", bold: true, size: 24, font: FONT_NAME })] }),
        new Paragraph({ spacing: { before: 0, after: 40 }, children: [new TextRun({ text: "Lớp:\t[Mã lớp]", size: 24, font: FONT_NAME })] }),
        new Paragraph({ spacing: { before: 0, after: 600 }, children: [new TextRun({ text: "Khoá:\t2022 – 2026", size: 24, font: FONT_NAME })] }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 400, after: 0 },
            children: [new TextRun({ text: "TP. Hồ Chí Minh, tháng 10 năm 2026", size: 22, font: FONT_NAME })]
        })
    ];

    // SECTION 2: BÁO CÁO TOÀN VĂN
    const bodyChildren = [];

    // LỜI CẢM ƠN
    bodyChildren.push(pHeading1("LỜI CẢM ƠN"));
    bodyChildren.push(pBody("Để hoàn thành báo cáo tốt nghiệp này, tôi xin gửi lời cảm ơn chân thành đến quý Thầy Cô Khoa Công nghệ Thông tin - Trường Đại học [Tên trường Đại học] đã tận tình truyền đạt cho tôi những kiến thức, kinh nghiệm bổ ích trong suốt những năm được học tại trường. Những nền tảng kiến thức quý giá ấy giúp ích cho tôi rất nhiều trong quá trình hoàn thành luận văn tốt nghiệp, đồng thời là hành trang hữu ích cho công việc phát triển phần mềm tương lai của tôi."));
    bodyChildren.push(pBody("Đặc biệt tôi xin gửi lời cảm ơn sâu sắc dành đến Thầy/Cô hướng dẫn vì Thầy/Cô đã tận tình hướng dẫn, góp ý, hỗ trợ tôi hết mình trong suốt quá trình xây dựng hệ thống. Tôi cũng xin cảm ơn các Thầy Cô trong bộ môn đã hỗ trợ, chỉ dẫn tôi về công nghệ. Nhờ sự giúp đỡ hết lòng của Thầy Cô mà báo cáo của tôi mới được hoàn thiện như ngày hôm nay."));
    bodyChildren.push(pBody("Tuy nhiên, thời gian thực hiện với kinh nghiệm hạn hẹp của bản thân mình, tôi đã cố gắng hết mình để tìm hiểu, phân tích, xây dựng hệ thống nhưng chắc chắn sẽ không tránh khỏi sự thiếu sót. Tôi rất mong nhận được sự thông cảm cũng như những lời góp ý và chỉ bảo tận tình của quý Thầy Cô, điều đó sẽ giúp tôi có thêm sự dày dặn trong kinh nghiệm và kiến thức của mình."));
    bodyChildren.push(pBody("Cuối cùng, tôi xin kính chúc tập thể quý Thầy Cô trường luôn dồi dào sức khỏe và thành công trong công việc giảng dạy."));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 180, after: 40 },
        children: [new TextRun({ text: "Tôi xin chân thành cảm ơn!", italics: true, size: 25, font: FONT_NAME })]
    }));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 0, after: 100 },
        children: [new TextRun({ text: "Trần Lê Thân", bold: true, size: 25, font: FONT_NAME })]
    }));

    // LỜI CAM ĐOAN
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("LỜI CAM ĐOAN"));
    bodyChildren.push(pBody("Tôi xin cam đoan đề tài: “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY” là một công trình nghiên cứu độc lập dưới sự hướng dẫn của Giảng viên hướng dẫn và sự hỗ trợ, chỉ dạy công nghệ từ các giảng viên bộ môn. Ngoài ra không có bất cứ sự sao chép từ người khác."));
    bodyChildren.push(pBody("Đề tài, nội dung báo cáo và mã nguồn chương trình là kết quả tìm hiểu, phân tích và lập trình nghiêm túc của bản thân tôi. Các số liệu, tài liệu tham khảo trong báo cáo đều được trích dẫn nguồn gốc rõ ràng theo đúng quy định."));
    bodyChildren.push(pBody("Nếu có bất kỳ sự không trung thực nào, tôi xin chịu hoàn toàn trách nhiệm trước Hội đồng và Nhà trường."));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 180, after: 40 },
        children: [new TextRun({ text: "Sinh viên thực hiện:", italics: true, size: 25, font: FONT_NAME })]
    }));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 0, after: 100 },
        children: [new TextRun({ text: "Trần Lê Thân", bold: true, size: 25, font: FONT_NAME })]
    }));

    // MỤC LỤC
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("MỤC LỤC"));
    const tocItems = [
        ["LỜI CẢM ƠN", "i"],
        ["LỜI CAM ĐOAN", "ii"],
        ["DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH", "iii"],
        ["DANH MỤC BẢNG", "iv"],
        ["LỜI MỞ ĐẦU", "1"],
        ["Chương 1. TỔNG QUAN TÀI LIỆU", "3"],
        ["  1.1 Khảo sát bài toán", "3"],
        ["    1.1.1. Đặt vấn đề", "3"],
        ["    1.1.2. Giải pháp", "4"],
        ["    1.1.3. Mục đích của đề tài", "5"],
        ["  1.2 Cơ sở lý thuyết", "6"],
        ["    1.2.1. HTML5", "6"],
        ["    1.2.2. CSS", "6"],
        ["    1.2.3. Boostrap 4 & Tailwind CSS", "7"],
        ["    1.2.4. Javascript", "7"],
        ["    1.2.5. Jquery & WebSockets", "8"],
        ["    1.2.6. PHP", "8"],
        ["    1.2.7. Laravel", "9"],
        ["    1.2.8. MySQL Server", "10"],
        ["    1.2.9. Visual Studio Code", "11"],
        ["    1.2.10. Laragon", "11"],
        ["Chương 2. PHƯƠNG PHÁP THỰC HIỆN", "12"],
        ["  2.1 Phân tích hệ thống", "12"],
        ["    2.1.1 Quy trình nghiệp vụ", "12"],
        ["    2.1.2 Thông tin yêu cầu", "14"],
        ["    2.1.3 Sơ đồ chức năng của hệ thống", "16"],
        ["  2.2 Các biểu đồ thiết kế hệ thống", "19"],
        ["    2.2.1 Lượt đồ Use case & Bảng đặc tả Use case", "19"],
        ["    2.2.2 Lược đồ Activity (13 lưu đồ nghiệp vụ)", "28"],
        ["    2.2.3 Biểu đồ thành phần", "35"],
        ["    2.2.4 Biểu đồ triển khai", "36"],
        ["  2.3 Cơ sở dữ liệu", "37"],
        ["Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ", "48"],
        ["  3.1 Một số giao diện website", "48"],
        ["    3.1.1 Phần quản trị", "48"],
        ["    3.1.2 File excel xuất ra từ quản trị", "54"],
        ["    3.1.3 Phần người dùng (Màn hình lớn & Màn hình nhỏ)", "55"],
        ["  3.2 Kết quả Kiểm thử tự động (Automation Testing)", "58"],
        ["  3.3 Đánh giá hiệu năng và an toàn bảo mật", "60"],
        ["Chương 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN", "62"],
        ["  4.1 Kết luận", "62"],
        ["  4.2 Định hướng phát triển (Đề tài & Bản thân)", "63"],
        ["PHỤ LỤC", "66"],
        ["DANH MỤC TÀI LIỆU THAM KHẢO", "68"]
    ];
    tocItems.forEach(([title, page]) => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 30, after: 30, line: 300 },
            children: [
                new TextRun({ text: title, size: 24, font: FONT_NAME, bold: title.startsWith("Chương") || title.startsWith("LỜI") || title.startsWith("DANH") || title.startsWith("PHỤ") }),
                new TextRun({ text: "\t" + page, size: 24, font: FONT_NAME })
            ]
        }));
    });

    // DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH"));
    const figureItems = [
        "Sơ đồ 2.1 Sơ đồ chức năng của Admin",
        "Sơ đồ 2.2 Sơ đồ chức năng của Nhân viên quầy / thu ngân",
        "Sơ đồ 2.3 Sơ đồ chức năng của Đầu bếp (Kitchen)",
        "Sơ đồ 2.4 Sơ đồ chức năng của Khách hàng",
        "Lược đồ 2.1 Lược đồ use case tổng quát",
        "Lược đồ 2.2 Lược đồ chức năng đăng nhập",
        "Lược đồ 2.3 Lược đồ chức năng reset password",
        "Lược đồ 2.4 Lược đồ tìm kiếm",
        "Lược đồ 2.5 Lược đồ đặt món theo nhóm Realtime",
        "Lược đồ 2.6 Lược đồ thanh toán PayOS VietQR",
        "Lược đồ 2.7 Lược đồ điều phối Bếp FIFO",
        "Lược đồ 2.8 Lược đồ quản lý thông tin hóa đơn và in bill",
        "Lược đồ 2.9 Lược đồ quản lý thông tin khách hàng",
        "Lược đồ 2.10 Lược đồ quản lý thông tin món ăn",
        "Lược đồ 2.11 Lược đồ quản lý thông tin nhà cung cấp",
        "Lược đồ 2.12 Lược đồ quản lý kho nguyên liệu và nhập kho",
        "Lược đồ 2.13 Lược đồ quản lý nhân viên và phân quyền RBAC",
        "Biểu đồ 2.1 Biểu đồ thành phần hệ thống",
        "Biểu đồ 2.2 Biểu đồ thành phần quản trị viên",
        "Biểu đồ 2.3 Biểu đồ thành phần người dùng",
        "Biểu đồ 2.4 Biểu đồ triển khai",
        "Hình 2.1 Sơ đồ Cơ sở dữ liệu toàn hệ thống",
        "Hình 3.1 Giao diện đăng nhập quản trị",
        "Hình 3.2 Giao diện Dashboard thống kê doanh thu",
        "Hình 3.3 Giao diện quản lý thực đơn món ăn",
        "Hình 3.4 Giao diện quản lý đơn đặt hàng",
        "Hình 3.5 Giao diện quản lý khách hàng",
        "Hình 3.6 Giao diện quản lý kho nguyên liệu",
        "Hình 3.7 Giao diện quản lý nhà cung cấp",
        "Hình 3.8 Giao diện Màn hình Bếp FIFO",
        "Hình 3.9 Giao diện Bàn làm việc thu ngân & In hóa đơn",
        "Hình 3.10 Giao diện Reset Password",
        "Hình 3.11 Mẫu file xuất báo cáo / hóa đơn bán hàng",
        "Hình 3.12 Giao diện Trang chủ và Menu trên màn hình lớn",
        "Hình 3.13 Giao diện Phòng đặt món nhóm Realtime trên màn hình lớn",
        "Hình 3.14 Giao diện Thanh toán PayOS VietQR trên màn hình lớn",
        "Hình 3.15 Giao diện Chatbot Trợ lý AI Gemini trên màn hình lớn",
        "Hình 3.16 Giao diện người dùng Responsive trên màn hình điện thoại di động"
    ];
    figureItems.forEach(item => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 30, after: 30, line: 300 },
            children: [new TextRun({ text: item, size: 24, font: FONT_NAME })]
        }));
    });

    // DANH MỤC BẢNG
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("DANH MỤC BẢNG"));
    const tableItems = [
        "Bảng 2.1 Luồng sự kiện chính use case đăng nhập",
        "Bảng 2.2 Luồng sự kiện chính use case đăng xuất",
        "Bảng 2.3 Luồng sự kiện chính use case tìm kiếm và xem món ăn",
        "Bảng 2.4 Luồng sự kiện chính use case đặt món theo nhóm Realtime",
        "Bảng 2.5 Luồng sự kiện chính use case thanh toán tự động VietQR PayOS",
        "Bảng 2.6 Luồng sự kiện chính use case Trợ lý AI Gemini tư vấn món",
        "Bảng 2.7 Luồng sự kiện chính use case điều phối chế biến Bếp FIFO",
        "Bảng 2.8 Luồng sự kiện chính use case đăng ký gói ăn định kỳ",
        "Bảng 2.9 Luồng sự kiện chính use case quản lý thực đơn món ăn",
        "Bảng 2.10 Luồng sự kiện chính use case quản lý kho và nhà cung cấp",
        "Bảng 2.11 Luồng sự kiện chính use case quản lý khuyến mãi và voucher",
        "Bảng 2.12 Tóm tắt ý nghĩa dữ liệu bảng Users",
        "Bảng 2.13 Tóm tắt ý nghĩa dữ liệu bảng Categories",
        "Bảng 2.14 Tóm tắt ý nghĩa dữ liệu bảng Dishes",
        "Bảng 2.15 Tóm tắt ý nghĩa dữ liệu bảng Orders",
        "Bảng 2.16 Tóm tắt ý nghĩa dữ liệu bảng Order_Items",
        "Bảng 2.17 Tóm tắt ý nghĩa dữ liệu bảng Group_Orders",
        "Bảng 2.18 Tóm tắt ý nghĩa dữ liệu bảng Service_Packages",
        "Bảng 2.19 Tóm tắt ý nghĩa dữ liệu bảng Subscriptions",
        "Bảng 2.20 Tóm tắt ý nghĩa dữ liệu bảng Coupons & User_Coupons",
        "Bảng 2.21 Tóm tắt ý nghĩa dữ liệu bảng Suppliers",
        "Bảng 2.22 Tóm tắt ý nghĩa dữ liệu bảng Inventory_Items",
        "Bảng 2.23 Tóm tắt ý nghĩa dữ liệu bảng Goods_Receipts",
        "Bảng 3.1 Bảng kết quả kiểm thử tự động Automation Testing PHPUnit",
        "Bảng 4.1 Bảng lộ trình phát triển 6 tính năng thiết yếu"
    ];
    tableItems.forEach(item => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 30, after: 30, line: 300 },
            children: [new TextRun({ text: item, size: 24, font: FONT_NAME })]
        }));
    });

    // LỜI MỞ ĐẦU
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("LỜI MỞ ĐẦU"));
    bodyChildren.push(pBody("Trong thời đại kinh tế số và nhịp sống công nghiệp hiện đại, thương mại điện tử đã trở thành một phần không thể thiếu trong đời sống hàng ngày của người tiêu dùng. Đặc biệt trong lĩnh vực ẩm thực và dịch vụ ăn uống (F&B), nhu cầu đặt món trực tuyến giao tận nơi đang tăng trưởng theo cấp số nhân. Người dùng ngày càng kỳ vọng vào sự tiện lợi, tốc độ phản hồi nhanh, thanh toán mượt mà và khả năng phục vụ theo nhóm."));
    bodyChildren.push(pBody("Tuy nhiên, các giải pháp bán hàng truyền thống và nhiều hệ thống hiện có vẫn tồn tại những bất cập lớn:"));
    bodyChildren.push(pBullet("Khi đặt món ăn cho tập thể", "Tại các văn phòng công sở, lớp học, nhóm bạn bè, việc gom đơn qua giấy nháp, file Excel hoặc tin nhắn rời rạc thường tốn nhiều thời gian (từ 15 - 30 phút), rất dễ ghi nhầm món, sót ghi chú của từng cá nhân và phiền toái khi tính toán chia tiền sòng phẳng."));
    bodyChildren.push(pBullet("Tâm lý đắn đo khi chọn món", "Người tiêu dùng thường xuyên mất nhiều thời gian lướt menu mà không biết chọn món gì do thiếu công cụ tư vấn theo sở thích, tâm trạng và ngân sách."));
    bodyChildren.push(pBullet("Đứt gãy luồng quầy và bếp", "Luồng thông tin giữa quầy thu ngân và khu vực bếp chế biến thường bị đứt gãy, dẫn đến việc nấu nhầm thứ tự ưu tiên hoặc làm trễ đơn vào giờ cao điểm."));
    bodyChildren.push(pBullet("Đối soát ngân hàng thủ công", "Quy trình đối soát chuyển khoản ngân hàng bằng chụp ảnh biên lai thủ công tiềm ẩn nhiều rủi ro gian lận và làm chậm mạch xử lý đơn hàng."));
    bodyChildren.push(pBody("Nhận thức được những vấn đề thực tiễn đó, tôi đã lựa chọn đề tài: “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY”. Đề tài hướng tới việc xây dựng một hệ thống hoàn chỉnh, chuẩn hóa toàn diện từ phân hệ khách hàng, màn hình điều phối bếp đến quản trị viên, ứng dụng các công nghệ thời gian thực (WebSockets), trí tuệ nhân tạo (Google Gemini AI) và cổng thanh toán tự động VietQR PayOS."));

    // CHƯƠNG 1
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 1. TỔNG QUAN TÀI LIỆU"));
    bodyChildren.push(pHeading2("1.1\tKhảo sát bài toán"));
    bodyChildren.push(pHeading3("1.1.1.\tĐặt vấn đề"));
    bodyChildren.push(pBody("Hiện nay, sự phát triển nhanh chóng của công nghệ thông tin đã tác động mạnh mẽ đến thói quen mua sắm của người tiêu dùng. Trong lĩnh vực F&B, số lượng khách hàng đặt đồ ăn qua mạng ngày càng nhiều. Tuy nhiên, việc vận hành một chuỗi quán ăn hay nhà hàng giao hàng trực tuyến gặp phải nhiều thách thức lớn:"));
    bodyChildren.push(pBullet("Thứ nhất", "Việc quản lý thông tin khách hàng, thực đơn, tồn kho nguyên vật liệu và điều phối đơn hàng nếu làm bằng phương pháp thủ công hoặc các phần mềm rời rạc sẽ tốn rất nhiều thời gian và nhân lực."));
    bodyChildren.push(pBullet("Thứ hai", "Đối với khách hàng tập thể, việc đặt món chung luôn là một trở ngại lớn: người gom đơn phải hỏi từng người, ghi chép từng yêu cầu riêng (không cay, ít đá, thêm cơm) và tự cộng tiền, dễ dẫn đến sai sót và thâm hụt tiền bạc."));
    bodyChildren.push(pBullet("Thứ ba", "Sự thiếu hụt kết nối thời gian thực giữa quầy bán hàng và bếp nấu khiến các quán ăn thường bị quá tải, nhầm lẫn thứ tự đơn hàng khi lượng khách tăng đột biến."));
    bodyChildren.push(pBody("Chính vì vậy, xây dựng một hệ thống website quản lý bán và giao đồ ăn trực tuyến khép kín, hiện đại là giải pháp cấp thiết và thiết thực nhất."));

    bodyChildren.push(pHeading3("1.1.2.\tGiải pháp"));
    bodyChildren.push(pBody("Tôi đã đưa ra giải pháp là xây dựng website hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm mang tên FOODDAILY, để mang đến cho nhà hàng và người dùng một giải pháp công nghệ toàn diện:"));
    bodyChildren.push(pBullet("Đặt món theo nhóm Realtime (Group Order)", "Cho phép khách hàng tạo phòng đặt món chung theo thời gian thực qua mã PIN / QR Code, các thành viên tự chọn món trên điện thoại cá nhân và giỏ hàng tự động đồng bộ."));
    bodyChildren.push(pBullet("Trí tuệ nhân tạo AI Gemini", "Tích hợp Google Gemini 1.5 Flash AI hỗ trợ tư vấn dinh dưỡng và gợi ý món ăn phù hợp theo tâm trạng, ngân sách và thời tiết."));
    bodyChildren.push(pBullet("Cổng thanh toán tự động VietQR PayOS", "Tự động hóa khâu thanh toán qua cổng VietQR PayOS, đối soát giao dịch ngân hàng và kích hoạt đơn hàng sang bếp chỉ trong 1 - 2 giây bằng Webhook."));
    bodyChildren.push(pBullet("Màn hình Bếp FIFO (Kitchen Display System)", "Sắp xếp đơn hàng theo nguyên tắc vào trước làm trước và tự động gom số lượng món giúp tối ưu hóa công suất chế biến."));
    bodyChildren.push(pBullet("Gói ăn định kỳ (Meal Subscriptions)", "Cung cấp mô hình gói ăn theo tuần/tháng với tính năng linh hoạt đổi món hằng ngày."));

    bodyChildren.push(pHeading3("1.1.3.\tMục đích của đề tài"));
    bodyChildren.push(pBody("Xây dựng một hệ thống website giao đồ ăn trực tuyến đáp ứng đầy đủ các yêu cầu về quy trình, nghiệp vụ F&B hiện đại, giúp nhà hàng tối ưu hóa quy trình bán hàng, giảm thiểu sai sót và tiếp cận đông đảo khách hàng trên môi trường Internet."));
    bodyChildren.push(pBody("Đồng thời, đồ án là cơ hội để nghiên cứu chuyên sâu và nâng cao kỹ năng lập trình web hiện đại trên nền tảng PHP và Framework Laravel, làm chủ các thư viện mã nguồn mở, cơ chế truyền dữ liệu thời gian thực (WebSockets), thiết kế cơ sở dữ liệu quan hệ MySQL, và ứng dụng vào bài toán thực tiễn."));

    bodyChildren.push(pHeading2("1.2\tCơ sở lý thuyết"));
    bodyChildren.push(pHeading3("1.2.1.\tHTML5"));
    bodyChildren.push(pBody("HTML5 (HyperText Markup Language 5) là phiên bản mới nhất của ngôn ngữ đánh dấu siêu văn bản, là bộ khung cấu trúc tiêu chuẩn cho mọi trang web hiện đại. HTML5 bổ sung nhiều thẻ ngữ nghĩa như header, nav, section, article, footer, hỗ trợ đa phương tiện và chuẩn SEO."));

    bodyChildren.push(pHeading3("1.2.2.\tCSS"));
    bodyChildren.push(pBody("CSS (Cascading Style Sheets) định dạng hình thức hiển thị cho các phần tử HTML như màu sắc, font chữ, bố cục khung nhìn và hiệu ứng chuyển động, giúp trang web có tính thẩm mỹ và chuyên nghiệp cao."));

    bodyChildren.push(pHeading3("1.2.3.\tBoostrap 4 & Tailwind CSS"));
    bodyChildren.push(pBody("Dự án kết hợp Tailwind CSS v4 và Bootstrap components để xây dựng giao diện theo chuẩn Mobile-First Responsive, tự động co giãn hiển thị tối ưu trên máy tính để bàn, máy tính bảng và điện thoại di động."));

    bodyChildren.push(pHeading3("1.2.4.\tJavascript"));
    bodyChildren.push(pBody("Javascript là ngôn ngữ lập trình kịch bản phía máy khách xử lý các tương tác người dùng như kiểm tra tính hợp lệ biểu mẫu, tính toán giỏ hàng, vòng quay may mắn và điều khiển các thành phần động trên trang web."));

    bodyChildren.push(pHeading3("1.2.5.\tJquery & WebSockets"));
    bodyChildren.push(pBody("Sử dụng Fetch API và AJAX để cập nhật dữ liệu không cần tải lại trang. Kết hợp công nghệ WebSockets (Laravel Reverb) để đồng bộ phòng đặt nhóm và đẩy trạng thái đơn hàng từ Bếp đến khách hàng thời gian thực."));

    bodyChildren.push(pHeading3("1.2.6.\tPHP"));
    bodyChildren.push(pBody("Hệ thống sử dụng PHP 8.3 với JIT Compiler hiệu năng cao, hỗ trợ kiểm tra kiểu dữ liệu nghiêm ngặt và xử lý logic backend an toàn, nhanh chóng."));

    bodyChildren.push(pHeading3("1.2.7.\tLaravel"));
    bodyChildren.push(pBody("Laravel 13 là framework PHP hàng đầu thế giới với kiến trúc MVC, Eloquent ORM chống truy vấn N+1, Blade template an toàn, Artisan task scheduling tự động và hệ thống Middleware bảo mật."));

    bodyChildren.push(pHeading3("1.2.8.\tMySQL Server"));
    bodyChildren.push(pBody("Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0 sử dụng InnoDB engine, hỗ trợ toàn vẹn giao dịch ACID và các chỉ mục Composite Index tối ưu truy vấn nhanh dưới 5ms."));

    bodyChildren.push(pHeading3("1.2.9.\tVisual Studio Code"));
    bodyChildren.push(pBody("Trình soạn thảo mã nguồn gọn nhẹ, đa năng với hệ sinh thái extension phong phú hỗ trợ viết mã, gỡ lỗi và quản lý phiên bản Git hiệu quả."));

    bodyChildren.push(pHeading3("1.2.10.\tLaragon"));
    bodyChildren.push(pBody("Môi trường máy chủ phát triển cục bộ tiện dụng trên Windows, tích hợp sẵn PHP 8.3, MySQL 8.0, Nginx, Node.js và Composer, giúp thử nghiệm hệ thống nhanh chóng."));

    // CHƯƠNG 2
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 2. PHƯƠNG PHÁP THỰC HIỆN"));
    bodyChildren.push(pHeading2("2.1\tPhân tích hệ thống"));
    bodyChildren.push(pHeading3("2.1.1\tQuy trình nghiệp vụ"));
    bodyChildren.push(pBody("Quy trình bán và giao đồ ăn trực tuyến trên hệ thống FOODDAILY được chuẩn hóa khép kín và vận hành tự động qua 7 bước chi tiết sau:"));

    // Bước 1
    bodyChildren.push(pHeading4("Bước 1: Khách hàng xem thực đơn, tìm kiếm và lựa chọn hình thức đặt món"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Khách hàng vãng lai (Guest) hoặc Khách hàng thành viên đã có tài khoản (Member). Mục đích: Giúp người dùng tiếp cận danh mục ẩm thực phong phú, trực quan của FOODDAILY; hỗ trợ tìm thấy món ăn phù hợp với khẩu vị, chế độ dinh dưỡng và ngân sách cá nhân thông qua các công cụ tìm kiếm và hỗ trợ AI thông minh."));
    bodyChildren.push(pBullet("Duyệt danh mục ẩm thực", "Giao diện hiển thị thanh phân loại danh mục gồm: Món chính (Cơm, Bún, Phở), Món ăn nhanh (Gà rán, Burger, Khoai tây), Đồ uống thanh mát (Trà sữa, Trà trái cây, Cà phê), Món tráng miệng và Combo tiết kiệm. Mỗi món ăn được hiển thị dưới dạng Card sản phẩm sắc nét với các thông tin: Tên món ăn, Hình ảnh đại diện độ phân giải cao, Đơn giá bán ưu đãi kèm giá gốc gạch ngang, Nhãn danh mục, Huy hiệu nổi bật (Best Seller, Hot Deal), Số lượt đánh giá và số sao trung bình."));
    bodyChildren.push(pBullet("Tìm kiếm và Bộ lọc đa tiêu chí", "Người dùng có thể tìm kiếm tức thì theo từ khóa tên món (hỗ trợ cả tiếng Việt có dấu và không dấu). Hệ thống cung cấp bộ lọc linh hoạt: Lọc theo khoảng giá ngân sách (Dưới 30.000đ, 30.000đ - 70.000đ, Trên 70.000đ), Lọc theo danh mục và sắp xếp theo: Phổ biến nhất, Mới nhất, Giá tăng/giảm dần, Đánh giá cao nhất."));
    bodyChildren.push(pBullet("Vòng quay may mắn ('Hôm nay ăn gì?')", "Khách hàng bấm vào biểu tượng Vòng quay may mắn trên màn hình, thiết lập mức trần ngân sách cho bữa ăn (ví dụ: tối đa 60.000 VNĐ) và bấm nút 'Quay ngay'. Vòng quay Canvas quay với hiệu ứng đồ họa và âm thanh sống động. Hệ thống chọn ngẫu nhiên một món ăn phù hợp với ngân sách và hiển thị cửa sổ trúng thưởng kèm nút 'Cho vào giỏ ngay' và 'Xem chi tiết món'."));
    bodyChildren.push(pBullet("Trợ lý Trí tuệ Nhân tạo Google Gemini 1.5 Flash AI", "Khách hàng mở hộp thoại Chatbot AI ở góc phải màn hình, nhập câu hỏi bằng ngôn ngữ tự nhiên (Ví dụ: 'Trời hôm nay mưa lạnh, mình có ngân sách 50k, muốn ăn món gì ấm bụng và không bị cay?'). Hệ thống phân tích ngữ cảnh và trả về lời tư vấn chu đáo kèm các thẻ món ăn gợi ý có sẵn nút mua nhanh."));
    bodyChildren.push(pBullet("Cơ chế Context Injection của AI", "Controller GeminiChatController@chat trích xuất danh sách các món ăn đang kinh doanh (is_active = 1, stock > 0) trong cơ sở dữ liệu MySQL, định dạng ngắn gọn tên món, giá bán, danh mục nạp vào System Instruction của mô hình Google Gemini 1.5 Flash. Khi người dùng hỏi, AI đối soát và chỉ gợi ý các món thực sự có trong menu của quán, tránh tình trạng bịa đặt (hallucination)."));
    bodyChildren.push(pBullet("Cấu trúc dữ liệu & Hình thức đặt món", "Bảng dishes (id, category_id, name, slug, price, original_price, image, description, stock, is_featured, is_active), bảng categories (id, name, slug, icon). Khách hàng chọn Đặt món cá nhân (lưu vào giỏ hàng riêng) hoặc bấm Đặt món theo nhóm (Group Order) để chuyển sang Bước 2. Món có stock <= 0 hoặc is_active = 0 sẽ tự động gắn nhãn Tạm hết hàng và khóa nút đặt."));

    // Bước 2
    bodyChildren.push(pHeading4("Bước 2: Tiếp nhận và gom đơn nhóm theo thời gian thực (Realtime Group Order)"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Chủ phòng (Host), Các thành viên tham gia phòng (Members), Máy chủ Realtime Server (Laravel Reverb / WebSockets). Mục đích: Giải quyết bài toán đặt đồ ăn tập thể tại các văn phòng công sở, lớp học hay nhóm bạn bè; loại bỏ quy trình gom đơn thủ công qua giấy nháp hay bảng tính Excel; cho phép mọi người cùng chọn món trên thiết bị riêng và đồng bộ giỏ hàng chung tức thì."));
    bodyChildren.push(pBullet("Khởi tạo phòng nhóm", "Chủ phòng nhấn nút 'Đặt món theo nhóm', đặt tên phòng (Ví dụ: 'Trưa nay ăn gì - Team Marketing') và tạo phòng. Hệ thống cấp mã PIN ngẫu nhiên 6 ký tự độc nhất (ví dụ: FD8866) và tự động sinh mã QR Code động cùng đường dẫn trực tiếp: https://fooddaily.vn/group-order/FD8866."));
    bodyChildren.push(pBullet("Chia sẻ và Gia nhập phòng", "Chủ phòng sao chép liên kết hoặc gửi ảnh mã QR vào nhóm Zalo, Messenger, Slack. Các thành viên quét mã QR hoặc truy cập website nhập mã PIN để tham gia phòng. Thành viên có thể đăng nhập tài khoản hoặc nhập tên hiển thị (Nickname, ví dụ: 'Tuấn Anh Backend', 'Chị Hương Kế toán')."));
    bodyChildren.push(pBullet("Chọn món và Đồng bộ thời gian thực", "Mỗi thành viên duyệt thực đơn ngay trong giao diện phòng nhóm, chọn món, số lượng và nhập ghi chú cá nhân (Ví dụ: 'Ít cay, không lấy hành lá, thêm cơm'). Khi thành viên bấm 'Thêm vào nhóm', giỏ hàng nhóm của tất cả các thành viên khác lập tức cập nhật tức thì (Realtime Synchronization) qua WebSocket channel presence-group-order.{room_code} mà không cần tải lại trang."));
    bodyChildren.push(pBullet("Bảng phân tách chi phí minh bạch (Split Bill)", "Giao diện hiển thị danh sách accordion theo từng thành viên: Tên thành viên -> Các món đã chọn -> Tổng tiền riêng của cá nhân đó (member_subtotal). Cuối trang hiển thị tổng số món và tổng tiền toàn phòng (room_total), giúp việc tính toán tiền nong giữa các thành viên hoàn toàn minh bạch, không sợ thiếu sót."));
    bodyChildren.push(pBullet("Chốt đơn nhóm (Lock Order)", "Chủ phòng theo dõi danh sách, khi tất cả thành viên đã chọn xong, Chủ phòng nhấn nút 'Chốt đơn nhóm & Thanh toán'. Hệ thống chuyển group_orders.status = 'locked', khóa mọi thao tác sửa món của thành viên và chuyển sang màn hình thanh toán."));

    // Bước 3
    bodyChildren.push(pHeading4("Bước 3: Xác nhận thông tin đơn hàng và Thanh toán tự động (PayOS VietQR / COD)"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Khách hàng / Chủ phòng, Cổng thanh toán trực tuyến PayOS, Hệ thống Core FOODDAILY, Ứng dụng Mobile Banking. Mục đích: Thu thập đầy đủ thông tin giao nhận hàng, áp dụng các chính sách ưu đãi giảm giá (Voucher), và thực hiện quy trình thanh toán đa kênh tiện lợi: đối soát thanh toán trực tuyến tự động 100% không tiền mặt qua VietQR hoặc thanh toán tiền mặt COD khi nhận hàng."));
    bodyChildren.push(pBullet("Nhập thông tin giao hàng & Áp dụng Voucher", "Khách hàng kiểm tra tóm tắt giỏ hàng, điền Họ tên người nhận (customer_name), Số điện thoại (customer_phone), Địa chỉ chi tiết (shipping_address), Ghi chú giao hàng (shipping_note). Khách chọn mã giảm giá trong Ví Voucher (user_coupons), hệ thống kiểm tra hạn dùng, đơn tối thiểu và tính tổng tiền: Tổng tiền = Tiền món + Phí vận chuyển - Tiền giảm giá voucher."));
    bodyChildren.push(pBullet("Tạo đơn hàng trong hệ thống", "Hệ thống khởi tạo bản ghi trong bảng orders với mã đơn duy nhất dạng số nguyên cho cổng thanh toán và chuỗi hiển thị cho khách hàng (Ví dụ: order_code = 'FD-20261012-0089'), order_status = 'pending', payment_status = 'pending'."));
    bodyChildren.push(pBullet("Phương thức 1: Tiền mặt khi nhận hàng (COD)", "Lưu payment_method = 'cod', order_status = 'confirmed'. Đơn hàng chuyển ngay sang Màn hình Bếp (Bước 4)."));
    bodyChildren.push(pBullet("Phương thức 2: Chuyển khoản trực tuyến VietQR tự động qua PayOS", "Hệ thống gọi API PayOS POST /v2/payment-requests truyền thông số mã đơn, số tiền, nội dung chuyển khoản. PayOS phản hồi thông tin tạo giao dịch kèm chuỗi mã VietQR chuẩn Napas 24/7. Màn hình khách hiển thị mã QR động, hướng dẫn thanh toán và đồng hồ đếm ngược hiệu lực 15 phút."));
    bodyChildren.push(pBullet("Cơ chế Khớp lệnh Tự động qua Webhook (1 - 2 giây)", "Khách quét mã chuyển tiền qua App ngân hàng. Ngân hàng báo có tiền -> PayOS phát tín hiệu Webhook (HTTP POST) gửi dữ liệu thanh toán đến URL: /webhook/payos. Backend xác thực Chữ ký số HMAC-SHA256 với CHECKSUM_KEY bí mật. Khi chữ ký số trùng khớp: Cập nhật payment_status = 'paid', order_status = 'confirmed', lưu mã đối soát transaction_id, đánh dấu voucher đã sử dụng, phát sự kiện WebSockets PaymentSuccessEvent về máy khách (chỉ mất 1 - 2 giây để màn hình khách chuyển sang Báo thành công), bắn thông báo Telegram cho Admin và đẩy đơn sang Màn hình Bếp FIFO."));

    // Bước 4
    bodyChildren.push(pHeading4("Bước 4: Chuyển đơn sang Màn hình Bếp FIFO và Điều phối chế biến"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Nhân viên Bếp (Chef / Kitchen Staff), Hệ thống Màn hình Bếp (Kitchen Display System - KDS). Mục đích: Số hóa toàn bộ hoạt động chế biến trong gian bếp; triệt tiêu hoàn toàn việc dùng phiếu giấy gọi món dễ thất lạc, ướt rách; tối ưu hóa công suất chế biến bằng thuật toán gộp mẻ nấu và đảm bảo tính công bằng theo nguyên tắc FIFO."));
    bodyChildren.push(pBullet("Tiếp nhận đơn hàng tự động Realtime", "Ngay khi đơn hàng được xác nhận (COD) hoặc thanh toán thành công (VietQR), hệ thống đẩy đơn sang KDS qua WebSocket private-kitchen-orders. Màn hình bếp tự động phát âm thanh cảnh báo ding-dong và hiển thị thẻ đơn hàng mới."));
    bodyChildren.push(pBullet("Thuật toán Hàng đợi Ưu tiên FIFO (First In, First Out)", "Các đơn hàng được sắp xếp theo thời gian đặt (orders.created_at ASC), đơn nào đặt trước sẽ nằm ở đầu hàng đợi bên trái. Thẻ đơn hàng tự động đổi màu cảnh báo theo thời gian chờ: Màu Xanh lá (< 10 phút), Màu Vàng cam (10 - 20 phút), Màu Đỏ cảnh báo nguy cơ trễ hạn (> 20 phút)."));
    bodyChildren.push(pBullet("Thuật toán Gom món thông minh (Batch Cooking Aggregation)", "Phía trên màn hình Bếp hiển thị bảng tổng hợp gộp số lượng món cần làm của toàn bộ các đơn đang chờ: Ví dụ: Đơn #1 có 2 Cơm gà xối mỡ, Đơn #2 có 3 Cơm gà xối mỡ -> Bảng gom món hiển thị: 'Cơm gà xối mỡ: Tổng 5 phần cần làm'. Đầu bếp chỉ cần chiên 5 phần gà cùng lúc một mẻ, tiết kiệm 60% thời gian chế biến."));
    bodyChildren.push(pBullet("Thao tác chuyển trạng thái chế biến", "Đầu bếp bấm 'Bắt đầu nấu' -> orders.order_status = 'preparing'. Nấu xong đầy đủ các món, đầu bếp bấm 'Đã nấu xong' -> orders.order_status = 'cooked'. Thẻ đơn phát chuông hoàn tất và tự động chuyển sang Bàn làm việc của quầy Thu ngân (Bước 5)."));

    // Bước 5
    bodyChildren.push(pHeading4("Bước 5: Quầy thu ngân đóng gói, in hóa đơn 1 chạm và Bàn giao vận chuyển"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Nhân viên Quầy Thu ngân (Cashier / Dispatcher), Máy in nhiệt POS K80 (80mm). Mục đích: Đối soát món ăn thực tế với yêu cầu của khách, in hóa đơn thanh toán chuẩn xác chỉ với 1 chạm, đóng gói chuyên nghiệp và bàn giao cho tài xế giao hàng."));
    bodyChildren.push(pBullet("Tiếp nhận đơn tại Bàn làm việc (Staff Workspace)", "Khi Bếp nhấn 'Đã nấu xong', đơn hàng lập tức trượt vào danh mục 'Chờ đóng gói & In hóa đơn' trên màn hình thu ngân."));
    bodyChildren.push(pBullet("Thao tác In hóa đơn 1 chạm (One-Touch Bill Printing)", "Thu ngân bấm nút 'In hóa đơn' (hoặc bấm phím tắt nhanh F9). Hệ thống gửi lệnh xuất bill trực tiếp tới máy in nhiệt chuẩn K80 (80mm) in ra hóa đơn bán lẻ chuyên nghiệp gồm: Header nhà hàng FOODDAILY; Mã đơn tra cứu; Ngày giờ in; Thông tin người nhận; Bảng chi tiết từng món kèm ghi chú riêng; Tổng kết thanh toán; Dòng trạng thái in đậm to rõ: 'ĐÃ THANH TOÁN (VIETQR ONLINE) - KHÔNG THU TIỀN' (nếu trả VietQR) hoặc 'THU TIỀN MẶT KHI GIAO (COD): [Số tiền] VNĐ' (nếu đơn COD); Mã vạch Barcode/QR tra cứu đơn."));
    bodyChildren.push(pBullet("Đóng gói và Bàn giao Shipper", "Nhân viên quầy kiểm tra từng hộp món ăn đối chiếu với bill, kẹp hóa đơn vào túi đồ ăn và bấm nút 'Bàn giao Shipper'. Hệ thống chuyển orders.order_status = 'delivering', tự động kích hoạt thông báo Realtime cho khách hàng."));

    // Bước 6
    bodyChildren.push(pHeading4("Bước 6: Giao hàng và Tra cứu tiến độ đơn hàng thời gian thực (Realtime Tracking)"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Nhân viên giao hàng (Shipper), Khách hàng (Customer). Mục đích: Vận chuyển đồ ăn an toàn đến tận tay khách hàng; đồng thời cung cấp công cụ theo dõi tiến độ đơn hàng thời gian thực trên website, minh bạch hóa từng giai đoạn chế biến và vận chuyển."));
    bodyChildren.push(pBullet("Vận chuyển món ăn", "Shipper nhận túi đồ ăn kèm hóa đơn, di chuyển theo định vị tới địa chỉ khách hàng. Shipper liên hệ khách trước khi đến nơi."));
    bodyChildren.push(pBullet("Tra cứu tiến độ đơn hàng Realtime (Order Tracking)", "Khách hàng mở trang 'Tra cứu đơn hàng' trên website FOODDAILY, nhập mã đơn hàng (order_code). Màn hình hiển thị Thanh tiến trình 5 giai đoạn (Step Progress Tracker): 1. Đã đặt hàng -> 2. Đã xác nhận & Thanh toán -> 3. Bếp đang nấu -> 4. Đang giao hàng -> 5. Đã giao thành công. Khi quầy thu ngân bấm 'Bàn giao Shipper' ở Bước 5, thanh tiến trình trên điện thoại của khách lập tức chuyển động sáng đèn ở bước 'Đang giao hàng' kèm lời nhắc chu đáo mà không cần nhấn F5 tải lại trang."));

    // Bước 7
    bodyChildren.push(pHeading4("Bước 7: Khách nhận hàng, Tích điểm thành viên và Gửi đánh giá"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân tham gia", "Tác nhân: Khách hàng, Shipper, Hệ thống tích điểm Loyalty và Quản lý Đánh giá Review. Mục đích: Kết thúc chu trình giao hàng thành công, kích hoạt chính sách tri ân khách hàng thân thiết thông qua điểm thưởng tích lũy (Loyalty Points), và thu thập phản hồi khách quan từ khách hàng để cải tiến chất lượng món ăn và dịch vụ."));
    bodyChildren.push(pBullet("Hoàn tất giao hàng", "Khách nhận túi đồ ăn, kiểm tra tem niêm phong và hóa đơn. Khách trả tiền mặt (nếu COD) hoặc nhận hàng ngay (nếu đã thanh toán VietQR). Đơn hàng chuyển sang trạng thái hoàn tất: orders.order_status = 'completed', lưu thời gian giao delivered_at."));
    bodyChildren.push(pBullet("Cơ chế Tích điểm thưởng Thành viên (Loyalty Points)", "Nếu khách hàng có tài khoản thành viên, hệ thống tự động tính điểm theo công thức: Điểm tích lũy = floor(Tổng tiền thanh toán / 10.000). Ví dụ: Đơn hàng 250.000 VNĐ tích ngay 25 điểm thưởng. Điểm được cộng dồn vào users.loyalty_points. Khách hàng dùng điểm để thăng hạng tài khoản (Đồng, Bạc, Vàng, Kim Cương) và đổi lấy các Voucher ưu đãi độc quyền trong Ví Voucher cho các lần đặt tiếp theo."));
    bodyChildren.push(pBullet("Đánh giá chất lượng món ăn (Rating & Review)", "Sau khi hoàn thành đơn, website hiển thị hộp thoại mời đánh giá bữa ăn: Khách hàng chấm điểm số sao từ 1 đến 5 sao cho từng món ăn đã thưởng thức, nhập nhận xét chi tiết về hương vị, độ nóng, cách bày trí và có thể đính kèm ảnh chụp thực tế. Đánh giá được lưu vào bảng reviews (dish_id, user_id, order_id, rating, comment). Điểm sao trung bình của món ăn được tự động cập nhật lại và hiển thị công khai trên thực đơn."));

    // 2.1.2 THÔNG TIN YÊU CẦU CHI TIẾT
    bodyChildren.push(pHeading3("2.1.2\tThông tin yêu cầu"));
    bodyChildren.push(pBody("Để hệ thống FOODDAILY vận hành trơn tru, chính xác và đồng bộ giữa các phân hệ (Khách hàng, Nhà bếp, Thu ngân, Shipper và Quản trị viên), hệ thống thu thập, xử lý và quản lý chặt chẽ 4 luồng thực thể thông tin nghiệp vụ cốt lõi sau:"));

    // 1. Thông tin khách hàng
    bodyChildren.push(pHeading4("1. Thông tin khách hàng (Customer Information)"));
    bodyChildren.push(pBullet("Mục đích quản lý", "Quản lý danh tính số của người dùng, xác thực đăng nhập bảo mật, phân quyền theo vai trò (RBAC), theo dõi lịch sử đặt món, lưu trữ sổ địa chỉ nhận hàng cá nhân hóa, tích lũy điểm thưởng thành viên và chăm sóc khách hàng tự động."));
    bodyChildren.push(pBullet("Mã khách hàng (ID)", "Số nguyên tự tăng (BIGINT UNSIGNED), khóa chính (PRIMARY KEY), định danh duy nhất của mỗi khách hàng trên toàn hệ thống."));
    bodyChildren.push(pBullet("Họ và tên (fullname)", "Chuỗi ký tự Unicode (VARCHAR(150), NOT NULL), họ tên đầy đủ của khách hàng để hiển thị lời chào trên giao diện cá nhân và in trên hóa đơn thanh toán."));
    bodyChildren.push(pBullet("Email đăng nhập", "Chuỗi ký tự (VARCHAR(191), UNIQUE, NOT NULL), tài khoản đăng nhập chính của khách hàng, nhận thông báo hóa đơn điện tử, thông báo đơn hàng và liên kết đặt lại mật khẩu an toàn."));
    bodyChildren.push(pBullet("Mật khẩu mã hóa (password)", "Chuỗi ký tự băm an toàn (VARCHAR(255), NOT NULL), sử dụng thuật toán băm một chiều tiên tiến Bcrypt (chuẩn công nghiệp cost=12, độ dài 60 ký tự), tuyệt đối không lưu mật khẩu dạng rõ (plaintext), đảm bảo an toàn ngay cả khi cơ sở dữ liệu bị truy cập trái phép."));
    bodyChildren.push(pBullet("Số điện thoại (phone)", "Chuỗi ký tự số (VARCHAR(20), NULLABLE), số điện thoại liên lạc chính để nhân viên giao hàng (Shipper) liên hệ giao đồ ăn và nhận mã xác minh OTP khi cần thiết."));
    bodyChildren.push(pBullet("Địa chỉ nhận hàng (address)", "Văn bản chi tiết (TEXT hoặc VARCHAR(255), NULLABLE), lưu địa chỉ giao hàng mặc định gồm: Số nhà, ngõ/ngách, tên đường, phường/xã, quận/huyện. Hệ thống hỗ trợ lưu thêm company_address (địa chỉ công ty) và other_address (địa chỉ khác) kèm cờ default_address_type để khách linh hoạt chuyển đổi khi đặt món giao tận nơi."));
    bodyChildren.push(pBullet("Điểm tích lũy (points / loyalty_points)", "Số nguyên không âm (INT UNSIGNED, DEFAULT 0), điểm thưởng tích lũy được cộng tự động sau mỗi đơn hàng giao thành công (10.000 VNĐ = 1 điểm). Điểm này dùng để xếp hạng thành viên (Bronze, Silver, Gold, Diamond) và quy đổi sang các voucher giảm giá trong Ví Voucher."));
    bodyChildren.push(pBullet("Thuộc tính bổ trợ & Quy tắc bảo mật", "Ảnh đại diện (avatar), Giới tính (gender), Ngày sinh nhật (birthday - tặng voucher sinh nhật tự động), Trạng thái tài khoản (status: Active/Blocked), Xác thực 2 lớp (two_factor_enabled). Ràng buộc: Email duy nhất toàn hệ thống, mật khẩu tối thiểu 8 ký tự, có cơ chế Rate Limiting chống tấn công vét cạn."));

    // 2. Thông tin món ăn
    bodyChildren.push(pHeading4("2. Thông tin món ăn & Thực đơn (Dishes & Categories Information)"));
    bodyChildren.push(pBullet("Mục đích quản lý", "Số hóa toàn bộ thực đơn ẩm thực của nhà hàng; phân nhóm khoa học theo danh mục; quản lý giá niêm yết, giá khuyến mãi, hình ảnh trực quan; kiểm soát lượng suất ăn phục vụ trong ngày nhằm tránh tình trạng khách đặt vượt quá năng lực phục vụ của nhà bếp."));
    bodyChildren.push(pBullet("Mã món ăn (ID món)", "Số nguyên tự tăng (BIGINT UNSIGNED), khóa chính (PRIMARY KEY), định danh duy nhất cho từng món ăn."));
    bodyChildren.push(pBullet("Mã danh mục (category_id)", "Khóa ngoại (BIGINT UNSIGNED), liên kết tới bảng categories (Món chính: cơm/bún/phở, Món ăn nhanh, Đồ uống thanh mát, Tráng miệng, Combo tiết kiệm)."));
    bodyChildren.push(pBullet("Tên món ăn (dish_name / name)", "Chuỗi ký tự Unicode (VARCHAR(255), NOT NULL), tên thương phẩm món ăn (ví dụ: 'Cơm Tấm Sườn Bì Chả Đặc Biệt', 'Trà Đào Cam Sả Tươi')."));
    bodyChildren.push(pBullet("Đường dẫn tĩnh (slug)", "Chuỗi ký tự không dấu (VARCHAR(255), UNIQUE), định danh URL thân thiện cho SEO (ví dụ: com-tam-suon-bi-cha-dac-biet), hỗ trợ chia sẻ mạng xã hội."));
    bodyChildren.push(pBullet("Đơn giá bán (price)", "Kiểu số thập phân chính xác cao (DECIMAL(10,2), NOT NULL), giá bán thực tế mà khách hàng thanh toán cho một suất ăn."));
    bodyChildren.push(pBullet("Giá gốc niêm yết (original_price)", "Kiểu số thập phân (DECIMAL(10,2), NULLABLE), dùng hiển thị giá gạch ngang để kích thích nhu cầu mua sắm khi có chương trình giảm giá."));
    bodyChildren.push(pBullet("Ảnh đại diện Cloudinary (image_url / image)", "Chuỗi URL (VARCHAR(500)), liên kết ảnh món ăn chất lượng cao lưu trữ trên nền tảng đám mây Cloudinary hoặc máy chủ, hỗ trợ định dạng nén tối ưu WebP tải siêu tốc."));
    bodyChildren.push(pBullet("Mô tả chi tiết (description)", "Đoạn văn bản (TEXT), miêu tả thành phần, hương vị đặc trưng, hàm lượng calo và khuyến cáo dị ứng thực phẩm."));
    bodyChildren.push(pBullet("Số suất bán trong ngày (stock)", "Số nguyên không âm (INT UNSIGNED, DEFAULT 0), số lượng suất ăn nhà bếp có thể chuẩn bị trong ngày; hệ thống tự động trừ lùi số lượng khi có đơn đặt thành công."));
    bodyChildren.push(pBullet("Trạng thái kinh doanh & Nổi bật", "Trạng thái kinh doanh (is_available / is_active: 1 - Đang phục vụ, 0 - Tạm ngưng); Món nổi bật (is_featured: 1 - Best Seller hiển thị trang chủ). Ràng buộc: Khi stock = 0 hoặc is_available = 0, hệ thống tự động gắn nhãn Tạm hết hàng và khóa nút đặt."));

    // 3. Thông tin đơn hàng
    bodyChildren.push(pHeading4("3. Thông tin đơn đặt hàng & Thanh toán (Orders & Payments Information)"));
    bodyChildren.push(pBullet("Mục đích quản lý", "Lưu trữ và quản lý xuyên suốt vòng đời của mọi giao dịch mua bán đồ ăn từ khâu khởi tạo, áp voucher, thanh toán trực tuyến VietQR/COD, chuyển bếp nấu, in hóa đơn đến khi shipper giao tận tay khách hàng; phục vụ công tác đối soát kế toán và giải quyết khiếu nại."));
    bodyChildren.push(pBullet("Mã đơn hàng (order_code)", "Chuỗi ký tự định danh duy nhất (VARCHAR(50), UNIQUE, ví dụ: FD-20261012-0089), dùng cho khách tra cứu trực tuyến và in trên hóa đơn bán lẻ. Đồng thời lưu mã số nguyên đối soát cho Cổng PayOS."));
    bodyChildren.push(pBullet("Tên người nhận (customer_name / delivery_name)", "Chuỗi ký tự (VARCHAR(150), NOT NULL), họ tên người trực tiếp nhận đồ ăn."));
    bodyChildren.push(pBullet("Số điện thoại nhận hàng (delivery_phone)", "Chuỗi ký tự số (VARCHAR(20), NOT NULL), số điện thoại để Shipper gọi khi giao hàng."));
    bodyChildren.push(pBullet("Địa chỉ giao hàng (delivery_address)", "Văn bản chi tiết (TEXT, NOT NULL), ghi rõ số nhà, ngõ, tên đường, phường/xã, quận/huyện để Shipper định vị tuyến đường di chuyển."));
    bodyChildren.push(pBullet("Tổng tiền món ăn (total_amount)", "Kiểu số thập phân (DECIMAL(12,2)), tổng giá trị của tất cả các món ăn trong đơn trước khi giảm trừ voucher."));
    bodyChildren.push(pBullet("Tổng tiền thanh toán cuối cùng (final_amount)", "Kiểu số thập phân (DECIMAL(12,2)), số tiền thực tế khách hàng phải chi trả sau khi cộng phí vận chuyển và trừ tiền giảm giá voucher: final_amount = total_amount + shipping_fee - discount_amount."));
    bodyChildren.push(pBullet("Phương thức & Trạng thái thanh toán", "Phương thức thanh toán (payment_method: cod - Tiền mặt khi giao hàng, payos - Chuyển khoản VietQR tự động qua Cổng PayOS, vnpay, momo). Trạng thái thanh toán (payment_status: pending - Chờ thanh toán, paid - Đã thanh toán thành công, failed - Thất bại, refunded - Đã hoàn tiền)."));
    bodyChildren.push(pBullet("Tiến độ xử lý đơn hàng (order_status)", "ENUM 7 trạng thái chuẩn F&B: pending -> confirmed -> preparing -> cooked -> delivering -> completed -> cancelled."));
    bodyChildren.push(pBullet("Mã giao dịch ngân hàng PayOS (payment_transaction_id)", "Chuỗi ký tự (VARCHAR(100), NULLABLE), mã tham chiếu giao dịch ngân hàng do PayOS trả về khi khớp lệnh Webhook thành công. Kèm thông tin payment_bank, payment_paid_at, delivered_at."));
    bodyChildren.push(pBullet("Chi tiết món ăn trong đơn (order_items)", "Bảng liên kết gồm dish_id, quantity (số lượng), price (đơn giá tại thời điểm mua), item_notes (ghi chú riêng cho từng món: không hành, ít đá, thêm ớt)."));

    // 4. Thông tin nhà cung cấp & Kho
    bodyChildren.push(pHeading4("4. Thông tin nhà cung cấp, Kho nguyên liệu & Phiếu nhập kho (Suppliers, Inventory & Goods Receipts)"));
    bodyChildren.push(pBullet("Mục đích quản lý", "Quản trị chuỗi cung ứng thực phẩm đầu vào của nhà hàng; kiểm soát tồn kho định lượng nguyên vật liệu (thịt, rau, gia vị, gạo, bao bì); tự động phát hiện và cảnh báo các mặt hàng chạm ngưỡng tồn kho tối thiểu nhằm tránh tình trạng thiếu hụt nguyên liệu phục vụ; quản lý lịch sử nhập kho và giá vốn hàng bán (COGS)."));
    bodyChildren.push(pBullet("Thông tin Nhà cung cấp (Bảng suppliers)", "Tên nhà cung cấp (name: VARCHAR(255)), Người đại diện liên hệ (contact_name: VARCHAR(150)), Số điện thoại (phone: VARCHAR(20)), Địa chỉ cơ sở (address: TEXT), Mã số thuế (tax_code: VARCHAR(50)), Email (email: VARCHAR(191))."));
    bodyChildren.push(pBullet("Thông tin Kho nguyên liệu tồn kho (Bảng inventory_items)", "Mã nguyên liệu (item_code: VARCHAR(50), UNIQUE, VD: NL-THITHEO-01), Tên nguyên liệu (name: VARCHAR(255)), Phân loại nhóm (category: Thực phẩm tươi sống, Rau củ quả, Gia vị, Đồ uống, Bao bì), Đơn vị tính (unit: kg, gam, lít, chai, thùng, hộp, cái), Số lượng tồn kho (current_stock: DECIMAL(10,2)), Ngưỡng báo nhập tối thiểu (min_stock: DECIMAL(10,2)), Đơn giá vốn nhập (cost_price: DECIMAL(12,2)), Nhà cung cấp liên kết (supplier_id)."));
    bodyChildren.push(pBullet("Cảnh báo tồn kho & Trạng thái", "Khi current_stock <= min_stock, hệ thống lập tức hiển thị cảnh báo đỏ trên Dashboard quản trị viên. Trạng thái tồn kho: in_stock (Còn hàng), low_stock (Sắp hết hàng chạm ngưỡng), out_of_stock (Đã hết hàng)."));
    bodyChildren.push(pBullet("Phiếu nhập kho nguyên liệu (Bảng goods_receipts & goods_receipt_items)", "Mã phiếu nhập (receipt_code: VARCHAR(50)), Ngày giờ nhập (receipt_date: DATETIME), Tổng tiền nhập kho (total_amount: DECIMAL(14,2)), Trạng thái phiếu (status: draft, completed, cancelled). Chi tiết mặt hàng: Mã nguyên liệu, số lượng nhập, đơn giá nhập, thành tiền, hạn sử dụng lô hàng (expiry_date). Khi duyệt completed, hệ thống tự động cộng dồn tồn kho current_stock và tính lại giá vốn cost_price."));

    // 5. Thông tin phòng đặt nhóm, Gói ăn định kỳ & Voucher ví
    bodyChildren.push(pHeading4("5. Thông tin phòng đặt nhóm, Gói ăn định kỳ & Voucher ví"));
    bodyChildren.push(pBullet("Phòng đặt món nhóm Realtime (group_orders, group_order_items)", "Mã phòng room_code (mã PIN 6 ký tự), người tạo phòng host_user_id, trạng thái phòng status (open, locked), danh sách thành viên member_name, chi tiết món ăn từng thành viên chọn kèm đơn giá và ghi chú riêng."));
    bodyChildren.push(pBullet("Ví Voucher khuyến mãi (coupons, user_coupons)", "Mã voucher code, loại giảm giá discount_type (% hoặc số tiền), giá trị giảm discount_value, giá trị đơn tối thiểu min_order_value, hạn dùng start_date/end_date và trạng thái đã sử dụng is_used."));
    bodyChildren.push(pBullet("Gói ăn định kỳ Meal Subscription (service_packages, subscriptions)", "Tên gói ăn, số ngày phục vụ (1 tuần, 1 tháng), số bữa/ngày, ngày bắt đầu/kết thúc, thời gian giao hàng mong muốn hằng ngày và danh sách thực đơn đổi món hằng ngày của khách."));

    bodyChildren.push(pHeading3("2.1.3\tSơ đồ chức năng của hệ thống"));
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
                    transformation: { width: 580, height: 207 }
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
                    transformation: { width: 560, height: 244 }
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
                    transformation: { width: 560, height: 244 }
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
                    transformation: { width: 560, height: 244 }
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
    }));

    bodyChildren.push(pHeading2("2.2\tCác biểu đồ thiết kế hệ thống"));
    bodyChildren.push(pHeading3("2.2.1\tLượt đồ Use case"));
    bodyChildren.push(pBody("Lược đồ 2.1 Lược đồ use case tổng quát toàn hệ thống FOODDAILY gồm 4 tác nhân: Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên."));

    // 11 Bảng đặc tả Use case
    bodyChildren.push(pHeading4("2.2.2.1\tĐặc tả use case đăng nhập vào hệ thống."));
    bodyChildren.push(pBody("Mục đích: Đảm bảo xác thực thông tin người sử dụng và an toàn hệ thống.\nTác nhân: Khách hàng, nhân viên, đầu bếp, Admin.\nMô tả chung: Khi tác nhân muốn thao tác với hệ thống cần phải đăng nhập."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn chức năng đăng nhập.", "-"],
            ["-", "2. Hiển thị Form đăng nhập."],
            ["3. Nhập Email và Mật khẩu.", "-"],
            ["4. Nhấn nút Đăng nhập gửi thông tin.", "-"],
            ["-", "5. Kiểm tra thông tin đăng nhập, nếu đúng thì cho phép truy cập và chuyển hướng theo vai trò; nếu sai thì hiển thị thông báo lỗi."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.2\tĐặc tả use case đăng xuất vào hệ thống."));
    bodyChildren.push(pBody("Mục đích: Người dùng đăng xuất ra khỏi hệ thống để bảo mật.\nTác nhân: Khách hàng, nhân viên, đầu bếp, Admin."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn chức năng đăng xuất.", "-"],
            ["-", "2. Hệ thống hủy Session, xóa cookie xác thực, vô hiệu hóa BFCache và chuyển về trang chủ."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.3\tĐặc tả use case tìm kiếm và xem chi tiết món ăn."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Nhập từ khóa hoặc chọn lọc theo danh mục/giá.", "-"],
            ["-", "2. Hệ thống truy vấn CSDL và trả về danh sách món ăn thỏa điều kiện."],
            ["3. Nhấp chọn món ăn cụ thể.", "-"],
            ["-", "4. Hiển thị thông tin chi tiết món ăn, hình ảnh và đánh giá."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.4\tĐặc tả use case đặt món theo nhóm Realtime."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chủ phòng nhấn 'Đặt món theo nhóm'.", "-"],
            ["-", "2. Hệ thống sinh mã Room Code và QR Code chia sẻ."],
            ["3. Thành viên tham gia phòng và bấm chọn món.", "-"],
            ["-", "4. Broadcast WebSocket đồng bộ giỏ hàng nhóm cho tất cả mọi người."],
            ["5. Chủ phòng nhấn 'Chốt đơn nhóm'.", "-"],
            ["-", "6. Khóa phòng và chuyển sang màn hình thanh toán."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.5\tĐặc tả use case thanh toán tự động VietQR PayOS."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn phương thức VietQR và xác nhận đặt hàng.", "-"],
            ["-", "2. Gọi API PayOS sinh mã QR VietQR động hiển thị trên màn hình."],
            ["3. Khách quét mã chuyển tiền qua App ngân hàng.", "-"],
            ["-", "4. Nhận Webhook từ PayOS, kiểm tra chữ ký HMAC-SHA256, đổi trạng thái sang 'paid' và bắn chuông sang Bếp FIFO."],
            ["-", "5. Màn hình khách hàng tự động chuyển sang thông báo thanh toán thành công."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.6\tĐặc tả use case Trợ lý AI Gemini tư vấn món ăn."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng mở hộp thoại và gửi câu hỏi tư vấn.", "-"],
            ["-", "2. Nạp danh sách món ăn đang bán vào Context Prompt gửi đến Google Gemini 1.5 Flash."],
            ["-", "3. Gemini trả về câu trả lời tư vấn kèm danh sách ID món phù hợp."],
            ["-", "4. Hiển thị tin nhắn tư vấn kèm các thẻ món ăn có nút mua nhanh."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.7\tĐặc tả use case điều phối chế biến tại Màn hình Bếp FIFO."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Đầu bếp truy cập màn hình Bếp.", "-"],
            ["-", "2. Hiển thị hàng đợi chế biến theo thứ tự FIFO và bảng gom tổng số lượng từng món."],
            ["3. Nhấn 'Bắt đầu nấu' trên đơn hàng.", "-"],
            ["-", "4. Cập nhật trạng thái đơn sang 'preparing'."],
            ["5. Nấu xong nhấn 'Đã nấu xong'.", "-"],
            ["-", "6. Chuyển đơn sang 'cooked' và đẩy sang quầy thu ngân đóng gói."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.8\tĐặc tả use case đăng ký và quản lý gói ăn định kỳ."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng chọn gói combo (7 ngày / 30 ngày) và thanh toán.", "-"],
            ["-", "2. Sinh lịch giao suất ăn hằng ngày trong bảng Daily_Schedules."],
            ["3. Khách hàng xem lịch và chọn đổi món trước giờ cắt đơn.", "-"],
            ["-", "4. Cập nhật món ăn mới cho ngày tương ứng."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.9\tĐặc tả use case quản lý thực đơn món ăn."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin chọn Thêm mới hoặc Sửa món ăn.", "-"],
            ["2. Nhập tên, danh mục, giá, tải ảnh lên và nhấn Lưu.", "-"],
            ["-", "3. Tải ảnh lên Cloudinary, lưu dữ liệu vào bảng Dishes và thông báo thành công."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.10\tĐặc tả use case quản lý kho nguyên liệu và nhà cung cấp."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn mục Nhập kho, chọn NCC và danh sách nguyên liệu.", "-"],
            ["2. Nhấn 'Hoàn tất phiếu nhập'.", "-"],
            ["-", "3. Lưu phiếu nhập Goods_Receipts và tự động cộng dồn số lượng tồn kho Inventory_Items."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.11\tĐặc tả use case quản lý khuyến mãi và cấp voucher ví."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin nhập thông tin mã voucher (% giảm, mức giảm tối đa, hạn dùng).", "-"],
            ["2. Nhấn 'Phát hành và cấp vào ví hội viên'.", "-"],
            ["-", "3. Lưu bảng Coupons, tự động insert User_Coupons cho toàn bộ thành viên và gửi email thông báo."]
        ],
        [4700, 4700]
    ));

    // LƯỢC ĐỒ ACTIVITY
    bodyChildren.push(pHeading3("2.2.2\tLược đồ Activity"));
    bodyChildren.push(pBullet("2.2.2.1 Lược đồ chức năng đăng nhập", "Nhập thông tin -> So khớp CSDL -> Sai thì báo lỗi / Đúng thì tạo Session và điều hướng."));
    bodyChildren.push(pBullet("2.2.2.3 Lược đồ chức năng reset password", "Nhập Email -> Nhận mã OTP qua email -> Nhập OTP xác thực -> Đặt mật khẩu mới."));
    bodyChildren.push(pBullet("2.2.2.4 Lược đồ tìm kiếm", "Nhập từ khóa / Lọc danh mục / Mức giá -> Truy vấn CSDL -> Hiển thị danh sách món."));
    bodyChildren.push(pBullet("2.2.2.5 Lược đồ đặt món theo nhóm Realtime", "Tạo phòng -> Sinh Room Code & QR -> Thành viên chọn món -> Broadcast WebSocket đồng bộ -> Chốt đơn."));
    bodyChildren.push(pBullet("2.2.2.6 Lược đồ thanh toán PayOS VietQR", "Chọn VietQR -> Sinh mã QR động -> Quét mã ngân hàng -> Webhook kiểm tra HMAC -> Đổi 'paid' và bắn vào Bếp."));
    bodyChildren.push(pBullet("2.2.2.7 Lược đồ điều phối Bếp FIFO", "Xem hàng đợi đơn FIFO và bảng gộp số lượng -> Bắt đầu nấu (preparing) -> Nấu xong (cooked) -> Bàn giao thu ngân."));
    bodyChildren.push(pBullet("2.2.2.8 Lược đồ quản lý hóa đơn và in bill", "Thu ngân chọn đơn nấu xong -> Bấm In bill 1 chạm -> In hóa đơn nhiệt 80mm -> Giao tài xế."));
    bodyChildren.push(pBullet("2.2.2.9 Lược đồ quản lý khách hàng", "Xem danh sách thành viên -> Khóa/Mở khóa hoặc xem lịch sử chi tiêu."));
    bodyChildren.push(pBullet("2.2.2.10 Lược đồ quản lý món ăn", "Nhập thông tin món -> Tải ảnh Cloudinary -> Lưu bảng Dishes."));
    bodyChildren.push(pBullet("2.2.2.11 Lược đồ quản lý nhà cung cấp", "Nhập thông tin đối tác -> Lưu bảng Suppliers."));
    bodyChildren.push(pBullet("2.2.2.12 Lược đồ quản lý kho nguyên liệu", "Lập phiếu nhập kho -> Lưu Goods_Receipts -> Tự động cộng tồn kho Inventory_Items."));
    bodyChildren.push(pBullet("2.2.2.13 Lược đồ quản lý nhân viên và phân quyền", "Chọn nhân viên -> Gán vai trò (Staff, Kitchen, Admin) -> Đồng bộ quyền Spatie RBAC."));

    // BIỂU ĐỒ THÀNH PHẦN & TRIỂN KHAI
    bodyChildren.push(pHeading3("2.2.3\tBiểu đồ thành phần"));
    bodyChildren.push(pBullet("2.2.4.1 Biểu đồ thành phần hệ thống", "Tầng giao diện Client, tầng backend Laravel 13 và tầng CSDL MySQL 8.0 kết nối các API bên ngoài."));
    bodyChildren.push(pBullet("2.2.4.2 Biểu đồ thành phần quản trị viên", "Các Controller quản trị kết nối với Service Layer: Dashboard, Món ăn, Đơn hàng, Kho và Phân quyền."));
    bodyChildren.push(pBullet("2.2.4.4 Biểu đồ thành phần người dùng", "Các Controller phía khách hàng: ShopController, CartController, GroupOrderController, SubscriptionController."));

    bodyChildren.push(pHeading3("2.2.4\tBiểu đồ triển khai"));
    bodyChildren.push(pBody("Hệ thống triển khai trên máy chủ Laragon / Nginx Web Server kết hợp PHP 8.3 FPM, MySQL 8.0, Laravel Reverb WebSocket Server (Port 8080) và giao tiếp an toàn qua HTTPS với Google Gemini, PayOS VietQR, Cloudinary."));

    // 2.3 CƠ SỞ DỮ LIỆU
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading2("2.3\tCơ sở dữ liệu"));
    bodyChildren.push(pBody("Tóm tắt ý nghĩa các bảng dữ liệu trong hệ thống FOODDAILY:"));

    bodyChildren.push(pHeading3("Table Users"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Gía trị tối đa", "Mô tả"],
        [
            ["id", "bigint", "Primary key, Auto Increment, Not Null", "20", "Mã tài khoản"],
            ["name", "nvarchar", "Not Null", "255", "Họ và tên người dùng"],
            ["email", "nvarchar", "Unique, Not Null", "255", "Email đăng nhập"],
            ["password", "nvarchar", "Not Null", "255", "Mật khẩu mã hóa Bcrypt"],
            ["role", "nvarchar", "Not Null", "50", "Vai trò (customer, staff, kitchen, admin)"],
            ["phone", "nvarchar", "Null", "20", "Số điện thoại liên hệ"],
            ["address", "nvarchar", "Null", "500", "Địa chỉ giao hàng mặc định"],
            ["loyalty_points", "int", "Not Null, Default 0", "11", "Điểm thưởng tích lũy"],
            ["is_active", "tinyint", "Not Null, Default 1", "1", "Trạng thái hoạt động"]
        ],
        [1800, 1800, 2200, 1000, 2600]
    ));

    bodyChildren.push(pHeading3("Table Categories"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Gía trị tối đa", "Mô tả"],
        [
            ["id", "bigint", "Primary key, Auto Increment, Not Null", "20", "Mã danh mục"],
            ["name", "nvarchar", "Not Null", "255", "Tên danh mục món"],
            ["slug", "nvarchar", "Unique, Not Null", "255", "Đường dẫn URL"],
            ["image", "nvarchar", "Null", "255", "Ảnh danh mục"],
            ["is_active", "tinyint", "Not Null, Default 1", "1", "Trạng thái hiển thị"]
        ],
        [1800, 1800, 2200, 1000, 2600]
    ));

    bodyChildren.push(pHeading3("Table Dishes"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Gía trị tối đa", "Mô tả"],
        [
            ["id", "bigint", "Primary key, Auto Increment, Not Null", "20", "Mã món ăn"],
            ["category_id", "bigint", "Foreign key (categories.id), Not Null", "20", "Mã danh mục trực thuộc"],
            ["name", "nvarchar", "Not Null", "255", "Tên món ăn"],
            ["slug", "nvarchar", "Unique, Not Null", "255", "Đường dẫn URL"],
            ["price", "decimal", "Not Null", "12,2", "Giá bán hiện tại"],
            ["original_price", "decimal", "Null", "12,2", "Giá gốc niêm yết"],
            ["image", "nvarchar", "Null", "255", "Link ảnh Cloudinary"],
            ["stock", "int", "Not Null, Default 100", "11", "Số suất phục vụ trong ngày"],
            ["is_active", "tinyint", "Not Null, Default 1", "1", "Trạng thái kinh doanh"]
        ],
        [1800, 1800, 2200, 1000, 2600]
    ));

    bodyChildren.push(pHeading3("Table Orders"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Gía trị tối đa", "Mô tả"],
        [
            ["id", "bigint", "Primary key, Auto Increment, Not Null", "20", "Mã đơn hàng"],
            ["order_code", "nvarchar", "Unique, Not Null", "50", "Mã hiển thị tra cứu đơn"],
            ["customer_name", "nvarchar", "Not Null", "255", "Tên người nhận hàng"],
            ["customer_phone", "nvarchar", "Not Null", "20", "Số điện thoại nhận hàng"],
            ["shipping_address", "nvarchar", "Not Null", "500", "Địa chỉ giao hàng"],
            ["total_amount", "decimal", "Not Null", "12,2", "Tổng số tiền thanh toán"],
            ["payment_method", "nvarchar", "Not Null", "50", "Phương thức (payos, momo, cod)"],
            ["payment_status", "nvarchar", "Not Null, Default 'pending'", "50", "Trạng thái trả tiền (pending, paid)"],
            ["order_status", "nvarchar", "Not Null, Default 'pending'", "50", "Tiến độ (pending, preparing, cooked, completed)"],
            ["transaction_code", "nvarchar", "Null", "100", "Mã giao dịch PayOS"]
        ],
        [1800, 1800, 2200, 1000, 2600]
    ));

    bodyChildren.push(pHeading3("Table Suppliers & Inventory_Items"));
    bodyChildren.push(makeTable(
        ["Bảng", "Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Mô tả"],
        [
            ["Suppliers", "id", "bigint", "Primary key, Auto Increment", "Mã nhà cung cấp"],
            ["Suppliers", "name", "nvarchar", "Not Null", "Tên nhà cung cấp thực phẩm"],
            ["Suppliers", "phone", "nvarchar", "Not Null", "Số điện thoại đối tác"],
            ["Inventory_Items", "id", "bigint", "Primary key, Auto Increment", "Mã nguyên vật liệu"],
            ["Inventory_Items", "name", "nvarchar", "Not Null", "Tên nguyên liệu thô"],
            ["Inventory_Items", "unit", "nvarchar", "Not Null", "Đơn vị tính (Kg, Lít)"],
            ["Inventory_Items", "current_stock", "decimal", "Not Null", "Số lượng tồn kho"],
            ["Inventory_Items", "min_threshold", "decimal", "Not Null", "Ngưỡng cảnh báo nhập"]
        ],
        [1800, 1800, 1800, 1800, 2200]
    ));

    // CHƯƠNG 3
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ"));
    bodyChildren.push(pHeading2("3.1\tMột số giao diện website"));
    bodyChildren.push(pHeading3("3.1.1 Phần quản trị"));
    bodyChildren.push(pBullet("3.1.3.1 Giao diện đăng nhập", "Form đăng nhập quản trị an toàn, hỗ trợ ghi nhớ phiên và khôi phục mật khẩu OTP."));
    bodyChildren.push(pBullet("3.1.3.2 Giao diện Dashboard thống kê doanh thu", "Biểu đồ trực quan doanh thu theo ngày/tháng, số đơn hàng mới, khách hàng mới và top món bán chạy."));
    bodyChildren.push(pBullet("3.1.3.3 Giao diện quản lý thực đơn món ăn", "Bảng danh sách món ăn, hỗ trợ lọc theo danh mục, tìm kiếm nhanh và bật tắt trạng thái kinh doanh 1 chạm."));
    bodyChildren.push(pBullet("3.1.3.4 Giao diện quản lý đơn đặt hàng", "Theo dõi đơn hàng theo các trạng thái (Chờ xác nhận, Đã thanh toán, Đang nấu, Đang giao, Hoàn tất)."));
    bodyChildren.push(pBullet("3.1.3.5 Giao diện quản lý khách hàng", "Danh sách khách hàng, điểm tích lũy, tổng chi tiêu và hỗ trợ khóa/mở khóa tài khoản."));
    bodyChildren.push(pBullet("3.1.3.6 Giao diện quản lý kho nguyên liệu", "Theo dõi tồn kho nguyên vật liệu và tự động cảnh báo màu đỏ các mặt hàng sắp cạn kiệt."));
    bodyChildren.push(pBullet("3.1.3.7 Giao diện quản lý nhà cung cấp", "Quản lý danh bạ đối tác cung cấp thực phẩm và lịch sử nhập hàng."));
    bodyChildren.push(pBullet("3.1.3.8 Giao diện Màn hình Bếp FIFO", "Hàng đợi chế biến sắp xếp vào trước làm trước, tự động gom số lượng từng món để nấu hàng loạt."));
    bodyChildren.push(pBullet("3.1.3.9 Giao diện Bàn làm việc thu ngân & In hóa đơn", "Bàn làm việc thu ngân tiếp nhận đơn nấu xong, in hóa đơn nhiệt 80mm chỉ bằng 1 cú nhấp chuột."));
    bodyChildren.push(pBullet("3.1.3.10 Giao diện Reset Password", "Màn hình gửi mã OTP và đặt lại mật khẩu mới an toàn."));

    bodyChildren.push(pHeading3("3.1.2 File excel xuất ra từ quản trị"));
    bodyChildren.push(pBody("Hệ thống hỗ trợ xuất file Excel/CSV phục vụ thống kê báo cáo doanh thu, đơn hàng, tồn kho nguyên vật liệu và xuất file sao lưu Customer Backup (.zip) an toàn."));

    bodyChildren.push(pHeading3("3.1.3 Phần người dùng"));
    bodyChildren.push(pBullet("3.1.3.1 Giao diện người dùng ở thiết bị màn hình lớn", "Giao diện Desktop hiện đại: Trang chủ ẩm thực, thực đơn trực quan, Vòng quay may mắn 'Hôm nay ăn gì', Phòng đặt món nhóm Realtime, Thanh toán tự động VietQR PayOS và Trợ lý AI Gemini."));
    bodyChildren.push(pBullet("3.1.3.2 Giao diện người dùng ở thiết bị màn hình nhỏ", "Giao diện Responsive chuẩn Mobile-First: Thanh điều hướng đáy (Bottom Nav), giỏ hàng nổi cố định và hỗ trợ cài đặt Progressive Web App (PWA)."));

    bodyChildren.push(pHeading2("3.2\tKết quả Kiểm thử tự động (Automation Testing với PHPUnit)"));
    bodyChildren.push(pBody("Hệ thống được kiểm thử tự động toàn diện qua 8 Test Suites với 41/41 Test Cases đạt tỷ lệ thành công 100%:"));
    bodyChildren.push(makeTable(
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
        [600, 2700, 1100, 3600, 1400]
    ));

    bodyChildren.push(pHeading2("3.3\tĐánh giá hiệu năng và an toàn bảo mật"));
    bodyChildren.push(pBullet("Hiệu năng tối ưu", "Loại bỏ hoàn toàn lỗi truy vấn N+1 nhờ Eager Loading; đánh chỉ mục Composite Index giúp tốc độ truy vấn đơn hàng dưới 5ms; CSS/JS nén chỉ 45 KB đạt Google Lighthouse trên 95 điểm."));
    bodyChildren.push(pBullet("Middleware PreventBackHistory chống BFCache", "Thiết lập tiêu đề 'no-cache, no-store, must-revalidate', ngăn chặn rò rỉ thông tin quản trị khi người dùng bấm nút Back sau khi đăng xuất."));
    bodyChildren.push(pBullet("Bảo mật Webhook HMAC-SHA256", "Xác thực chữ ký số bằng mã băm bí mật, ngăn chặn 100% các hành vi gửi thông báo chuyển khoản giả mạo."));

    // CHƯƠNG 4
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN"));
    bodyChildren.push(pHeading2("4.1\tKết luận"));
    bodyChildren.push(pHeading3("Kết luận về khóa luận"));
    bodyChildren.push(pBody("Qua quá trình tìm hiểu, phân tích nghiệp vụ và lập trình thực nghiệm, tôi đã hoàn thành việc xây dựng website hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm FOODDAILY với đầy đủ các chức năng đã đề ra trong mục tiêu ban đầu."));

    bodyChildren.push(pHeading3("Ưu điểm của đề tài"));
    bodyChildren.push(pBullet("Hoàn thiện hệ sinh thái F&B khép kín", "Từ khách hàng, màn hình điều phối bếp đến quản trị viên vận hành trơn tru."));
    bodyChildren.push(pBullet("Ứng dụng công nghệ hiện đại", "Đặt món nhóm Realtime qua WebSockets, Trợ lý AI Google Gemini 1.5 Flash, Thanh toán VietQR PayOS tự động và Bếp FIFO tối ưu công suất nấu."));
    bodyChildren.push(pBullet("Độ ổn định và bảo mật cao", "Kiến trúc Laravel 13 phân tầng sạch sẽ, bảo mật chống rò rỉ phiên BFCache và đạt 100% tỷ lệ kiểm thử tự động (41/41 Test Cases Passed)."));

    bodyChildren.push(pHeading3("Hạn chế của đề tài"));
    bodyChildren.push(pBullet("Quản lý kho chưa tự trừ theo định lượng món", "Hiện tại mới dừng ở mức nhập kho và cảnh báo tồn kho thấp, chưa tự trừ lùi nguyên liệu thô theo công thức (Recipe/BOM) khi bếp nấu xong."));
    bodyChildren.push(pBullet("Chưa có cổng ứng dụng cho tài xế", "Chưa có phân hệ Driver Portal để shipper nhận đơn và định vị GPS trực tiếp trên bản đồ."));

    bodyChildren.push(pHeading2("4.2\tĐịnh hướng phát triển"));
    bodyChildren.push(pHeading3("Phát triển đề tài"));
    bodyChildren.push(makeTable(
        ["STT", "Tính năng nâng cấp", "Mô tả chi tiết", "Lý do bắt buộc phải có"],
        [
            ["1", "Định lượng món & Tự trừ kho (Recipe/BOM)", "Cài đặt công thức (VD: 1 Phở = 150g bánh + 100g bò). Bếp nấu xong tự trừ nguyên liệu thô.", "Quản lý chính xác giá vốn (COGS), kiểm soát thất thoát nguyên liệu."],
            ["2", "Cổng thông tin Tài xế (Driver Portal)", "Giao diện riêng cho shipper: nhận thông báo món nấu xong, xem địa chỉ, chụp ảnh giao hàng.", "Hoàn thiện mắt xích vận hành: Khách -> Bếp -> Shipper -> Giao tận tay."],
            ["3", "Định vị tài xế Live GPS Tracking", "Truyền tọa độ GPS của shipper qua WebSocket lên bản đồ cho khách theo dõi kèm ETA.", "Tính năng tiêu chuẩn của app giao đồ ăn, giảm tình trạng khách sốt ruột hủy đơn."],
            ["4", "Chuông báo âm thanh & Hẹn giờ Bếp", "Chuông reo to khi có đơn mới; đồng hồ đếm ngược đổi màu cảnh báo khi nấu trễ giờ.", "Đầu bếp tay luôn bận nấu, không thể nhìn màn hình; giúp không bao giờ sót đơn."],
            ["5", "Thông báo đẩy Web Push / Zalo ZNS", "Gửi thông báo trực tiếp đến điện thoại khi đơn duyệt, đang nấu, đang giao và đã tới nơi.", "Khách đặt xong thường tắt tab web; thông báo đẩy giúp nhận đồ ăn kịp thời."],
            ["6", "Hạng hội viên & Tích điểm (Loyalty)", "Tích điểm % giá trị đơn hàng, thăng hạng (Đồng, Bạc, Vàng, Kim Cương) để nhận mã giảm giá độc quyền.", "F&B phụ thuộc trên 60% vào khách quen; tích điểm là công cụ giữ chân khách bắt buộc."]
        ],
        [600, 2400, 3400, 3000]
    ));

    bodyChildren.push(pHeading3("Phát triển bản thân"));
    bodyChildren.push(pBullet("Cần nâng cao khả năng tự học các kiến thức mới", "Chủ động tiếp cận các công nghệ hiện đại trong ngành phần mềm."));
    bodyChildren.push(pBullet("Cần nâng cao thêm khả năng suy luận, tư duy logic", "Tối ưu hóa thuật toán và thiết kế kiến trúc hệ thống quy mô lớn."));
    bodyChildren.push(pBullet("Cải thiện hơn nữa về kiến thức chuyên môn", "Nghiên cứu sâu về kiến trúc phân tán (Microservices), cân bằng tải và an toàn thông tin."));

    // PHỤ LỤC & TÀI LIỆU THAM KHẢO
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("PHỤ LỤC"));
    bodyChildren.push(pHeading2("Mã nguồn xử lý Webhook PayOS xác thực chữ ký HMAC-SHA256:"));
    const codeWebhook = `public function handleWebhook(Request $request) {
    $payload = json_decode($request->getContent(), true);
    if (!isset($payload['data']) || !isset($payload['signature'])) {
        return response()->json(['error' => 'Invalid webhook payload'], 400);
    }
    $data = $payload['data'];
    ksort($data);
    $signData = [];
    foreach ($data as $key => $value) {
        $signData[] = "{$key}={$value}";
    }
    $computedSignature = hash_hmac('sha256', implode('&', $signData), config('services.payos.checksum_key'));
    if (!hash_equals($computedSignature, $payload['signature'])) {
        return response()->json(['error' => 'Invalid signature'], 400);
    }
    $order = Order::where('transaction_code', $data['orderCode'])->first();
    if ($order && $order->payment_status !== 'paid') {
        $order->update(['payment_status' => 'paid', 'order_status' => 'confirmed']);
        broadcast(new DishUpdated('kitchen_new_order', ['order_id' => $order->id]));
    }
    return response()->json(['success' => true], 200);
}`;
    bodyChildren.push(...pCode(codeWebhook));

    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("DANH MỤC TÀI LIỆU THAM KHẢO"));
    const refs = [
        "[ 1 ] https://laravel.com/docs/13.x — Tài liệu chính thức Framework Laravel 13.",
        "[ 2 ] https://www.php.net/manual/en/migration83.php — Tài liệu ngôn ngữ lập trình PHP 8.3.",
        "[ 3 ] https://payos.vn/docs/ — Tài liệu tích hợp cổng thanh toán tự động VietQR PayOS.",
        "[ 4 ] https://ai.google.dev/docs — Tài liệu hướng dẫn tích hợp Google Gemini AI API.",
        "[ 5 ] https://spatie.be/docs/laravel-permission/ — Tài liệu phân quyền vai trò Spatie Laravel Permission.",
        "[ 6 ] https://tailwindcss.com/docs — Tài liệu thiết kế giao diện Tailwind CSS v4.",
        "[ 7 ] https://dev.mysql.com/doc/refman/8.0/en/ — Tài liệu hệ quản trị cơ sở dữ liệu MySQL 8.0.",
        "[ 8 ] https://developer.mozilla.org/en-US/docs/Glossary/bfcache — Tài liệu MDN về cơ chế Back/Forward Cache.",
        "[ 9 ] https://stackoverflow.com/ — Diễn đàn trao đổi kỹ thuật lập trình và giải quyết lỗi phần mềm.",
        "[ 10 ] https://w3schools.com/ — Tài liệu hướng dẫn HTML5, CSS3 và Javascript tiêu chuẩn."
    ];
    refs.forEach(r => bodyChildren.push(pBody(r)));

    // TẠO DOCUMENT HOÀN CHỈNH
    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: FONT_NAME, size: 25, color: "000000" },
                    paragraph: { spacing: { line: 360, before: 60, after: 60 } }
                }
            }
        },
        sections: [
            // Section 1: Trang bìa (không có Header/Footer)
            {
                properties: {
                    page: {
                        margin: {
                            top: 1134,    // 20 mm
                            bottom: 1134, // 20 mm
                            left: 1701,   // 30 mm (lề gáy đóng sách chuẩn)
                            right: 1134,  // 20 mm
                            header: 720,
                            footer: 720
                        }
                    }
                },
                children: coverChildren
            },
            // Section 2: Toàn bộ nội dung báo cáo (có Header và Footer số trang)
            {
                properties: {
                    page: {
                        margin: {
                            top: 1134,    // 20 mm
                            bottom: 1134, // 20 mm
                            left: 1701,   // 30 mm (lề gáy đóng sách chuẩn)
                            right: 1134,  // 20 mm
                            header: 720,
                            footer: 720
                        }
                    }
                },
                headers: {
                    default: new Header({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                spacing: { after: 120 },
                                children: [
                                    new TextRun({
                                        text: "Đề tài: Xây dựng hệ thống giao đồ ăn trực tuyến FOODDAILY",
                                        italics: true,
                                        size: 20,
                                        font: FONT_NAME,
                                        color: "475569"
                                    })
                                ]
                            })
                        ]
                    })
                },
                footers: {
                    default: new Footer({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                spacing: { before: 120 },
                                children: [
                                    new TextRun({
                                        text: "SVTH: Trần Lê Thân\tTrang ",
                                        size: 20,
                                        font: FONT_NAME,
                                        color: "475569"
                                    }),
                                    new TextRun({
                                        children: [PageNumber.CURRENT],
                                        size: 20,
                                        font: FONT_NAME,
                                        color: "475569"
                                    })
                                ]
                            })
                        ]
                    })
                },
                children: bodyChildren
            }
        ]
    });

    const outputPath = path.join(__dirname, '..', 'docs', 'BAO_CAO_DU_AN_FOODDAILY_CHUAN_MAU.docx');
    Packer.toBuffer(doc).then(buffer => {
        fs.writeFileSync(outputPath, buffer);
        console.log('Successfully written full-formatted report to:', outputPath);
        console.log('File size:', buffer.length, 'bytes');
    }).catch(err => {
        console.error('Error creating docx:', err);
    });
}

buildDocx();


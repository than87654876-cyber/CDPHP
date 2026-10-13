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
    ShadingType
} = require('docx');

const FONT_NAME = "Times New Roman";

const borderThin = { style: BorderStyle.SINGLE, size: 4, color: "000000" };
const tableBorders = {
    top: borderThin,
    bottom: borderThin,
    left: borderThin,
    right: borderThin,
    insideHorizontal: { style: BorderStyle.SINGLE, size: 4, color: "D1D5DB" },
    insideVertical: { style: BorderStyle.SINGLE, size: 4, color: "D1D5DB" },
};

const cellMargins = { top: 100, bottom: 100, left: 140, right: 140 };

function hCell(text, width, align = AlignmentType.CENTER, bg = "E2E8F0") {
    return new TableCell({
        width: { size: width, type: WidthType.DXA },
        shading: { fill: bg, type: ShadingType.CLEAR },
        margins: cellMargins,
        children: [
            new Paragraph({
                alignment: align,
                children: [
                    new TextRun({ text, bold: true, color: "000000", size: 23, font: FONT_NAME })
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
                    new TextRun({ text: String(text), bold: isBold, color: "000000", size: 22, font: FONT_NAME })
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
        spacing: { before: 280, after: 120 },
        children: [
            new TextRun({ text, bold: true, size: 28, font: FONT_NAME })
        ]
    });
}

function pHeading2(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 200, after: 80 },
        children: [
            new TextRun({ text, bold: true, size: 26, font: FONT_NAME })
        ]
    });
}

function pHeading3(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 140, after: 60 },
        children: [
            new TextRun({ text, bold: true, size: 24, font: FONT_NAME })
        ]
    });
}

function pHeading4(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 100, after: 40 },
        children: [
            new TextRun({ text, bold: true, italics: true, size: 23, font: FONT_NAME })
        ]
    });
}

function pBody(text, isItalic = false) {
    return new Paragraph({
        spacing: { before: 40, after: 60, line: 360 },
        alignment: AlignmentType.JUSTIFIED,
        children: [
            new TextRun({ text, size: 24, font: FONT_NAME, italics: isItalic })
        ]
    });
}

function pBullet(boldPrefix, content) {
    return new Paragraph({
        spacing: { before: 30, after: 50, line: 360 },
        alignment: AlignmentType.JUSTIFIED,
        children: [
            new TextRun({ text: boldPrefix ? (boldPrefix + ": ") : "• ", bold: !!boldPrefix, size: 24, font: FONT_NAME }),
            new TextRun({ text: content, size: 24, font: FONT_NAME })
        ]
    });
}

function pageBreak() {
    return new Paragraph({
        children: [new TextRun({ text: "", break: 1 })],
        pageBreakBefore: true
    });
}

function generateDocx() {
    const children = [];

    // ==========================================
    // TRANG BÌA (Theo chuẩn HOVATEN_MSSV.docx)
    // ==========================================
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 60 },
        children: [new TextRun({ text: "TRƯỜNG ĐẠI HỌC [TÊN TRƯỜNG ĐẠI HỌC]", bold: true, size: 28, font: FONT_NAME })]
    }));
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 600 },
        children: [new TextRun({ text: "KHOA CÔNG NGHỆ THÔNG TIN", bold: true, size: 26, font: FONT_NAME })]
    }));

    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 400, after: 80 },
        children: [new TextRun({ text: "ĐỒ ÁN TỐT NGHIỆP", bold: true, size: 28, font: FONT_NAME })]
    }));
    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 400 },
        children: [new TextRun({ text: "CHUYÊN NGÀNH: CÔNG NGHỆ THÔNG TIN", bold: true, size: 24, font: FONT_NAME })]
    }));

    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 200, after: 80 },
        children: [new TextRun({ text: "ĐỀ TÀI: XÂY DỰNG HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY", bold: true, size: 28, font: FONT_NAME })]
    }));

    children.push(new Paragraph({ spacing: { before: 400, after: 40 }, children: [new TextRun({ text: "Giảng viên hướng dẫn:\tThS. [Họ và tên Giảng viên hướng dẫn]", size: 24, font: FONT_NAME })] }));
    children.push(new Paragraph({ spacing: { before: 0, after: 40 }, children: [new TextRun({ text: "Sinh viên thực hiện:\tTrần Lê Thân", bold: true, size: 24, font: FONT_NAME })] }));
    children.push(new Paragraph({ spacing: { before: 0, after: 40 }, children: [new TextRun({ text: "Lớp:\t[Mã lớp]", size: 24, font: FONT_NAME })] }));
    children.push(new Paragraph({ spacing: { before: 0, after: 400 }, children: [new TextRun({ text: "Khoá:\t2022 – 2026", size: 24, font: FONT_NAME })] }));

    children.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 300, after: 0 },
        children: [new TextRun({ text: "TP. Hồ Chí Minh, tháng 10 năm 2026", size: 22, font: FONT_NAME })]
    }));

    // ==========================================
    // LỜI CẢM ƠN
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("LỜI CẢM ƠN"));
    children.push(pBody("Để hoàn thành báo cáo tốt nghiệp này, tôi xin gửi lời cảm ơn chân thành đến quý Thầy Cô Khoa Công nghệ Thông tin - Trường Đại học [Tên trường Đại học] đã tận tình truyền đạt cho tôi những kiến thức, kinh nghiệm bổ ích trong suốt những năm được học tại trường. Những nền tảng kiến thức quý giá ấy giúp ích cho tôi rất nhiều trong quá trình hoàn thành luận văn tốt nghiệp, đồng thời là hành trang hữu ích cho công việc phát triển phần mềm tương lai của tôi."));
    children.push(pBody("Đặc biệt tôi xin gửi lời cảm ơn sâu sắc dành đến Thầy/Cô hướng dẫn vì Thầy/Cô đã tận tình hướng dẫn, góp ý, hỗ trợ tôi hết mình trong suốt quá trình xây dựng hệ thống. Tôi cũng xin cảm ơn các Thầy Cô trong bộ môn đã hỗ trợ, chỉ dẫn tôi về công nghệ. Nhờ sự giúp đỡ hết lòng của Thầy Cô mà báo cáo của tôi mới được hoàn thiện như ngày hôm nay."));
    children.push(pBody("Tuy nhiên, thời gian thực hiện với kinh nghiệm hạn hẹp của bản thân mình, tôi đã cố gắng hết mình để tìm hiểu, phân tích, xây dựng hệ thống nhưng chắc chắn sẽ không tránh khỏi sự thiếu sót. Tôi rất mong nhận được sự thông cảm cũng như những lời góp ý và chỉ bảo tận tình của quý Thầy Cô, điều đó sẽ giúp tôi có thêm sự dày dặn trong kinh nghiệm và kiến thức của mình."));
    children.push(pBody("Cuối cùng, tôi xin kính chúc tập thể quý Thầy Cô trường luôn dồi dào sức khỏe và thành công trong công việc giảng dạy."));
    children.push(new Paragraph({ spacing: { before: 140, after: 40 }, children: [new TextRun({ text: "Tôi xin chân thành cảm ơn!", italics: true, size: 24, font: FONT_NAME })] }));

    // ==========================================
    // LỜI CAM ĐOAN
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("LỜI CAM ĐOAN"));
    children.push(pBody("Tôi xin cam đoan đề tài: “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY” là một công trình nghiên cứu độc lập dưới sự hướng dẫn của Giảng viên hướng dẫn và sự hỗ trợ, chỉ dạy công nghệ từ các giảng viên bộ môn. Ngoài ra không có bất cứ sự sao chép từ người khác."));
    children.push(pBody("Đề tài, nội dung báo cáo và mã nguồn chương trình là kết quả tìm hiểu, phân tích và lập trình nghiêm túc của bản thân tôi. Các số liệu, tài liệu tham khảo trong báo cáo đều được trích dẫn nguồn gốc rõ ràng theo đúng quy định."));
    children.push(pBody("Nếu có bất kỳ sự không trung thực nào, tôi xin chịu hoàn toàn trách nhiệm trước Hội đồng và Nhà trường."));

    // ==========================================
    // LỜI MỞ ĐẦU
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("LỜI MỞ ĐẦU"));
    children.push(pBody("Trong thời đại công nghệ số và nhịp sống đô thị hiện đại, nhu cầu sử dụng các dịch vụ đặt đồ ăn trực tuyến ngày càng trở nên phổ biến và thiết yếu. Người tiêu dùng ngày nay không chỉ mong muốn xem thực đơn và đặt hàng nhanh chóng mà còn mong muốn những trải nghiệm tương tác thông minh, giải quyết được các bất tiện trong đời sống thường nhật."));
    children.push(pBody("Thực tế cho thấy, việc đặt món ăn tập thể tại các văn phòng công sở hay trường học thường gặp nhiều khó khăn: người gom đơn phải ghi chép thủ công từng món, từng loại dặn dò riêng (không hành, ít cay), dễ dẫn đến sai sót nhầm lẫn và phiền phức khi chia tiền. Đồng thời, khách hàng thường xuyên rơi vào trạng thái băn khoăn 'hôm nay ăn gì' do thiếu công cụ gợi ý thông minh; việc liên lạc giữa quầy thu ngân và nhà bếp dễ bị đứt gãy gây trễ đơn; quy trình chuyển khoản ngân hàng đối soát bằng hình ảnh biên lai thủ công tiềm ẩn nhiều rủi ro."));
    children.push(pBody("Xuất phát từ thực tiễn trên, tôi quyết định chọn đề tài “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY”. Đề tài tập trung xây dựng nền tảng website khép kín từ khách hàng đến nhà bếp và quản trị viên, tích hợp đặt món nhóm thời gian thực, trợ lý ảo Google Gemini AI và thanh toán tự động VietQR PayOS."));

    // ==========================================
    // CHƯƠNG 1
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("Chương 1. TỔNG QUAN TÀI LIỆU"));
    children.push(pHeading2("1.1\tKhảo sát bài toán"));
    children.push(pHeading3("1.1.1.\tĐặt vấn đề"));
    children.push(pBody("Hiện nay, số lượng người tiêu dùng chuyển sang đặt đồ ăn qua mạng ngày càng nhiều. Đối với các quán ăn và chuỗi nhà hàng, việc quản lý bán hàng theo phương pháp truyền thống bộc lộ nhiều điểm hạn chế:"));
    children.push(pBullet("Khó khăn khi đặt món nhóm tập thể", "Mỗi trưa văn phòng hay nhóm bạn cùng đặt ăn thường phải ghi chép vào giấy hoặc tin nhắn, tốn từ 15-30 phút, dễ nhầm món và khó thu tiền từng người."));
    children.push(pBullet("Tâm lý phân vân chọn món", "Khách hàng mất nhiều thời gian lướt thực đơn mà không biết chọn món gì do thiếu công cụ tư vấn theo sở thích và ngân sách."));
    children.push(pBullet("Nghẽn mạch quầy và bếp", "Phiếu giấy dễ thất lạc, đầu bếp không biết đơn nào cần nấu trước dẫn đến việc phục vụ không đúng thứ tự ưu tiên."));
    children.push(pBullet("Đối soát chuyển khoản thủ công chậm trễ", "Khách hàng phải chụp ảnh gửi bill, nhân viên mất thời gian kiểm tra và dễ bị kẻ gian lợi dụng bill giả mạo."));

    children.push(pHeading3("1.1.2.\tGiải pháp"));
    children.push(pBody("Tôi đã đưa ra giải pháp là xây dựng website hệ thống giao đồ ăn trực tuyến mang tên FOODDAILY với các tính năng vượt trội:"));
    children.push(pBullet("Đặt món theo nhóm Realtime", "Tạo phòng đặt món chung qua mã PIN / QR Code, thành viên tự chọn món trên điện thoại cá nhân, giỏ hàng tự động đồng bộ và tự chia tiền từng người."));
    children.push(pBullet("Trợ lý AI Gemini tư vấn thông minh", "Chatbot AI hiểu ngữ cảnh, phân tích tâm trạng và ngân sách để gợi ý đúng món ăn có sẵn tại quán kèm nút mua nhanh."));
    children.push(pBullet("Thanh toán tự động VietQR PayOS", "Mã QR động chuẩn VietQR Napas 24/7, Webhook tự động khớp lệnh chuyển trạng thái đơn sang đã thanh toán trong 1-2 giây."));
    children.push(pBullet("Màn hình Bếp FIFO", "Hàng đợi chế biến sắp xếp vào trước làm trước, tự động gom nhóm số lượng từng món để nấu hàng loạt."));
    children.push(pBullet("Gói ăn định kỳ", "Đăng ký combo theo tuần/tháng với tính năng linh hoạt đổi món mỗi ngày."));

    children.push(pHeading3("1.1.3.\tMục đích của đề tài"));
    children.push(pBody("Xây dựng một hệ thống quản lý bán và giao đồ ăn trực tuyến đáp ứng đầy đủ các quy trình nghiệp vụ F&B hiện đại, tiếp cận khách hàng tiềm năng trên môi trường Internet. Đồng thời nâng cao kỹ năng lập trình web với PHP trên Framework Laravel, làm chủ WebSockets thời gian thực, tích hợp API thanh toán và cơ sở dữ liệu MySQL."));

    children.push(pHeading2("1.2\tCơ sở lý thuyết"));
    children.push(pHeading3("1.2.1.\tHTML5"));
    children.push(pBody("HTML5 là phiên bản mới nhất của ngôn ngữ đánh dấu siêu văn bản, hỗ trợ các thẻ ngữ nghĩa (header, nav, section, article, footer) và đa phương tiện. HTML5 được dùng để xây dựng bộ khung chuẩn cho toàn bộ giao diện website."));

    children.push(pHeading3("1.2.2.\tCSS"));
    children.push(pBody("CSS được sử dụng để định dạng phong cách hiển thị (màu sắc, phông chữ, bố cục, khoảng cách) cho các thành phần HTML, giúp trang web có tính thẩm mỹ cao và thu hút người dùng."));

    children.push(pHeading3("1.2.3.\tBoostrap 4 & Tailwind CSS"));
    children.push(pBody("Dự án kết hợp Tailwind CSS v4 và Bootstrap components để xây dựng giao diện chuẩn Responsive Mobile-First, tự động co giãn hiển thị hoàn hảo trên máy tính để bàn, máy tính bảng và điện thoại di động."));

    children.push(pHeading3("1.2.4.\tJavascript"));
    children.push(pBody("Javascript là ngôn ngữ lập trình kịch bản phía máy khách, xử lý các tương tác người dùng như kiểm tra biểu mẫu, tính tiền giỏ hàng, vòng quay may mắn và điều khiển các thành phần động trên trang web."));

    children.push(pHeading3("1.2.5.\tJquery & WebSockets"));
    children.push(pBody("Xử lý các yêu cầu bất đồng bộ AJAX (Fetch API/Jquery) để cập nhật dữ liệu không cần tải lại trang. Kết hợp công nghệ WebSockets (Laravel Reverb) để đồng bộ phòng đặt nhóm và đẩy trạng thái đơn hàng thời gian thực."));

    children.push(pHeading3("1.2.6.\tPHP"));
    children.push(pBody("Hệ thống sử dụng PHP 8.3 với JIT compiler hiệu năng cao, hỗ trợ kiểm tra kiểu dữ liệu nghiêm ngặt và xử lý logic backend an toàn, nhanh chóng."));

    children.push(pHeading3("1.2.7.\tLaravel"));
    children.push(pBody("Laravel 13 là framework PHP hàng đầu với kiến trúc MVC, Eloquent ORM chống truy vấn N+1, Blade template an toàn, Artisan task scheduling tự động và hệ thống Middleware bảo mật."));

    children.push(pHeading3("1.2.8.\tMySQL Server"));
    children.push(pBody("Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0 sử dụng InnoDB engine, hỗ trợ toàn vẹn giao dịch ACID và các chỉ mục Composite Index tối ưu truy vấn nhanh dưới 5ms."));

    children.push(pHeading3("1.2.9.\tVisual Studio Code"));
    children.push(pBody("Trình soạn thảo mã nguồn gọn nhẹ, đa năng với hệ sinh thái extension phong phú hỗ trợ viết mã, gỡ lỗi và quản lý phiên bản Git hiệu quả."));

    children.push(pHeading3("1.2.10.\tLaragon"));
    children.push(pBody("Môi trường máy chủ phát triển cục bộ tiện dụng trên Windows, tích hợp sẵn PHP 8.3, MySQL 8.0, Nginx, Node.js và Composer, giúp thử nghiệm hệ thống nhanh chóng."));

    // ==========================================
    // CHƯƠNG 2
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("Chương 2. PHƯƠNG PHÁP THỰC HIỆN"));
    children.push(pHeading2("2.1\tPhân tích hệ thống"));
    children.push(pHeading3("2.1.1\tQuy trình nghiệp vụ"));
    children.push(pBody("Quy trình nghiệp vụ hệ thống gồm 7 bước:"));
    children.push(pBullet("Bước 1: Khách hàng đặt hàng", "Khách hàng xem thực đơn, tìm kiếm món ăn hoặc dùng trợ lý AI / Vòng quay may mắn để chọn món. Khách hàng chọn đặt cá nhân hoặc tạo phòng đặt món theo nhóm."));
    children.push(pBullet("Bước 2: Tiếp nhận và gom đơn nhóm", "Chủ phòng tạo phòng, hệ thống cấp mã PIN và QR Code. Thành viên tham gia tự chọn món và giỏ hàng nhóm tự động đồng bộ thời gian thực."));
    children.push(pBullet("Bước 3: Xác nhận thông tin và thanh toán", "Khách hàng nhập thông tin nhận hàng, áp voucher và thanh toán qua PayOS VietQR hoặc tiền mặt COD."));
    children.push(pBullet("Bước 4: Chuyển đơn sang Bếp FIFO chế biến", "Đơn hàng tự động xuất hiện trên Màn hình Bếp FIFO theo thứ tự ưu tiên. Bếp xem bảng gom số lượng và tiến hành nấu."));
    children.push(pBullet("Bước 5: Quầy thu ngân đóng gói và in bill", "Nhân viên quầy nhận đơn đã nấu xong, in hóa đơn thanh toán 1 chạm và đóng gói bàn giao cho đơn vị giao hàng."));
    children.push(pBullet("Bước 6: Giao hàng và tra cứu đơn realtime", "Đơn vị vận chuyển giao hàng; khách hàng theo dõi tiến độ đơn hàng thời gian thực trên website."));
    children.push(pBullet("Bước 7: Khách nhận hàng, tích điểm và đánh giá", "Khách nhận hàng thành công, hệ thống tự động tích điểm thưởng và tiếp nhận đánh giá sao chất lượng món ăn."));

    children.push(pHeading3("2.1.2\tThông tin yêu cầu"));
    children.push(pBody("Hệ thống quản lý các luồng thông tin yêu cầu:"));
    children.push(pBullet("Thông tin khách hàng", "ID, họ tên, email, mật khẩu mã hóa, số điện thoại, địa chỉ nhận hàng, điểm tích lũy."));
    children.push(pBullet("Thông tin món ăn", "ID món, ID danh mục, tên món, slug, đơn giá, giá gốc, ảnh đại diện Cloudinary, mô tả, số suất bán trong ngày, trạng thái kinh doanh."));
    children.push(pBullet("Thông tin đơn hàng", "Mã đơn hàng, tên người nhận, số điện thoại, địa chỉ giao, tổng tiền, phương thức thanh toán, trạng thái thanh toán, tiến độ đơn, mã giao dịch PayOS."));
    children.push(pBullet("Thông tin nhà cung cấp & Kho", "Tên nhà cung cấp, điện thoại, địa chỉ; tên nguyên liệu tồn kho, đơn vị tính, số lượng tồn, ngưỡng báo nhập, phiếu nhập kho."));

    children.push(pHeading3("2.1.3\tSơ đồ chức năng của hệ thống"));
    children.push(pBody("Hệ thống phân chia 4 sơ đồ chức năng chính:"));
    children.push(pBullet("Sơ đồ 2.1 Sơ đồ chức năng của Admin", "Thống kê doanh thu, quản lý thực đơn, quản lý đơn hàng, quản lý kho nguyên liệu, quản lý voucher, phân quyền RBAC, sao lưu dữ liệu."));
    children.push(pBullet("Sơ đồ 2.2 Sơ đồ chức năng nhân viên", "Bàn làm việc Workspace, xác nhận đơn hàng, in hóa đơn nhiệt 80mm, xem cảnh báo tồn kho."));
    children.push(pBullet("Sơ đồ 2.3 Sơ đồ chức năng đầu bếp", "Xem hàng đợi Bếp FIFO, xem bảng gộp số lượng từng món, cập nhật bắt đầu nấu và nấu xong."));
    children.push(pBullet("Sơ đồ 2.4 Sơ đồ chức năng khách hàng", "Xem menu, tìm kiếm, đặt món nhóm realtime, thanh toán VietQR PayOS, hỏi trợ lý AI Gemini, đăng ký gói ăn, tra cứu đơn realtime."));

    children.push(pHeading2("2.2\tCác biểu đồ thiết kế hệ thống"));
    children.push(pHeading3("2.2.1\tLượt đồ Use case"));
    children.push(pBody("Lược đồ 2.1 Lược đồ use case tổng quát toàn hệ thống FOODDAILY gồm 4 tác nhân: Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên."));

    // Bảng đặc tả Use case UC01
    children.push(pHeading4("2.2.2.1\tĐặc tả use case đăng nhập vào hệ thống."));
    children.push(pBody("Mục đích: Đảm bảo xác thực thông tin người sử dụng và an toàn hệ thống.\nTác nhân: Khách hàng, nhân viên, đầu bếp, Admin.\nMô tả chung: Khi tác nhân muốn thao tác với hệ thống cần phải đăng nhập."));
    const ucLoginTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn chức năng đăng nhập.", "-"],
            ["-", "2. Hiển thị Form đăng nhập."],
            ["3. Nhập Email và Mật khẩu.", "-"],
            ["4. Nhấn nút Đăng nhập gửi thông tin.", "-"],
            ["-", "5. Kiểm tra thông tin đăng nhập, nếu đúng thì cho phép truy cập và chuyển hướng theo vai trò; nếu sai thì hiển thị thông báo lỗi."]
        ],
        [4700, 4700]
    );
    children.push(ucLoginTable);
    children.push(pBody("Luồng thay thế: Người dùng có thể chọn đăng nhập nhanh bằng Google OAuth 2.0.\nĐiều kiện trước: Người dùng đã có tài khoản trên hệ thống.\nĐiều kiện sau: Khởi tạo phiên làm việc thành công."));

    // Bảng đặc tả Use case UC02
    children.push(pHeading4("2.2.2.2\tĐặc tả use case đăng xuất vào hệ thống."));
    children.push(pBody("Mục đích: Người dùng đăng xuất ra khỏi hệ thống để bảo mật.\nTác nhân: Khách hàng, nhân viên, đầu bếp, Admin.\nMô tả chung: Tác nhân đăng xuất khi không sử dụng nữa."));
    const ucLogoutTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn chức năng đăng xuất.", "-"],
            ["-", "2. Hệ thống hủy Session, xóa cookie xác thực, vô hiệu hóa BFCache và chuyển về trang chủ."]
        ],
        [4700, 4700]
    );
    children.push(ucLogoutTable);

    // Bảng đặc tả Use case UC03 - Tìm kiếm
    children.push(pHeading4("2.2.2.3\tĐặc tả use case tìm kiếm và xem chi tiết món ăn."));
    const ucSearchTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Nhập từ khóa hoặc chọn lọc theo danh mục/giá.", "-"],
            ["-", "2. Hệ thống truy vấn CSDL và trả về danh sách món ăn thỏa điều kiện."],
            ["3. Nhấp chọn món ăn cụ thể.", "-"],
            ["-", "4. Hiển thị thông tin chi tiết món ăn, hình ảnh và đánh giá."]
        ],
        [4700, 4700]
    );
    children.push(ucSearchTable);

    // Bảng đặc tả Use case UC04 - Đặt món nhóm
    children.push(pHeading4("2.2.2.4\tĐặc tả use case đặt món theo nhóm Realtime."));
    const ucGroupTable = makeTable(
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
    );
    children.push(ucGroupTable);

    // Bảng đặc tả Use case UC05 - Thanh toán VietQR
    children.push(pHeading4("2.2.2.5\tĐặc tả use case thanh toán tự động VietQR PayOS."));
    const ucPayTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn phương thức VietQR và xác nhận đặt hàng.", "-"],
            ["-", "2. Gọi API PayOS sinh mã QR VietQR động hiển thị trên màn hình."],
            ["3. Khách quét mã chuyển tiền qua App ngân hàng.", "-"],
            ["-", "4. Nhận Webhook từ PayOS, kiểm tra chữ ký HMAC-SHA256, đổi trạng thái sang 'paid' và bắn chuông sang Bếp FIFO."],
            ["-", "5. Màn hình khách hàng tự động chuyển sang thông báo thanh toán thành công."]
        ],
        [4700, 4700]
    );
    children.push(ucPayTable);

    // Bảng đặc tả Use case UC06 - Trợ lý AI
    children.push(pHeading4("2.2.2.6\tĐặc tả use case Trợ lý AI Gemini tư vấn món ăn."));
    const ucAiTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng mở hộp thoại và gửi câu hỏi tư vấn.", "-"],
            ["-", "2. Nạp danh sách món ăn đang bán vào Context Prompt gửi đến Google Gemini 1.5 Flash."],
            ["-", "3. Gemini trả về câu trả lời tư vấn kèm danh sách ID món phù hợp."],
            ["-", "4. Hiển thị tin nhắn tư vấn kèm các thẻ món ăn có nút mua nhanh."]
        ],
        [4700, 4700]
    );
    children.push(ucAiTable);

    // Bảng đặc tả Use case UC07 - Bếp FIFO
    children.push(pHeading4("2.2.2.7\tĐặc tả use case điều phối chế biến tại Màn hình Bếp FIFO."));
    const ucKitchenTable = makeTable(
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
    );
    children.push(ucKitchenTable);

    // Bảng đặc tả Use case UC08 - Gói ăn định kỳ
    children.push(pHeading4("2.2.2.8\tĐặc tả use case đăng ký và quản lý gói ăn định kỳ."));
    const ucSubTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng chọn gói combo (7 ngày / 30 ngày) và thanh toán.", "-"],
            ["-", "2. Sinh lịch giao suất ăn hằng ngày trong bảng Daily_Schedules."],
            ["3. Khách hàng xem lịch và chọn đổi món trước giờ cắt đơn.", "-"],
            ["-", "4. Cập nhật món ăn mới cho ngày tương ứng."]
        ],
        [4700, 4700]
    );
    children.push(ucSubTable);

    // Bảng đặc tả Use case UC09 - Quản lý thực đơn
    children.push(pHeading4("2.2.2.9\tĐặc tả use case quản lý thực đơn món ăn."));
    const ucMenuTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin chọn Thêm mới hoặc Sửa món ăn.", "-"],
            ["2. Nhập tên, danh mục, giá, tải ảnh lên và nhấn Lưu.", "-"],
            ["-", "3. Tải ảnh lên Cloudinary, lưu dữ liệu vào bảng Dishes và thông báo thành công."]
        ],
        [4700, 4700]
    );
    children.push(ucMenuTable);

    // Bảng đặc tả Use case UC10 - Quản lý kho
    children.push(pHeading4("2.2.2.10\tĐặc tả use case quản lý kho nguyên liệu và nhà cung cấp."));
    const ucInvTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chọn mục Nhập kho, chọn NCC và danh sách nguyên liệu.", "-"],
            ["2. Nhấn 'Hoàn tất phiếu nhập'.", "-"],
            ["-", "3. Lưu phiếu nhập Goods_Receipts và tự động cộng dồn số lượng tồn kho Inventory_Items."]
        ],
        [4700, 4700]
    );
    children.push(ucInvTable);

    // Bảng đặc tả Use case UC11 - Voucher
    children.push(pHeading4("2.2.2.11\tĐặc tả use case quản lý khuyến mãi và cấp voucher ví."));
    const ucPromoTable = makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin nhập thông tin mã voucher (% giảm, mức giảm tối đa, hạn dùng).", "-"],
            ["2. Nhấn 'Phát hành và cấp vào ví hội viên'.", "-"],
            ["-", "3. Lưu bảng Coupons, tự động insert User_Coupons cho toàn bộ thành viên và gửi email thông báo."]
        ],
        [4700, 4700]
    );
    children.push(ucPromoTable);

    // ==========================================
    // 2.2.2 LƯỢC ĐỒ ACTIVITY
    // ==========================================
    children.push(pHeading3("2.2.2\tLược đồ Activity"));
    children.push(pBody("Hệ thống xây dựng 13 lược đồ hoạt động tương ứng với các chức năng nghiệp vụ cốt lõi:"));
    children.push(pBullet("2.2.2.1 Lược đồ chức năng đăng nhập", "Nhập Email/Mật khẩu -> Kiểm tra CSDL -> Sai thì báo lỗi / Đúng thì khởi tạo Session và điều hướng theo vai trò."));
    children.push(pBullet("2.2.2.3 Lược đồ chức năng reset password", "Nhập Email -> Sinh mã OTP gửi email -> Nhập OTP kiểm tra -> Đúng thì cập nhật mật khẩu mới mã hóa Bcrypt."));
    children.push(pBullet("2.2.2.4 Lược đồ tìm kiếm", "Nhập từ khóa / chọn danh mục / khoảng giá -> Truy vấn CSDL -> Trả về danh sách món ăn."));
    children.push(pBullet("2.2.2.5 Lược đồ đặt món theo nhóm Realtime", "Tạo phòng -> Sinh Room Code & QR -> Thành viên vào chọn món -> Broadcast WebSocket đồng bộ giỏ hàng -> Chủ phòng chốt đơn."));
    children.push(pBullet("2.2.2.6 Lược đồ thanh toán PayOS VietQR", "Chọn VietQR -> Sinh mã QR động -> Khách quét mã chuyển khoản -> Webhook kiểm tra HMAC-SHA256 -> Đổi trạng thái 'paid' và bắn vào Bếp."));
    children.push(pBullet("2.2.2.7 Lược đồ điều phối Bếp FIFO", "Bếp xem danh sách đơn FIFO và bảng gom số lượng -> Bấm bắt đầu nấu -> Nấu xong đẩy sang quầy đóng gói."));
    children.push(pBullet("2.2.2.8 Lược đồ quản lý hóa đơn và in bill", "Thu ngân chọn đơn đã nấu xong -> Bấm In bill 1 chạm -> Xuất phiếu in nhiệt 80mm -> Giao tài xế."));
    children.push(pBullet("2.2.2.9 Lược đồ quản lý thông tin khách hàng", "Admin xem danh sách thành viên -> Khóa/Mở khóa tài khoản hoặc xem lịch sử mua hàng."));
    children.push(pBullet("2.2.2.10 Lược đồ quản lý thông tin món ăn", "Admin nhập thông tin món -> Tải ảnh lên Cloudinary -> Lưu bảng Dishes."));
    children.push(pBullet("2.2.2.11 Lược đồ quản lý thông tin nhà cung cấp", "Nhập thông tin đối tác thực phẩm -> Lưu bảng Suppliers."));
    children.push(pBullet("2.2.2.12 Lược đồ quản lý kho nguyên liệu", "Lập phiếu nhập kho -> Lưu Goods_Receipts -> Tự động cộng tồn kho Inventory_Items."));
    children.push(pBullet("2.2.2.13 Lược đồ quản lý nhân viên và phân quyền", "Admin chọn tài khoản nhân viên -> Gán vai trò (Staff, Kitchen, Admin) -> Đồng bộ quyền Spatie RBAC."));

    // Biểu đồ thành phần & triển khai
    children.push(pHeading3("2.2.3\tBiểu đồ thành phần"));
    children.push(pBullet("2.2.4.1 Biểu đồ thành phần hệ thống", "Gồm tầng giao diện Client, tầng xử lý backend Laravel 13 và tầng CSDL MySQL 8.0 kết nối các API bên ngoài."));
    children.push(pBullet("2.2.4.2 Biểu đồ thành phần quản trị viên", "Các Controller quản trị kết nối với Service Layer: Dashboard, Món ăn, Đơn hàng, Kho và Phân quyền."));
    children.push(pBullet("2.2.4.4 Biểu đồ thành phần người dùng", "Các Controller phía khách hàng: ShopController, CartController, GroupOrderController, SubscriptionController."));

    children.push(pHeading3("2.2.4\tBiểu đồ triển khai"));
    children.push(pBody("Hệ thống triển khai trên máy chủ Laragon / Nginx Web Server kết hợp PHP 8.3 FPM, MySQL 8.0, Laravel Reverb WebSocket Server (Port 8080) và giao tiếp an toàn qua HTTPS với Google Gemini, PayOS VietQR, Cloudinary."));

    // ==========================================
    // 2.3 CƠ SỞ DỮ LIỆU
    // ==========================================
    children.push(pHeading2("2.3\tCơ sở dữ liệu"));
    children.push(pBody("Dưới đây là tóm tắt ý nghĩa các bảng dữ liệu chính trong hệ thống FOODDAILY:"));

    // Table Users
    children.push(pHeading3("Table Users"));
    const tblUsers = makeTable(
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
    );
    children.push(tblUsers);

    // Table Categories
    children.push(pHeading3("Table Categories"));
    const tblCat = makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Gía trị tối đa", "Mô tả"],
        [
            ["id", "bigint", "Primary key, Auto Increment, Not Null", "20", "Mã danh mục"],
            ["name", "nvarchar", "Not Null", "255", "Tên danh mục món"],
            ["slug", "nvarchar", "Unique, Not Null", "255", "Đường dẫn URL"],
            ["image", "nvarchar", "Null", "255", "Ảnh danh mục"],
            ["is_active", "tinyint", "Not Null, Default 1", "1", "Trạng thái hiển thị"]
        ],
        [1800, 1800, 2200, 1000, 2600]
    );
    children.push(tblCat);

    // Table Dishes
    children.push(pHeading3("Table Dishes"));
    const tblDishes = makeTable(
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
    );
    children.push(tblDishes);

    // Table Orders
    children.push(pHeading3("Table Orders"));
    const tblOrders = makeTable(
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
    );
    children.push(tblOrders);

    // Table Suppliers & Inventory
    children.push(pHeading3("Table Suppliers & Inventory_Items"));
    const tblInv = makeTable(
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
    );
    children.push(tblInv);

    // ==========================================
    // CHƯƠNG 3
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ"));
    children.push(pHeading2("3.1\tMột số giao diện website"));
    children.push(pHeading3("3.1.1 Phần quản trị"));
    children.push(pBullet("3.1.3.1 Giao diện đăng nhập", "Form đăng nhập quản trị an toàn, hỗ trợ ghi nhớ phiên và khôi phục mật khẩu OTP."));
    children.push(pBullet("3.1.3.2 Giao diện Dashboard thống kê doanh thu", "Biểu đồ trực quan doanh thu theo ngày/tháng, số đơn hàng mới, khách hàng mới và top món bán chạy."));
    children.push(pBullet("3.1.3.3 Giao diện quản lý thực đơn món ăn", "Bảng danh sách món ăn, hỗ trợ lọc theo danh mục, tìm kiếm nhanh và bật tắt trạng thái kinh doanh 1 chạm."));
    children.push(pBullet("3.1.3.4 Giao diện quản lý đơn đặt hàng", "Theo dõi đơn hàng theo các trạng thái (Chờ xác nhận, Đã thanh toán, Đang nấu, Đang giao, Hoàn tất)."));
    children.push(pBullet("3.1.3.5 Giao diện quản lý khách hàng", "Danh sách khách hàng, điểm tích lũy, tổng chi tiêu và hỗ trợ khóa/mở khóa tài khoản."));
    children.push(pBullet("3.1.3.6 Giao diện quản lý kho nguyên liệu", "Theo dõi tồn kho nguyên vật liệu và tự động cảnh báo màu đỏ các mặt hàng sắp cạn kiệt."));
    children.push(pBullet("3.1.3.7 Giao diện quản lý nhà cung cấp", "Quản lý danh bạ đối tác cung cấp thực phẩm và lịch sử nhập hàng."));
    children.push(pBullet("3.1.3.8 Giao diện Màn hình Bếp FIFO", "Hàng đợi chế biến sắp xếp vào trước làm trước, tự động gom số lượng từng món để nấu hàng loạt."));
    children.push(pBullet("3.1.3.9 Giao diện Bàn làm việc thu ngân & In hóa đơn", "Bàn làm việc thu ngân tiếp nhận đơn nấu xong, in hóa đơn nhiệt 80mm chỉ bằng 1 cú nhấp chuột."));
    children.push(pBullet("3.1.3.10 Giao diện Reset Password", "Màn hình gửi mã OTP và đặt lại mật khẩu mới an toàn."));

    children.push(pHeading3("3.1.2 File excel xuất ra từ quản trị"));
    children.push(pBody("Hệ thống hỗ trợ xuất file Excel/CSV phục vụ thống kê báo cáo doanh thu, đơn hàng, tồn kho nguyên vật liệu và xuất file sao lưu Customer Backup (.zip) an toàn."));

    children.push(pHeading3("3.1.3 Phần người dùng"));
    children.push(pBullet("3.1.3.1 Giao diện người dùng ở thiết bị màn hình lớn", "Giao diện Desktop hiện đại: Trang chủ ẩm thực, thực đơn trực quan, Vòng quay may mắn 'Hôm nay ăn gì', Phòng đặt món nhóm Realtime, Thanh toán tự động VietQR PayOS và Trợ lý AI Gemini."));
    children.push(pBullet("3.1.3.2 Giao diện người dùng ở thiết bị màn hình nhỏ", "Giao diện Responsive chuẩn Mobile-First: Thanh điều hướng đáy (Bottom Nav), giỏ hàng nổi cố định và hỗ trợ cài đặt Progressive Web App (PWA)."));

    // ==========================================
    // CHƯƠNG 4
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("Chương 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN"));
    children.push(pHeading2("4.1\tKết luận"));

    children.push(pHeading3("Kết luận đạt được"));
    children.push(pBullet("Đã xây dựng hoàn thiện ứng dụng web", "Hoàn thiện 100% hệ sinh thái Website thương mại điện tử chuyên ngành ẩm thực và giao đồ ăn trực tuyến FOODDAILY, khép kín quy trình từ Khách hàng đặt món, Bếp FIFO chế biến, Quầy thu ngân đóng gói in hóa đơn, cho đến Phân hệ Quản trị tối cao (Admin Dashboard)."));
    children.push(pBullet("Đáp ứng toàn diện các tiêu chuẩn của một Website thương mại hiện đại", "Đáp ứng đầy đủ các yêu cầu về tính sẵn sàng, giao diện trực quan, bảo mật dữ liệu, chuẩn SEO và tối ưu hóa trải nghiệm người dùng trên đa nền tảng thiết bị (Responsive Mobile-First & Desktop)."));
    children.push(pBullet("Website có giao diện thân thiện, thẩm mỹ cao và dễ dàng sử dụng", "Thiết kế hiện đại phong cách ẩm thực với gam màu đỏ cam chủ đạo (#ee4d2d), bố cục khoa học, hiệu ứng chuyển động mượt mà, phân loại danh mục thông minh và thanh tìm kiếm đa năng giúp đặt món nhanh chóng trong 1 phút."));
    children.push(pBullet("Tích hợp sâu rộng các công nghệ và dịch vụ số tiên tiến", "Tích hợp Trí tuệ nhân tạo Google Gemini 1.5 Flash AI tư vấn món ăn thông minh; Tích hợp cổng thanh toán tự động VietQR PayOS (Napas 24/7) khớp lệnh Webhook 1 - 2 giây; Tích hợp Đặt món theo nhóm thời gian thực (Group Order Realtime) qua WebSockets; Tích hợp Vòng quay may mắn 'Hôm nay ăn gì?' cấp voucher tự động; Tích hợp Màn hình Bếp FIFO tự động gom số lượng món; Tích hợp Email tự động SMTP PHPMailer, Bot Telegram và Web Push Notification; Tích hợp Progressive Web App (PWA) cài đặt ứng dụng độc lập trên điện thoại và máy tính."));
    children.push(pBullet("Hỗ trợ tính toán, báo giá và chiết khấu tức thì, chính xác", "Tự động tính tổng tiền đơn hàng, trừ chiết khấu voucher linh hoạt, tính phí vận chuyển theo khu vực, giúp tiết kiệm tối đa thời gian, công sức và hạn chế sai sót số liệu so với phương pháp thủ công."));
    children.push(pBullet("Thiết kế tối ưu cho công tác quản trị và vận hành doanh nghiệp", "Phân hệ Admin chuyên biệt cung cấp biểu đồ trực quan hóa doanh thu ngày/tháng/năm, quản lý kho nguyên vật liệu cảnh báo sắp hết hàng, quản lý nhà cung cấp, phân quyền vai trò nhân viên (RBAC) và xuất file báo cáo Excel (.xlsx) chuyên nghiệp chỉ với một thao tác."));
    children.push(pBullet("Vận dụng toàn diện kiến thức đã học để hoàn thành luận văn tốt nghiệp", "Tổng hợp và vận dụng nhuần nhuyễn toàn bộ khối kiến thức chuyên ngành Công nghệ Thông tin (Công nghệ Web, Cơ sở dữ liệu nâng cao, Phân tích thiết kế hệ thống, An toàn bảo mật, Trí tuệ nhân tạo) để hoàn thành đồ án tốt nghiệp có hàm lượng thực tiễn cao."));

    children.push(pHeading3("Lý thuyết, kỹ năng và kinh nghiệm"));
    children.push(pBullet("Làm chủ công cụ và môi trường phát triển hiện đại", "Sử dụng thành thạo và thuần thục phần mềm Visual Studio Code; làm chủ môi trường máy chủ cục bộ Laragon (Apache 2.4, MySQL 8.0, PHP 8.3); quản lý phiên bản mã nguồn chuyên nghiệp với Git và GitHub."));
    children.push(pBullet("Nắm vững phương pháp luận xây dựng Website chuẩn công nghiệp", "Hiểu sâu sắc quy trình phát triển phần mềm theo mô hình MVC (Model - View - Controller), từ khâu thu thập khảo sát nghiệp vụ, phân tích thiết kế hệ thống (Use Case, Activity, BFD, ERD), đến hiện thực hóa mã nguồn có tính mở rộng cao và khả năng tái sử dụng tốt."));
    children.push(pBullet("Nâng cao chuyên sâu về ngôn ngữ PHP và Framework Laravel", "Làm chủ toàn diện các thành phần cốt lõi của Laravel Framework: Routing, Middleware, Controller, Blade Template, Service Provider, Service Container, Event & Listener, Notification, Queue; hiểu rõ cơ chế vòng đời Request Lifecycle và cấu hình môi trường sản xuất."));
    children.push(pBullet("Thành thạo kỹ thuật truy vấn và bảo mật cơ sở dữ liệu nâng cao", "Ứng dụng nhuần nhuyễn Eloquent ORM của Laravel (Eager Loading with, whereHas, withSum) loại bỏ triệt để lỗi N+1 Query; sử dụng thành thạo Laravel Authentication & Authorization (Bcrypt, Session Encryption, Spatie RBAC); áp dụng Database Transactions đảm bảo tính toàn vẹn dữ liệu ACID; tự động hóa kiểm thử phần mềm bằng PHPUnit & Laravel Feature Testing."));

    children.push(pHeading3("Khuyết điểm"));
    children.push(pBullet("Tốc độ xử lý của một số chức năng thống kê còn hơi chậm", "Khi cơ sở dữ liệu có lượng bản ghi đơn hàng và lịch sử lớn, các truy vấn thống kê báo cáo đa bảng (như Báo cáo doanh thu đa năm) có thể xảy ra độ trễ nhẹ do chưa áp dụng giải pháp lưu trữ bộ nhớ đệm phân tán Redis."));
    children.push(pBullet("Các chức năng quản lý kho chưa đa dạng và tự động hóa cao", "Mặc dù hệ thống đã có phân hệ Quản lý nguyên liệu và Cảnh báo ngưỡng tồn kho, nhưng chưa xây dựng được công thức định lượng chi tiết cho từng món ăn (Recipe / BOM) để tự động trừ lùi nguyên liệu thô trong kho ngay khi Bếp nấu xong."));
    children.push(pBullet("Chưa triển khai được trang web lên môi trường thực tế (Production)", "Dự án hiện tại mới chỉ được cài đặt, thử nghiệm và vận hành trên môi trường máy chủ cục bộ (Localhost trên máy tính cá nhân qua Laragon/PHP CLI), chưa được đóng gói Docker và đưa lên môi trường Internet công cộng (VPS / Cloud Server) có tên miền riêng và chứng chỉ SSL/TLS."));
    children.push(pBullet("Cơ sở dữ liệu và Code chưa được tối ưu hóa triệt để", "Một số bảng dữ liệu lớn trong MySQL chưa được đánh chỉ mục (Index) chuyên sâu cho các cột thường xuyên tìm kiếm; mã nguồn giao diện frontend vẫn còn một số đoạn script nội tuyến cần được tái cấu trúc thành các module độc lập."));
    children.push(pBullet("Độ bảo mật của trang Web vẫn chưa được cao ở mức quy mô doanh nghiệp lớn", "Dù đã ngăn chặn thành công các lỗ hổng cơ bản (SQL Injection, XSS, CSRF, BFCache leak), hệ thống vẫn chưa được trang bị tường lửa ứng dụng web (WAF), hệ thống chống tấn công DDoS chuyên dụng, cũng như chưa tích hợp xác thực hai yếu tố (2FA) cho tài khoản Quản trị viên."));

    children.push(pHeading3("Kinh nghiệm đúc kết"));
    children.push(pBullet("Cần nâng cao khả năng tự học các kiến thức mới", "Ngành công nghệ thông tin luôn biến đổi từng ngày; việc hình thành tác phong chủ động đọc tài liệu kỹ thuật chính thức (Official Documentation), nghiên cứu các phiên bản phần mềm mới (PHP 8.3, Laravel 13, Tailwind CSS v4) và tìm hiểu các công nghệ đột phá (AI Generative, WebSockets) là yếu tố sống còn để nhanh chóng làm chủ và ứng dụng công nghệ vào thực tiễn."));
    children.push(pBullet("Cần nâng cao thêm khả năng suy luận, tư duy logic và thiết kế hệ thống", "Nhận thức rõ ràng rằng giai đoạn phân tích thiết kế ban đầu (thiết kế cơ sở dữ liệu chuẩn hóa 3NF, mô hình hóa luồng dữ liệu nghiệp vụ, xác định quan hệ các thực thể) quyết định phần lớn sự thành công của dự án. Một thiết kế tốt sẽ giúp quá trình viết code mạch lạc, hạn chế tối đa lỗi logic và giảm thiểu chi phí bảo trì hệ thống về sau."));
    children.push(pBullet("Cải thiện hơn nữa về kiến thức chuyên môn", "Cần tiếp tục tích lũy và hoàn thiện các kiến thức nâng cao về cấu trúc dữ liệu và giải thuật, các mẫu thiết kế phần mềm (Design Patterns), kiến trúc hướng dịch vụ (Microservices), cơ chế cân bằng tải (Load Balancing) và an toàn thông tin mạng để sẵn sàng đáp ứng yêu cầu công việc tại các doanh nghiệp công nghệ lớn."));

    children.push(pHeading2("4.2\tĐịnh hướng phát triển"));
    children.push(pHeading3("Phát triển đề tài"));
    children.push(pBullet("1. Định lượng món & Tự động trừ kho (Recipe/BOM)", "Thiết lập công thức chế biến từng món, bếp nấu xong tự động trừ nguyên liệu tồn kho để quản lý giá vốn COGS và chống thất thoát."));
    children.push(pBullet("2. Cổng điều phối cho Tài xế (Driver Portal)", "Giao diện riêng cho shipper nhận đơn nấu xong, xem địa chỉ và chụp ảnh xác nhận giao hàng."));
    children.push(pBullet("3. Định vị tài xế Live GPS Tracking", "Truyền tọa độ GPS của shipper qua WebSockets lên bản đồ trang tra cứu kèm thời gian dự kiến giao hàng."));
    children.push(pBullet("4. Triển khai hệ thống lên hạ tầng đám mây Internet (Cloud Deployment)", "Đóng gói toàn bộ ứng dụng bằng Docker Container, triển khai lên máy chủ đám mây (AWS / DigitalOcean) với tên miền riêng và chứng chỉ SSL HTTPS."));
    children.push(pBullet("5. Tối ưu hóa hiệu năng với Redis Cache & Message Queue", "Ứng dụng Redis làm bộ nhớ đệm cho dữ liệu thực đơn, phân quyền người dùng và sử dụng hàng đợi (Queue Workers) để xử lý bất đồng bộ các tác vụ nặng."));
    children.push(pBullet("6. Hệ thống Hạng thành viên & Tích điểm (Loyalty Program)", "Tích điểm % giá trị đơn hàng, thăng hạng (Đồng, Bạc, Vàng, Kim Cương) để nhận voucher độc quyền."));

    children.push(pHeading3("Phát triển bản thân"));
    children.push(pBullet("Cần nâng cao khả năng tự học các kiến thức mới", "Chủ động nghiên cứu các xu hướng công nghệ mới như Điện toán đám mây (Cloud Native), Trí tuệ nhân tạo tạo sinh (GenAI Agents) và Công nghệ Web thời gian thực."));
    children.push(pBullet("Cần nâng cao thêm khả năng suy luận, tư duy logic, phân tích thiết kế hệ thống", "Rèn luyện tư duy phản biện, kỹ năng phân tích giải quyết vấn đề phức tạp và khả năng thiết kế hệ thống phần mềm quy mô lớn có độ chịu tải cao."));
    children.push(pBullet("Cải thiện hơn nữa về kiến thức chuyên môn", "Đi sâu tìm hiểu về kiến trúc phân tán (Microservices), tối ưu hóa cơ sở dữ liệu lớn (Big Data Optimization) và an toàn thông tin mạng (Cybersecurity)."));

    // ==========================================
    // PHỤ LỤC & TÀI LIỆU THAM KHẢO
    // ==========================================
    children.push(pageBreak());
    children.push(pHeading1("PHỤ LỤC"));
    children.push(pBody("Phụ lục trình bày đoạn mã nguồn Service xử lý Webhook PayOS xác thực chữ ký số HMAC-SHA256:"));
    children.push(pBody("public function handleWebhook(Request $request) {\n    $payload = json_decode($request->getContent(), true);\n    $data = $payload['data'];\n    ksort($data);\n    $signData = [];\n    foreach ($data as $key => $value) { $signData[] = \"{$key}={$value}\"; }\n    $computedSignature = hash_hmac('sha256', implode('&', $signData), config('services.payos.checksum_key'));\n    if (!hash_equals($computedSignature, $payload['signature'])) { return response()->json(['error' => 'Invalid signature'], 400); }\n    $order = Order::where('transaction_code', $data['orderCode'])->first();\n    if ($order && $order->payment_status !== 'paid') {\n        $order->update(['payment_status' => 'paid', 'order_status' => 'confirmed']);\n        broadcast(new DishUpdated('kitchen_new_order', ['order_id' => $order->id]));\n    }\n    return response()->json(['success' => true], 200);\n}"));

    children.push(pageBreak());
    children.push(pHeading1("DANH MỤC TÀI LIỆU THAM KHẢO"));
    children.push(pBody("[ 1 ] https://laravel.com/docs/13.x"));
    children.push(pBody("[ 2 ] https://www.php.net/manual/en/migration83.php"));
    children.push(pBody("[ 3 ] https://payos.vn/docs/"));
    children.push(pBody("[ 4 ] https://ai.google.dev/docs"));
    children.push(pBody("[ 5 ] https://spatie.be/docs/laravel-permission/"));
    children.push(pBody("[ 6 ] https://tailwindcss.com/docs"));
    children.push(pBody("[ 7 ] https://dev.mysql.com/doc/refman/8.0/en/"));
    children.push(pBody("[ 8 ] https://developer.mozilla.org/en-US/docs/Glossary/bfcache"));
    children.push(pBody("[ 9 ] https://stackoverflow.com/"));
    children.push(pBody("[ 10 ] https://w3schools.com/"));

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: FONT_NAME, size: 24, color: "000000" },
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
        console.log('Successfully created identical structure DOCX at:', outputPath);
        console.log('File size:', buffer.length, 'bytes');
    }).catch(err => {
        console.error('Error generating docx:', err);
    });
}

generateDocx();

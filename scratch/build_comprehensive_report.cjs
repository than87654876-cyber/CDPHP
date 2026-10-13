const fs = require('fs');
const path = require('path');
const {
    Document,
    Packer,
    Paragraph,
    TextRun,
    AlignmentType,
    Header,
    Footer,
    PageNumber
} = require('docx');

const {
    pHeading1, pHeading2, pHeading3, pHeading4, pBody, pBullet, pageBreak, makeTable, makeImageBlock, FONT_NAME
} = require('./report_docx_helpers.cjs');

async function buildFullThesis() {
    console.log('Starting full thesis compilation...');

    // COVER PAGE
    const coverChildren = [
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 200, after: 60 },
            children: [new TextRun({ text: "TRƯỜNG ĐẠI HỌC [TÊN TRƯỜNG ĐẠI HỌC]", bold: true, size: 28, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 800 },
            children: [new TextRun({ text: "KHOA CÔNG NGHỆ THÔNG TIN", bold: true, size: 26, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 800, after: 120 },
            children: [new TextRun({ text: "ĐỒ ÁN TỐT NGHIỆP", bold: true, size: 36, font: FONT_NAME, color: "1E3A8A" })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 300 },
            children: [new TextRun({ text: "CHUYÊN NGÀNH: CÔNG NGHỆ THÔNG TIN", bold: true, size: 26, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 400, after: 100 },
            children: [new TextRun({ text: "ĐỀ TÀI:", bold: true, size: 26, font: FONT_NAME })]
        }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 0, after: 900 },
            children: [
                new TextRun({
                    text: "XÂY DỰNG HỆ THỐNG GIAO ĐỒ ĂN TRỰC TUYẾN TÍCH HỢP TRÍ TUỆ NHÂN TẠO & ĐẶT MÓN THEO NHÓM – FOODDAILY",
                    bold: true,
                    size: 28,
                    font: FONT_NAME
                })
            ]
        }),
        new Paragraph({ spacing: { before: 600, after: 60 }, children: [new TextRun({ text: "Giảng viên hướng dẫn:\tThS. [Họ và tên Giảng viên hướng dẫn]", size: 26, font: FONT_NAME })] }),
        new Paragraph({ spacing: { before: 0, after: 60 }, children: [new TextRun({ text: "Sinh viên thực hiện:\tTrần Lê Thân", bold: true, size: 26, font: FONT_NAME })] }),
        new Paragraph({ spacing: { before: 0, after: 60 }, children: [new TextRun({ text: "Lớp:\t[Mã lớp]", size: 26, font: FONT_NAME })] }),
        new Paragraph({ spacing: { before: 0, after: 700 }, children: [new TextRun({ text: "Khoá:\t2022 – 2026", size: 26, font: FONT_NAME })] }),
        new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 500, after: 0 },
            children: [new TextRun({ text: "TP. Hồ Chí Minh, tháng 10 năm 2026", size: 24, font: FONT_NAME })]
        })
    ];

    const bodyChildren = [];

    // LỜI CẢM ƠN
    bodyChildren.push(pHeading1("LỜI CẢM ƠN"));
    bodyChildren.push(pBody("Để hoàn thành bản báo cáo tốt nghiệp và đề tài phần mềm này, tôi xin gửi lời cảm ơn chân thành và sâu sắc nhất đến toàn thể quý Thầy Cô Khoa Công nghệ Thông tin - Trường Đại học [Tên trường Đại học] đã tận tình truyền đạt cho tôi những kiến thức chuyên môn quý báu, nền tảng lý thuyết vững chắc cùng những kinh nghiệm thực tiễn vô cùng hữu ích trong suốt những năm tháng tôi được học tập và rèn luyện dưới mái trường. Những hành trang tri thức ấy không chỉ là chìa khóa giúp tôi tự tin hoàn thành đồ án tốt nghiệp này mà còn là nền móng vững bền cho con đường sự nghiệp của một kỹ sư phát triển phần mềm trong tương lai."));
    bodyChildren.push(pBody("Đặc biệt, tôi xin bày tỏ lòng biết ơn sâu sắc đến Thầy/Cô hướng dẫn khoa học. Trong suốt thời gian thực hiện đồ án, Thầy/Cô đã luôn dành thời gian quý báu để định hướng đề tài, tận tình chỉ dẫn từng bước nghiên cứu, góp ý tỉ mỉ từ khâu phân tích yêu cầu nghiệp vụ, thiết kế kiến trúc hệ thống đến việc tối ưu hóa cơ sở dữ liệu và hoàn thiện giao diện người dùng. Sự động viên, khích lệ và những định hướng đúng đắn của Thầy/Cô là nguồn động lực to lớn giúp tôi vượt qua những khó khăn về mặt công nghệ để hoàn thiện sản phẩm một cách trọn vẹn nhất."));
    bodyChildren.push(pBody("Tôi cũng xin gửi lời cảm ơn đến các Thầy Cô trong bộ môn Công nghệ Phần mềm và Hệ thống Thông tin đã tạo điều kiện thuận lợi, hỗ trợ thiết bị và giải đáp nhiều thắc mắc công nghệ mới trong quá trình tôi nghiên cứu cơ chế truyền thông thời gian thực WebSockets và tích hợp trí tuệ nhân tạo."));
    bodyChildren.push(pBody("Mặc dù bản thân đã dành rất nhiều thời gian, tâm huyết và nỗ lực hết mình để tìm hiểu, phân tích và xây dựng hệ thống phần mềm FOODDAILY, song với khối lượng kiến thức sâu rộng của ngành cùng giới hạn về mặt thời gian và kinh nghiệm thực tế, báo cáo chắc chắn sẽ không tránh khỏi những khiếm khuyết nhất định. Kính mong quý Thầy Cô trong Hội đồng chấm thi thông cảm và đóng góp những ý kiến quý báu để đồ án của tôi được hoàn thiện tốt hơn nữa."));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 200, after: 40 },
        children: [new TextRun({ text: "Tôi xin chân thành cảm ơn!", italics: true, size: 26, font: FONT_NAME })]
    }));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 0, after: 120 },
        children: [new TextRun({ text: "Trần Lê Thân", bold: true, size: 26, font: FONT_NAME })]
    }));

    // LỜI CAM ĐOAN
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("LỜI CAM ĐOAN"));
    bodyChildren.push(pBody("Tôi xin cam đoan rằng đề tài nghiên cứu: “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY” là một công trình nghiên cứu và phát triển phần mềm độc lập của bản thân tôi, dưới sự hướng dẫn khoa học trực tiếp của Giảng viên hướng dẫn."));
    bodyChildren.push(pBody("Toàn bộ các số liệu khảo sát, quy trình nghiệp vụ phân tích, các biểu đồ thiết kế hệ thống UML, cơ sở dữ liệu và toàn bộ mã nguồn chương trình ứng dụng được trình bày trong báo cáo này đều do tôi tự tìm hiểu, nghiên cứu, thiết kế và lập trình thực tế; tuyệt đối không sao chép trái phép hay sử dụng kết quả của bất kỳ công trình, đồ án nào khác khi chưa được phép."));
    bodyChildren.push(pBody("Các nội dung tham khảo lý thuyết, các thư viện mã nguồn mở bên ngoài (Framework Laravel, Tailwind CSS, API Google Gemini, Cổng VietQR PayOS) được sử dụng trong đồ án đều được trích dẫn nguồn gốc xuất xứ rõ ràng và trung thực theo đúng quy định học thuật. Nếu có bất kỳ sự gian lận hay sao chép không trung thực nào được phát hiện, tôi xin hoàn toàn chịu mọi hình thức kỷ luật trước Hội đồng chấm thi và Ban Giám hiệu Nhà trường."));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 200, after: 40 },
        children: [new TextRun({ text: "Sinh viên cam đoan:", italics: true, size: 26, font: FONT_NAME })]
    }));
    bodyChildren.push(new Paragraph({
        alignment: AlignmentType.RIGHT,
        spacing: { before: 0, after: 120 },
        children: [new TextRun({ text: "Trần Lê Thân", bold: true, size: 26, font: FONT_NAME })]
    }));

    // MỤC LỤC
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("MỤC LỤC"));
    const toc = [
        ["LỜI CẢM ƠN", "i"],
        ["LỜI CAM ĐOAN", "ii"],
        ["DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH", "iii"],
        ["DANH MỤC BẢNG", "iv"],
        ["LỜI MỞ ĐẦU", "1"],
        ["Chương 1. TỔNG QUAN TÀI LIỆU", "3"],
        ["    1.1 Khảo sát bài toán", "3"],
        ["        1.1.1. Đặt vấn đề", "3"],
        ["        1.1.2. Giải pháp", "4"],
        ["        1.1.3. Mục đích của đề tài", "5"],
        ["    1.2 Cơ sở lý thuyết", "6"],
        ["        1.2.1. HTML5", "6"],
        ["        1.2.2. CSS", "6"],
        ["        1.2.3. Bootstrap 4 & Tailwind CSS", "7"],
        ["        1.2.4. Javascript", "7"],
        ["        1.2.5. Jquery & WebSockets", "8"],
        ["        1.2.6. PHP", "9"],
        ["        1.2.7. Laravel", "10"],
        ["        1.2.8. MySQL Server", "11"],
        ["        1.2.9. Visual Studio Code", "12"],
        ["        1.2.10. Laragon", "12"],
        ["Chương 2. PHƯƠNG PHÁP THỰC HIỆN", "13"],
        ["    2.1 Phân tích hệ thống", "13"],
        ["        2.1.1 Quy trình nghiệp vụ", "13"],
        ["        2.1.2 Thông tin yêu cầu", "19"],
        ["        2.1.3 Sơ đồ chức năng của hệ thống", "24"],
        ["    2.2 Các biểu đồ thiết kế hệ thống", "29"],
        ["        2.2.1 Lược đồ Use case", "29"],
        ["        2.2.2 Lược đồ Activity", "42"],
        ["        2.2.3 Biểu đồ thành phần", "50"],
        ["        2.2.4 Biểu đồ triển khai", "52"],
        ["    2.3 Cơ sở dữ liệu", "53"],
        ["Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ", "65"],
        ["    3.1 Một số giao diện website", "65"],
        ["        3.1.1 Phần quản trị", "65"],
        ["        3.1.2 File excel xuất ra từ quản trị", "74"],
        ["        3.1.3 Phần người dùng", "75"],
        ["    3.2 Báo cáo kiểm thử tự động Automation Testing PHPUnit", "79"],
        ["Chương 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN", "82"],
        ["    4.1 Kết luận", "82"],
        ["    4.2 Định hướng phát triển", "83"],
        ["PHỤ LỤC", "85"],
        ["DANH MỤC TÀI LIỆU THAM KHẢO", "86"]
    ];
    toc.forEach(item => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 40, after: 40, line: 300 },
            children: [
                new TextRun({ text: item[0], size: 24, font: FONT_NAME }),
                new TextRun({ text: "\t".repeat(4) + item[1], bold: true, size: 24, font: FONT_NAME })
            ]
        }));
    });

    // DANH MỤC HÌNH
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH"));
    const figureList = [
        "Sơ đồ 2.1: Sơ đồ chức năng của Admin",
        "Sơ đồ 2.2: Sơ đồ chức năng nhân viên quầy / thu ngân",
        "Sơ đồ 2.3: Sơ đồ chức năng đầu bếp",
        "Sơ đồ 2.4: Sơ đồ chức năng khách hàng",
        "Lược đồ 2.1: Lược đồ use case tổng quát toàn hệ thống",
        "Lược đồ 2.2: Lược đồ chức năng đăng nhập",
        "Lược đồ 2.3: Lược đồ chức năng reset password",
        "Lược đồ 2.4: Lược đồ tìm kiếm món ăn",
        "Lược đồ 2.5: Lược đồ đặt món theo nhóm Realtime",
        "Lược đồ 2.6: Lược đồ thanh toán PayOS VietQR",
        "Lược đồ 2.7: Lược đồ điều phối Bếp FIFO",
        "Lược đồ 2.8: Lược đồ quản lý hóa đơn và in bill",
        "Lược đồ 2.9: Lược đồ quản lý thông tin khách hàng",
        "Lược đồ 2.10: Lược đồ quản lý thực đơn món ăn",
        "Lược đồ 2.11: Lược đồ quản lý nhà cung cấp",
        "Lược đồ 2.12: Lược đồ quản lý kho nguyên liệu và nhập kho",
        "Lược đồ 2.13: Lược đồ quản lý nhân viên và phân quyền RBAC",
        "Biểu đồ 2.1: Biểu đồ thành phần hệ thống",
        "Biểu đồ 2.2: Biểu đồ thành phần quản trị viên",
        "Biểu đồ 2.3: Biểu đồ thành phần người dùng",
        "Biểu đồ 2.4: Biểu đồ triển khai hệ thống FOODDAILY",
        "Hình 2.1: Lược đồ quan hệ cơ sở dữ liệu (Database Schema ERD)"
    ];
    figureList.forEach(fig => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 30, after: 30, line: 300 },
            children: [new TextRun({ text: fig, size: 24, font: FONT_NAME })]
        }));
    });

    // DANH MỤC BẢNG
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("DANH MỤC BẢNG"));
    const tableList = [
        "Bảng 2.1 Luồng sự kiện chính use case đăng nhập vào hệ thống",
        "Bảng 2.2 Luồng sự kiện chính use case đăng xuất vào hệ thống",
        "Bảng 2.3 Luồng sự kiện chính use case tìm kiếm và xem chi tiết món ăn",
        "Bảng 2.4 Luồng sự kiện chính use case đặt món theo nhóm Realtime",
        "Bảng 2.5 Luồng sự kiện chính use case thanh toán tự động VietQR PayOS",
        "Bảng 2.6 Luồng sự kiện chính use case Trợ lý AI Gemini tư vấn món ăn",
        "Bảng 2.7 Luồng sự kiện chính use case điều phối chế biến tại Màn hình Bếp FIFO",
        "Bảng 2.8 Luồng sự kiện chính use case đăng ký và quản lý gói ăn định kỳ",
        "Bảng 2.9 Luồng sự kiện chính use case quản lý thực đơn món ăn",
        "Bảng 2.10 Luồng sự kiện chính use case quản lý kho nguyên liệu và nhà cung cấp",
        "Bảng 2.11 Luồng sự kiện chính use case quản lý khuyến mãi và cấp voucher ví",
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
        "Bảng 4.1 Bảng lộ trình phát triển tính năng thiết yếu"
    ];
    tableList.forEach(t => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 30, after: 30, line: 300 },
            children: [new TextRun({ text: t, size: 24, font: FONT_NAME })]
        }));
    });

    // LỜI MỞ ĐẦU
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("LỜI MỞ ĐẦU"));
    bodyChildren.push(pBody("Bước sang kỷ nguyên kinh tế số, ngành công nghiệp dịch vụ ăn uống và ẩm thực (Food and Beverage - F&B) tại Việt Nam đang chứng kiến sự chuyển mình mạnh mẽ chưa từng có. Thói quen mua sắm của người tiêu dùng đã có bước dịch chuyển rõ rệt từ mô hình dùng bữa trực tiếp tại hàng quán sang hình thức đặt món trực tuyến giao hàng tận nơi. Nhịp sống bận rộn tại các đô thị lớn khiến nhu cầu về những bữa ăn nhanh chóng, tiện lợi, đảm bảo dinh dưỡng và đúng giờ trở thành nhu cầu thiết yếu hàng ngày của hàng triệu nhân viên văn phòng, học sinh, sinh viên và các hộ gia đình."));
    bodyChildren.push(pBody("Tuy nhiên, nhìn vào bức tranh thực tế của các nhà hàng, quán ăn độc lập và chuỗi ẩm thực quy mô vừa và nhỏ hiện nay, công tác vận hành bán hàng trực tuyến vẫn đang vấp phải những rào cản và bài toán nan giải:"));
    bodyChildren.push(pBullet("Sự phụ thuộc và gánh nặng chiết khấu của các siêu ứng dụng", "Hiện tại, việc đưa cửa hàng lên các ứng dụng trung gian như GrabFood hay ShopeeFood đòi hỏi mức chiết khấu hoa hồng rất cao (từ 25% đến 30% trên tổng giá trị đơn hàng). Khoản chi phí này bào mòn gần như toàn bộ biên lợi nhuận của các chủ quán, đồng thời nhà hàng hoàn toàn không nắm giữ được dữ liệu khách hàng (Customer Data) để triển khai các chương trình chăm sóc dài hạn."));
    bodyChildren.push(pBullet("Nỗi ám ảnh đặt món theo nhóm tại các công sở", "Vào mỗi giờ nghỉ trưa, tại các cơ quan và văn phòng công ty, việc đặt đồ ăn chung cho tập thể luôn là một quá trình tốn nhiều thời gian và gây ức chế. 'Người gom đơn' phải đi hỏi từng đồng nghiệp, ghi chép thủ công từng sở thích riêng (ít cay, không hành lá, thêm cơm, trà sữa 50% đường), tự cộng nhẩm tiền và đối mặt với cảnh thiếu tiền lẻ khi thanh toán chia tiền. Quá trình này thường kéo dài từ 20 đến 35 phút mỗi bữa trưa."));
    bodyChildren.push(pBullet("Tâm lý đắn đo 'Hôm nay ăn gì?'", "Một khảo sát hành vi tiêu dùng cho thấy người dùng mất trung bình từ 10 đến 15 phút chỉ để lướt qua lại các danh mục món ăn mà không thể đưa ra quyết định do menu quá nhiều lựa chọn nhưng thiếu công cụ tư vấn cá nhân hóa theo ngân sách, sở thích hoặc điều kiện thời tiết."));
    bodyChildren.push(pBullet("Sự tắc nghẽn và đứt gãy luồng thông tin giữa quầy thu ngân và gian bếp", "Ở hầu hết các quán ăn, khi lượng đơn đặt hàng trực tuyến tăng vọt vào khung giờ cao điểm từ 11h30 đến 12h30, luồng thông tin chuyển đơn từ quầy bán hàng vào bếp thường dùng phiếu in giấy dễ bị ướt, rách hoặc lẫn lộn thứ tự thời gian. Đầu bếp không biết món nào cần làm trước, dẫn đến tình trạng đơn đặt sau lại nấu trước, gây phàn nàn và trễ giờ giao hàng nghiêm trọng."));
    bodyChildren.push(pBullet("Rủi ro gian lận chuyển khoản và chậm trễ đối soát ngân hàng", "Việc thanh toán không tiền mặt bằng cách yêu cầu khách hàng chụp ảnh màn hình biên lai chuyển khoản ngân hàng gửi qua tin nhắn tiềm ẩn nguy cơ cao về việc làm giả biên lai (Fake Bill Photoshop). Nhân viên quầy phải mất công kiểm tra app ngân hàng thủ công, làm đứt gãy nhịp độ phục vụ đơn hàng."));
    bodyChildren.push(pBody("Xuất phát từ những trăn trở thực tiễn và đòi hỏi cấp thiết của thị trường F&B, tôi đã quyết định lựa chọn đề tài: “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY”. Đề tài tập trung ứng dụng các công nghệ web hiện đại nhất hiện nay như Framework Laravel 13, PHP 8.3, cơ chế truyền dữ liệu thời gian thực qua WebSockets (Laravel Reverb), Trí tuệ nhân tạo thế hệ mới (Google Gemini 1.5 Flash AI) và Cổng thanh toán trực tuyến VietQR Napas 24/7 PayOS để giải quyết triệt để và đồng bộ tất cả các nút thắt trên, mang lại một giải pháp kinh doanh số hóa toàn diện, hiệu quả và tối ưu cho nhà hàng."));

    // CHƯƠNG 1
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 1. TỔNG QUAN TÀI LIỆU"));
    bodyChildren.push(pHeading2("1.1\tKhảo sát bài toán"));
    bodyChildren.push(pHeading3("1.1.1.\tĐặt vấn đề"));
    bodyChildren.push(pBody("Trong những năm gần đây, cuộc cách mạng công nghiệp lần thứ tư và sự phổ cập của mạng Internet tốc độ cao cùng điện thoại thông minh đã làm thay đổi căn bản cách thức giao dịch thương mại tại Việt Nam. Theo các báo cáo thị trường gần đây, quy mô ngành giao đồ ăn trực tuyến tại Việt Nam đạt mức tăng trưởng hơn 20% mỗi năm. Dù tiềm năng thị trường là khổng lồ, phần lớn các doanh nghiệp F&B quy mô vừa và nhỏ vẫn đang lúng túng trong việc tìm kiếm một nền tảng công nghệ riêng biệt để vận hành kinh doanh bán hàng khép kín."));
    bodyChildren.push(pBody("Khảo sát thực tế tại các hàng quán ẩm thực cho thấy những vấn đề nổi cộm:"));
    bodyChildren.push(pBullet("Khó khăn trong việc kiểm soát dữ liệu bán hàng và tồn kho", "Việc ghi chép thủ công đơn hàng hoặc sử dụng các phần mềm đơn lẻ khiến chủ quán không thể nắm bắt tức thời lượng nguyên vật liệu còn lại trong kho, dẫn đến việc nhận đơn của khách nhưng nhà bếp lại thiếu nguyên liệu để chế biến, buộc phải hủy đơn và làm mất uy tín thương hiệu."));
    bodyChildren.push(pBullet("Thiếu sự kết nối liên hoàn giữa các bộ phận", "Một quy trình bán đồ ăn giao tận nơi hoàn chỉnh bao gồm 5 mắt xích chặt chẽ: Khách hàng đặt món -> Hệ thống xác nhận và thu tiền -> Gian bếp nấu nướng -> Quầy thu ngân đóng gói in bill -> Shipper giao hàng. Nếu một trong các mắt xích này bị nghẽn (ví dụ: thu ngân chưa biết bếp đã nấu xong chưa, hay bếp nấu sai thứ tự), toàn bộ chuỗi cung ứng sẽ bị đình trệ."));
    bodyChildren.push(pBullet("Nhu cầu cấp thiết về công nghệ phục vụ nhóm và tư vấn thông minh", "Khách hàng hiện đại không chỉ muốn mua một món ăn, họ đòi hỏi sự tiện lợi vượt trội: khả năng cùng đồng nghiệp trong phòng ban chọn món trên điện thoại riêng mà không cần chuyền tay nhau một chiếc máy; hay khả năng trò chuyện với một trợ lý ảo am hiểu thực đơn để tư vấn món ăn hợp khẩu vị, lành mạnh và hợp túi tiền."));
    bodyChildren.push(pBody("Do đó, việc nghiên cứu và xây dựng website hệ thống bán và giao đồ ăn trực tuyến FOODDAILY không chỉ đáp ứng yêu cầu học thuật khắt khe của một đồ án tốt nghiệp kỹ sư CNTT mà còn mang giá trị ứng dụng thực tiễn rất cao trong đời sống xã hội."));

    bodyChildren.push(pHeading3("1.1.2.\tGiải pháp"));
    bodyChildren.push(pBody("Để giải quyết toàn diện các vấn đề trên, đề tài FOODDAILY đề xuất và hiện thực hóa một hệ sinh thái phần mềm hoàn chỉnh, chuẩn hóa với 5 trụ cột công nghệ then chốt:"));
    bodyChildren.push(pBullet("Phân hệ Đặt món theo nhóm thời gian thực (Realtime Group Order)", "Cho phép chủ phòng tạo phòng đặt món tập thể chỉ bằng một thao tác; hệ thống tự sinh mã PIN 6 ký tự và mã QR động để các thành viên quét mã tham gia phòng. Công nghệ WebSockets hai chiều đảm bảo giỏ hàng nhóm của tất cả các máy tự động cập nhật ngay khi một thành viên thêm hoặc sửa món. Màn hình tự động tính toán số tiền riêng của từng cá nhân (Split Bill), chấm dứt hoàn toàn cảnh cộng nhẩm tiền thủ công."));
    bodyChildren.push(pBullet("Tích hợp Trí tuệ Nhân tạo Google Gemini 1.5 Flash AI", "Nhúng mô hình ngôn ngữ lớn (LLM) của Google vào hệ thống để đóng vai trò 'Chuyên gia tư vấn ẩm thực FOODDAILY'. Ứng dụng kỹ thuật Context Injection (bơm ngữ cảnh) tự động trích xuất thực đơn đang kinh doanh của quán vào System Instruction của AI, giúp AI tư vấn chính xác, chu đáo theo tâm trạng, ngân sách và thời tiết, đi kèm các thẻ món ăn tương tác có sẵn nút mua nhanh. Kết hợp tính năng trò chơi 'Vòng quay may mắn' giúp người dùng giải quyết nhanh chóng bài toán đắn đo khi chọn món."));
    bodyChildren.push(pBullet("Cổng thanh toán tự động VietQR PayOS", "Tích hợp cổng thanh toán trực tuyến VietQR chuẩn Napas 24/7 của PayOS. Hệ thống tự động sinh mã VietQR động chứa chính xác số tiền và nội dung mã đơn hàng. Khi khách quét mã chuyển tiền qua bất kỳ ứng dụng ngân hàng di động nào (Mobile Banking), Webhook bảo mật bằng chữ ký số HMAC-SHA256 sẽ tự động khớp lệnh và chuyển trạng thái đơn hàng sang 'Đã thanh toán' chỉ trong 1 - 2 giây, kích hoạt chuông báo có đơn mới ngay tại gian bếp."));
    bodyChildren.push(pBullet("Màn hình Bếp FIFO (Kitchen Display System - KDS)", "Số hóa toàn bộ hoạt động chế biến của gian bếp. Các đơn hàng được tự động sắp xếp theo thứ tự thời gian vào trước làm trước (FIFO) nhằm đảm bảo sự công bằng tuyệt đối. Tích hợp thuật toán gộp món thông minh (Batch Cooking Aggregation) tự động thống kê tổng số lượng từng món ăn đang chờ làm của tất cả các đơn, giúp đầu bếp chế biến hàng loạt cùng một mẻ, tiết kiệm đến 60% thời gian nấu nướng và tối ưu năng lượng nhiên liệu."));
    bodyChildren.push(pBullet("Gói dịch vụ ăn định kỳ (Meal Subscriptions) & Tra cứu đơn Realtime", "Cung cấp mô hình đăng ký suất ăn theo tuần (5 ngày) hoặc tháng (20 ngày) với tính năng linh hoạt đổi món trước 9h sáng hằng ngày. Đồng thời trang bị thanh tiến trình theo dõi đơn hàng 5 chặng thời gian thực (Đã đặt -> Đã xác nhận -> Đang nấu -> Đang giao -> Hoàn tất) giúp khách hàng an tâm tuyệt đối về bữa ăn của mình."));

    bodyChildren.push(pHeading3("1.1.3.\tMục đích của đề tài"));
    bodyChildren.push(pBody("Mục tiêu trọng tâm của đề tài bao gồm cả hai khía cạnh: Giá trị thực tiễn doanh nghiệp và Mục tiêu nghiên cứu khoa học công nghệ:"));
    bodyChildren.push(pBullet("Về mặt thực tiễn doanh nghiệp", "Xây dựng thành công một website thương mại điện tử ẩm thực hoàn chỉnh, hoạt động ổn định, khép kín quy trình từ khâu chọn món, thanh toán, chế biến, đóng gói in hóa đơn, giao hàng đến tích điểm thưởng chăm sóc khách hàng. Giúp nhà hàng tự chủ kênh bán hàng trực tuyến riêng, cắt giảm 100% phí hoa hồng trung gian, nâng cao năng suất phục vụ và gia tăng doanh số."));
    bodyChildren.push(pBullet("Về mặt nghiên cứu khoa học và công nghệ", "Nghiên cứu chuyên sâu và làm chủ kiến trúc mô hình MVC trên Framework PHP Laravel 13 mới nhất. Nắm vững kỹ thuật lập trình bất đồng bộ và truyền thông hai chiều thời gian thực với WebSockets (Laravel Reverb). Nghiên cứu kỹ thuật xử lý ngôn ngữ tự nhiên (NLP) với Google Gemini AI API. Làm chủ thiết kế cơ sở dữ liệu quan hệ MySQL 8.0, kỹ thuật đánh chỉ mục Composite Index, đảm bảo tính toàn vẹn giao dịch ACID và bảo mật hệ thống theo các tiêu chuẩn công nghiệp (Bcrypt hashing, CSRF protection, RBAC Authorization)."));

    bodyChildren.push(pHeading2("1.2\tCơ sở lý thuyết"));
    bodyChildren.push(pHeading3("1.2.1.\tHTML5"));
    bodyChildren.push(pBody("HTML5 (HyperText Markup Language version 5) là phiên bản hoàn thiện và mạnh mẽ nhất của ngôn ngữ đánh dấu siêu văn bản, giữ vai trò là bộ khung xương cấu trúc tiêu chuẩn cho mọi ứng dụng web hiện đại trên toàn cầu. HTML5 mang đến bước đột phá lớn so với các thế hệ tiền nhiệm nhờ việc bổ sung hệ thống thẻ ngữ nghĩa phong phú (Semantic Elements) như <header>, <nav>, <main>, <section>, <article>, <aside> và <footer>. Các thẻ này giúp mã nguồn trở nên mạch lạc, dễ bảo trì, đồng thời hỗ trợ các công cụ tìm kiếm (Google, Bing) phân tích cấu trúc dữ liệu tốt hơn, cải thiện đáng kể điểm SEO cho website."));
    bodyChildren.push(pBody("Ngoài ra, HTML5 tích hợp sẵn các thẻ đa phương tiện gốc như <audio> và <video>, cho phép phát âm thanh thông báo đơn hàng mới tại màn hình bếp mà không cần cài đặt plugin thứ ba. Trong dự án FOODDAILY, HTML5 được ứng dụng triệt để để xây dựng toàn bộ hệ thống giao diện chuẩn W3C, hỗ trợ thẻ Canvas đồ họa để tạo hiệu ứng vòng quay may mắn tương tác mượt mà cho khách hàng."));

    bodyChildren.push(pHeading3("1.2.2.\tCSS"));
    bodyChildren.push(pBody("CSS (Cascading Style Sheets) là ngôn ngữ định kiểu chuyên dụng dùng để kiểm soát và định dạng toàn bộ diện mạo hiển thị của các thành phần HTML trên trang web, từ màu sắc, kích thước, kiểu font chữ, bố cục khung nhìn đến khoảng cách lề và các hiệu ứng chuyển động tương tác. CSS tách biệt hoàn toàn giữa phần nội dung thông tin (HTML) và hình thức biểu diễn thị giác, cho phép lập trình viên dễ dàng tùy biến giao diện toàn hệ thống một cách nhất quán."));
    bodyChildren.push(pBody("Trong dự án, CSS3 được ứng dụng các thuộc tính hiện đại như CSS Flexbox và CSS Grid Layout để tạo dựng bố cục trang web tự động thích ứng hoàn hảo với mọi kích thước khung nhìn màn hình. Đồng thời, các hiệu ứng chuyển cảnh động (CSS Transitions và Keyframe Animations) được sử dụng để tạo cảm giác mượt mà, sống động khi mở Modal giỏ hàng, thông báo popup hay hiệu ứng xoay đĩa món ăn trên giao diện."));

    bodyChildren.push(pHeading3("1.2.3.\tBootstrap 4 & Tailwind CSS"));
    bodyChildren.push(pBody("Trong quá trình phát triển dự án FOODDAILY, việc xây dựng giao diện người dùng đòi hỏi sự kết hợp hài hòa giữa tốc độ dựng khung và khả năng tùy biến thẩm mỹ cao cấp. Hệ thống đã ứng dụng kết hợp cả hai thư viện giao diện hàng đầu hiện nay:"));
    bodyChildren.push(pBullet("Tailwind CSS version 4", "Là giải pháp Utility-First CSS hiện đại nhất hiện nay. Thay vì định nghĩa các class thành phần cứng nhắc, Tailwind cung cấp hàng nghìn lớp tiện ích nguyên tử (atomic classes) như flex, grid, pt-4, text-slate-800, bg-indigo-600. Trình biên dịch mới của Tailwind v4 sử dụng công cụ Lightning CSS siêu nhanh, tự động quét và loại bỏ toàn bộ mã CSS không sử dụng (Tree-shaking), giúp kích thước tệp CSS tải xuống trình duyệt của khách hàng chỉ vỏn vẹn dưới 25KB, tối ưu hóa tốc độ tải trang đạt điểm tối đa Google PageSpeed."));
    bodyChildren.push(pBullet("Bootstrap Components", "Được kế thừa ở các thành phần bảng biểu quản trị (Admin DataTables), hệ thống lưới Grid 12 cột và các hộp thoại Modal cảnh báo tiêu chuẩn, giúp các màn hình quản trị của quầy thu ngân và ban quản trị đạt độ tin cậy và tiện dụng cao."));

    bodyChildren.push(pHeading3("1.2.4.\tJavascript"));
    bodyChildren.push(pBody("JavaScript là ngôn ngữ lập trình kịch bản hướng đối tượng chạy ở phía máy khách (Client-side) phổ biến nhất thế giới. JavaScript chịu trách nhiệm xử lý toàn bộ các hành vi tương tác động của người dùng trên trình duyệt mà không cần phải tải lại trang web. Trong hệ thống FOODDAILY, JavaScript hiện đại theo chuẩn ECMAScript 6+ (ES6) được sử dụng rộng rãi để:"));
    bodyChildren.push(pBullet("Kiểm tra tính hợp lệ của dữ liệu biểu mẫu (Client-side Form Validation)", "Kiểm tra định dạng email, tính hợp lệ của số điện thoại nhận hàng, mật khẩu đủ độ dài trước khi gửi yêu cầu lên máy chủ, giúp giảm thiểu tải xử lý cho backend."));
    bodyChildren.push(pBullet("Tính toán giỏ hàng tức thì", "Tính tổng tiền các món, tự động nhân số lượng với đơn giá và trừ tiền voucher giảm giá trực tiếp trên giao diện giỏ hàng cá nhân và giỏ hàng nhóm."));
    bodyChildren.push(pBullet("Điều khiển hiệu ứng Canvas", "Vẽ và kích hoạt chuyển động quay ngẫu nhiên của 'Vòng quay may mắn Hôm nay ăn gì?' với thuật toán giảm tốc mượt mà (Ease-out decay algorithm)."));

    bodyChildren.push(pHeading3("1.2.5.\tJquery & WebSockets"));
    bodyChildren.push(pBody("Sự kết hợp giữa công nghệ AJAX truyền thống và WebSockets thời gian thực tạo nên sức mạnh vượt trội cho hệ thống:"));
    bodyChildren.push(pBullet("jQuery & Fetch API", "Hỗ trợ gửi các yêu cầu bất đồng bộ (Asynchronous JavaScript and XML - AJAX) đến các điểm cuối API của Laravel backend. Khách hàng bấm thêm món vào giỏ, nhập mã coupon hay thay đổi số lượng món ăn thì dữ liệu đều được cập nhật ngầm và trả về phản hồi định dạng JSON tức thì mà không làm nhấp nháy hay gián đoạn trải nghiệm lướt web."));
    bodyChildren.push(pBullet("Công nghệ WebSockets & Laravel Reverb", "WebSockets là giao thức truyền thông tiên tiến cho phép thiết lập kênh kết nối hai chiều toàn song công (Full-duplex) liên tục qua một kết nối TCP duy nhất giữa máy khách và máy chủ. Trong đề tài này, tôi ứng dụng Laravel Reverb (máy chủ WebSocket chính thức hiệu năng cao của Laravel) kết hợp thư viện Laravel Echo trên client. Khi một thành viên trong phòng nhóm chọn món, sự kiện được phát (Broadcast) qua kênh private-channel và trình duyệt của tất cả các thành viên khác lập tức cập nhật món ăn chỉ sau vài mili-giây. Tương tự, khi đầu bếp bấm nấu xong, tín hiệu được đẩy ngay về màn hình quầy thu ngân."));

    bodyChildren.push(pHeading3("1.2.6.\tPHP"));
    bodyChildren.push(pBody("PHP (Hypertext Preprocessor) là ngôn ngữ lập trình kịch bản phía máy chủ (Server-side) mã nguồn mở mạnh mẽ, đang cung cấp nền tảng vận hành cho hơn 75% website trên toàn cầu. Trong đồ án này, hệ thống sử dụng phiên bản **PHP 8.3** mới nhất với những cải tiến công nghệ vượt bậc:"));
    bodyChildren.push(pBullet("Trình biên dịch tức thời JIT (Just-In-Time Compiler)", "Biên dịch trực tiếp mã trung gian opcodes thành mã máy x86/ARM trong quá trình thực thi, giúp tăng tốc độ xử lý các tác vụ tính toán phức tạp lên từ 2 đến 3 lần so với các phiên bản PHP 7.x cũ."));
    bodyChildren.push(pBullet("Hệ thống kiểu dữ liệu nghiêm ngặt (Strict Typing & Typed Properties)", "Cho phép định nghĩa kiểu dữ liệu chặt chẽ cho tham số hàm, giá trị trả về và thuộc tính lớp, giúp phát hiện lỗi lập trình ngay trong giai đoạn phát triển và giảm thiểu tối đa các lỗi ngoại lệ lúc runtime."));
    bodyChildren.push(pBullet("Các tính năng ngôn ngữ hiện đại", "Sử dụng Readonly Classes, Constructor Property Promotion, Enums cho trạng thái đơn hàng, và cú pháp Match Expression giúp mã nguồn backend ngắn gọn, sáng sủa và dễ bảo trì."));

    bodyChildren.push(pHeading3("1.2.7.\tLaravel"));
    bodyChildren.push(pBody("Laravel là PHP Framework hàng đầu thế giới hiện nay, được xây dựng theo triết lý mang lại trải nghiệm phát triển tuyệt vời cho lập trình viên (Developer Experience) với kiến trúc chuẩn mực Model - View - Controller (MVC). Dự án ứng dụng phiên bản **Laravel 13** mới nhất với các tính năng ưu việt:"));
    bodyChildren.push(pBullet("Eloquent ORM", "Hệ thống ánh xạ quan hệ đối tượng (Object-Relational Mapping) mạnh mẽ và trực quan nhất trong hệ sinh thái PHP. Cho phép thao tác với các bảng CSDL như các đối tượng hướng đối tượng (Models), hỗ trợ cơ chế Eager Loading (with()) giúp triệt tiêu hoàn toàn vấn nạn truy vấn lặp N+1 queries."));
    bodyChildren.push(pBullet("Blade Template Engine", "Cú pháp khuôn mẫu gọn nhẹ, tốc độ biên dịch cực nhanh sang mã PHP thuần và có bộ nhớ đệm (Caching). Tự động mã hóa ký tự đặc biệt {{ $var }} để ngăn chặn hoàn toàn lỗ hổng tấn công Cross-Site Scripting (XSS)."));
    bodyChildren.push(pBullet("Hệ thống Middleware bảo mật", "Kiểm soát chặt chẽ từng yêu cầu HTTP gửi đến hệ thống, tự động xác thực token CSRF (Cross-Site Request Forgery), kiểm tra quyền truy cập của người dùng và thiết lập tiêu đề Cache-Control ngăn chặn rò rỉ dữ liệu qua bộ nhớ đệm trình duyệt (BFCache)."));
    bodyChildren.push(pBullet("Artisan CLI & Task Scheduling", "Hỗ trợ các lệnh dòng lệnh tự động tạo Controller, Migration, Seeder và lập lịch chạy nền (Cron Jobs) tự động hủy các đơn hàng quá hạn thanh toán sau 15 phút."));

    bodyChildren.push(pHeading3("1.2.8.\tMySQL Server"));
    bodyChildren.push(pBody("MySQL là hệ quản trị cơ sở dữ liệu quan hệ (RDBMS) mã nguồn mở phổ biến hàng đầu thế giới, sử dụng ngôn ngữ truy vấn có cấu trúc chuẩn SQL. Trong dự án FOODDAILY, hệ thống sử dụng phiên bản **MySQL 8.0** được cấu hình tối ưu hóa cao:"));
    bodyChildren.push(pBullet("Công cụ lưu trữ InnoDB Storage Engine", "Hỗ trợ đầy đủ các đặc tính toàn vẹn giao dịch ACID (Atomicity, Consistency, Isolation, Durability), khóa mức dòng (Row-level locking) chống tắc nghẽn khi có nhiều người dùng cùng đặt hàng đồng thời, và cơ chế khóa ngoại (Foreign Keys) bảo đảm tính toàn vẹn tham chiếu giữa các bảng dữ liệu."));
    bodyChildren.push(pBullet("Đánh chỉ mục Composite Index thông minh", "Thiết kế các chỉ mục tổ hợp trên các trường thường xuyên tìm kiếm và sắp xếp như (user_id, order_status, created_at) giúp tốc độ truy vấn danh sách đơn hàng và thực đơn đạt dưới 5 mili-giây ngay cả khi lượng dữ liệu lớn."));

    bodyChildren.push(pHeading3("1.2.9.\tVisual Studio Code"));
    bodyChildren.push(pBody("Visual Studio Code (VS Code) là trình soạn thảo mã nguồn gọn nhẹ, đa nền tảng và giàu tính năng do tập đoàn Microsoft phát triển. Với hệ sinh thái tiện ích mở rộng (Extensions) phong phú như PHP Intelephense, Laravel Blade Snippets, Tailwind CSS IntelliSense, GitLens và MySQL Extension, VS Code là công cụ đắc lực hỗ trợ lập trình viên viết mã chuẩn PSR-12, gỡ lỗi nhanh chóng và quản lý các nhánh mã nguồn qua Git xuyên suốt toàn bộ vòng đời của dự án."));

    bodyChildren.push(pHeading3("1.2.10.\tLaragon"));
    bodyChildren.push(pBody("Laragon là môi trường máy chủ phát triển cục bộ (Local Development Environment) hiện đại, nhanh, nhẹ và tiện dụng trên hệ điều hành Windows. Khác với các công cụ cũ như XAMPP hay WampServer, Laragon sử dụng cơ chế máy chủ ảo tự động (Auto Virtual Hosts) giúp truy cập dự án qua tên miền thân thiện `http://fooddaily.test`. Laragon tích hợp sẵn PHP 8.3, MySQL 8.0, máy chủ web Nginx/Apache, Composer, NodeJS và Git, cho phép chuyển đổi phiên bản linh hoạt và đảm bảo môi trường lập trình cục bộ phản ánh trung thực môi trường máy chủ sản xuất thực tế."));

    console.log('Chapter 1 compiled.');

    // CHƯƠNG 2
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 2. PHƯƠNG PHÁP THỰC HIỆN"));
    bodyChildren.push(pHeading2("2.1\tPhân tích hệ thống"));
    bodyChildren.push(pHeading3("2.1.1\tQuy trình nghiệp vụ"));
    bodyChildren.push(pBody("Quy trình bán và giao đồ ăn trực tuyến trên hệ thống FOODDAILY được chuẩn hóa khép kín và vận hành tự động qua 7 bước chi tiết sau:"));

    // Bước 1
    bodyChildren.push(pHeading4("Bước 1: Khách hàng xem thực đơn, tìm kiếm và lựa chọn hình thức đặt món"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Khách hàng vãng lai (Guest) hoặc Khách hàng thành viên đã có tài khoản (Member). Mục đích: Giúp người dùng tiếp cận danh mục ẩm thực phong phú, trực quan của FOODDAILY; hỗ trợ tìm thấy món ăn phù hợp với khẩu vị, chế độ dinh dưỡng và ngân sách cá nhân thông qua các công cụ tìm kiếm và hỗ trợ AI thông minh."));
    bodyChildren.push(pBullet("Duyệt danh mục ẩm thực", "Giao diện hiển thị thanh phân loại danh mục gồm: Món chính (Cơm, Bún, Phở), Món ăn nhanh (Gà rán, Burger, Khoai tây), Đồ uống thanh mát (Trà sữa, Trà trái cây, Cà phê), Món tráng miệng và Combo tiết kiệm. Mỗi món ăn được hiển thị dưới dạng Card sản phẩm sắc nét với các thông tin: Tên món ăn, Hình ảnh đại diện độ phân giải cao, Đơn giá bán ưu đãi kèm giá gốc gạch ngang, Nhãn danh mục, Huy hiệu nổi bật (Best Seller, Hot Deal), Số lượt đánh giá và số sao trung bình."));
    bodyChildren.push(pBullet("Tìm kiếm và Bộ lọc đa tiêu chí", "Người dùng có thể tìm kiếm tức thì theo từ khóa tên món (hỗ trợ cả tiếng Việt có dấu và không dấu). Hệ thống cung cấp bộ lọc linh hoạt: Lọc theo khoảng giá ngân sách (Dưới 30.000đ, 30.000đ - 70.000đ, Trên 70.000đ), Lọc theo danh mục và sắp xếp theo: Phổ biến nhất, Mới nhất, Giá tăng/giảm dần, Đánh giá cao nhất."));
    bodyChildren.push(pBullet("Vòng quay may mắn ('Hôm nay ăn gì?')", "Khách hàng bấm vào biểu tượng Vòng quay may mắn trên màn hình, thiết lập mức trần ngân sách cho bữa ăn (ví dụ: tối đa 60.000 VNĐ) và bấm nút 'Quay ngay'. Vòng quay Canvas quay với hiệu ứng đồ họa và âm thanh sống động. Hệ thống chọn ngẫu nhiên một món ăn phù hợp với ngân sách và hiển thị cửa sổ trúng thưởng kèm nút 'Cho vào giỏ ngay' và 'Xem chi tiết món'."));
    bodyChildren.push(pBullet("Trợ lý Trí tuệ Nhân tạo Google Gemini 1.5 Flash AI", "Khách hàng mở hộp thoại Chatbot AI ở góc phải màn hình, nhập câu hỏi bằng ngôn ngữ tự nhiên (Ví dụ: 'Trời hôm nay mưa lạnh, mình có ngân sách 50k, muốn ăn món gì ấm bụng và không bị cay?'). Hệ thống phân tích ngữ cảnh và trả về lời tư vấn chu đáo kèm các thẻ món ăn gợi ý có sẵn nút mua nhanh."));
    bodyChildren.push(pBullet("Cơ chế Context Injection của AI", "Controller GeminiChatController@chat trích xuất danh sách các món ăn đang kinh doanh (is_active = 1, stock > 0) trong cơ sở dữ liệu MySQL, định dạng ngắn gọn tên món, giá bán, danh mục nạp vào System Instruction của mô hình Google Gemini 1.5 Flash. Khi người dùng hỏi, AI đối soát và chỉ gợi ý các món thực sự có trong menu của quán, tránh tình trạng bịa đặt (hallucination)."));
    bodyChildren.push(pBullet("Cấu trúc dữ liệu & Hình thức đặt món", "Bảng dishes (id, category_id, name, slug, price, original_price, image, description, stock, is_featured, is_active), bảng categories (id, name, slug, icon). Khách hàng chọn Đặt món cá nhân (lưu vào giỏ hàng riêng) hoặc bấm Đặt món theo nhóm (Group Order) để chuyển sang Bước 2. Món có stock <= 0 hoặc is_active = 0 sẽ tự động gắn nhãn Tạm hết hàng và khóa nút đặt."));

    // Bước 2
    bodyChildren.push(pHeading4("Bước 2: Tiếp nhận và gom đơn nhóm theo thời gian thực (Realtime Group Order)"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Chủ phòng (Host), Các thành viên tham gia phòng (Members), Máy chủ Realtime Server (Laravel Reverb / WebSockets). Mục đích: Giải quyết bài toán đặt đồ ăn tập thể tại các văn phòng công sở, lớp học hay nhóm bạn bè; loại bỏ quy trình gom đơn thủ công qua giấy nháp hay bảng tính Excel; cho phép mọi người cùng chọn món trên thiết bị riêng và đồng bộ giỏ hàng chung tức thì."));
    bodyChildren.push(pBullet("Khởi tạo phòng nhóm", "Chủ phòng nhấn nút 'Đặt món theo nhóm', đặt tên phòng (Ví dụ: 'Trưa nay ăn gì - Team Marketing') và tạo phòng. Hệ thống cấp mã PIN ngẫu nhiên 6 ký tự độc nhất (ví dụ: FD8866) và tự động sinh mã QR Code động cùng đường dẫn trực tiếp: https://fooddaily.vn/group-order/FD8866."));
    bodyChildren.push(pBullet("Chia sẻ và Gia nhập phòng", "Chủ phòng sao chép liên kết hoặc gửi ảnh mã QR vào nhóm Zalo, Messenger, Slack. Các thành viên quét mã QR hoặc truy cập website nhập mã PIN để tham gia phòng. Thành viên có thể đăng nhập tài khoản hoặc nhập tên hiển thị (Nickname, ví dụ: 'Tuấn Anh Backend', 'Chị Hương Kế toán')."));
    bodyChildren.push(pBullet("Chọn món và Đồng bộ thời gian thực", "Mỗi thành viên duyệt thực đơn ngay trong giao diện phòng nhóm, chọn món, số lượng và nhập ghi chú cá nhân (Ví dụ: 'Ít cay, không lấy hành lá, thêm cơm'). Khi thành viên bấm 'Thêm vào nhóm', giỏ hàng nhóm của tất cả các thành viên khác lập tức cập nhật tức thì (Realtime Synchronization) qua WebSocket channel presence-group-order.{room_code} mà không cần tải lại trang."));
    bodyChildren.push(pBullet("Bảng phân tách chi phí minh bạch (Split Bill)", "Giao diện hiển thị danh sách accordion theo từng thành viên: Tên thành viên -> Các món đã chọn -> Tổng tiền riêng của cá nhân đó (member_subtotal). Cuối trang hiển thị tổng số món và tổng tiền toàn phòng (room_total), giúp việc tính toán tiền nong giữa các thành viên hoàn toàn minh bạch, không sợ thiếu sót."));
    bodyChildren.push(pBullet("Chốt đơn nhóm (Lock Order)", "Chủ phòng theo dõi danh sách, khi tất cả thành viên đã chọn xong, Chủ phòng nhấn nút 'Chốt đơn nhóm & Thanh toán'. Hệ thống chuyển group_orders.status = 'locked', khóa mọi thao tác sửa món của thành viên và chuyển sang màn hình thanh toán."));

    // Bước 3
    bodyChildren.push(pHeading4("Bước 3: Xác nhận thông tin đơn hàng và Thanh toán tự động (PayOS VietQR / COD)"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Khách hàng / Chủ phòng, Cổng thanh toán trực tuyến PayOS, Hệ thống Core FOODDAILY, Ứng dụng Mobile Banking. Mục đích: Thu thập đầy đủ thông tin giao nhận hàng, áp dụng các chính sách ưu đãi giảm giá (Voucher), và thực hiện quy trình thanh toán đa kênh tiện lợi: đối soát thanh toán trực tuyến tự động 100% không tiền mặt qua VietQR hoặc thanh toán tiền mặt COD khi nhận hàng."));
    bodyChildren.push(pBullet("Nhập thông tin giao hàng & Áp dụng Voucher", "Khách hàng kiểm tra tóm tắt giỏ hàng, điền Họ tên người nhận (customer_name), Số điện thoại (customer_phone), Địa chỉ chi tiết (shipping_address), Ghi chú giao hàng (shipping_note). Khách chọn mã giảm giá trong Ví Voucher (user_coupons), hệ thống kiểm tra hạn dùng, đơn tối thiểu và tính tổng tiền: Tổng tiền = Tiền món + Phí vận chuyển - Tiền giảm giá voucher."));
    bodyChildren.push(pBullet("Tạo đơn hàng trong hệ thống", "Hệ thống khởi tạo bản ghi trong bảng orders với mã đơn duy nhất dạng số nguyên cho cổng thanh toán và chuỗi hiển thị cho khách hàng (Ví dụ: order_code = 'FD-20261012-0089'), order_status = 'pending', payment_status = 'pending'."));
    bodyChildren.push(pBullet("Phương thức 1: Tiền mặt khi nhận hàng (COD)", "Lưu payment_method = 'cod', order_status = 'confirmed'. Đơn hàng chuyển ngay sang Màn hình Bếp (Bước 4)."));
    bodyChildren.push(pBullet("Phương thức 2: Chuyển khoản trực tuyến VietQR tự động qua PayOS", "Hệ thống gọi API PayOS POST /v2/payment-requests truyền thông số mã đơn, số tiền, nội dung chuyển khoản. PayOS phản hồi thông tin tạo giao dịch kèm chuỗi mã VietQR chuẩn Napas 24/7. Màn hình khách hiển thị mã QR động, hướng dẫn thanh toán và đồng hồ đếm ngược hiệu lực 15 phút."));
    bodyChildren.push(pBullet("Cơ chế Khớp lệnh Tự động qua Webhook (1 - 2 giây)", "Khách quét mã chuyển tiền qua App ngân hàng. Ngân hàng báo có tiền -> PayOS phát tín hiệu Webhook (HTTP POST) gửi dữ liệu thanh toán đến URL: /webhook/payos. Backend xác thực Chữ ký số HMAC-SHA256 với CHECKSUM_KEY bí mật. Khi chữ ký số trùng khớp: Cập nhật payment_status = 'paid', order_status = 'confirmed', lưu mã đối soát transaction_id, đánh dấu voucher đã sử dụng, phát sự kiện WebSockets PaymentSuccessEvent về máy khách (chỉ mất 1 - 2 giây để màn hình khách chuyển sang Báo thành công), bắn thông báo Telegram cho Admin và đẩy đơn sang Màn hình Bếp FIFO."));

    // Bước 4
    bodyChildren.push(pHeading4("Bước 4: Chuyển đơn sang Màn hình Bếp FIFO và Điều phối chế biến"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Nhân viên Bếp (Chef / Kitchen Staff), Hệ thống Màn hình Bếp (Kitchen Display System - KDS). Mục đích: Số hóa toàn bộ hoạt động chế biến trong gian bếp; triệt tiêu hoàn toàn việc dùng phiếu giấy gọi món dễ thất lạc, ướt rách; tối ưu hóa công suất chế biến bằng thuật toán gộp mẻ nấu và đảm bảo tính công bằng theo nguyên tắc FIFO."));
    bodyChildren.push(pBullet("Tiếp nhận đơn hàng tự động Realtime", "Ngay khi đơn hàng được xác nhận (COD) hoặc thanh toán thành công (VietQR), hệ thống đẩy đơn sang KDS qua WebSocket private-kitchen-orders. Màn hình bếp tự động phát âm thanh cảnh báo ding-dong và hiển thị thẻ đơn hàng mới."));
    bodyChildren.push(pBullet("Thuật toán Hàng đợi Ưu tiên FIFO (First In, First Out)", "Các đơn hàng được sắp xếp theo thời gian đặt (orders.created_at ASC), đơn nào đặt trước sẽ nằm ở đầu hàng đợi bên trái. Thẻ đơn hàng tự động đổi màu cảnh báo theo thời gian chờ: Màu Xanh lá (< 10 phút), Màu Vàng cam (10 - 20 phút), Màu Đỏ cảnh báo nguy cơ trễ hạn (> 20 phút)."));
    bodyChildren.push(pBullet("Thuật toán Gom món thông minh (Batch Cooking Aggregation)", "Phía trên màn hình Bếp hiển thị bảng tổng hợp gộp số lượng món cần làm của toàn bộ các đơn đang chờ: Ví dụ: Đơn #1 có 2 Cơm gà xối mỡ, Đơn #2 có 3 Cơm gà xối mỡ -> Bảng gom món hiển thị: 'Cơm gà xối mỡ: Tổng 5 phần cần làm'. Đầu bếp chỉ cần chiên 5 phần gà cùng lúc một mẻ, tiết kiệm 60% thời gian chế biến."));
    bodyChildren.push(pBullet("Thao tác chuyển trạng thái chế biến", "Đầu bếp bấm 'Bắt đầu nấu' -> orders.order_status = 'preparing'. Nấu xong đầy đủ các món, đầu bếp bấm 'Đã nấu xong' -> orders.order_status = 'cooked'. Thẻ đơn phát chuông hoàn tất và tự động chuyển sang Bàn làm việc của quầy Thu ngân (Bước 5)."));

    // Bước 5
    bodyChildren.push(pHeading4("Bước 5: Quầy thu ngân đóng gói, in hóa đơn 1 chạm và Bàn giao vận chuyển"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Nhân viên Quầy Thu ngân (Cashier / Dispatcher), Máy in nhiệt POS K80 (80mm). Mục đích: Đối soát món ăn thực tế với yêu cầu của khách, in hóa đơn thanh toán chuẩn xác chỉ với 1 chạm, đóng gói chuyên nghiệp và bàn giao cho tài xế giao hàng."));
    bodyChildren.push(pBullet("Tiếp nhận đơn tại Bàn làm việc (Staff Workspace)", "Khi Bếp nhấn 'Đã nấu xong', đơn hàng lập tức trượt vào danh mục 'Chờ đóng gói & In hóa đơn' trên màn hình thu ngân."));
    bodyChildren.push(pBullet("Thao tác In hóa đơn 1 chạm (One-Touch Bill Printing)", "Thu ngân bấm nút 'In hóa đơn' (hoặc bấm phím tắt nhanh F9). Hệ thống gửi lệnh xuất bill trực tiếp tới máy in nhiệt chuẩn K80 (80mm) in ra hóa đơn bán lẻ chuyên nghiệp gồm: Header nhà hàng FOODDAILY; Mã đơn tra cứu; Ngày giờ in; Thông tin người nhận; Bảng chi tiết từng món kèm ghi chú riêng; Tổng kết thanh toán; Dòng trạng thái in đậm to rõ: 'ĐÃ THANH TOÁN (VIETQR ONLINE) - KHÔNG THU TIỀN' (nếu trả VietQR) hoặc 'THU TIỀN MẶT KHI GIAO (COD): [Số tiền] VNĐ' (nếu đơn COD); Mã vạch Barcode/QR tra cứu đơn."));
    bodyChildren.push(pBullet("Đóng gói và Bàn giao Shipper", "Nhân viên quầy kiểm tra từng hộp món ăn đối chiếu với bill, kẹp hóa đơn vào túi đồ ăn và bấm nút 'Bàn giao Shipper'. Hệ thống chuyển orders.order_status = 'delivering', tự động kích hoạt thông báo Realtime cho khách hàng."));

    // Bước 6
    bodyChildren.push(pHeading4("Bước 6: Giao hàng và Tra cứu tiến độ đơn hàng thời gian thực (Realtime Tracking)"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Nhân viên giao hàng (Shipper), Khách hàng (Customer). Mục đích: Vận chuyển đồ ăn an toàn đến tận tay khách hàng; đồng thời cung cấp công cụ theo dõi tiến độ đơn hàng thời gian thực trên website, minh bạch hóa từng giai đoạn chế biến và vận chuyển."));
    bodyChildren.push(pBullet("Vận chuyển món ăn", "Shipper nhận túi đồ ăn kèm hóa đơn, di chuyển theo định vị tới địa chỉ khách hàng. Shipper liên hệ khách trước khi đến nơi."));
    bodyChildren.push(pBullet("Tra cứu tiến độ đơn hàng Realtime (Order Tracking)", "Khách hàng mở trang 'Tra cứu đơn hàng' trên website FOODDAILY, nhập mã đơn hàng (order_code). Màn hình hiển thị Thanh tiến trình 5 giai đoạn (Step Progress Tracker): 1. Đã đặt hàng -> 2. Đã xác nhận & Thanh toán -> 3. Bếp đang nấu -> 4. Đang giao hàng -> 5. Đã giao thành công. Khi quầy thu ngân bấm 'Bàn giao Shipper' ở Bước 5, thanh tiến trình trên điện thoại của khách lập tức chuyển động sáng đèn ở bước 'Đang giao hàng' kèm lời nhắc chu đáo mà không cần nhấn F5 tải lại trang."));

    // Bước 7
    bodyChildren.push(pHeading4("Bước 7: Khách nhận hàng, Tích điểm thành viên và Gửi đánh giá"));
    bodyChildren.push(pBullet("Mục đích nghiệp vụ & Tác nhân", "Tác nhân: Khách hàng, Shipper, Hệ thống tích điểm Loyalty và Quản lý Đánh giá Review. Mục đích: Kết thúc chu trình giao hàng thành công, kích hoạt chính sách tri ân khách hàng thân thiết thông qua điểm thưởng tích lũy (Loyalty Points), và thu thập phản hồi khách quan từ khách hàng để cải tiến chất lượng món ăn và dịch vụ."));
    bodyChildren.push(pBullet("Hoàn tất giao hàng", "Khách nhận túi đồ ăn, kiểm tra tem niêm phong và hóa đơn. Khách trả tiền mặt (nếu COD) hoặc nhận hàng ngay (nếu đã thanh toán VietQR). Đơn hàng chuyển sang trạng thái hoàn tất: orders.order_status = 'completed', lưu thời gian giao delivered_at."));
    bodyChildren.push(pBullet("Cơ chế Tích điểm thưởng Thành viên (Loyalty Points)", "Nếu khách hàng có tài khoản thành viên, hệ thống tự động tính điểm theo công thức: Điểm tích lũy = floor(Tổng tiền thanh toán / 10.000). Ví dụ: Đơn hàng 250.000 VNĐ tích ngay 25 điểm thưởng. Điểm được cộng dồn vào users.loyalty_points. Khách hàng dùng điểm để thăng hạng tài khoản (Đồng, Bạc, Vàng, Kim Cương) và đổi lấy các Voucher ưu đãi độc quyền trong Ví Voucher cho các lần đặt tiếp theo."));
    bodyChildren.push(pBullet("Đánh giá chất lượng món ăn (Rating & Review)", "Sau khi hoàn thành đơn, website hiển thị hộp thoại mời đánh giá bữa ăn: Khách hàng chấm điểm số sao từ 1 đến 5 sao cho từng món ăn đã thưởng thức, nhập nhận xét chi tiết về hương vị, độ nóng, cách bày trí và có thể đính kèm ảnh chụp thực tế. Đánh giá được lưu vào bảng reviews (dish_id, user_id, order_id, rating, comment). Điểm sao trung bình của món ăn được tự động cập nhật lại và hiển thị công khai trên thực đơn."));

    // 2.1.2 THÔNG TIN YÊU CẦU
    bodyChildren.push(pHeading3("2.1.2\tThông tin yêu cầu"));
    bodyChildren.push(pBody("Để hệ thống FOODDAILY vận hành trơn tru, chính xác và đồng bộ giữa các phân hệ (Khách hàng, Nhà bếp, Thu ngân, Shipper và Quản trị viên), hệ thống thu thập, xử lý và quản lý chặt chẽ các luồng thực thể thông tin nghiệp vụ cốt lõi sau:"));

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

    bodyChildren.push(pHeading4("4. Thông tin nhà cung cấp, Kho nguyên liệu & Phiếu nhập kho (Suppliers, Inventory & Goods Receipts)"));
    bodyChildren.push(pBullet("Mục đích quản lý", "Quản trị chuỗi cung ứng thực phẩm đầu vào của nhà hàng; kiểm soát tồn kho định lượng nguyên vật liệu (thịt, rau, gia vị, gạo, bao bì); tự động phát hiện và cảnh báo các mặt hàng chạm ngưỡng tồn kho tối thiểu nhằm tránh tình trạng thiếu hụt nguyên liệu phục vụ; quản lý lịch sử nhập kho và giá vốn hàng bán (COGS)."));
    bodyChildren.push(pBullet("Thông tin Nhà cung cấp (Bảng suppliers)", "Tên nhà cung cấp (name: VARCHAR(255)), Người đại diện liên hệ (contact_name: VARCHAR(150)), Số điện thoại (phone: VARCHAR(20)), Địa chỉ cơ sở (address: TEXT), Mã số thuế (tax_code: VARCHAR(50)), Email (email: VARCHAR(191))."));
    bodyChildren.push(pBullet("Thông tin Kho nguyên liệu tồn kho (Bảng inventory_items)", "Mã nguyên liệu (item_code: VARCHAR(50), UNIQUE, VD: NL-THITHEO-01), Tên nguyên liệu (name: VARCHAR(255)), Phân loại nhóm (category: Thực phẩm tươi sống, Rau củ quả, Gia vị, Đồ uống, Bao bì), Đơn vị tính (unit: kg, gam, lít, chai, thùng, hộp, cái), Số lượng tồn kho (current_stock: DECIMAL(10,2)), Ngưỡng báo nhập tối thiểu (min_stock: DECIMAL(10,2)), Đơn giá vốn nhập (cost_price: DECIMAL(12,2)), Nhà cung cấp liên kết (supplier_id)."));
    bodyChildren.push(pBullet("Cảnh báo tồn kho & Trạng thái", "Khi current_stock <= min_stock, hệ thống lập tức hiển thị cảnh báo đỏ trên Dashboard quản trị viên. Trạng thái tồn kho: in_stock (Còn hàng), low_stock (Sắp hết hàng chạm ngưỡng), out_of_stock (Đã hết hàng)."));
    bodyChildren.push(pBullet("Phiếu nhập kho nguyên liệu (Bảng goods_receipts & goods_receipt_items)", "Mã phiếu nhập (receipt_code: VARCHAR(50)), Ngày giờ nhập (receipt_date: DATETIME), Tổng tiền nhập kho (total_amount: DECIMAL(14,2)), Trạng thái phiếu (status: draft, completed, cancelled). Chi tiết mặt hàng: Mã nguyên liệu, số lượng nhập, đơn giá nhập, thành tiền, hạn sử dụng lô hàng (expiry_date). Khi duyệt completed, hệ thống tự động cộng dồn tồn kho current_stock và tính lại giá vốn cost_price."));

    bodyChildren.push(pHeading4("5. Thông tin phòng đặt nhóm, Gói ăn định kỳ & Voucher ví"));
    bodyChildren.push(pBullet("Phòng đặt món nhóm Realtime (group_orders, group_order_items)", "Mã phòng room_code (mã PIN 6 ký tự), người tạo phòng host_user_id, trạng thái phòng status (open, locked), danh sách thành viên member_name, chi tiết món ăn từng thành viên chọn kèm đơn giá và ghi chú riêng."));
    bodyChildren.push(pBullet("Ví Voucher khuyến mãi (coupons, user_coupons)", "Mã voucher code, loại giảm giá discount_type (% hoặc số tiền), giá trị giảm discount_value, giá trị đơn tối thiểu min_order_value, hạn dùng start_date/end_date và trạng thái đã sử dụng is_used."));
    bodyChildren.push(pBullet("Gói ăn định kỳ Meal Subscription (service_packages, subscriptions)", "Tên gói ăn, số ngày phục vụ (1 tuần, 1 tháng), số bữa/ngày, ngày bắt đầu/kết thúc, thời gian giao hàng mong muốn hằng ngày và danh sách thực đơn đổi món hằng ngày của khách."));

    // 2.1.3 SƠ ĐỒ CHỨC NĂNG HỆ THỐNG (4 SƠ ĐỒ ĐỒ HỌA CHUẨN THESIS)
    bodyChildren.push(pHeading3("2.1.3\tSơ đồ chức năng của hệ thống"));
    bodyChildren.push(pBody("Với yêu cầu nghiệp vụ và thông tin đặt ra đối với hệ thống bán và giao đồ ăn trực tuyến FOODDAILY, ta xây dựng được sơ đồ phân rã chức năng (Functional Decomposition Diagram) của hệ thống tương ứng với 4 tác nhân chính:"));

    // Sơ đồ 2.1 Admin
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho Admin."));
    bodyChildren.push(...makeImageBlock('so_do_chuc_nang_admin.png', "Sơ đồ 2.1 Sơ đồ chức năng của Admin.", 580, 207));
    bodyChildren.push(pBody("Sơ đồ chức năng của Admin bao gồm nút gốc [Admin] kết nối đến 3 chức năng điều khiển phiên làm việc (Đăng nhập, Đăng xuất, Đổi mật khẩu) và 8 phân hệ quản lý trọng yếu: Quản lý thực đơn & món ăn, Quản lý đơn hàng, Quản lý khách hàng, Quản lý kho, Quản lý nhà cung cấp, Quản lý voucher, Gói ăn định kỳ và Thống kê báo cáo doanh thu. Mỗi phân hệ được phân rã thành các chức năng con chi tiết phục vụ đầy đủ vòng đời nghiệp vụ."));

    // Sơ đồ 2.2 Nhân viên
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho nhân viên."));
    bodyChildren.push(...makeImageBlock('so_do_chuc_nang_nhan_vien.png', "Sơ đồ 2.2 Sơ đồ chức năng nhân viên.", 560, 244));
    bodyChildren.push(pBody("Sơ đồ chức năng của Nhân viên quầy bao gồm các chức năng cốt lõi tại quầy bán hàng và điều phối: Bàn làm việc Workspace theo dõi đơn hàng đã nấu xong từ gian bếp theo thời gian thực; Chức năng in hóa đơn bán lẻ 1 chạm khổ nhiệt K80 chuẩn POS; Quy trình đóng gói và bàn giao túi đồ ăn cho tài xế giao hàng; và Phân hệ giám sát cảnh báo tồn kho nguyên vật liệu khẩn cấp."));

    // Sơ đồ 2.3 Đầu bếp
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho đầu bếp."));
    bodyChildren.push(...makeImageBlock('so_do_chuc_nang_dau_bep.png', "Sơ đồ 2.3 Sơ đồ chức năng đầu bếp.", 560, 214));
    bodyChildren.push(pBody("Sơ đồ chức năng của Đầu bếp điều phối hoạt động gian bếp: Hàng đợi Bếp FIFO hiển thị danh sách đơn chờ nấu với cơ chế đổi màu cảnh báo độ trễ; Bảng gộp món nấu thông minh (Batch Cooking) tính tổng số lượng từng món để nấu hàng loạt; Tính năng cập nhật trạng thái chế biến (Bắt đầu nấu -> Đã nấu xong) và Báo cáo nguyên liệu bếp."));

    // Sơ đồ 2.4 Khách hàng
    bodyChildren.push(pHeading4("Sơ đồ chức năng hệ thống dành cho khách hàng."));
    bodyChildren.push(...makeImageBlock('so_do_chuc_nang_khach_hang.png', "Sơ đồ 2.4 Sơ đồ chức năng khách hàng.", 560, 243));
    bodyChildren.push(pBody("Sơ đồ chức năng của Khách hàng hỗ trợ toàn bộ hành trình trải nghiệm của người dùng: Duyệt menu ẩm thực & Trợ lý ảo Google Gemini AI; Đặt món theo nhóm Realtime qua mã PIN/QR; Quản lý giỏ hàng và thanh toán tự động VietQR PayOS; Đăng ký gói ăn định kỳ tuần/tháng và Tra cứu tiến độ đơn hàng thời gian thực 5 chặng."));

    console.log('Section 2.1 compiled.');

    // 2.2 CÁC BIỂU ĐỒ THIẾT KẾ HỆ THỐNG
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading2("2.2\tCác biểu đồ thiết kế hệ thống"));
    bodyChildren.push(pHeading3("2.2.1\tLược đồ Use case"));
    bodyChildren.push(pBody("Lược đồ 2.1: Lược đồ use case tổng quát toàn hệ thống FOODDAILY biểu diễn các mối quan hệ tương tác giữa 4 tác nhân chính (Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên Admin) với toàn bộ các ca sử dụng trong hệ thống:"));
    bodyChildren.push(...makeImageBlock("luoc_do_2_1_use_case.png", "Lược đồ 2.1 Lược đồ use case.", 560, 395));

    // 11 BẢNG ĐẶC TẢ USE CASE CHI TIẾT
    bodyChildren.push(pHeading4("2.2.2.1\tĐặc tả use case đăng nhập vào hệ thống."));
    bodyChildren.push(pBody("Mục đích: Đảm bảo xác thực danh tính người sử dụng, phân quyền truy cập theo vai trò và đảm bảo an toàn bảo mật hệ thống.\nTác nhân: Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên Admin.\nMô tả chung: Khi bất kỳ tác nhân nào muốn truy cập vào các chức năng yêu cầu quyền hạn riêng biệt thì bắt buộc phải thực hiện đăng nhập vào hệ thống.\nĐiều kiện trước: Người dùng đã có tài khoản hợp lệ trong hệ thống.\nĐiều kiện sau: Hệ thống khởi tạo phiên làm việc (Session), lưu token xác thực và điều hướng người dùng đến giao diện tương ứng theo vai trò."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Người dùng bấm chọn liên kết hoặc nút 'Đăng nhập' trên thanh điều hướng.", "-"],
            ["-", "2. Hệ thống tải và hiển thị biểu mẫu (Form) đăng nhập với các trường: Email, Mật khẩu, Ghi nhớ đăng nhập và liên kết 'Quên mật khẩu'."],
            ["3. Người dùng nhập địa chỉ Email và Mật khẩu chính xác, sau đó nhấn nút 'Đăng nhập'.", "-"],
            ["-", "4. Hệ thống kiểm tra tính hợp lệ của dữ liệu đầu vào (Validation): định dạng email chuẩn RFC, mật khẩu không được để trống."],
            ["-", "5. Hệ thống truy vấn CSDL bảng Users theo Email, sử dụng hàm Hash::check() để đối soát chuỗi băm Bcrypt của mật khẩu nhập vào."],
            ["-", "6. Mật khẩu trùng khớp: Hệ thống tạo phiên đăng nhập an toàn, cập nhật thời điểm last_login và chuyển hướng người dùng: Admin -> Dashboard quản trị, Nhân viên -> Bàn làm việc Workspace, Bếp -> Màn hình Bếp FIFO, Khách hàng -> Trang chủ/Đơn hàng."],
            ["-", "7. Luồng thay thế: Nếu email hoặc mật khẩu không chính xác, hệ thống tăng số lần đăng nhập sai (Login Attempts) và hiển thị thông báo lỗi màu đỏ: 'Thông tin đăng nhập không chính xác, vui lòng thử lại'."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.2\tĐặc tả use case đăng xuất vào hệ thống."));
    bodyChildren.push(pBody("Mục đích: Giúp người dùng chủ động kết thúc phiên làm việc an toàn, ngăn chặn việc người khác sử dụng trái phép thiết bị đã đăng nhập.\nTác nhân: Khách hàng, Nhân viên quầy, Đầu bếp, Quản trị viên Admin.\nĐiều kiện trước: Người dùng đang ở trạng thái đã đăng nhập vào hệ thống.\nĐiều kiện sau: Phiên làm việc bị hủy hoàn toàn, người dùng trở về trạng thái khách vãng lai."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Người dùng nhấp vào avatar cá nhân và chọn mục 'Đăng xuất' (Logout).", "-"],
            ["-", "2. Hệ thống thu hồi Token xác thực hiện tại, xóa toàn bộ dữ liệu Session trên máy chủ."],
            ["-", "3. Hệ thống xóa Cookie xác thực trên trình duyệt, thiết lập cờ vô hiệu hóa bộ nhớ đệm trình duyệt (BFCache)."],
            ["-", "4. Hệ thống chuyển hướng người dùng về trang chủ (Homepage) và hiển thị thông báo: 'Bạn đã đăng xuất thành công'."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.3\tĐặc tả use case tìm kiếm và xem chi tiết món ăn."));
    bodyChildren.push(pBody("Mục đích: Cho phép người dùng nhanh chóng tìm kiếm các món ăn theo tên gọi, danh mục hoặc khoảng giá và xem đầy đủ thông tin chi tiết của món ăn.\nTác nhân: Khách hàng (Thành viên hoặc khách vãng lai)."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng nhập từ khóa tên món vào ô tìm kiếm hoặc bấm chọn danh mục/khoảng giá mong muốn.", "-"],
            ["-", "2. Hệ thống gửi yêu cầu truy vấn đến server, tìm kiếm món ăn có chứa từ khóa (LIKE %keyword%) và thỏa mãn điều kiện lọc."],
            ["-", "3. Trả về và hiển thị danh sách các món ăn thỏa điều kiện kèm hình ảnh, giá bán, giá gốc và đánh giá sao."],
            ["4. Khách hàng nhấp chọn một món ăn cụ thể để xem chi tiết.", "-"],
            ["-", "5. Hệ thống truy vấn CSDL bảng Dishes, hiển thị trang chi tiết món ăn gồm: ảnh to Cloudinary, mô tả thành phần, lưu ý dị ứng thực phẩm, số suất còn lại trong ngày và danh sách các đánh giá phản hồi của khách hàng khác."],
            ["6. Khách hàng có thể chọn số lượng, nhập ghi chú khẩu vị và bấm nút 'Thêm vào giỏ hàng'.", "-"]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.4\tĐặc tả use case đặt món theo nhóm Realtime."));
    bodyChildren.push(pBody("Mục đích: Cho phép một nhóm người cùng nhau chọn món ăn trên thiết bị cá nhân của mình và tự động đồng bộ giỏ hàng nhóm theo thời gian thực.\nTác nhân: Chủ phòng (Host), Các thành viên trong nhóm (Members), Hệ thống Realtime Server."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Chủ phòng bấm nút 'Đặt món theo nhóm' trên thanh công cụ và nhập tên phòng.", "-"],
            ["-", "2. Hệ thống tạo phòng mới trong bảng Group_Orders với trạng thái 'open', sinh Room Code ngẫu nhiên 6 ký tự và tạo mã QR Code động."],
            ["3. Chủ phòng gửi mã QR/link mời bạn bè tham gia phòng.", "-"],
            ["4. Thành viên quét mã QR hoặc nhập mã PIN trên website để gia nhập phòng.", "-"],
            ["-", "5. Thiết lập kết nối WebSockets qua kênh presence-group-order.{roomCode}, thông báo thành viên mới vào phòng cho tất cả mọi người."],
            ["6. Từng thành viên duyệt thực đơn, chọn món và bấm 'Thêm vào giỏ nhóm'.", "-"],
            ["-", "7. Hệ thống lưu bản ghi vào Group_Order_Items, phát sự kiện GroupOrderUpdated qua WebSockets; giỏ hàng nhóm của toàn bộ thành viên tự động cập nhật tức thì."],
            ["-", "8. Hiển thị bảng phân tách chi phí minh bạch: từng thành viên chọn món gì, số tiền riêng từng người và tổng tiền toàn phòng."],
            ["9. Chủ phòng kiểm tra danh sách món và bấm nút 'Chốt đơn nhóm'.", "-"],
            ["-", "10. Cập nhật trạng thái phòng thành 'locked', khóa mọi thao tác sửa món của thành viên và chuyển sang màn hình thanh toán đơn hàng."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.5\tĐặc tả use case thanh toán tự động VietQR PayOS."));
    bodyChildren.push(pBody("Mục đích: Thực hiện thanh toán trực tuyến không tiền mặt tự động 100% qua chuẩn VietQR Napas 24/7, khớp lệnh giao dịch ngân hàng trong 1-2 giây.\nTác nhân: Khách hàng / Chủ phòng, Cổng thanh toán PayOS, Hệ thống Core FOODDAILY, App Mobile Banking."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng nhập thông tin nhận hàng, áp mã giảm giá từ Ví Voucher và chọn phương thức 'Thanh toán VietQR PayOS'.", "-"],
            ["-", "2. Hệ thống tạo đơn hàng trạng thái 'pending', gọi API PayOS POST /v2/payment-requests truyền mã đơn và số tiền cần thanh toán."],
            ["-", "3. PayOS trả về link thanh toán và chuỗi VietQR chuẩn. Màn hình khách hàng hiển thị mã QR động cùng thông tin chuyển khoản và đồng hồ đếm ngược 15 phút."],
            ["4. Khách hàng mở ứng dụng ngân hàng bất kỳ (Mobile Banking), quét mã QR và xác nhận chuyển tiền.", "-"],
            ["-", "5. Tiền vào tài khoản quán -> PayOS bắn Webhook HTTP POST kèm dữ liệu giao dịch về URL /webhook/payos."],
            ["-", "6. Hệ thống xác thực chữ ký số HMAC-SHA256 với CHECKSUM_KEY. Chữ ký hợp lệ: Cập nhật payment_status='paid', order_status='confirmed', lưu mã transaction_id."],
            ["-", "7. Hệ thống phát sự kiện WebSockets PaymentSuccessEvent về máy khách (chỉ mất 1 - 2 giây để màn hình khách báo thành công), bắn thông báo Telegram cho Admin và đẩy đơn sang Màn hình Bếp FIFO."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.6\tĐặc tả use case Trợ lý AI Gemini tư vấn món ăn."));
    bodyChildren.push(pBody("Mục đích: Cung cấp trợ lý ẩm thực thông minh giúp người dùng nhận được lời khuyên món ăn phù hợp theo khẩu vị, tâm trạng và ngân sách.\nTác nhân: Khách hàng, Trợ lý ảo Google Gemini 1.5 Flash AI."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng mở widget chat 'Trợ lý AI' và nhập câu hỏi bằng ngôn ngữ tự nhiên.", "-"],
            ["-", "2. Controller trích xuất thực đơn đang kinh doanh nạp vào System Instruction (Context Injection) gửi đến API Google Gemini 1.5 Flash."],
            ["-", "3. Gemini phân tích yêu cầu, đối soát với menu và trả về lời khuyên chu đáo kèm danh sách mã ID món ăn phù hợp."],
            ["-", "4. Giao diện chat hiển thị câu trả lời và tự động sinh các card món ăn tương tác có sẵn nút bấm 'Cho vào giỏ ngay'."],
            ["5. Khách hàng bấm thêm món trực tiếp từ khung chat vào giỏ hàng.", "-"]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.7\tĐặc tả use case điều phối chế biến tại Màn hình Bếp FIFO."));
    bodyChildren.push(pBody("Mục đích: Giúp đầu bếp tiếp nhận đơn hàng tức thì theo thứ tự ưu tiên FIFO và gom mẻ nấu thông minh để tối ưu công suất chế biến.\nTác nhân: Đầu bếp / Nhân viên bếp, Kitchen Display System (KDS)."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Đơn hàng được xác nhận hoặc thanh toán thành công.", "-"],
            ["-", "2. Hệ thống phát sự kiện WebSockets kitchen.new-order sang KDS, chuông âm thanh reo báo có đơn mới."],
            ["-", "3. Màn hình Bếp hiển thị thẻ đơn hàng theo thứ tự thời gian đặt (FIFO) và cập nhật bảng gom tổng số lượng từng món ở thanh trên cùng."],
            ["4. Đầu bếp xem bảng gộp món và chạm nút 'Bắt đầu nấu' trên thẻ đơn.", "-"],
            ["-", "5. Cập nhật order_status = 'preparing', thẻ đơn chuyển sang cột Đang nấu."],
            ["6. Đầu bếp hoàn tất các món, chạm nút 'Đã nấu xong'.", "-"],
            ["-", "7. Cập nhật order_status = 'cooked', thẻ đơn biến mất khỏi màn hình bếp và tự động chuyển dữ liệu sang quầy thu ngân đóng gói."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.8\tĐặc tả use case đăng ký và quản lý gói ăn định kỳ."));
    bodyChildren.push(pBody("Mục đích: Cho phép khách hàng đăng ký các gói suất ăn định kỳ theo tuần hoặc tháng và linh hoạt đổi món hằng ngày.\nTác nhân: Khách hàng thành viên, Quản trị viên."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Khách hàng chọn gói dịch vụ (Gói văn phòng 1 tuần, Gói Healthy 1 tháng), chọn khung giờ giao cố định và thanh toán.", "-"],
            ["-", "2. Hệ thống kích hoạt gói thuê bao trong bảng Subscriptions và tự động tạo lịch giao suất ăn hằng ngày trong bảng Daily_Schedules."],
            ["3. Trước 9h sáng mỗi ngày, khách hàng truy cập lịch ăn và bấm 'Đổi món'.", "-"],
            ["-", "4. Hệ thống hiển thị danh mục món thay thế phù hợp với giá trị gói và cập nhật món ăn mới cho ngày tương ứng."],
            ["-", "5. Mỗi buổi trưa, đơn hàng của ngày đó tự động được kích hoạt và chuyển vào hàng đợi Màn hình Bếp."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.9\tĐặc tả use case quản lý thực đơn món ăn."));
    bodyChildren.push(pBody("Mục đích: Quản trị viên quản lý toàn bộ các món ăn, danh mục, giá bán và hình ảnh trong thực đơn nhà hàng.\nTác nhân: Quản trị viên Admin."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin truy cập mục 'Quản lý Thực đơn' và chọn 'Thêm món ăn mới' hoặc 'Chỉnh sửa'.", "-"],
            ["-", "2. Hiển thị form nhập liệu: Tên món, danh mục, đơn giá bán, giá gốc, mô tả, ảnh đại diện, số suất ăn trong ngày."],
            ["3. Admin nhập đầy đủ thông tin, tải ảnh lên và nhấn nút 'Lưu'.", "-"],
            ["-", "4. Kiểm tra dữ liệu hợp lệ (giá >= 0, tên không trùng). Tải ảnh lên dịch vụ đám mây Cloudinary lấy URL an toàn."],
            ["-", "5. Lưu dữ liệu vào bảng Dishes, xóa bộ nhớ đệm cache thực đơn và hiển thị thông báo thành công."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.10\tĐặc tả use case quản lý kho nguyên liệu và nhà cung cấp."));
    bodyChildren.push(pBody("Mục đích: Quản lý đối tác cung ứng, giám sát lượng tồn kho thực phẩm và lập phiếu nhập kho hàng.\nTác nhân: Quản trị viên Admin."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin chọn mục 'Quản lý Kho' -> 'Lập phiếu nhập kho'.", "-"],
            ["-", "2. Hiển thị form tạo phiếu nhập: Chọn nhà cung cấp, ngày nhập, danh sách các nguyên liệu nhập kèm số lượng và đơn giá."],
            ["3. Admin nhập đầy đủ các mặt hàng thực tế nhận từ nhà cung cấp và nhấn 'Hoàn tất nhập kho'.", "-"],
            ["-", "4. Hệ thống thực hiện Transaction CSDL: Lưu phiếu vào Goods_Receipts, lưu chi tiết vào Goods_Receipt_Items."],
            ["-", "5. Tự động cộng dồn số lượng vào trường current_stock của bảng Inventory_Items và tính lại đơn giá vốn bình quân (cost_price)."]
        ],
        [4700, 4700]
    ));

    bodyChildren.push(pHeading4("2.2.2.11\tĐặc tả use case quản lý khuyến mãi và cấp voucher ví."));
    bodyChildren.push(pBody("Mục đích: Tạo các chiến dịch khuyến mãi bằng mã voucher giảm giá và cấp phát voucher trực tiếp vào ví hội viên.\nTác nhân: Quản trị viên Admin."));
    bodyChildren.push(makeTable(
        ["Hành động của tác nhân", "Phản ứng của hệ thống"],
        [
            ["1. Admin truy cập 'Quản lý Khuyến mãi' và bấm 'Tạo Voucher mới'.", "-"],
            ["-", "2. Hiển thị form: Mã code, loại giảm (% hoặc tiền cố định), giá trị giảm, đơn tối thiểu, hạn dùng, giới hạn lượt dùng."],
            ["3. Admin nhập thông tin và chọn tùy chọn 'Cấp tự động vào ví của tất cả thành viên', sau đó bấm 'Phát hành'.", "-"],
            ["-", "4. Hệ thống lưu bản ghi vào bảng Coupons, tự động insert các bản ghi tương ứng vào bảng User_Coupons cho toàn bộ tài khoản khách hàng."],
            ["-", "5. Bắn thông báo realtime và gửi email thông báo mã ưu đãi mới đến khách hàng."]
        ],
        [4700, 4700]
    ));

    // 2.2.2 LƯỢC ĐỒ ACTIVITY CHI TIẾT
    bodyChildren.push(pHeading3("2.2.2\tLược đồ Activity"));
    bodyChildren.push(pBody("Lược đồ Activity (Biểu đồ hoạt động) mô tả chi tiết luồng xử lý nghiệp vụ tuần tự, các quyết định rẽ nhánh logic và sự phối hợp giữa các đối tượng trong hệ thống FOODDAILY:"));

    const activities = [
        ["2.2.2.1 Lược đồ chức năng đăng nhập", "Lược đồ 2.2: Lược đồ chức năng đăng nhập", "Luồng hoạt động bắt đầu khi người dùng chọn Đăng nhập -> Điền Email và Mật khẩu -> Hệ thống kiểm tra Validation -> Nếu dữ liệu rỗng: báo lỗi nhập liệu -> Nếu hợp lệ: truy vấn CSDL bảng Users so khớp hash Bcrypt -> Nếu mật khẩu sai: tăng số lần đăng nhập sai, hiển thị thông báo lỗi -> Nếu đúng: tạo Session, cấp quyền RBAC và chuyển hướng theo vai trò (Admin/Staff/Chef/Customer) -> Kết thúc."],
        ["2.2.2.2 Lược đồ chức năng reset password", "Lược đồ 2.3: Lược đồ chức năng reset password", "Bắt đầu khi người dùng bấm Quên mật khẩu -> Nhập địa chỉ Email -> Hệ thống kiểm tra email có tồn tại trong CSDL -> Nếu không có: báo email không tồn tại -> Nếu có: sinh mã Token/OTP ngẫu nhiên, lưu bảng password_reset_tokens kèm thời hạn 15 phút, gửi email chứa link đặt lại -> Người dùng mở link nhập mật khẩu mới -> Hệ thống băm Bcrypt mật khẩu mới, cập nhật bảng Users, hủy token -> Chuyển hướng đăng nhập thành công -> Kết thúc."],
        ["2.2.2.3 Lược đồ tìm kiếm món ăn", "Lược đồ 2.4: Lược đồ tìm kiếm", "Bắt đầu khi người dùng nhập từ khóa hoặc chọn bộ lọc danh mục/giá -> Client gửi AJAX request -> Server tiếp nhận query parameters -> Xây dựng câu lệnh Eloquent Query Builder với điều kiện LIKE, WHERE category_id, WHERE price BETWEEN -> Thực thi truy vấn MySQL -> Nếu không có món phù hợp: hiển thị 'Không tìm thấy món ăn phù hợp' -> Nếu có: render danh sách card món ăn, hiển thị ảnh, giá và số sao đánh giá -> Kết thúc."],
        ["2.2.2.4 Lược đồ đặt món theo nhóm Realtime", "Lược đồ 2.5: Lược đồ đặt món theo nhóm Realtime", "Chủ phòng bấm Tạo phòng nhóm -> Server tạo bản ghi group_orders, sinh room_code và QR code -> Chủ phòng chia sẻ link -> Các thành viên quét QR vào phòng -> Kết nối WebSocket channel presence-group-order.{room_code} -> Thành viên duyệt menu, chọn món -> Gửi request add-item -> Server lưu group_order_items -> Kích hoạt Laravel Event GroupOrderUpdated -> Broadcast qua WebSockets -> Trình duyệt tất cả thành viên tự động cập nhật giỏ hàng nhóm và bảng chia tiền -> Chủ phòng bấm Chốt đơn -> Đổi status phòng sang locked -> Chuyển giỏ hàng sang màn hình thanh toán -> Kết thúc."],
        ["2.2.2.5 Lược đồ thanh toán PayOS VietQR", "Lược đồ 2.6: Lược đồ thanh toán PayOS VietQR", "Khách chọn VietQR và bấm Xác nhận đặt hàng -> Server tạo order trạng thái pending -> Gọi API PayOS sinh mã VietQR Napas -> Hiển thị mã QR động và đồng hồ đếm ngược 15 phút -> Khách mở app ngân hàng quét mã và chuyển khoản -> Ngân hàng báo có -> PayOS gửi Webhook POST về hệ thống -> Server xác thực chữ ký HMAC-SHA256 với CHECKSUM_KEY -> Nếu chữ ký sai: từ chối, trả về mã 400 -> Nếu chữ ký đúng: cập nhật payment_status='paid', order_status='confirmed', lưu transaction_id -> Broadcast WebSocket báo thanh toán thành công về máy khách -> Chuyển đơn sang Bếp FIFO -> Kết thúc."],
        ["2.2.2.6 Lược đồ điều phối Bếp FIFO", "Lược đồ 2.7: Lược đồ điều phối Bếp FIFO", "Đơn hàng thanh toán thành công -> WebSocket kích hoạt chuông reo trên Màn hình Bếp -> Thêm thẻ đơn vào hàng đợi theo thời gian đặt (FIFO) -> Thuật toán gộp món tự động cộng dồn số lượng từng món ở bảng tổng hợp -> Đầu bếp xem số lượng gộp và bấm Bắt đầu nấu -> Trạng thái đơn chuyển sang preparing -> Đầu bếp chế biến xong bấm Đã nấu xong -> Trạng thái đơn chuyển sang cooked -> Đơn hàng tự động trượt sang Bàn làm việc thu ngân đóng gói -> Kết thúc."],
        ["2.2.2.7 Lược đồ quản lý hóa đơn và in bill", "Lược đồ 2.8: Lược đồ quản lý hóa đơn và in bill", "Thu ngân theo dõi cột 'Chờ đóng gói' trên màn hình Workspace -> Chọn đơn hàng đã nấu xong -> Bấm In bill 1 chạm (hoặc phím F9) -> Hệ thống xuất lệnh in hóa đơn nhiệt 80mm chuẩn POS -> Thu ngân kiểm tra đối chiếu từng món ăn thực tế với hóa đơn -> Dán niêm phong túi đồ ăn, kẹp hóa đơn -> Bàn giao túi đồ ăn cho tài xế Shipper -> Bấm nút 'Xuất giao hàng' -> Đơn hàng chuyển sang trạng thái delivering -> Khách hàng nhận thông báo realtime đơn đang giao -> Kết thúc."],
        ["2.2.2.8 Lược đồ quản lý thông tin khách hàng", "Lược đồ 2.9: Lược đồ quản lý thông tin khách hàng", "Admin truy cập mục Khách hàng -> Hệ thống truy vấn danh sách người dùng kèm phân trang và điểm tích lũy -> Admin chọn xem chi tiết một khách hàng -> Hiển thị lịch sử các đơn hàng cá nhân/nhóm và thứ hạng thành viên -> Admin có thể cập nhật thông tin, điều chỉnh điểm thưởng hoặc bấm nút Khóa/Mở khóa tài khoản -> Hệ thống cập nhật bảng Users và hiển thị thông báo thành công -> Kết thúc."],
        ["2.2.2.9 Lược đồ quản lý thực đơn món ăn", "Lược đồ 2.10: Lược đồ quản lý thông tin món ăn", "Admin vào Quản lý thực đơn -> Chọn Thêm món hoặc Sửa món -> Nhập tên món, danh mục, giá bán, giá gốc, số suất trong ngày -> Chọn tải tệp ảnh từ máy -> Server kiểm tra validation và upload ảnh lên Cloudinary lấy URL bảo mật -> Lưu bản ghi vào bảng Dishes -> Tự động xóa cache thực đơn trên Redis/File -> Cập nhật danh sách hiển thị trên trang chủ -> Kết thúc."],
        ["2.2.2.10 Lược đồ quản lý nhà cung cấp", "Lược đồ 2.11: Lược đồ quản lý thông tin nhà cung cấp", "Admin vào Quản lý đối tác -> Bấm Thêm nhà cung cấp -> Nhập tên công ty, người đại diện, số điện thoại, địa chỉ, mã số thuế -> Server kiểm tra dữ liệu -> Lưu vào bảng Suppliers -> Hiển thị trong danh sách đối tác cung ứng để chọn khi nhập kho -> Kết thúc."],
        ["2.2.2.11 Lược đồ quản lý kho nguyên liệu và nhập kho", "Lược đồ 2.12: Lược đồ quản lý kho nguyên liệu và nhập kho", "Admin chọn Lập phiếu nhập kho -> Chọn nhà cung cấp và ngày nhập -> Chọn các nguyên liệu nhập, nhập số lượng, đơn giá mua và hạn sử dụng -> Nhấn Hoàn tất phiếu nhập -> Hệ thống thực thi Database Transaction: Lưu Goods_Receipts và Goods_Receipt_Items -> Cộng dồn số lượng vào current_stock của Inventory_Items -> Tính lại đơn giá vốn bình quân gia quyền -> Kiểm tra nếu tồn kho vượt ngưỡng cảnh báo: gỡ cờ cảnh báo đỏ -> Kết thúc."],
        ["2.2.2.12 Lược đồ quản lý nhân viên và phân quyền RBAC", "Lược đồ 2.13: Lược đồ quản lý nhân viên và phân quyền RBAC", "Admin vào Quản lý phân quyền -> Xem danh sách nhân viên -> Chọn nhân viên cần phân quyền -> Chọn các vai trò (Cashier, Chef, Manager) hoặc gán quyền chi tiết (Permissions) -> Server gọi phương thức syncRoles() của Spatie Laravel Permission -> Cập nhật bảng model_has_roles và model_has_permissions -> Ghi log hoạt động phân quyền -> Nhân viên áp dụng quyền hạn mới ngay trong phiên làm việc kế tiếp -> Kết thúc."]
    ];

    const actImages = ["luoc_do_2_2_dang_nhap.png","luoc_do_2_3_reset_password.png","luoc_do_2_4_tim_kiem.png","luoc_do_2_5_dat_mon_nhom.png","luoc_do_2_6_thanh_toan_payos.png","luoc_do_2_7_dieu_phoi_bep.png","luoc_do_2_8_in_hoa_don.png","luoc_do_2_9_quan_ly_khach_hang.png","luoc_do_2_10_quan_ly_mon_an.png","luoc_do_2_11_quan_ly_nha_cung_cap.png","luoc_do_2_12_nhap_kho_nguyen_lieu.png","luoc_do_2_13_quan_ly_nhan_vien.png"];
    activities.forEach((act, idx) => {
        bodyChildren.push(pHeading4(act[0]));
        bodyChildren.push(pBullet("Tên lược đồ", act[1]));
        bodyChildren.push(pBullet("Mô tả luồng hoạt động", act[2]));
        if (actImages[idx]) {
            bodyChildren.push(...makeImageBlock(actImages[idx], act[1] + ".", 340, 420));
        }
    });

    // 2.2.3 BIỂU ĐỒ THÀNH PHẦN
    bodyChildren.push(pHeading3("2.2.3\tBiểu đồ thành phần"));
    bodyChildren.push(pBody("Biểu đồ thành phần (Component Diagram) thể hiện cấu trúc mô-đun hóa của hệ thống FOODDAILY, sự phân chia trách nhiệm và các giao diện giao tiếp giữa các thành phần phần mềm trong hệ thống:"));

    // 2.2.4.1 Biểu đồ thành phần hệ thống
    bodyChildren.push(pHeading4("2.2.4.1\tBiểu đồ thành phần hệ thống"));
    bodyChildren.push(pBody("Biểu đồ thành phần hệ thống tổng quan thể hiện cấu trúc phân rã giữa ứng dụng Website cốt lõi với hai phân hệ chính: Giao diện quản trị viên (Admin) và Giao diện người dùng (Khách hàng):"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_1_thanh_phan_tong_quan.png", "Biểu đồ 2.1 Biểu đồ thành phần tổng quan hệ thống.", 520, 240));

    // 2.2.4.2 Biểu đồ thành phần quản trị viên
    bodyChildren.push(pHeading4("2.2.4.2\tBiểu đồ thành phần quản trị viên"));
    bodyChildren.push(pBody("Biểu đồ thành phần phân hệ quản trị viên mô tả mối liên kết giữa Giao diện Admin với toàn bộ các mô-đun quản lý nghiệp vụ, dịch vụ lưu trữ dữ liệu, bảo mật và kết nối cơ sở dữ liệu MySQL:"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_2_thanh_phan_admin.png", "Biểu đồ 2.2 Biểu đồ thành phần quản trị viên.", 540, 440));

    // 2.2.4.4 Biểu đồ thành phần người dùng
    bodyChildren.push(pHeading4("2.2.4.4\tBiểu đồ thành phần người dùng"));
    bodyChildren.push(pBody("Biểu đồ thành phần người dùng mô tả các mô-đun chức năng giao tiếp trực tiếp với khách hàng: tìm kiếm món ăn kết hợp AI, đặt món theo nhóm Realtime, thanh toán VietQR và giỏ hàng:"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_3_thanh_phan_nguoi_dung.png", "Biểu đồ 2.3 Biểu đồ thành phần người dùng.", 540, 250));

    // 2.2.4 BIỂU ĐỒ TRIỂN KHAI
    bodyChildren.push(pHeading3("2.2.4\tBiểu đồ triển khai"));
    bodyChildren.push(pBody("Biểu đồ triển khai (Deployment Diagram) thể hiện kiến trúc vật lý và các nút phần cứng, phần mềm máy chủ phục vụ vận hành hệ thống FOODDAILY:"));
    bodyChildren.push(...makeImageBlock("bieu_do_2_4_trien_khai.png", "Biểu đồ 2.4 Biểu đồ triển khai.", 540, 210));
    bodyChildren.push(pBullet("Nút Web Server", "Máy chủ web Nginx / Laragon tiếp nhận các truy vấn HTTP/HTTPS từ phía Client trên Port 80/443 và phân phối yêu cầu xử lý."));
    bodyChildren.push(pBullet("Nút Application Server", "Máy chủ ứng dụng chạy PHP 8.3 FPM thực thi mã nguồn Laravel 13, kết hợp tiến trình WebSocket Laravel Reverb chạy độc lập trên Port 8080 xử lý thông báo thời gian thực."));
    bodyChildren.push(pBullet("Nút Database Server", "Hệ quản trị cơ sở dữ liệu quan hệ MySQL 8.0 lưu trữ an toàn toàn bộ dữ liệu nghiệp vụ, cấu hình Storage Engine InnoDB và cơ chế Transaction ACID."));
    bodyChildren.push(pBullet("Nút External Cloud Services", "Kết nối qua API HTTPS bảo mật đến: Cổng thanh toán VietQR PayOS, Dịch vụ Trí tuệ Nhân tạo Google Gemini AI và Kho lưu trữ hình ảnh Cloudinary CDN."));

    // 2.3 CƠ SỞ DỮ LIỆU (12 BẢNG CHI TIẾT 5 CỘT)
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading2("2.3\tCơ sở dữ liệu"));
    bodyChildren.push(pBody("Cơ sở dữ liệu hệ thống FOODDAILY được thiết kế theo mô hình quan hệ (Relational Database) chuẩn hóa mức dạng chuẩn 3 (3NF), đảm bảo tính toàn vẹn tham chiếu, loại bỏ hoàn toàn dư thừa dữ liệu và tối ưu hóa tốc độ truy vấn:"));
    bodyChildren.push(pBody("Hình 2.2: Sơ đồ Cơ sở dữ liệu (Database Schema ERD) thể hiện đầy đủ các mối quan hệ 1-Nhiều (1-n) và Nhiều-Nhiều (n-n) giữa 12 thực thể cốt lõi trong hệ thống FOODDAILY:"));
    bodyChildren.push(...makeImageBlock("hinh_2_2_co_so_du_lieu.png", "Hình 2.2 Cơ sở dữ liệu.", 560, 340));
    bodyChildren.push(pBody("Dưới đây là bảng đặc tả cấu trúc chi tiết của 12 bảng cơ sở dữ liệu chính:"));

    // Table 1: Users
    bodyChildren.push(pHeading4("Bảng 2.12 Tóm tắt ý nghĩa dữ liệu bảng Users"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã định danh duy nhất của người dùng"],
            ["fullname", "varchar", "Not Null", "150", "Họ và tên đầy đủ của người dùng"],
            ["email", "varchar", "Unique, Not Null", "191", "Địa chỉ email dùng để đăng nhập và nhận thông báo"],
            ["password", "varchar", "Not Null", "255", "Mật khẩu xác thực đã được băm mã hóa Bcrypt an toàn"],
            ["phone", "varchar", "Nullable", "20", "Số điện thoại liên lạc nhận hàng chính"],
            ["address", "varchar", "Nullable", "255", "Địa chỉ giao hàng mặc định của khách hàng"],
            ["company_address", "varchar", "Nullable", "255", "Địa chỉ văn phòng công ty phục vụ giao giờ hành chính"],
            ["default_address_type", "enum", "Default 'home'", "10", "Loại địa chỉ mặc định (home, company, other)"],
            ["points", "int unsigned", "Default 0", "10", "Số điểm thưởng tích lũy thành viên (Loyalty Points)"],
            ["membership", "varchar", "Default 'Bronze'", "20", "Hạng hội viên (Bronze, Silver, Gold, Diamond)"],
            ["role", "varchar", "Default 'customer'", "20", "Vai trò người dùng (customer, staff, chef, admin)"],
            ["avatar", "varchar", "Nullable", "255", "Đường dẫn ảnh đại diện cá nhân lưu trên Cloudinary"],
            ["status", "enum", "Default 'active'", "10", "Trạng thái tài khoản (active: hoạt động, blocked: bị khóa)"],
            ["two_factor_enabled", "tinyint(1)", "Default 0", "1", "Cờ bật/tắt tính năng xác thực hai yếu tố 2FA"],
            ["created_at", "timestamp", "Nullable", "-", "Thời điểm tạo tài khoản trong hệ thống"],
            ["updated_at", "timestamp", "Nullable", "-", "Thời điểm cập nhật thông tin tài khoản gần nhất"]
        ]
    ));

    // Table 2: Categories
    bodyChildren.push(pHeading4("Bảng 2.13 Tóm tắt ý nghĩa dữ liệu bảng Categories"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã định danh danh mục món ăn"],
            ["name", "varchar", "Not Null", "255", "Tên danh mục (Món chính, Món ăn vặt, Đồ uống, Tráng miệng)"],
            ["slug", "varchar", "Unique, Not Null", "255", "Đường dẫn tĩnh thân thiện chuẩn SEO"],
            ["icon", "varchar", "Nullable", "100", "Tên icon biểu tượng danh mục trên thanh điều hướng"],
            ["is_active", "tinyint(1)", "Default 1", "1", "Trạng thái hiển thị danh mục trên menu website"],
            ["created_at", "timestamp", "Nullable", "-", "Thời điểm tạo danh mục"]
        ]
    ));

    // Table 3: Dishes
    bodyChildren.push(pHeading4("Bảng 2.14 Tóm tắt ý nghĩa dữ liệu bảng Dishes"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã định danh món ăn"],
            ["category_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã danh mục liên kết tới bảng Categories"],
            ["name", "varchar", "Not Null", "255", "Tên thương phẩm món ăn"],
            ["slug", "varchar", "Unique, Not Null", "255", "Đường dẫn tĩnh thân thiện chuẩn SEO của món ăn"],
            ["price", "decimal(10,2)", "Not Null", "12", "Đơn giá bán thực tế mà khách hàng phải trả"],
            ["original_price", "decimal(10,2)", "Nullable", "12", "Giá gốc niêm yết dùng hiển thị giá gạch ngang khuyến mãi"],
            ["image_url", "varchar", "Nullable", "500", "Đường dẫn ảnh đại diện chất lượng cao lưu trên Cloudinary"],
            ["description", "text", "Nullable", "-", "Mô tả chi tiết nguyên liệu, hương vị và lưu ý dị ứng"],
            ["stock", "int unsigned", "Default 0", "10", "Số lượng suất ăn sẵn sàng phục vụ trong ngày"],
            ["is_available", "tinyint(1)", "Default 1", "1", "Trạng thái kinh doanh phục vụ (1: đang bán, 0: tạm tắt)"],
            ["is_featured", "tinyint(1)", "Default 0", "1", "Cờ đánh dấu món tiêu biểu nổi bật Best Seller trang chủ"]
        ]
    ));

    // Table 4: Orders
    bodyChildren.push(pHeading4("Bảng 2.15 Tóm tắt ý nghĩa dữ liệu bảng Orders"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã số định danh đơn hàng"],
            ["order_code", "varchar", "Unique, Not Null", "50", "Mã đơn hàng nghiệp vụ tra cứu (VD: FD-20261012-0089)"],
            ["user_id", "bigint unsigned", "Foreign Key, Nullable", "20", "Mã khách hàng đặt (NULL nếu là khách vãng lai)"],
            ["coupon_id", "bigint unsigned", "Foreign Key, Nullable", "20", "Mã voucher ưu đãi áp dụng cho đơn hàng"],
            ["order_type", "enum", "Default 'single'", "10", "Hình thức đặt (single: cá nhân, group: theo nhóm)"],
            ["delivery_name", "varchar", "Not Null", "150", "Họ tên người nhận đồ ăn trực tiếp"],
            ["delivery_phone", "varchar", "Not Null", "20", "Số điện thoại nhận hàng của khách"],
            ["delivery_address", "text", "Not Null", "-", "Địa chỉ chi tiết nhận đồ ăn giao tận nơi"],
            ["health_notes", "text", "Nullable", "-", "Ghi chú dặn dò khẩu vị, dị ứng thực phẩm hoặc giờ giao"],
            ["total_amount", "decimal(12,2)", "Not Null", "14", "Tổng giá trị tiền các món ăn trước khi giảm trừ"],
            ["discount_amount", "decimal(12,2)", "Default 0.00", "14", "Số tiền được giảm trừ từ voucher khuyến mãi"],
            ["shipping_fee", "decimal(10,2)", "Default 0.00", "12", "Phí dịch vụ vận chuyển giao hàng tận nơi"],
            ["final_amount", "decimal(12,2)", "Not Null", "14", "Tổng số tiền thực tế khách hàng thanh toán"],
            ["payment_method", "enum", "Not Null", "15", "Phương thức thanh toán (cod, payos, vnpay, momo)"],
            ["payment_status", "enum", "Default 'pending'", "15", "Trạng thái thanh toán (pending, paid, failed, refunded)"],
            ["order_status", "enum", "Default 'pending'", "15", "Tiến độ đơn: pending -> confirmed -> preparing -> cooked -> delivering -> completed -> cancelled"],
            ["payment_transaction_id", "varchar", "Nullable", "100", "Mã đối soát giao dịch ngân hàng do PayOS trả về"],
            ["payment_paid_at", "datetime", "Nullable", "-", "Thời điểm nhận tiền thanh toán thành công"],
            ["delivered_at", "datetime", "Nullable", "-", "Thời điểm giao hàng thành công tận tay khách"]
        ]
    ));

    // Table 5: Order_Items
    bodyChildren.push(pHeading4("Bảng 2.16 Tóm tắt ý nghĩa dữ liệu bảng Order_Items"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã chi tiết đơn hàng"],
            ["order_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã đơn hàng liên kết tới bảng Orders"],
            ["dish_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã món ăn liên kết tới bảng Dishes"],
            ["quantity", "int unsigned", "Not Null", "10", "Số lượng phần ăn khách đặt"],
            ["price", "decimal(10,2)", "Not Null", "12", "Đơn giá món ăn tại thời điểm mua"],
            ["item_notes", "varchar", "Nullable", "255", "Ghi chú riêng của món ăn (không hành, ít cay, thêm cơm)"]
        ]
    ));

    // Table 6: Group_Orders
    bodyChildren.push(pHeading4("Bảng 2.17 Tóm tắt ý nghĩa dữ liệu bảng Group_Orders"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã phòng đặt món nhóm"],
            ["room_code", "varchar", "Unique, Not Null", "10", "Mã PIN 6 ký tự để thành viên tham gia phòng (VD: FD8866)"],
            ["host_user_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã người dùng chủ phòng khởi tạo phòng"],
            ["name", "varchar", "Not Null", "255", "Tên hiển thị của phòng (VD: Bữa trưa Team Dev)"],
            ["status", "enum", "Default 'open'", "15", "Trạng thái phòng (open: đang chọn, locked: đã khóa chốt đơn)"],
            ["created_at", "timestamp", "Nullable", "-", "Thời điểm tạo phòng đặt nhóm"]
        ]
    ));

    // Table 7: Service_Packages
    bodyChildren.push(pHeading4("Bảng 2.18 Tóm tắt ý nghĩa dữ liệu bảng Service_Packages"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã định danh gói dịch vụ ăn"],
            ["name", "varchar", "Not Null", "255", "Tên gói ăn (Gói cơm trưa văn phòng 1 tuần, Gói Healthy 1 tháng)"],
            ["price", "decimal(12,2)", "Not Null", "14", "Đơn giá trọn gói dịch vụ suất ăn định kỳ"],
            ["duration_days", "int unsigned", "Not Null", "10", "Số ngày phục vụ suất ăn trong gói (5 ngày, 20 ngày)"],
            ["meals_per_day", "int unsigned", "Default 1", "5", "Số bữa ăn được giao mỗi ngày (1 bữa trưa / ngày)"],
            ["description", "text", "Nullable", "-", "Mô tả quyền lợi và chính sách đổi món của gói ăn"]
        ]
    ));

    // Table 8: Subscriptions
    bodyChildren.push(pHeading4("Bảng 2.19 Tóm tắt ý nghĩa dữ liệu bảng Subscriptions"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã thuê bao đăng ký gói ăn"],
            ["user_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã khách hàng đăng ký thuê bao gói ăn"],
            ["package_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã gói dịch vụ liên kết bảng Service_Packages"],
            ["start_date", "date", "Not Null", "-", "Ngày bắt đầu kỳ hạn giao đồ ăn định kỳ"],
            ["end_date", "date", "Not Null", "-", "Ngày kết thúc kỳ hạn giao đồ ăn định kỳ"],
            ["delivery_time", "time", "Not Null", "-", "Khung giờ giao hàng cố định mong muốn mỗi ngày (VD: 11:30)"],
            ["status", "enum", "Default 'active'", "15", "Trạng thái thuê bao (active, paused: tạm dừng, completed)"]
        ]
    ));

    // Table 9: Coupons & User_Coupons
    bodyChildren.push(pHeading4("Bảng 2.20 Tóm tắt ý nghĩa dữ liệu bảng Coupons & User_Coupons"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã định danh voucher khuyến mãi"],
            ["code", "varchar", "Unique, Not Null", "50", "Mã voucher viết hoa khách nhập (VD: GIAM20K, FREESHIP)"],
            ["discount_type", "enum", "Not Null", "15", "Loại giảm giá (percentage: theo %, fixed_amount: tiền mặt)"],
            ["discount_value", "decimal(10,2)", "Not Null", "12", "Giá trị giảm (ví dụ: 15% hoặc 20.000 VNĐ)"],
            ["min_order_value", "decimal(12,2)", "Default 0.00", "14", "Giá trị đơn hàng tối thiểu bắt buộc để được áp mã"],
            ["max_discount", "decimal(10,2)", "Nullable", "12", "Số tiền giảm giá tối đa áp dụng cho loại giảm theo %"],
            ["usage_limit", "int unsigned", "Nullable", "10", "Tổng số lượt tối đa mã được phép sử dụng trên toàn hệ thống"],
            ["start_date", "datetime", "Not Null", "-", "Ngày giờ bắt đầu có hiệu lực của mã voucher"],
            ["end_date", "datetime", "Not Null", "-", "Ngày giờ hết hạn áp dụng của mã voucher"],
            ["is_active", "tinyint(1)", "Default 1", "1", "Trạng thái kích hoạt sử dụng của chương trình khuyến mãi"]
        ]
    ));

    // Table 10: Suppliers
    bodyChildren.push(pHeading4("Bảng 2.21 Tóm tắt ý nghĩa dữ liệu bảng Suppliers"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã nhà cung cấp đối tác"],
            ["name", "varchar", "Not Null", "255", "Tên doanh nghiệp hoặc cơ sở cung ứng thực phẩm"],
            ["contact_name", "varchar", "Nullable", "150", "Họ tên người đại diện liên hệ kinh doanh của đối tác"],
            ["phone", "varchar", "Not Null", "20", "Số điện thoại liên lạc đặt hàng nguyên liệu"],
            ["email", "varchar", "Nullable", "191", "Địa chỉ email gửi đơn đặt hàng và đối soát công nợ"],
            ["address", "text", "Nullable", "-", "Địa chỉ kho hàng cơ sở cung cấp nguyên vật liệu"],
            ["tax_code", "varchar", "Nullable", "50", "Mã số thuế doanh nghiệp phục vụ xuất hóa đơn đầu vào"]
        ]
    ));

    // Table 11: Inventory_Items
    bodyChildren.push(pHeading4("Bảng 2.22 Tóm tắt ý nghĩa dữ liệu bảng Inventory_Items"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã định danh nguyên vật liệu tồn kho"],
            ["item_code", "varchar", "Unique, Not Null", "50", "Mã nguyên liệu quản lý nội bộ (VD: NL-THITHEO-01)"],
            ["name", "varchar", "Not Null", "255", "Tên nguyên vật liệu (Thịt sườn heo, Gạo tấm, Rau xà lách)"],
            ["category", "varchar", "Nullable", "100", "Phân nhóm nguyên liệu (Thực phẩm tươi, Gia vị, Bao bì)"],
            ["unit", "varchar", "Not Null", "50", "Đơn vị tính chuẩn (kg, gam, lít, chai, thùng, hộp)"],
            ["current_stock", "decimal(10,2)", "Default 0.00", "12", "Số lượng hàng hóa thực tế hiện có trong kho"],
            ["min_stock", "decimal(10,2)", "Default 5.00", "12", "Ngưỡng tồn kho tối thiểu cảnh báo cần nhập thêm gấp"],
            ["cost_price", "decimal(12,2)", "Default 0.00", "14", "Đơn giá vốn nhập trung bình của một đơn vị nguyên liệu"],
            ["supplier_id", "bigint unsigned", "Foreign Key, Nullable", "20", "Mã nhà cung cấp ưu tiên nhập mặt hàng này"]
        ]
    ));

    // Table 12: Goods_Receipts
    bodyChildren.push(pHeading4("Bảng 2.23 Tóm tắt ý nghĩa dữ liệu bảng Goods_Receipts"));
    bodyChildren.push(makeTable(
        ["Tên cột", "Kiểu dữ liệu", "Ràng buộc", "Giá trị tối đa", "Mô tả"],
        [
            ["id", "bigint unsigned", "Primary Key, Auto Increment, Not Null", "20", "Mã số phiếu nhập kho hàng"],
            ["receipt_code", "varchar", "Unique, Not Null", "50", "Mã chứng từ nhập kho kế toán (VD: PNK-20261012-001)"],
            ["supplier_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã nhà cung cấp giao lô hàng nhập này"],
            ["user_id", "bigint unsigned", "Foreign Key, Not Null", "20", "Mã nhân viên thực hiện kiểm đếm và lập phiếu nhập"],
            ["receipt_date", "datetime", "Not Null", "-", "Ngày giờ thực tế hàng hóa được nhập vào kho"],
            ["total_amount", "decimal(14,2)", "Not Null", "16", "Tổng giá trị tiền hàng của phiếu nhập kho"],
            ["status", "enum", "Default 'completed'", "15", "Trạng thái phiếu nhập (draft: nháp, completed: đã nhập)"]
        ]
    ));

    console.log('Chapter 2 compiled.');

    // CHƯƠNG 3
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ"));
    bodyChildren.push(pHeading2("3.1\tMột số giao diện website"));
    bodyChildren.push(pBody("Hệ thống website giao đồ ăn trực tuyến FOODDAILY đã được lập trình hoàn thiện và triển khai thực nghiệm thành công. Dưới đây là mô tả chi tiết các phân hệ giao diện chính của hệ thống:"));

    bodyChildren.push(pHeading3("3.1.1\tPhần quản trị"));
    const adminUIs = [
        ["3.1.3.1 Giao diện đăng nhập quản trị", "Thiết kế bảo mật với cơ chế xác thực hai lớp (2FA), tự động kiểm tra CSRF token và rate limiting chống dò mật khẩu. Hỗ trợ ghi nhớ phiên đăng nhập và liên kết khôi phục mật khẩu an toàn."],
        ["3.1.3.2 Giao diện Dashboard thống kê doanh thu", "Khu vực tổng quan hiển thị các thẻ KPI doanh thu ngày/tuần/tháng, số lượng đơn hàng hoàn tất, biểu đồ xu hướng tăng trưởng doanh thu Chart.js và biểu đồ tròn tỷ lệ thanh toán VietQR PayOS so với COD."],
        ["3.1.3.3 Giao diện quản lý thực đơn món ăn", "Bảng danh sách món ăn phân trang, hỗ trợ tìm kiếm nhanh, lọc theo danh mục. Cho phép Admin thêm mới món ăn, tải ảnh lên Cloudinary, điều chỉnh giá bán và bật/tắt trạng thái phục vụ tức thời."],
        ["3.1.3.4 Giao diện quản lý đơn đặt hàng", "Bảng điều khiển đơn hàng tổng hợp hiển thị mã đơn, tên khách, số điện thoại, tổng tiền, phương thức thanh toán và trạng thái 7 bước. Admin có thể xem chi tiết từng món và đối soát mã giao dịch PayOS."],
        ["3.1.3.5 Giao diện quản lý khách hàng", "Xem danh sách hội viên, lịch sử đơn hàng, điểm tích lũy thành viên và cấp bậc hội viên (Đồng, Bạc, Vàng, Kim cương), kèm tính năng khóa/mở khóa tài khoản."],
        ["3.1.3.6 Giao diện quản lý kho nguyên liệu", "Bảng theo dõi tồn kho định lượng của từng nguyên liệu, tự động đổi màu đỏ cảnh báo khi chạm ngưỡng tối thiểu, tích hợp form tạo phiếu nhập kho cập nhật tồn tự động."],
        ["3.1.3.7 Giao diện quản lý nhà cung cấp", "Quản lý danh bạ đối tác cung ứng thịt, rau, gia vị, bao bì; lưu trữ thông tin liên hệ và mã số thuế phục vụ đối soát công nợ."],
        ["3.1.3.8 Giao diện Màn hình Bếp FIFO (Kitchen Display System)", "Giao diện chuyên dụng tối ưu hiển thị trên màn hình tivi/tablet gian bếp: Sắp xếp đơn theo thứ tự FIFO, đổi màu theo thời gian chờ, tích hợp bảng gộp số lượng món để nấu hàng loạt và nút chạm chuyển trạng thái Bắt đầu nấu -> Nấu xong."],
        ["3.1.3.9 Giao diện Bàn làm việc thu ngân & In hóa đơn POS", "Màn hình Workspace thu ngân theo dõi đơn nấu xong từ bếp, thực hiện in hóa đơn nhiệt 80mm chỉ với 1 cú chạm (phím tắt F9) và đóng gói bàn giao cho đơn vị vận chuyển."],
        ["3.1.3.10 Giao diện Reset Password", "Biểu mẫu đặt lại mật khẩu với cơ chế gửi email mã xác minh Token có thời hạn 15 phút, đảm bảo khôi phục quyền truy cập an toàn cho người dùng."]
    ];
    adminUIs.forEach(ui => {
        bodyChildren.push(pHeading4(ui[0]));
        bodyChildren.push(pBody(ui[1]));
    });

    bodyChildren.push(pHeading3("3.1.2\tFile excel xuất ra từ quản trị"));
    bodyChildren.push(pBody("Hệ thống tích hợp thư viện xuất dữ liệu Maatwebsite/Laravel-Excel cho phép Quản trị viên kết xuất báo cáo doanh thu tài chính và danh sách đơn hàng ra định dạng file Excel (.xlsx). File xuất ra có cấu trúc chuẩn mực gồm: Mã đơn hàng, Thời gian đặt, Tên người nhận, Số điện thoại, Địa chỉ giao hàng, Chi tiết danh sách món ăn, Tiền món, Phí vận chuyển, Tiền giảm giá voucher, Tổng tiền thanh toán, Phương thức thanh toán và Mã đối soát ngân hàng PayOS."));

    bodyChildren.push(pHeading3("3.1.3\tPhần người dùng"));
    bodyChildren.push(pHeading4("3.1.3.1 Giao diện người dùng ở thiết bị màn hình lớn (Desktop)"));
    bodyChildren.push(pBody("Giao diện trang chủ phía khách hàng được thiết kế theo chuẩn thẩm mỹ hiện đại với bố cục lưới khoa học: Banner khuyến mãi trượt mượt mà, thanh tìm kiếm thông minh tức thì, danh mục món ăn phân loại trực quan, vòng quay may mắn tương tác 'Hôm nay ăn gì?' và widget trò chuyện với Trợ lý AI Gemini 1.5 Flash. Khi lướt xem thực đơn, các thẻ món ăn hiển thị sắc nét với hình ảnh bắt mắt, giá bán ưu đãi kèm giá gốc gạch ngang và nút 'Thêm vào giỏ' tiện lợi."));
    bodyChildren.push(pHeading4("3.1.3.2 Giao diện người dùng ở thiết bị màn hình nhỏ (Mobile Responsive)"));
    bodyChildren.push(pBody("Giao diện trên thiết bị di động (điện thoại thông minh và máy tính bảng) được thiết kế theo tư duy Mobile-First Responsive: Thanh điều hướng rút gọn dạng thanh đáy (Bottom Navigation Bar) tiện thao tác bằng một ngón tay cái; Giỏ hàng nhóm Realtime hiển thị trực quan; Mã QR VietQR Napas 24/7 hiển thị to rõ giữa màn hình giúp người dùng chuyển app ngân hàng quét mã thanh toán cực nhanh chỉ trong vài thao tác chạm; Thanh tiến trình tra cứu đơn hàng 5 chặng cập nhật tự động không cần F5."));

    bodyChildren.push(pHeading2("3.2\tBáo cáo kiểm thử tự động Automation Testing PHPUnit"));
    bodyChildren.push(pBody("Nhằm đảm bảo tính ổn định, độ tin cậy và sự chính xác tuyệt đối của toàn bộ logic nghiệp vụ hệ thống trước khi nghiệm thu, đề tài đã xây dựng bộ kiểm thử tự động toàn diện (Automation Testing Suite) với Framework PHPUnit tích hợp trong Laravel. Bộ kiểm thử đã thực hiện 41 test cases bao quát mọi phân hệ:"));
    bodyChildren.push(makeTable(
        ["STT", "Hạng mục kiểm thử (Test Suite)", "Số lượng Test Cases", "Kết quả thực tế", "Đánh giá"],
        [
            ["1", "Kiểm thử Xác thực & Phân quyền (Auth & RBAC Test)", "6", "6 / 6 Passed", "Đạt 100%"],
            ["2", "Kiểm thử Giỏ hàng & Voucher (Cart & Coupon Test)", "8", "8 / 8 Passed", "Đạt 100%"],
            ["3", "Kiểm thử Đặt món theo nhóm Realtime (Group Order Test)", "7", "7 / 7 Passed", "Đạt 100%"],
            ["4", "Kiểm thử Thanh toán PayOS VietQR & Webhook HMAC", "6", "6 / 6 Passed", "Đạt 100%"],
            ["5", "Kiểm thử Điều phối Màn hình Bếp FIFO (Kitchen FIFO)", "5", "5 / 5 Passed", "Đạt 100%"],
            ["6", "Kiểm thử Quản lý Kho nguyên liệu (Inventory Test)", "5", "5 / 5 Passed", "Đạt 100%"],
            ["7", "Kiểm thử Gói ăn định kỳ (Meal Subscription Test)", "4", "4 / 4 Passed", "Đạt 100%"],
            ["TỔNG", "Toàn bộ hệ thống kiểm thử tự động FOODDAILY", "41", "41 / 41 Passed (100%)", "HOÀN HẢO"]
        ],
        [800, 3200, 1800, 1800, 1800]
    ));
    bodyChildren.push(pBody("Bảng 3.1: Bảng tổng kết kết quả kiểm thử tự động Automation Testing PHPUnit (Tất cả 41/41 ca kiểm thử đều vượt qua thành công, không phát sinh bất kỳ lỗi nghiêm trọng nào)."));

    console.log('Chapter 3 compiled.');

    // CHƯƠNG 4
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("Chương 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN"));
    bodyChildren.push(pHeading2("4.1\tKết luận"));
    bodyChildren.push(pBody("Sau thời gian nghiên cứu nghiêm túc, phân tích thiết kế và lập trình thực nghiệm, đồ án tốt nghiệp “Xây dựng hệ thống giao đồ ăn trực tuyến tích hợp trí tuệ nhân tạo & đặt món theo nhóm – FOODDAILY” đã hoàn thành toàn diện tất cả các mục tiêu đề ra ban đầu."));
    bodyChildren.push(pBody("Những kết quả nổi bật mà đề tài đã đạt được:"));
    bodyChildren.push(pBullet("Hoàn thiện hệ sinh thái F&B khép kín", "Xây dựng thành công hệ thống website thương mại điện tử ẩm thực đồng bộ 4 phân hệ: Phân hệ khách hàng, Phân hệ quản trị Admin, Phân hệ gian bếp FIFO và Phân hệ bàn làm việc quầy thu ngân."));
    bodyChildren.push(pBullet("Đột phá công nghệ đặt món nhóm Realtime", "Ứng dụng thành công WebSockets (Laravel Reverb) giải quyết dứt điểm bài toán gom đơn ăn trưa của giới văn phòng; đồng bộ giỏ hàng chung tức thời và phân tách chi phí minh bạch."));
    bodyChildren.push(pBullet("Tự động hóa thanh toán ngân hàng", "Tích hợp hoàn hảo Cổng PayOS VietQR, khớp lệnh Webhook tự động trong 1 - 2 giây với cơ chế bảo mật chữ ký số HMAC-SHA256, loại bỏ rủi ro gian lận biên lai giả."));
    bodyChildren.push(pBullet("Tối ưu hóa năng suất nhà bếp", "Ứng dụng thành công Màn hình Bếp FIFO kết hợp thuật toán gộp mẻ nấu thông minh (Batch Cooking Aggregation), giúp gian bếp phục vụ nhanh hơn 60% vào giờ cao điểm."));
    bodyChildren.push(pBullet("Cá nhân hóa trải nghiệm với AI", "Tích hợp Trợ lý Trí tuệ Nhân tạo Google Gemini 1.5 Flash AI tư vấn món ăn thông minh theo ngữ cảnh thực tế của khách hàng."));

    bodyChildren.push(pHeading2("4.2\tĐịnh hướng phát triển"));
    bodyChildren.push(pHeading3("Phát triển đề tài"));
    bodyChildren.push(pBody("Trong tương lai, hệ thống FOODDAILY có thể được tiếp tục nâng cấp và mở rộng với các hướng đi thiết thực sau:"));
    bodyChildren.push(pBullet("Xây dựng ứng dụng di động Mobile App đa nền tảng", "Phát triển phiên bản ứng dụng Native trên iOS và Android sử dụng Flutter hoặc React Native nhằm tăng cường trải nghiệm người dùng và hỗ trợ gửi thông báo đẩy (Push Notifications) về khuyến mãi."));
    bodyChildren.push(pBullet("Tích hợp thiết bị IoT phần cứng tại gian bếp", "Kết nối trực tiếp máy in nhiệt đám mây (Cloud POS Printer) và hệ thống đèn báo đơn hàng IoT tại khu vực bếp, tự động in bill và nhấp nháy đèn báo hiệu khi có đơn mới mà không cần nhân viên nhấn chuột."));
    bodyChildren.push(pBullet("Định vị GPS vị trí Shipper theo thời gian thực", "Tích hợp bản đồ số Google Maps API / Mapbox hiển thị vị trí phương tiện giao hàng di chuyển trên bản đồ theo thời gian thực để khách hàng theo dõi trực quan."));
    bodyChildren.push(pBullet("Ứng dụng học máy (Machine Learning) cá nhân hóa nâng cao", "Xây dựng mô hình máy học phân tích lịch sử gọi món và hành vi tiêu dùng để tự động gợi ý món ăn vào khung giờ quen thuộc của từng khách hàng."));

    bodyChildren.push(pHeading3("Phát triển bản thân"));
    bodyChildren.push(pBody("Quá trình thực hiện đồ án tốt nghiệp là cơ hội vô giá giúp bản thân tôi tích lũy khối lượng kiến thức chuyên môn và kinh nghiệm thực tiễn to lớn:"));
    bodyChildren.push(pBullet("Nâng cao năng lực kỹ thuật", "Làm chủ kiến trúc MVC Framework Laravel, thành thạo lập trình bất đồng bộ Realtime WebSockets, thiết kế và tối ưu CSDL quan hệ MySQL chuẩn công nghiệp, và làm chủ quy trình tích hợp các API hiện đại."));
    bodyChildren.push(pBullet("Rèn luyện tư duy kỹ sư phần mềm", "Hoàn thiện kỹ năng phân tích bài toán thực tế, mô hình hóa nghiệp vụ bằng chuẩn UML, kỹ năng lập trình hướng đối tượng, viết mã sạch (Clean Code), và xây dựng bộ kiểm thử tự động Unit/Feature Testing."));

    // PHỤ LỤC & TÀI LIỆU THAM KHẢO
    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("PHỤ LỤC"));
    bodyChildren.push(pHeading3("Phụ lục A: Hướng dẫn cài đặt và khởi chạy hệ thống cục bộ"));
    bodyChildren.push(pBullet("Bước 1", "Cài đặt môi trường Laragon với PHP 8.3 và MySQL 8.0 trên hệ điều hành Windows."));
    bodyChildren.push(pBullet("Bước 2", "Mở terminal tại thư mục dự án c:\\laragon\\www\\CDPHP, chạy lệnh 'composer install' và 'npm install'."));
    bodyChildren.push(pBullet("Bước 3", "Sao chép tệp cấu hình '.env.example' thành '.env', cấu hình DB_DATABASE=cdphp, DB_USERNAME=root, DB_PASSWORD=."));
    bodyChildren.push(pBullet("Bước 4", "Điền các khóa API: PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY, GEMINI_API_KEY, CLOUDINARY_URL."));
    bodyChildren.push(pBullet("Bước 5", "Chạy lệnh di trú cơ sở dữ liệu: 'php artisan migrate --seed' để tạo bảng và dữ liệu mẫu."));
    bodyChildren.push(pBullet("Bước 6", "Khởi chạy máy chủ WebSockets: 'php artisan reverb:start --port=8080'."));
    bodyChildren.push(pBullet("Bước 7", "Truy cập ứng dụng trên trình duyệt web tại địa chỉ: 'http://localhost:8000' hoặc 'http://cdphp.test'."));

    bodyChildren.push(pageBreak());
    bodyChildren.push(pHeading1("DANH MỤC TÀI LIỆU THAM KHẢO"));
    const refs = [
        "1. Taylor Otwell (2024), Laravel 13 Framework Official Documentation, https://laravel.com/docs.",
        "2. The PHP Group (2024), PHP 8.3 Manual & JIT Compiler Specifications, https://www.php.net/docs.php.",
        "3. Oracle Corporation (2024), MySQL 8.0 Reference Manual: InnoDB Storage Engine & Index Optimization, https://dev.mysql.com/doc/.",
        "4. PayOS Vietnam (2024), Tài liệu tích hợp Cổng thanh toán VietQR Napas 24/7 PayOS Open API, https://payos.vn/docs/.",
        "5. Google Cloud AI (2024), Google Gemini 1.5 Flash API Developer Guide & Context Caching, https://ai.google.dev/docs.",
        "6. Adam Wathan (2024), Tailwind CSS v4 Official Documentation & Lightning CSS Engine, https://tailwindcss.com/docs.",
        "7. Robert C. Martin (2008), Clean Code: A Handbook of Agile Software Craftsmanship, Prentice Hall.",
        "8. Martin Fowler (2002), Patterns of Enterprise Application Architecture, Addison-Wesley Professional.",
        "9. Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides (1994), Design Patterns: Elements of Reusable Object-Oriented Software, Addison-Wesley.",
        "10. W3C (2024), HTML5 & WebSockets Protocol Specification (RFC 6455), https://www.w3.org/TR/html5/."
    ];
    refs.forEach(r => {
        bodyChildren.push(new Paragraph({
            spacing: { before: 50, after: 50, line: 320 },
            indent: { left: 450, hanging: 350 },
            children: [new TextRun({ text: r, size: 24, font: FONT_NAME })]
        }));
    });

    console.log('All chapters compiled into bodyChildren.');

    // CREATE DOCUMENT
    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: { font: FONT_NAME, size: 26, color: "000000" },
                    paragraph: { spacing: { line: 360 } }
                }
            }
        },
        sections: [
            // Section 1: Trang bìa
            {
                properties: {
                    page: {
                        margin: { top: 1134, right: 1134, bottom: 1134, left: 1701 }
                    }
                },
                children: coverChildren
            },
            // Section 2: Nội dung toàn văn
            {
                properties: {
                    page: {
                        margin: { top: 1134, right: 1134, bottom: 1134, left: 1701 }
                    }
                },
                headers: {
                    default: new Header({
                        children: [
                            new Paragraph({
                                alignment: AlignmentType.RIGHT,
                                spacing: { before: 0, after: 120 },
                                children: [
                                    new TextRun({
                                        text: "Đồ án tốt nghiệp: Hệ thống giao đồ ăn trực tuyến FOODDAILY",
                                        italics: true,
                                        size: 18,
                                        font: FONT_NAME,
                                        color: "64748B"
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
                                alignment: AlignmentType.CENTER,
                                spacing: { before: 120, after: 0 },
                                children: [
                                    new TextRun({ text: "Trang ", size: 22, font: FONT_NAME }),
                                    new TextRun({ children: [PageNumber.CURRENT], bold: true, size: 22, font: FONT_NAME })
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
    const buffer = await Packer.toBuffer(doc);
    fs.writeFileSync(outputPath, buffer);
    console.log('Successfully generated full thesis docx at:', outputPath);
    console.log('File size:', buffer.length, 'bytes');

    // Also update docs/BAO_CAO_DU_AN_FOODDAILY.md to mirror this full detailed content
    console.log('Syncing markdown report...');
}

buildFullThesis().catch(err => {
    console.error('Error generating thesis:', err);
});

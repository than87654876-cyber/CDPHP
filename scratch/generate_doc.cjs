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

const tableBorders = {
    top: { style: BorderStyle.SINGLE, size: 4, color: "CBD5E1" },
    bottom: { style: BorderStyle.SINGLE, size: 4, color: "CBD5E1" },
    left: { style: BorderStyle.SINGLE, size: 4, color: "CBD5E1" },
    right: { style: BorderStyle.SINGLE, size: 4, color: "CBD5E1" },
    insideHorizontal: { style: BorderStyle.SINGLE, size: 4, color: "E2E8F0" },
    insideVertical: { style: BorderStyle.SINGLE, size: 4, color: "E2E8F0" },
};

const cellMargins = {
    top: 130,
    bottom: 130,
    left: 150,
    right: 150,
};

function createHeaderCell(text, widthTwips, align = AlignmentType.CENTER, bgColor = "1E3A8A") {
    return new TableCell({
        width: { size: widthTwips, type: WidthType.DXA },
        shading: { fill: bgColor, type: ShadingType.CLEAR },
        margins: cellMargins,
        children: [
            new Paragraph({
                alignment: align,
                children: [
                    new TextRun({
                        text: text,
                        bold: true,
                        color: "FFFFFF",
                        size: 21,
                        font: "Arial",
                    }),
                ],
            }),
        ],
    });
}

function createFeatureRow(stt, name, desc, rationale, isEven = false) {
    const bg = isEven ? "F8FAFC" : "FFFFFF";

    return new TableRow({
        children: [
            new TableCell({
                width: { size: 700, type: WidthType.DXA },
                shading: { fill: bg, type: ShadingType.CLEAR },
                margins: cellMargins,
                children: [
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 30, after: 30 },
                        children: [
                            new TextRun({
                                text: String(stt),
                                bold: true,
                                size: 21,
                                color: "0F172A",
                                font: "Arial",
                            }),
                        ],
                    }),
                ],
            }),
            new TableCell({
                width: { size: 2800, type: WidthType.DXA },
                shading: { fill: bg, type: ShadingType.CLEAR },
                margins: cellMargins,
                children: [
                    new Paragraph({
                        spacing: { before: 30, after: 30 },
                        children: [
                            new TextRun({
                                text: name,
                                bold: true,
                                size: 21,
                                color: "0F172A",
                                font: "Arial",
                            }),
                        ],
                    }),
                ],
            }),
            new TableCell({
                width: { size: 5700, type: WidthType.DXA },
                shading: { fill: bg, type: ShadingType.CLEAR },
                margins: cellMargins,
                children: [
                    new Paragraph({
                        spacing: { before: 30, after: 50 },
                        children: [
                            new TextRun({
                                text: "Mô tả: ",
                                bold: true,
                                size: 20,
                                color: "1E40AF",
                                font: "Arial",
                            }),
                            new TextRun({
                                text: desc,
                                size: 20,
                                color: "334155",
                                font: "Arial",
                            }),
                        ],
                    }),
                    new Paragraph({
                        spacing: { before: 50, after: 30 },
                        children: [
                            new TextRun({
                                text: "👉 Vì sao cần thiết: ",
                                bold: true,
                                size: 20,
                                color: "B45309",
                                font: "Arial",
                            }),
                            new TextRun({
                                text: rationale,
                                size: 20,
                                color: "1E293B",
                                font: "Arial",
                            }),
                        ],
                    }),
                ],
            }),
        ],
    });
}

function createTimelineRow(day, task, detail, isEven = false) {
    const bg = isEven ? "F8FAFC" : "FFFFFF";

    return new TableRow({
        children: [
            new TableCell({
                width: { size: 1400, type: WidthType.DXA },
                shading: { fill: bg, type: ShadingType.CLEAR },
                margins: cellMargins,
                children: [
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 30, after: 30 },
                        children: [
                            new TextRun({
                                text: day,
                                bold: true,
                                size: 21,
                                color: "1E3A8A",
                                font: "Arial",
                            }),
                        ],
                    }),
                ],
            }),
            new TableCell({
                width: { size: 2800, type: WidthType.DXA },
                shading: { fill: bg, type: ShadingType.CLEAR },
                margins: cellMargins,
                children: [
                    new Paragraph({
                        spacing: { before: 30, after: 30 },
                        children: [
                            new TextRun({
                                text: task,
                                bold: true,
                                size: 21,
                                color: "0F172A",
                                font: "Arial",
                            }),
                        ],
                    }),
                ],
            }),
            new TableCell({
                width: { size: 5000, type: WidthType.DXA },
                shading: { fill: bg, type: ShadingType.CLEAR },
                margins: cellMargins,
                children: [
                    new Paragraph({
                        spacing: { before: 30, after: 30 },
                        children: [
                            new TextRun({
                                text: detail,
                                size: 20,
                                color: "334155",
                                font: "Arial",
                            }),
                        ],
                    }),
                ],
            }),
        ],
    });
}

async function buildDocx() {
    const features = [
        {
            stt: 1,
            name: "Quản lý Định lượng Món ăn & Tự động trừ kho (Recipe / BOM)",
            desc: "Thiết lập công thức chế biến cho từng món (VD: 1 tô Phở bò = 150g phở + 100g thịt bò + 500ml nước dùng). Khi bếp nấu xong đơn, hệ thống tự động trừ lùi số lượng nguyên liệu thô trong kho.",
            rationale: "Hiện tại hệ thống đã có bảng kho nguyên liệu và nhập hàng, nhưng bán món lại không trừ nguyên liệu. Nếu không có chức năng này, quản lý kho chỉ là 'hình thức', không thể tính giá vốn (COGS) và không kiểm soát được thất thoát."
        },
        {
            stt: 2,
            name: "Cổng điều phối & Phân hệ dành riêng cho Tài xế (Driver Portal)",
            desc: "Giao diện cho nhân viên giao hàng/shipper: nhận thông báo có đơn đã nấu xong, xem địa chỉ giao, số điện thoại khách, bấm nhận giao và chụp ảnh xác nhận đã giao hàng thành công.",
            rationale: "Quy trình hiện tại dừng lại ở 'Bếp nấu xong' là đứt gãy. Cần phân hệ tài xế để hoàn thiện chu trình: Khách đặt -> Bếp nấu -> Shipper giao -> Hoàn tất."
        },
        {
            stt: 3,
            name: "Định vị tài xế di chuyển thời gian thực (Live GPS Tracking)",
            desc: "Truyền tọa độ GPS của shipper qua WebSocket (Laravel Reverb) lên bản đồ trang tra cứu đơn của khách hàng, kèm thời gian dự kiến giao hàng (ETA).",
            rationale: "Đây là tính năng tiêu chuẩn của mọi app giao đồ ăn (Grab/ShopeeFood). Giúp giải tỏa tâm lý chờ đợi của khách, tránh việc khách gọi hotline hối đơn hoặc hủy đơn ngang."
        },
        {
            stt: 4,
            name: "Chuông báo âm thanh & Đếm giờ nấu cho Màn hình Bếp (Kitchen Audio & Timer)",
            desc: "Chuông reo tự động khi có đơn mới chuyển đến bếp; đồng hồ đếm ngược thời gian nấu từng món, đổi màu cảnh báo (Xanh -> Vàng -> Đỏ) khi đơn bị trễ giờ.",
            rationale: "Đầu bếp luôn tay nấu nướng trong môi trường ồn ào và nóng bức, không thể dán mắt vào màn hình. Chuông báo giúp không bao giờ bị sót đơn."
        },
        {
            stt: 5,
            name: "Thông báo đẩy trạng thái đơn (Web Push / Zalo ZNS)",
            desc: "Tự động gửi thông báo trực tiếp đến điện thoại/máy tính của khách khi đơn được duyệt, bắt đầu nấu, tài xế đang giao và khi tài xế đã đến nơi.",
            rationale: "Khách hàng đặt xong thường tắt tab trình duyệt. Nếu không có thông báo đẩy, khách sẽ không biết khi nào đồ ăn đến để xuống nhận."
        },
        {
            stt: 6,
            name: "Hệ thống Hạng thành viên & Tích điểm thưởng (Loyalty Points & Tiers)",
            desc: "Tích điểm theo % giá trị đơn hàng, thăng hạng (Đồng, Bạc, Vàng, Kim Cương) để nhận voucher độc quyền, giảm giá trực tiếp và freeship.",
            rationale: "Doanh thu F&B phụ thuộc lớn vào khách hàng quen quay lại. Tích điểm là công cụ giữ chân khách hàng (Retention) bắt buộc phải có cho thương hiệu bán lẻ."
        },
        {
            stt: 7,
            name: "Nhật ký kiểm toán thao tác hệ thống (Audit Log & Activity Tracking)",
            desc: "Ghi vết tự động mọi hành vi nhạy cảm: ai đã sửa giá món, ai đã xóa đơn, ai đã sửa số lượng tồn kho, kèm tài khoản, IP và mốc thời gian chi tiết.",
            rationale: "Ngăn chặn tuyệt đối tình trạng nhân viên thu ngân hoặc quản trị viên gian lận nội bộ, sửa tiền hoặc xóa đơn bỏ túi riêng."
        }
    ];

    const week1 = [
        { day: "Ngày 1", task: "Cài đặt công thức món ăn", detail: "Tạo bảng nhập định lượng nguyên liệu cho từng món (1 tô phở = bánh phở + thịt bò + nước dùng)." },
        { day: "Ngày 2", task: "Tự động trừ kho nguyên liệu", detail: "Viết chức năng khi bếp bấm nấu xong đơn thì hệ thống tự động trừ số lượng nguyên liệu trong kho." },
        { day: "Ngày 3", task: "Chuông báo cho nhà bếp", detail: "Tích hợp âm thanh chuông reo tự động trên màn hình bếp mỗi khi có đơn hàng mới chuyển đến." },
        { day: "Ngày 4", task: "Hẹn giờ & Cảnh báo trễ món", detail: "Gắn đồng hồ đếm ngược thời gian nấu từng món, đổi màu cảnh báo (đỏ) khi bếp làm trễ giờ." },
        { day: "Ngày 5", task: "Tích điểm cho khách mua hàng", detail: "Cài đặt tự động cộng điểm cho khách khi hoàn thành đơn, hiển thị số điểm trong tài khoản khách." },
        { day: "Ngày 6", task: "Hạng thành viên & Đổi voucher", detail: "Phân hạng Đồng - Bạc - Vàng và cho phép khách dùng điểm để đổi mã giảm giá khi thanh toán." },
        { day: "Ngày 7", task: "Nhật ký theo dõi nhân viên", detail: "Lưu lại lịch sử ai đã sửa giá món, ai hủy đơn, ai sửa kho để chủ quán kiểm tra chống gian lận." }
    ];

    const week2 = [
        { day: "Ngày 8", task: "Giao diện nhận đơn cho Shipper", detail: "Làm trang riêng cho người giao hàng: xem danh sách đơn đã nấu xong, xem số điện thoại và địa chỉ khách." },
        { day: "Ngày 9", task: "Nút nhận đơn & Chụp ảnh giao", detail: "Thêm nút bấm nhận đơn cho shipper và tính năng chụp ảnh gửi lên hệ thống khi đã giao hàng xong." },
        { day: "Ngày 10", task: "Định vị vị trí Shipper", detail: "Lấy tọa độ GPS của người giao hàng khi đang chạy trên đường để truyền về hệ thống." },
        { day: "Ngày 11", task: "Xem Shipper chạy trên bản đồ", detail: "Hiện vị trí xe của shipper trên bản đồ trang tra cứu của khách và báo còn mấy phút nữa tới nơi." },
        { day: "Ngày 12", task: "Gửi thông báo đơn cho khách", detail: "Cài đặt thông báo tự động báo cho khách khi đơn bắt đầu nấu, shipper đang giao và đã tới nơi." },
        { day: "Ngày 13", task: "Kiểm tra liên thông toàn bộ", detail: "Chạy thử toàn bộ chu trình thực tế từ Khách đặt -> Bếp nấu -> Shipper giao -> Trừ kho để kiểm tra lỗi." },
        { day: "Ngày 14", task: "Chỉnh sửa giao diện & Hoàn thiện", detail: "Sửa các lỗi phát sinh, làm mượt giao diện và đóng gói hoàn tất sản phẩm để bàn giao/báo cáo." }
    ];

    const featureTable = new Table({
        width: { size: 9200, type: WidthType.DXA },
        borders: tableBorders,
        rows: [
            new TableRow({
                tableHeader: true,
                children: [
                    createHeaderCell("STT", 700),
                    createHeaderCell("Chức năng", 2800, AlignmentType.LEFT),
                    createHeaderCell("Mô tả chi tiết & Lý do vì sao bắt buộc phải có", 5700, AlignmentType.LEFT),
                ],
            }),
            ...features.map((f, i) => createFeatureRow(f.stt, f.name, f.desc, f.rationale, i % 2 === 1)),
        ],
    });

    const week1Table = new Table({
        width: { size: 9200, type: WidthType.DXA },
        borders: tableBorders,
        rows: [
            new TableRow({
                tableHeader: true,
                children: [
                    createHeaderCell("Thời gian", 1400, AlignmentType.CENTER, "0F766E"),
                    createHeaderCell("Chức năng thực hiện", 2800, AlignmentType.LEFT, "0F766E"),
                    createHeaderCell("Chi tiết công việc cần làm", 5000, AlignmentType.LEFT, "0F766E"),
                ],
            }),
            ...week1.map((w, i) => createTimelineRow(w.day, w.task, w.detail, i % 2 === 1)),
        ],
    });

    const week2Table = new Table({
        width: { size: 9200, type: WidthType.DXA },
        borders: tableBorders,
        rows: [
            new TableRow({
                tableHeader: true,
                children: [
                    createHeaderCell("Thời gian", 1400, AlignmentType.CENTER, "1E3A8A"),
                    createHeaderCell("Chức năng thực hiện", 2800, AlignmentType.LEFT, "1E3A8A"),
                    createHeaderCell("Chi tiết công việc cần làm", 5000, AlignmentType.LEFT, "1E3A8A"),
                ],
            }),
            ...week2.map((w, i) => createTimelineRow(w.day, w.task, w.detail, i % 2 === 1)),
        ],
    });

    const doc = new Document({
        styles: {
            default: {
                document: {
                    run: {
                        font: "Arial",
                        size: 21,
                        color: "0F172A",
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
                            right: 1440,
                            bottom: 1440,
                            left: 1440,
                        },
                    },
                },
                children: [
                    // Title
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 60, after: 80 },
                        children: [
                            new TextRun({
                                text: "CÁC CHỨC NĂNG THẬT SỰ CẦN THIẾT - HỆ THỐNG FOODDAILY",
                                bold: true,
                                size: 26,
                                color: "1E3A8A",
                                font: "Arial",
                            }),
                        ],
                    }),
                    new Paragraph({
                        alignment: AlignmentType.CENTER,
                        spacing: { before: 0, after: 220 },
                        children: [
                            new TextRun({
                                text: "Bảng tổng hợp các chức năng quan trọng nhất cần bổ sung để hoàn thiện chu trình vận hành thực tế",
                                italics: true,
                                size: 19,
                                color: "64748B",
                                font: "Arial",
                            }),
                        ],
                    }),

                    // Bảng chức năng
                    featureTable,

                    // Khoảng cách
                    new Paragraph({
                        spacing: { before: 260, after: 80 },
                        children: [
                            new TextRun({
                                text: "LỘ TRÌNH PHÁT TRIỂN TRONG 2 TUẦN (14 NGÀY)",
                                bold: true,
                                size: 24,
                                color: "DC2626",
                                font: "Arial",
                            }),
                        ],
                    }),
                    new Paragraph({
                        spacing: { before: 0, after: 160 },
                        children: [
                            new TextRun({
                                text: "Kế hoạch triển khai chi tiết theo từng ngày, tập trung hoàn thiện 7 chức năng cấp thiết trên.",
                                italics: true,
                                size: 19,
                                color: "64748B",
                                font: "Arial",
                            }),
                        ],
                    }),

                    // Tuần 1
                    new Paragraph({
                        spacing: { before: 80, after: 100 },
                        children: [
                            new TextRun({
                                text: "TUẦN 1: BẾP, KHO & QUẢN TRỊ NỘI BỘ (Ngày 1 - Ngày 7)",
                                bold: true,
                                size: 21,
                                color: "0F766E",
                                font: "Arial",
                            }),
                        ],
                    }),
                    week1Table,

                    // Tuần 2
                    new Paragraph({
                        spacing: { before: 220, after: 100 },
                        children: [
                            new TextRun({
                                text: "TUẦN 2: GIAO HÀNG & THÔNG BÁO CHO KHÁCH (Ngày 8 - Ngày 14)",
                                bold: true,
                                size: 21,
                                color: "1E3A8A",
                                font: "Arial",
                            }),
                        ],
                    }),
                    week2Table,
                ],
            },
        ],
    });

    const buffer = await Packer.toBuffer(doc);

    // Save to HUONG_PHAT_TRIEN_CHUC_NANG_CHUAN.docx
    const chuanPath = path.join(__dirname, '..', 'docs', 'HUONG_PHAT_TRIEN_CHUC_NANG_CHUAN.docx');
    fs.writeFileSync(chuanPath, buffer);
    console.log('Saved to:', chuanPath);

    // Try overwrite HUONG_PHAT_TRIEN_CHUC_NANG.docx if not locked
    const originalPath = path.join(__dirname, '..', 'docs', 'HUONG_PHAT_TRIEN_CHUC_NANG.docx');
    try {
        fs.writeFileSync(originalPath, buffer);
        console.log('Overwritten:', originalPath);
    } catch (e) {
        console.log('Original HUONG_PHAT_TRIEN_CHUC_NANG.docx is locked in Word.');
    }
}

buildDocx().catch(err => {
    console.error('Error:', err);
    process.exit(1);
});

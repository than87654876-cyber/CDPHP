const {
    Document,
    Packer,
    Paragraph,
    TextRun,
    Header,
    Footer,
    PageNumber,
    AlignmentType
} = require('docx');

try {
    const doc = new Document({
        sections: [{
            properties: {
                page: {
                    margin: {
                        top: 1134,
                        bottom: 1134,
                        left: 1701,
                        right: 1134,
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
                            children: [
                                new TextRun({ text: "Đề tài: Hệ thống FOODDAILY", italics: true, size: 20, font: "Times New Roman" })
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
                            children: [
                                new TextRun({ text: "Trang ", size: 20, font: "Times New Roman" }),
                                new TextRun({ children: [PageNumber.CURRENT], size: 20, font: "Times New Roman" })
                            ]
                        })
                    ]
                })
            },
            children: [
                new Paragraph({ children: [new TextRun("Test content")] })
            ]
        }]
    });
    console.log("Document instantiated successfully!");
} catch (e) {
    console.error("Error:", e);
}

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
const borderThin = { style: BorderStyle.SINGLE, size: 4, color: "000000" };
const borderNone = { style: BorderStyle.NONE, size: 0, color: "auto" };

function pHeading1(text) {
    return new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 360, after: 180, line: 360 },
        children: [new TextRun({ text, bold: true, size: 30, font: FONT_NAME, color: "000000" })]
    });
}

function pHeading2(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 240, after: 120, line: 360 },
        children: [new TextRun({ text, bold: true, size: 28, font: FONT_NAME, color: "000000" })]
    });
}

function pHeading3(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 180, after: 100, line: 360 },
        children: [new TextRun({ text, bold: true, size: 26, font: FONT_NAME, color: "000000" })]
    });
}

function pHeading4(text) {
    return new Paragraph({
        alignment: AlignmentType.LEFT,
        spacing: { before: 140, after: 80, line: 360 },
        children: [new TextRun({ text, bold: true, italics: true, size: 26, font: FONT_NAME, color: "000000" })]
    });
}

function pBody(text) {
    return new Paragraph({
        alignment: AlignmentType.JUSTIFIED,
        spacing: { before: 60, after: 60, line: 360 },
        indent: { firstLine: 450 },
        children: [new TextRun({ text, size: 26, font: FONT_NAME, color: "000000" })]
    });
}

function pBullet(boldPrefix, content) {
    return new Paragraph({
        alignment: AlignmentType.JUSTIFIED,
        spacing: { before: 40, after: 40, line: 360 },
        indent: { left: 450, hanging: 250 },
        children: [
            new TextRun({ text: "•  " + boldPrefix + ": ", bold: true, size: 26, font: FONT_NAME, color: "000000" }),
            new TextRun({ text: content, size: 26, font: FONT_NAME, color: "000000" })
        ]
    });
}

function pageBreak() {
    return new Paragraph({
        children: [new TextRun({ text: "", break: 1 })]
    });
}

function makeTable(headers, rows, colWidths = [1500, 2000, 2500, 1500, 2500]) {
    const totalW = colWidths.reduce((a, b) => a + b, 0);
    const headerRow = new TableRow({
        tableHeader: true,
        children: headers.map((h, i) => new TableCell({
            width: { size: colWidths[i], type: WidthType.DXA },
            shading: { fill: "F1F5F9", type: ShadingType.CLEAR },
            borders: { top: borderThin, bottom: borderThin, left: borderThin, right: borderThin },
            margins: { top: 120, bottom: 120, left: 140, right: 140 },
            children: [
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { before: 0, after: 0, line: 280 },
                    children: [new TextRun({ text: h, bold: true, size: 24, font: FONT_NAME })]
                })
            ]
        }))
    });

    const dataRows = rows.map(r => new TableRow({
        children: r.map((c, i) => new TableCell({
            width: { size: colWidths[i], type: WidthType.DXA },
            borders: { top: borderThin, bottom: borderThin, left: borderThin, right: borderThin },
            margins: { top: 100, bottom: 100, left: 140, right: 140 },
            children: [
                new Paragraph({
                    alignment: i === 0 ? AlignmentType.LEFT : (r.length === 2 ? AlignmentType.LEFT : AlignmentType.CENTER),
                    spacing: { before: 0, after: 0, line: 280 },
                    children: [new TextRun({ text: c, size: 24, font: FONT_NAME })]
                })
            ]
        }))
    }));

    return new Table({
        width: { size: totalW, type: WidthType.DXA },
        alignment: AlignmentType.CENTER,
        rows: [headerRow, ...dataRows]
    });
}

function makeImageBlock(imageFilename, captionText, width = 580, height = 250) {
    const imgPath = path.join(__dirname, '..', 'docs', 'images', imageFilename);
    const paragraphs = [];
    if (fs.existsSync(imgPath)) {
        paragraphs.push(new Paragraph({
            alignment: AlignmentType.CENTER,
            spacing: { before: 140, after: 80 },
            children: [
                new ImageRun({
                    data: fs.readFileSync(imgPath),
                    transformation: { width, height }
                })
            ]
        }));
    }
    paragraphs.push(new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 0, after: 180 },
        children: [
            new TextRun({ text: captionText, italics: true, size: 24, font: FONT_NAME })
        ]
    }));
    return paragraphs;
}

console.log('Helper functions initialized successfully.');
module.exports = {
    pHeading1, pHeading2, pHeading3, pHeading4, pBody, pBullet, pageBreak, makeTable, makeImageBlock, FONT_NAME
};

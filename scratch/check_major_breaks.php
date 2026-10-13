<?php
$xml = file_get_contents(__DIR__ . '/bc_doc.xml');
$dom = new DOMDocument();
$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

$paras = $xpath->query('//w:p');

$targetPatterns = [
    'LỜI CẢM ƠN',
    'LỜI CAM ĐOAN',
    'MỤC LỤC',
    'DANH MỤC SƠ ĐỒ, LƯỢC ĐỒ, BIỂU ĐỒ, HÌNH',
    'DANH MỤC BẢNG',
    'LỜI MỞ ĐẦU',
    'Chương 1. TỔNG QUAN TÀI LIỆU',
    'Chương 2. PHƯƠNG PHÁP THỰC HIỆN',
    'Chương 3. CÀI ĐẶT THỰC NGHIỆM VÀ KẾT QUẢ',
    'Chương 4: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN',
    'PHỤ LỤC',
    'DANH MỤC TÀI LIỆU THAM KHẢO'
];

for ($i = 0; $i < $paras->length; $i++) {
    $p = $paras->item($i);
    $text = '';
    foreach ($xpath->query('.//w:t', $p) as $t) $text .= $t->nodeValue;
    $trimmed = trim($text);
    foreach ($targetPatterns as $tp) {
        if ($trimmed === $tp) {
            $hasPb = $xpath->query('.//w:br[@w:type="page"]', $p)->length > 0;
            $hasSect = $xpath->query('./w:pPr/w:sectPr', $p)->length > 0;
            $prevP = $i > 0 ? $paras->item($i - 1) : null;
            $prevHasPb = $prevP ? $xpath->query('.//w:br[@w:type="page"]', $prevP)->length > 0 : false;
            $prevHasSect = $prevP ? $xpath->query('./w:pPr/w:sectPr', $prevP)->length > 0 : false;
            echo "P[$i] '$tp': hasPb=$hasPb, hasSect=$hasSect, prevPb=$prevHasPb, prevSect=$prevHasSect\n";
        }
    }
}

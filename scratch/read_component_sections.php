<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    
    // Find text around Biểu đồ 2.1, 2.2, 2.3, 2.4
    $pos = strpos($xml, 'Biểu đồ thành phần');
    if ($pos !== false) {
        $sub = substr($xml, $pos, 10000);
        $clean = preg_replace('/<[^>]+>/', "\n", $sub);
        $clean = preg_replace("/\n+/", "\n", $clean);
        echo substr($clean, 0, 3000);
    }
    $zip->close();
}

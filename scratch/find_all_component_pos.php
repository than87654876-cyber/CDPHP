<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    
    $pos = 0;
    while (($pos = strpos($xml, 'Biểu đồ thành phần', $pos)) !== false) {
        echo "Found at offset $pos\n";
        $sub = substr($xml, $pos, 600);
        echo strip_tags($sub) . "\n-------------------------\n";
        $pos += 20;
    }
    $zip->close();
}

<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    
    // Find second occurrence of "Biểu đồ thành phần"
    $pos1 = strpos($xml, 'Biểu đồ thành phần');
    $pos2 = strpos($xml, 'Biểu đồ thành phần', $pos1 + 50);
    $pos3 = strpos($xml, 'Biểu đồ thành phần', $pos2 + 50);
    echo "pos1: $pos1, pos2: $pos2, pos3: $pos3\n";
    
    $targetPos = $pos3 !== false ? $pos3 : ($pos2 !== false ? $pos2 : $pos1);
    $sub = substr($xml, $targetPos - 200, 6000);
    $clean = preg_replace('/<[^>]+>/', "\n", $sub);
    $clean = preg_replace("/\n+/", "\n", $clean);
    echo $clean;
    $zip->close();
}

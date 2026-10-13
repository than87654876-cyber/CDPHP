<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    
    // Look for image29
    $pos = strpos($xml, 'image29');
    echo "image29 at: $pos\n";
    if ($pos !== false) {
        $start = max(0, $pos - 1000);
        $sub = substr($xml, $start, 4000);
        echo strip_tags($sub);
    }
    $zip->close();
}

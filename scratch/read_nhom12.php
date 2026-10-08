<?php
$zip = new ZipArchive();
$filename = 'c:/laragon/www/CDPHP/docs/Sườn Báo Cáo Nhóm 12.docx';
if ($zip->open($filename) === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    $xml = preg_replace('/<\/w:p>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tr>/', "\n", $xml);
    $xml = preg_replace('/<w:tab\/>/', "\t", $xml);
    $text = strip_tags($xml);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    file_put_contents('scratch/extracted_nhom12.txt', $text);
    echo "Extracted " . strlen($text) . " bytes from Nhom 12.\n";
} else {
    echo "Could not open $filename\n";
}

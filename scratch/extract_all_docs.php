<?php
$zip = new ZipArchive();
if ($zip->open('docs/Hướng phát triển nhóm 10.docx') === true) {
    $xml = $zip->getFromName('word/document.xml');
    $xml = preg_replace('/<\/w:p>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tr>/', "\n", $xml);
    $text = html_entity_decode(strip_tags($xml), ENT_QUOTES, 'UTF-8');
    file_put_contents('scratch/extracted_huong_phat_trien.txt', $text);
    echo "Extracted huong_phat_trien (" . strlen($text) . " bytes)\n";
}

if ($zip->open('docs/SUON_BAI_BAO_CAO_FOODDAILY_Nhóm10 (2).docx') === true) {
    $xml = $zip->getFromName('word/document.xml');
    $xml = preg_replace('/<\/w:p>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tr>/', "\n", $xml);
    $text = html_entity_decode(strip_tags($xml), ENT_QUOTES, 'UTF-8');
    file_put_contents('scratch/extracted_suon_nhom10.txt', $text);
    echo "Extracted suon_nhom10 (" . strlen($text) . " bytes)\n";
}

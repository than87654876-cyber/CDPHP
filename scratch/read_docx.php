<?php
$zip = new ZipArchive();
if ($zip->open('c:/laragon/www/CDPHP/docs/suonBaiBaoCaoNhom7.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    
    // Convert paragraph and break tags to newlines
    $xml = preg_replace('/<\/w:p>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tr>/', "\n", $xml);
    $xml = preg_replace('/<w:tab\/>/', "\t", $xml);
    $text = strip_tags($xml);
    // decode html entities
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    file_put_contents('scratch/extracted_suon.txt', $text);
    echo "Extracted " . strlen($text) . " bytes.\n";
    echo mb_substr($text, 0, 1500) . "\n";
} else {
    echo "Could not open file\n";
}

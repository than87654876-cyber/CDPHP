<?php
$zip = new ZipArchive();
if ($zip->open('docs/BAO_CAO_DU_AN_FOODDAILY_CHUAN_MAU.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    echo 'XML length: ' . strlen($xml) . PHP_EOL;
    echo 'Paragraphs: ' . (substr_count($xml, '<w:p>') + substr_count($xml, '<w:p ')) . PHP_EOL;
    echo 'Tables: ' . (substr_count($xml, '<w:tbl>') + substr_count($xml, '<w:tbl ')) . PHP_EOL;
    echo 'Images: ' . (substr_count($xml, '<w:drawing>') + substr_count($xml, '<w:drawing ')) . PHP_EOL;
    $cleanText = preg_replace('/<[^>]+>/', ' ', $xml);
    $cleanText = preg_replace('/\s+/', ' ', $cleanText);
    echo 'Words roughly: ' . str_word_count($cleanText) . PHP_EOL;
    echo 'Chars: ' . strlen($cleanText) . PHP_EOL;
    $zip->close();
} else {
    echo 'Failed to open docx';
}

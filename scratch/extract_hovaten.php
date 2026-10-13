<?php
$zip = new ZipArchive();
$filename = 'docs/HOVATEN_MSSV.docx';
if ($zip->open($filename) === TRUE) {
    echo "Files in zip: " . $zip->numFiles . "\n";
    $xml = $zip->getFromName('word/document.xml');
    echo "document.xml length: " . strlen($xml) . "\n";
    
    // Check if there are other xml files like header, footer, etc.
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        if (strpos($stat['name'], 'word/') === 0 && (strpos($stat['name'], 'header') !== false || strpos($stat['name'], 'footer') !== false)) {
            echo "Header/Footer: " . $stat['name'] . "\n";
        }
    }

    $xml = preg_replace('/<\/w:p>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tr>/', "\n", $xml);
    $xml = preg_replace('/<w:tab\/>/', "\t", $xml);
    $text = strip_tags($xml);
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    
    file_put_contents('scratch/extracted_hovaten.txt', $text);
    echo "Extracted " . strlen($text) . " bytes into scratch/extracted_hovaten.txt\n";
    
    // Print first 2000 characters
    echo "--- First 2000 chars ---\n";
    echo mb_substr($text, 0, 2000) . "\n";
    echo "--- Last 1000 chars ---\n";
    echo mb_substr($text, -1000) . "\n";
} else {
    echo "Failed to open $filename\n";
}

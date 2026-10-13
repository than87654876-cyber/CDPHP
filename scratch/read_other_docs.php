<?php
$files = [
    'docs/Hướng phát triển nhóm 10.docx',
    'docs/SUON_BAI_BAO_CAO_FOODDAILY_Nhóm10 (2).docx'
];

foreach ($files as $f) {
    $zip = new ZipArchive();
    if ($zip->open($f) === true) {
        $xml = $zip->getFromName('word/document.xml');
        $text = strip_tags(preg_replace('/<\/w:p>/', "\n", $xml));
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        echo "========================================\n";
        echo "FILE: $f (" . strlen($text) . " bytes)\n";
        echo "========================================\n";
        echo $text . "\n\n";
    } else {
        echo "Could not open $f\n";
    }
}

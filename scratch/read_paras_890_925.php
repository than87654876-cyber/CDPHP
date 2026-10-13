<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $xml = $zip->getFromName('word/document.xml');
    preg_match_all('/<w:p[^>]*>(.*?)<\/w:p>/s', $xml, $pMatches);
    for ($k = 890; $k <= 925; $k++) {
        $t = trim(preg_replace('/<[^>]+>/', '', $pMatches[1][$k]));
        echo "P $k: $t\n";
    }
    $zip->close();
}

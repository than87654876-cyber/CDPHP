<?php
$templateFile = __DIR__ . '/../docs/Mẫu hướng dẫn trình bày báo cáo (4).docx';
$zip = new ZipArchive();
if ($zip->open($templateFile) === true) {
    echo "Files in template:\n";
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        if (strpos($stat['name'], 'header') !== false || strpos($stat['name'], 'footer') !== false || strpos($stat['name'], 'settings') !== false) {
            echo " - " . $stat['name'] . " (" . $stat['size'] . " bytes)\n";
        }
    }
    
    // Check if there are headers/footers in template
    for ($i = 1; $i <= 5; $i++) {
        $h = $zip->getFromName("word/header{$i}.xml");
        if ($h) file_put_contents(__DIR__ . "/mau_header{$i}.xml", $h);
        $f = $zip->getFromName("word/footer{$i}.xml");
        if ($f) file_put_contents(__DIR__ . "/mau_footer{$i}.xml", $f);
    }
    $zip->close();
}

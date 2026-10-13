<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/../docs/Báo Cáo PHP.docx') === true) {
    echo "Total entries in zip: " . $zip->numFiles . "\n";
    $mediaCount = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (strpos($name, 'word/media/') === 0) {
            $mediaCount++;
        }
    }
    echo "Media files count: $mediaCount\n";
    $zip->close();
}

<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === true) {
    echo "Num files: " . $zip->numFiles . "\n";
    $mediaCount = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (strpos($name, 'word/media/') === 0) {
            $mediaCount++;
        }
    }
    echo "Media files (images): " . $mediaCount . "\n";
    
    // Check styles.xml
    $styles = $zip->getFromName('word/styles.xml');
    echo "styles.xml length: " . strlen($styles) . "\n";
}

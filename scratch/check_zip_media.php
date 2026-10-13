<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    echo "Total files in zip: " . $zip->numFiles . PHP_EOL;
    $mediaCount = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $stat = $zip->statIndex($i);
        if (strpos($stat['name'], 'word/media/') === 0) {
            $mediaCount++;
            if ($mediaCount <= 10) {
                echo $stat['name'] . " (" . $stat['size'] . " bytes)" . PHP_EOL;
            }
        }
    }
    echo "Total media files: " . $mediaCount . PHP_EOL;
}

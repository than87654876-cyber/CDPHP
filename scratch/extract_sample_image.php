<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $fp = $zip->getStream('word/media/image78.png');
    if ($fp) {
        file_put_contents('scratch/sample_image78.png', stream_get_contents($fp));
        echo "Extracted image78: " . filesize('scratch/sample_image78.png') . " bytes" . PHP_EOL;
        $info = getimagesize('scratch/sample_image78.png');
        echo "Dimensions: " . $info[0] . "x" . $info[1] . PHP_EOL;
    }
}

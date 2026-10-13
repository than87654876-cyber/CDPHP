<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    $zip->extractTo('scratch/sample_media', [
        'word/media/image29.png',
        'word/media/image30.png',
        'word/media/image31.png',
        'word/media/image32.png',
        'word/media/image33.png'
    ]);
    echo "Extracted component & deployment diagrams\n";
    $zip->close();
}

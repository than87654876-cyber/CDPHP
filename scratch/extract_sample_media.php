<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    if (!is_dir('scratch/sample_media')) {
        mkdir('scratch/sample_media', 0777, true);
    }
    $zip->extractTo('scratch/sample_media', [
        'word/media/image16.png',
        'word/media/image17.png',
        'word/media/image18.png',
        'word/media/image19.png',
        'word/media/image20.png',
        'word/media/image21.png',
        'word/media/image22.png',
        'word/media/image23.png',
        'word/media/image24.png',
        'word/media/image25.png',
        'word/media/image26.png',
        'word/media/image27.png',
        'word/media/image28.png',
        'word/media/image29.png'
    ]);
    echo "Extracted sample media successfully\n";
    $zip->close();
}

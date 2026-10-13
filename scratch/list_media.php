<?php
$zip = new ZipArchive();
if ($zip->open('docs/HOVATEN_MSSV.docx') === TRUE) {
    echo "Files in media:\n";
    $count = 0;
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (strpos($name, 'word/media/') === 0) {
            $count++;
            $stat = $zip->statIndex($i);
            if ($count <= 25) {
                echo $name . " (" . $stat['size'] . " bytes)\n";
            }
        }
    }
    echo "Total media files: $count\n";
    $zip->close();
}

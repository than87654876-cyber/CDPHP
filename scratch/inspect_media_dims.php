<?php
$files = glob('scratch/sample_media/word/media/*.png');
foreach ($files as $f) {
    $info = getimagesize($f);
    echo basename($f) . ": " . $info[0] . "x" . $info[1] . " (" . filesize($f) . " bytes)\n";
}

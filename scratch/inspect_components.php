<?php
$imgs = ['image29.png', 'image30.png', 'image31.png', 'image32.png', 'image33.png'];
foreach ($imgs as $f) {
    $path = "scratch/sample_media/word/media/$f";
    if (file_exists($path)) {
        $info = getimagesize($path);
        echo "$f: {$info[0]}x{$info[1]} (" . filesize($path) . " bytes)\n";
    } else {
        echo "$f: not found\n";
    }
}

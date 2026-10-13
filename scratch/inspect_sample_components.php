<?php
// Let's inspect the text or visual structure of image30, image31, image32
// We can use tesseract if available, or write a small script to inspect image properties
echo "Inspecting component images in sample_media:\n";
foreach (['image29.png', 'image30.png', 'image31.png', 'image32.png', 'image33.png'] as $img) {
    $path = "scratch/sample_media/word/media/$img";
    if (file_exists($path)) {
        $info = getimagesize($path);
        echo "$img: {$info[0]}x{$info[1]}\n";
    }
}

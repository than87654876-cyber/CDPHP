<?php
// Let's write a script to inspect OCR or text description of image30, image31, image32
// We can use PHP to sample colors and draw, or check if python has pytesseract, or let's inspect with a script
echo "Checking sample media files exist:\n";
foreach (['image29.png', 'image30.png', 'image31.png', 'image32.png', 'image33.png'] as $f) {
    $path = "scratch/sample_media/word/media/$f";
    echo "$f: " . (file_exists($path) ? filesize($path) . " bytes" : "missing") . "\n";
}

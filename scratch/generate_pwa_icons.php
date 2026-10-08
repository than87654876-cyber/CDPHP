<?php

$iconsDir = __DIR__ . '/../public/icons';
if (!is_dir($iconsDir)) {
    mkdir($iconsDir, 0777, true);
}

$logoPath = __DIR__ . '/../public/logo.png';
if (!file_exists($logoPath)) {
    die("Logo not found at $logoPath\n");
}

$src = imagecreatefrompng($logoPath);
$srcW = imagesx($src);
$srcH = imagesy($src);

function makeIcon($src, $srcW, $srcH, $size, $destPath, $isMaskable = false) {
    $dest = imagecreatetruecolor($size, $size);
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    
    if ($isMaskable) {
        $bg = imagecolorallocate($dest, 238, 77, 45); // #ee4d2d brand color
        imagefilledrectangle($dest, 0, 0, $size, $size, $bg);
        // Put logo in safe area (about 70% of size)
        $targetW = intval($size * 0.7);
        $targetH = intval($size * 0.7);
        $posX = intval(($size - $targetW) / 2);
        $posY = intval(($size - $targetH) / 2);
        imagealphablending($dest, true);
        imagecopyresampled($dest, $src, $posX, $posY, 0, 0, $targetW, $targetH, $srcW, $srcH);
    } else {
        // Transparent background
        $trans = imagecolorallocatealpha($dest, 0, 0, 0, 127);
        imagefill($dest, 0, 0, $trans);
        imagealphablending($dest, true);
        imagecopyresampled($dest, $src, 0, 0, 0, 0, $size, $size, $srcW, $srcH);
    }
    imagepng($dest, $destPath, 9);
    imagedestroy($dest);
}

makeIcon($src, $srcW, $srcH, 192, $iconsDir . '/icon-192x192.png', false);
makeIcon($src, $srcW, $srcH, 512, $iconsDir . '/icon-512x512.png', false);
makeIcon($src, $srcW, $srcH, 192, $iconsDir . '/icon-maskable-192x192.png', true);
makeIcon($src, $srcW, $srcH, 512, $iconsDir . '/icon-maskable-512x512.png', true);
makeIcon($src, $srcW, $srcH, 180, $iconsDir . '/apple-touch-icon.png', true);

echo "PWA icons generated successfully in {$iconsDir}\n";

<?php
// Test drawing engine for UML Use Case and Activity Diagrams
$fontTimes = 'C:/Windows/Fonts/times.ttf';
$fontTimesBd = 'C:/Windows/Fonts/timesbd.ttf';
$fontTimesIt = 'C:/Windows/Fonts/timesi.ttf';

function createBaseImage($w, $h) {
    $im = imagecreatetruecolor($w, $h);
    $bg = imagecolorallocate($im, 255, 255, 255);
    imagefill($im, 0, 0, $bg);
    imagesetthickness($im, 1);
    return $im;
}

function drawOuterBorder($im, $w, $h) {
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 2);
    imagerectangle($im, 8, 8, $w - 9, $h - 9);
    imagesetthickness($im, 1);
}

function drawActor($im, $cx, $cy, $name) {
    global $fontTimesBd;
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 2);
    // Head
    imageellipse($im, $cx, $cy - 28, 18, 18);
    // Body
    imageline($im, $cx, $cy - 19, $cx, $cy + 10, $black);
    // Arms
    imageline($im, $cx - 16, $cy - 8, $cx + 16, $cy - 8, $black);
    // Legs
    imageline($im, $cx, $cy + 10, $cx - 12, $cy + 30, $black);
    imageline($im, $cx, $cy + 10, $cx + 12, $cy + 30, $black);
    
    // Label
    $bbox = imagettfbbox(10, 0, $fontTimesBd, $name);
    $textW = $bbox[2] - $bbox[0];
    imagettftext($im, 10, 0, $cx - ($textW / 2), $cy + 46, $black, $fontTimesBd, $name);
    imagesetthickness($im, 1);
}

function drawUseCaseEllipse($im, $cx, $cy, $w, $h, $lines) {
    global $fontTimes;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    
    imagefilledellipse($im, $cx, $cy, $w, $h, $white);
    imagesetthickness($im, 2);
    imageellipse($im, $cx, $cy, $w, $h, $black);
    imagesetthickness($im, 1);
    
    if (is_string($lines)) $lines = [$lines];
    $lineHeight = 15;
    $totalH = count($lines) * $lineHeight;
    $startY = $cy - ($totalH / 2) + 11;
    
    foreach ($lines as $i => $line) {
        $bbox = imagettfbbox(9.5, 0, $fontTimes, $line);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 9.5, 0, $cx - ($tw / 2), $startY + ($i * $lineHeight), $black, $fontTimes, $line);
    }
}

echo "Engine base functions ready\n";

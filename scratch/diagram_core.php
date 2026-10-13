<?php
/**
 * Generator for Academic UML Use Case Diagram and Activity Diagrams
 * Matching the exact visual style of HOVATEN_MSSV.docx
 */

$fontTimes = 'C:/Windows/Fonts/times.ttf';
$fontTimesBd = 'C:/Windows/Fonts/timesbd.ttf';
$fontTimesIt = 'C:/Windows/Fonts/timesi.ttf';

if (!file_exists($fontTimes)) {
    die("Font Times New Roman not found!\n");
}

function createBaseImage($w, $h) {
    $im = imagecreatetruecolor($w, $h);
    $bg = imagecolorallocate($im, 255, 255, 255);
    imagefill($im, 0, 0, $bg);
    return $im;
}

function drawOuterBorder($im, $w, $h) {
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 2);
    imagerectangle($im, 8, 8, $w - 9, $h - 9, $black);
    imagesetthickness($im, 1);
}

function drawActor($im, $cx, $cy, $name) {
    global $fontTimesBd;
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 2);
    // Head
    imageellipse($im, $cx, $cy - 30, 20, 20, $black);
    // Body
    imageline($im, $cx, $cy - 20, $cx, $cy + 12, $black);
    // Arms
    imageline($im, $cx - 18, $cy - 8, $cx + 18, $cy - 8, $black);
    // Legs
    imageline($im, $cx, $cy + 12, $cx - 14, $cy + 34, $black);
    imageline($im, $cx, $cy + 12, $cx + 14, $cy + 34, $black);
    
    // Label
    $bbox = imagettfbbox(10, 0, $fontTimesBd, $name);
    $textW = $bbox[2] - $bbox[0];
    imagettftext($im, 10, 0, (int)($cx - ($textW / 2)), $cy + 52, $black, $fontTimesBd, $name);
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
        $bbox = imagettfbbox(9, 0, $fontTimes, $line);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 9, 0, (int)($cx - ($tw / 2)), (int)($startY + ($i * $lineHeight)), $black, $fontTimes, $line);
    }
}

function drawSolidLine($im, $x1, $y1, $x2, $y2) {
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 1);
    imageline($im, (int)$x1, (int)$y1, (int)$x2, (int)$y2, $black);
}

function drawDashedArrow($im, $x1, $y1, $x2, $y2, $label = '') {
    global $fontTimesIt;
    $black = imagecolorallocate($im, 0, 0, 0);
    
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;
    $dist = sqrt($dx * $dx + $dy * $dy);
    if ($dist < 1) return;
    $step = 6;
    $dashLen = 4;
    for ($i = 0; $i < $dist; $i += $step) {
        $p1x = $x1 + ($dx * $i / $dist);
        $p1y = $y1 + ($dy * $i / $dist);
        $p2x = $x1 + ($dx * min($i + $dashLen, $dist) / $dist);
        $p2y = $y1 + ($dy * min($i + $dashLen, $dist) / $dist);
        imageline($im, (int)$p1x, (int)$p1y, (int)$p2x, (int)$p2y, $black);
    }
    
    $angle = atan2($dy, $dx);
    $arrowLen = 9;
    $arrowAngle = M_PI / 6;
    $ax1 = $x2 - $arrowLen * cos($angle - $arrowAngle);
    $ay1 = $y2 - $arrowLen * sin($angle - $arrowAngle);
    $ax2 = $x2 - $arrowLen * cos($angle + $arrowAngle);
    $ay2 = $y2 - $arrowLen * sin($angle + $arrowAngle);
    imageline($im, (int)$x2, (int)$y2, (int)$ax1, (int)$ay1, $black);
    imageline($im, (int)$x2, (int)$y2, (int)$ax2, (int)$ay2, $black);
    
    if ($label !== '') {
        $midX = ($x1 + $x2) / 2;
        $midY = ($y1 + $y2) / 2 - 4;
        $bbox = imagettfbbox(8.5, 0, $fontTimesIt, $label);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 8.5, 0, (int)($midX - ($tw / 2)), (int)$midY, $black, $fontTimesIt, $label);
    }
}

// Activity Diagram Helpers
function drawStartNode($im, $cx, $cy) {
    $black = imagecolorallocate($im, 0, 0, 0);
    $red = imagecolorallocate($im, 200, 0, 0);
    imagefilledellipse($im, $cx, $cy, 22, 22, $black);
    // Red arrow down
    imagesetthickness($im, 2);
    imageline($im, $cx, $cy + 11, $cx, $cy + 35, $red);
    imageline($im, $cx, $cy + 35, $cx - 5, $cy + 27, $red);
    imageline($im, $cx, $cy + 35, $cx + 5, $cy + 27, $red);
    imagesetthickness($im, 1);
}

function drawEndNode($im, $cx, $cy) {
    $black = imagecolorallocate($im, 0, 0, 0);
    $red = imagecolorallocate($im, 200, 0, 0);
    imagesetthickness($im, 2);
    imageellipse($im, $cx, $cy, 26, 26, $red);
    imagefilledellipse($im, $cx, $cy, 16, 16, $black);
    imagesetthickness($im, 1);
}

function drawRoundedRect($im, $cx, $cy, $w, $h, $lines, $r = 8) {
    global $fontTimes;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    
    $x1 = (int)($cx - ($w / 2));
    $y1 = (int)($cy - ($h / 2));
    $x2 = (int)($cx + ($w / 2));
    $y2 = (int)($cy + ($h / 2));
    
    // Fill white
    imagefilledrectangle($im, $x1 + $r, $y1, $x2 - $r, $y2, $white);
    imagefilledrectangle($im, $x1, $y1 + $r, $x2, $y2 - $r, $white);
    imagefilledarc($im, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $white, IMG_ARC_PIE);
    imagefilledarc($im, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $white, IMG_ARC_PIE);
    imagefilledarc($im, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $white, IMG_ARC_PIE);
    imagefilledarc($im, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $white, IMG_ARC_PIE);
    
    // Border 2px
    imagesetthickness($im, 2);
    imageline($im, $x1 + $r, $y1, $x2 - $r, $y1, $black);
    imageline($im, $x1 + $r, $y2, $x2 - $r, $y2, $black);
    imageline($im, $x1, $y1 + $r, $x1, $y2 - $r, $black);
    imageline($im, $x2, $y1 + $r, $x2, $y2 - $r, $black);
    imagearc($im, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $black);
    imagearc($im, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $black);
    imagearc($im, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $black);
    imagearc($im, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $black);
    imagesetthickness($im, 1);
    
    if (is_string($lines)) $lines = [$lines];
    $lineH = 16;
    $totalH = count($lines) * $lineH;
    $startY = $cy - ($totalH / 2) + 12;
    foreach ($lines as $i => $line) {
        $bbox = imagettfbbox(10, 0, $fontTimes, $line);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 10, 0, (int)($cx - ($tw / 2)), (int)($startY + ($i * $lineH)), $black, $fontTimes, $line);
    }
}

function drawDecisionDiamond($im, $cx, $cy, $w, $h, $label = 'Kiểm tra') {
    global $fontTimes;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    
    $points = [
        (int)$cx, (int)($cy - ($h / 2)),
        (int)($cx + ($w / 2)), (int)$cy,
        (int)$cx, (int)($cy + ($h / 2)),
        (int)($cx - ($w / 2)), (int)$cy
    ];
    imagefilledpolygon($im, $points, $white);
    imagesetthickness($im, 2);
    imagepolygon($im, $points, $black);
    imagesetthickness($im, 1);
    
    $bbox = imagettfbbox(9.5, 0, $fontTimes, $label);
    $tw = $bbox[2] - $bbox[0];
    imagettftext($im, 9.5, 0, (int)($cx - ($tw / 2)), (int)($cy + 4), $black, $fontTimes, $label);
}

function drawOrthogonalArrow($im, $pts, $label = '', $labelPos = 'above') {
    global $fontTimes;
    $black = imagecolorallocate($im, 0, 0, 0);
    imagesetthickness($im, 2);
    for ($i = 0; $i < count($pts) - 1; $i++) {
        imageline($im, (int)$pts[$i][0], (int)$pts[$i][1], (int)$pts[$i+1][0], (int)$pts[$i+1][1], $black);
    }
    // Arrowhead at last segment
    $lastIdx = count($pts) - 1;
    $x2 = $pts[$lastIdx][0];
    $y2 = $pts[$lastIdx][1];
    $x1 = $pts[$lastIdx - 1][0];
    $y1 = $pts[$lastIdx - 1][1];
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;
    $angle = atan2($dy, $dx);
    $arrowLen = 8;
    $arrowAngle = M_PI / 6;
    $ax1 = $x2 - $arrowLen * cos($angle - $arrowAngle);
    $ay1 = $y2 - $arrowLen * sin($angle - $arrowAngle);
    $ax2 = $x2 - $arrowLen * cos($angle + $arrowAngle);
    $ay2 = $y2 - $arrowLen * sin($angle + $arrowAngle);
    imageline($im, (int)$x2, (int)$y2, (int)$ax1, (int)$ay1, $black);
    imageline($im, (int)$x2, (int)$y2, (int)$ax2, (int)$ay2, $black);
    imagesetthickness($im, 1);
    
    if ($label !== '') {
        $midX = ($pts[0][0] + $pts[1][0]) / 2;
        $midY = ($pts[0][1] + $pts[1][1]) / 2;
        $midY += ($labelPos === 'above') ? -6 : 14;
        $bbox = imagettfbbox(9, 0, $fontTimes, $label);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 9, 0, (int)($midX - ($tw / 2)), (int)$midY, $black, $fontTimes, $label);
    }
}

<?php
require_once __DIR__ . '/diagram_core.php';

// Helper to draw UML Component box
function drawComponentBox($im, $cx, $cy, $w, $h, $text) {
    global $fontTimesBd;
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    
    $x1 = (int)($cx - ($w / 2));
    $y1 = (int)($cy - ($h / 2));
    $x2 = (int)($cx + ($w / 2));
    $y2 = (int)($cy + ($h / 2));
    
    // Main rectangle
    imagefilledrectangle($im, $x1, $y1, $x2, $y2, $white);
    imagesetthickness($im, 2);
    imagerectangle($im, $x1, $y1, $x2, $y2, $black);
    
    // Two small tabs on the left border
    $tabW = 16;
    $tabH = 10;
    
    $t1Y = (int)($y1 + ($h * 0.3));
    $t2Y = (int)($y1 + ($h * 0.7));
    
    // Tab 1
    imagefilledrectangle($im, $x1 - ($tabW / 2), $t1Y - ($tabH / 2), $x1 + ($tabW / 2), $t1Y + ($tabH / 2), $white);
    imagerectangle($im, $x1 - ($tabW / 2), $t1Y - ($tabH / 2), $x1 + ($tabW / 2), $t1Y + ($tabH / 2), $black);
    
    // Tab 2
    imagefilledrectangle($im, $x1 - ($tabW / 2), $t2Y - ($tabH / 2), $x1 + ($tabW / 2), $t2Y + ($tabH / 2), $white);
    imagerectangle($im, $x1 - ($tabW / 2), $t2Y - ($tabH / 2), $x1 + ($tabW / 2), $t2Y + ($tabH / 2), $black);
    
    imagesetthickness($im, 1);
    
    // Text inside
    if (is_string($text)) $text = [$text];
    $lineH = 16;
    $totalH = count($text) * $lineH;
    $startY = $cy - ($totalH / 2) + 12;
    foreach ($text as $i => $line) {
        $bbox = imagettfbbox(10, 0, $fontTimesBd, $line);
        $tw = $bbox[2] - $bbox[0];
        imagettftext($im, 10, 0, (int)($cx - ($tw / 2) + 4), (int)($startY + ($i * $lineH)), $black, $fontTimesBd, $line);
    }
}

// Open triangle arrowhead dashed arrow (UML dependency --->>)
function drawDependencyArrow($im, $x1, $y1, $x2, $y2) {
    $black = imagecolorallocate($im, 0, 0, 0);
    $white = imagecolorallocate($im, 255, 255, 255);
    
    $dx = $x2 - $x1;
    $dy = $y2 - $y1;
    $dist = sqrt($dx * $dx + $dy * $dy);
    if ($dist < 1) return;
    
    $step = 6;
    $dashLen = 4;
    for ($i = 0; $i < $dist - 10; $i += $step) {
        $p1x = $x1 + ($dx * $i / $dist);
        $p1y = $y1 + ($dy * $i / $dist);
        $p2x = $x1 + ($dx * min($i + $dashLen, $dist - 10) / $dist);
        $p2y = $y1 + ($dy * min($i + $dashLen, $dist - 10) / $dist);
        imageline($im, (int)$p1x, (int)$p1y, (int)$p2x, (int)$p2y, $black);
    }
    
    // Open triangle arrowhead (white filled, black border)
    $angle = atan2($dy, $dx);
    $arrowLen = 12;
    $arrowAngle = M_PI / 6;
    $ax1 = $x2 - $arrowLen * cos($angle - $arrowAngle);
    $ay1 = $y2 - $arrowLen * sin($angle - $arrowAngle);
    $ax2 = $x2 - $arrowLen * cos($angle + $arrowAngle);
    $ay2 = $y2 - $arrowLen * sin($angle + $arrowAngle);
    
    $points = [
        (int)$x2, (int)$y2,
        (int)$ax1, (int)$ay1,
        (int)$ax2, (int)$ay2
    ];
    imagefilledpolygon($im, $points, $white);
    imagesetthickness($im, 1.5);
    imagepolygon($im, $points, $black);
    imagesetthickness($im, 1);
}

echo "Component helper ready\n";

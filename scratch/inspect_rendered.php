<?php
$html = file_get_contents('http://127.0.0.1:8000/');
$pos = strpos($html, 'forceDismissVoucher');
if ($pos !== false) {
    echo "Found forceDismissVoucher at $pos\n";
    echo substr($html, $pos - 50, 600) . "\n";
} else {
    echo "forceDismissVoucher not found\n";
}
$scriptPos = strpos($html, '/build/assets/app-');
while ($scriptPos !== false) {
    echo "Found asset: " . substr($html, $scriptPos, 45) . "\n";
    $scriptPos = strpos($html, '/build/assets/app-', $scriptPos + 1);
}

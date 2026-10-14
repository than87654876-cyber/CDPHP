<?php

$html = file_get_contents('http://127.0.0.1:8000/');
echo "Total HTML length: " . strlen($html) . " bytes\n\n";

// Count sections or tags
echo "<script> tags size: " . preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $html, $scripts) . " tags, total " . array_sum(array_map('strlen', $scripts[0])) . " bytes\n";
echo "<style> tags size: " . preg_match_all('/<style\b[^>]*>(.*?)<\/style>/is', $html, $styles) . " tags, total " . array_sum(array_map('strlen', $styles[0])) . " bytes\n";
echo "<svg> tags size: " . preg_match_all('/<svg\b[^>]*>(.*?)<\/svg>/is', $html, $svgs) . " tags, total " . array_sum(array_map('strlen', $svgs[0])) . " bytes\n";

// Find biggest blocks
$lines = explode("\n", $html);
echo "Total HTML lines: " . count($lines) . "\n";

// Look for large script blocks or JSON
foreach ($scripts[0] as $i => $s) {
    if (strlen($s) > 10000) {
        echo "Large script tag #$i: " . strlen($s) . " bytes. First 120 chars: " . substr(strip_tags($s), 0, 120) . "...\n";
    }
}

// Check how many dish cards
preg_match_all('/data-dish-id/i', $html, $dishCards);
echo "Dish cards count in HTML: " . count($dishCards[0]) . "\n";

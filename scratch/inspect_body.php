<?php
$text = file_get_contents('scratch/extracted_hovaten.txt');

// Find occurrence of "Chương 1. TỔNG QUAN" after TOC
$pos = mb_strpos($text, "Chương 1. TỔNG QUAN");
if ($pos !== false) {
    // Check if there is a second occurrence (body)
    $pos2 = mb_strpos($text, "Chương 1. TỔNG QUAN", $pos + 50);
    $targetPos = ($pos2 !== false) ? $pos2 : $pos;
    echo "=== BODY CHƯƠNG 1 (pos $targetPos) ===\n";
    echo mb_substr($text, $targetPos, 2500) . "\n\n";
}

$pos = mb_strpos($text, "Chương 2. PHƯƠNG PHÁP");
if ($pos !== false) {
    $pos2 = mb_strpos($text, "Chương 2. PHƯƠNG PHÁP", $pos + 50);
    $targetPos = ($pos2 !== false) ? $pos2 : $pos;
    echo "=== BODY CHƯƠNG 2 (pos $targetPos) ===\n";
    echo mb_substr($text, $targetPos, 2500) . "\n\n";
}

// Find a use case specification table in body
$pos = mb_strpos($text, "Tên Use case");
if ($pos === false) $pos = mb_strpos($text, "Tác nhân");
if ($pos !== false) {
    echo "=== USE CASE SPECIFICATION (pos $pos) ===\n";
    echo mb_substr($text, max(0, $pos - 200), 1500) . "\n\n";
}

// Find a database table specification in body
$pos = mb_strpos($text, "Kiểu dữ liệu");
if ($pos !== false) {
    echo "=== DB TABLE SPECIFICATION (pos $pos) ===\n";
    echo mb_substr($text, max(0, $pos - 200), 1500) . "\n\n";
}

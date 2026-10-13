<?php
$text = file_get_contents('scratch/extracted_hovaten.txt');

function showBodySection($text, $title, $maxLen = 1200) {
    // Find second occurrence (in body)
    $pos = mb_strpos($text, $title);
    if ($pos !== false) {
        $pos2 = mb_strpos($text, $title, $pos + mb_strlen($title) + 10);
        $target = ($pos2 !== false) ? $pos2 : $pos;
        echo "========================================\n";
        echo "BODY SECTION: $title (at pos $target)\n";
        echo "========================================\n";
        echo mb_substr($text, $target, $maxLen) . "\n\n";
    }
}

showBodySection($text, "1.1.1", 700);
showBodySection($text, "1.1.2", 700);
showBodySection($text, "1.1.3", 700);
showBodySection($text, "HTML5", 600);
showBodySection($text, "Bootstrap", 600);
showBodySection($text, "Laravel", 600);
showBodySection($text, "Quy trình nghiệp vụ", 1000);
showBodySection($text, "Thông tin yêu cầu", 1000);
showBodySection($text, "Sơ đồ chức năng", 800);
showBodySection($text, "Đặc tả use case", 1200);
showBodySection($text, "Kết luận", 800);
showBodySection($text, "Định hướng phát triển", 800);

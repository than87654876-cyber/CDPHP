<?php
$text = file_get_contents('scratch/extracted_hovaten.txt');

function showSection($text, $title, $maxLen = 1200) {
    $pos = mb_strpos($text, $title);
    if ($pos !== false) {
        // Find end or next heading
        echo "========================================\n";
        echo "SECTION: $title\n";
        echo "========================================\n";
        echo mb_substr($text, $pos, $maxLen) . "\n\n";
    }
}

showSection($text, "1.1.1.\tĐặt vấn đề", 600);
showSection($text, "1.1.2.\tGiải pháp", 600);
showSection($text, "1.1.3.\tMục đích của đề tài", 600);
showSection($text, "1.2.1.\tHTML5", 500);
showSection($text, "1.2.7.\tLaravel", 500);
showSection($text, "2.1.2\tThông tin yêu cầu", 600);
showSection($text, "2.1.3\tSơ đồ chức năng của hệ thống", 600);
showSection($text, "Đặc tả use case đăng nhập vào hệ thống", 800);
showSection($text, "4.1\tKết luận", 600);
showSection($text, "4.2\tĐịnh hướng phát triển", 600);

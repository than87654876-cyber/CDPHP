<?php

$baseUrl = 'http://127.0.0.1:8000';

function testUrl($url, $cookie = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    $start = microtime(true);
    $html = curl_exec($ch);
    $time = microtime(true) - $start;
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $size = strlen($html);
    $dnsTime = curl_getinfo($ch, CURLINFO_NAMELOOKUP_TIME);
    $connectTime = curl_getinfo($ch, CURLINFO_CONNECT_TIME);
    $startTransfer = curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME);
    curl_close($ch);

    echo "$url\n";
    echo "  Status: $httpCode | Size: " . number_format($size) . " bytes\n";
    echo "  TTFB: " . round($startTransfer * 1000) . "ms | Total Time: " . round($time * 1000) . "ms\n\n";
}

testUrl("$baseUrl/");
testUrl("$baseUrl/shop");
testUrl("$baseUrl/dangnhap");

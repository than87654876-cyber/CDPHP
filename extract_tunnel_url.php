<?php
$logFile = $argv[1] ?? (__DIR__ . '/storage/logs/cloudflared.log');
if (!file_exists($logFile)) {
    exit(1);
}
$content = file_get_contents($logFile);
if (preg_match_all('/https:\/\/[a-z0-9\-]+\.trycloudflare\.com/i', $content, $matches)) {
    $latest = end($matches[0]);
    echo $latest . '/api/sepay/webhook';
    exit(0);
}
exit(1);

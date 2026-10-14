<?php

$start = microtime(true);
require __DIR__ . '/../vendor/autoload.php';
$tAfterAutoload = microtime(true);

$app = require __DIR__ . '/../bootstrap/app.php';
$tAfterBootstrap = microtime(true);

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Enable DB query log
\Illuminate\Support\Facades\DB::enableQueryLog();

$request = \Illuminate\Http\Request::create('/', 'GET');
$tBeforeHandle = microtime(true);

$response = $kernel->handle($request);
$tAfterHandle = microtime(true);

$content = $response->getContent();
$tAfterContent = microtime(true);

$queries = \Illuminate\Support\Facades\DB::getQueryLog();
$totalQueryTime = array_sum(array_column($queries, 'time'));

echo "=== PERFORMANCE BREAKDOWN ===\n";
echo "Autoload time:     " . round(($tAfterAutoload - $start) * 1000, 2) . " ms\n";
echo "Bootstrap time:    " . round(($tAfterBootstrap - $tAfterAutoload) * 1000, 2) . " ms\n";
echo "Handle request:    " . round(($tAfterHandle - $tBeforeHandle) * 1000, 2) . " ms\n";
echo "Render content:    " . round(($tAfterContent - $tAfterHandle) * 1000, 2) . " ms\n";
echo "Total script time: " . round(($tAfterContent - $start) * 1000, 2) . " ms\n";
echo "-----------------------------\n";
echo "Total SQL Queries: " . count($queries) . "\n";
echo "Total SQL Time:    " . round($totalQueryTime, 2) . " ms\n";
echo "\nTop 15 slowest queries:\n";
usort($queries, function($a, $b) { return $b['time'] <=> $a['time']; });
foreach (array_slice($queries, 0, 15) as $i => $q) {
    echo "[" . ($i + 1) . "] (" . $q['time'] . " ms) " . substr($q['query'], 0, 100) . "...\n";
}

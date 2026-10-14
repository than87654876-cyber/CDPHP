<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$routes = [
    '/' => 'GET',
    '/shop' => 'GET',
    '/quanly/dangnhap' => 'GET',
    '/dangnhap' => 'GET',
    '/giohang' => 'GET',
    '/api/dishes/live-feed' => 'GET',
    '/api/notifications/poll' => 'GET',
];

echo "=== BENCHMARKING ROUTES (PHP-level execution time) ===\n";
foreach ($routes as $uri => $method) {
    \Illuminate\Support\Facades\DB::flushQueryLog();
    \Illuminate\Support\Facades\DB::enableQueryLog();

    $start = microtime(true);
    $req = \Illuminate\Http\Request::create($uri, $method);
    $res = $kernel->handle($req);
    $duration = (microtime(true) - $start) * 1000;

    $queries = \Illuminate\Support\Facades\DB::getQueryLog();
    $sqlCount = count($queries);
    $sqlTime = array_sum(array_column($queries, 'time'));

    printf("%-25s | Status: %d | Time: %7.2f ms | Queries: %2d (%6.2f ms)\n",
        $uri, $res->getStatusCode(), $duration, $sqlCount, $sqlTime
    );
}

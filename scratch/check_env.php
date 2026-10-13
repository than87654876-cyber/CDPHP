<?php
var_dump(is_readable('.env'), filesize('.env'));
$env = file_get_contents('.env');
echo 'APP_KEY in .env: ' . (strpos($env, 'APP_KEY') !== false ? 'YES' : 'NO') . PHP_EOL;

// Check what Dotenv loads
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
try {
    $loaded = $dotenv->load();
    echo "Dotenv loaded keys count: " . count($loaded) . "\n";
    echo "APP_KEY: " . ($_ENV['APP_KEY'] ?? 'NOT_SET') . "\n";
    echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'NOT_SET') . "\n";
} catch (\Throwable $e) {
    echo "Dotenv load ERROR: " . $e->getMessage() . "\n";
}

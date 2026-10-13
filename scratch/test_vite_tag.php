<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Test 1: with request to localhost/CDPHP/public
$req1 = Illuminate\Http\Request::create('http://localhost/CDPHP/public/');
app()->instance('request', $req1);
echo "=== REQUEST 1 (Laragon Apache): http://localhost/CDPHP/public/ ===\n";
echo "asset('logo.jpg'): " . asset('logo.jpg') . "\n";
echo "Vite tags:\n" . app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']) . "\n\n";

// Test 2: with request to localhost:8000
$req2 = Illuminate\Http\Request::create('http://localhost:8000/');
app()->instance('request', $req2);
echo "=== REQUEST 2 (php artisan serve): http://localhost:8000/ ===\n";
echo "asset('logo.jpg'): " . asset('logo.jpg') . "\n";
echo "Vite tags:\n" . app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']) . "\n\n";

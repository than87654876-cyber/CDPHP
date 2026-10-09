<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USERS AND ROLES ===\n";
$users = App\Models\User::all(['id', 'fullname', 'email', 'role', 'password']);
$commonPasswords = ['123456', 'password', '12345678', 'admin', 'admin123', 'than123', '123123'];

foreach ($users as $user) {
    $matched = 'unknown';
    foreach ($commonPasswords as $p) {
        if (Illuminate\Support\Facades\Hash::check($p, $user->password)) {
            $matched = $p;
            break;
        }
    }
    echo "ID: {$user->id} | Name: {$user->fullname} | Email: {$user->email} | Role: {$user->role} | Password: {$matched}\n";
}

echo "\n=== SPATIE ROLES & PERMISSIONS ===\n";
try {
    $roles = Spatie\Permission\Models\Role::all();
    foreach ($roles as $r) {
        echo "Role: {$r->name} (guard: {$r->guard_name})\n";
    }
} catch (\Throwable $e) {
    echo "Spatie Role error: " . $e->getMessage() . "\n";
}

echo "\n=== SYSTEM SUMMARY ===\n";
echo "Categories count: " . App\Models\Category::count() . "\n";
echo "Dishes count: " . App\Models\Dish::count() . "\n";
echo "Orders count: " . App\Models\Order::count() . "\n";
echo "Coupons count: " . App\Models\Coupon::count() . "\n";

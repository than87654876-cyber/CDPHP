<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Ensure staff exists
$staff = User::firstOrCreate(
    ['email' => 'staff@example.com'],
    [
        'fullname' => 'Nhân Viên Thu Ngân',
        'phone' => '0902222333',
        'password' => Hash::make('123456'),
        'role' => 'staff',
        'status' => true,
    ]
);
$staff->syncRoles(['staff']);

// Ensure kitchen exists
$kitchen = User::firstOrCreate(
    ['email' => 'kitchen@example.com'],
    [
        'fullname' => 'Đầu Bếp Trưởng',
        'phone' => '0903333444',
        'password' => Hash::make('123456'),
        'role' => 'kitchen',
        'status' => true,
    ]
);
$kitchen->syncRoles(['kitchen']);

echo "Staff & Kitchen accounts ensured successfully!\n";

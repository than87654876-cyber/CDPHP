<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all();
foreach ($users as $u) {
    $spatieRoles = $u->roles->pluck('name')->join(', ');
    echo "ID: {$u->id} | {$u->email} | DB role: {$u->role} | Spatie roles: [{$spatieRoles}]\n";
}

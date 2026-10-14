<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed basic data
        $this->call([
            CategorySeeder::class,
            DishSeeder::class,
            CouponSeeder::class,
            CustomerOrderHistorySeeder::class,
        ]);

        // Create test users
        User::factory(3)->create([
            'role' => 'customer',
        ]);

        // Create admin user (Matching Admin profile in layout)
        User::factory()->create([
            'fullname' => 'Dương Chí Bá',
            'email' => 'admin@example.com',
            'phone' => '0901111222',
            'role' => 'admin',
            'status' => true,
        ]);

        // Create staff user (Matching Staff profile)
        User::factory()->create([
            'fullname' => 'Nguyễn Văn Thắng',
            'email' => 'staff@example.com',
            'phone' => '0902222333',
            'role' => 'staff',
            'status' => true,
        ]);

        // Create customer user (Matching client profile in shop)
        $tung = User::factory()->create([
            'fullname' => 'Dương Bá Tùng',
            'email' => 'tung.db@gmail.com',
            'phone' => '0901234567',
            'role' => 'customer',
            'status' => true,
            'points' => 2450,
            'membership' => 'diamond',
            'notes' => 'Khách quen phân hiệu, thường dùng dịch vụ giao hàng Grab nội thành Ho Chi Minh City.',
        ]);
    }
}

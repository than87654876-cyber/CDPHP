<?php

namespace Database\Seeders;

use App\Models\DailySchedule;
use App\Models\Dish;
use App\Models\Order;
use App\Models\Subscription;
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
            ServicePackageSeeder::class,
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

        // Create subscription order
        $order = Order::create([
            'user_id' => $tung->id,
            'order_type' => 'subscription',
            'total_amount' => 179.99,
            'final_amount' => 179.99,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
            'health_notes' => 'Giao hàng hàng ngày lúc 7:30. SĐT: 0901234567. Địa chỉ: Trường Cao đẳng Công nghệ Thông tin TP.HCM (ITC)',
        ]);

        // Create subscription
        $subscription = Subscription::create([
            'order_id' => $order->id,
            'user_id' => $tung->id,
            'package_id' => 4, // Monthly Flex
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-30',
            'remaining_days' => 12,
            'status' => 'active',
        ]);

        // Seed daily schedules for this subscription
        $dishes = Dish::all();
        for ($day = 1; $day <= 30; $day++) {
            $deliveryDate = date('Y-m-d', strtotime('2026-06-01 + '.($day - 1).' days'));

            $status = 'pending';
            if ($day < 18) {
                $status = 'delivered';
            } elseif ($day == 18) {
                $status = 'pending'; // or in progress / pending
            }

            DailySchedule::create([
                'subscription_id' => $subscription->id,
                'delivery_date' => $deliveryDate,
                'dish_id' => $dishes->random()->id,
                'delivery_status' => $status,
                'is_locked' => ($day <= 18),
            ]);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Events\OrderUpdated;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderAutomationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the orders:cancel-overdue command cancels pending unpaid orders older than 15 minutes.
     */
    public function test_command_cancels_overdue_orders()
    {
        Event::fake([OrderUpdated::class]);

        $customer = User::create([
            'fullname' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'phone' => '0912345678',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
        ]);

        // 1. Create a recent pending order (should NOT be cancelled)
        $recentOrder = Order::create([
            'user_id' => $customer->id,
            'total_amount' => 100000,
            'final_amount' => 100000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);
        $recentOrder->created_at = Carbon::now()->subMinutes(10);
        $recentOrder->saveQuietly();

        // 2. Create an overdue pending order (should be cancelled)
        $overdueOrder = Order::create([
            'user_id' => $customer->id,
            'total_amount' => 120000,
            'final_amount' => 120000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);
        $overdueOrder->created_at = Carbon::now()->subMinutes(20);
        $overdueOrder->saveQuietly();

        // 3. Create an overdue but paid order (should NOT be cancelled)
        $paidOrder = new Order([
            'user_id' => $customer->id,
            'total_amount' => 150000,
            'final_amount' => 150000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'pending',
        ]);
        $paidOrder->created_at = Carbon::now()->subMinutes(25);
        $paidOrder->saveQuietly();

        // Run the custom Artisan command
        $this->artisan('orders:cancel-overdue')
            ->expectsOutput('Starting checking for overdue unpaid orders...')
            ->expectsOutput("Cancelled order FDL-{$overdueOrder->id}")
            ->expectsOutput('Successfully cancelled 1 overdue orders.')
            ->assertExitCode(0);

        // Assert database states
        $this->assertEquals('pending', $recentOrder->fresh()->order_status);
        $this->assertEquals('cancelled', $overdueOrder->fresh()->order_status);
        $this->assertEquals('pending', $paidOrder->fresh()->order_status);
        
        $this->assertStringContainsString('Tự động hủy đơn hàng quá hạn thanh toán', $overdueOrder->fresh()->health_notes);

        // Assert broadcasting event was dispatched for the cancelled order
        Event::assertDispatched(OrderUpdated::class, function ($event) use ($overdueOrder) {
            return $event->order->id === $overdueOrder->id && $event->action === 'cancelled';
        });

        Event::assertNotDispatched(OrderUpdated::class, function ($event) use ($recentOrder) {
            return $event->order->id === $recentOrder->id;
        });
    }

    /**
     * Test coupon validation API and its application during checkout.
     */
    public function test_coupon_validation_and_checkout_application()
    {
        $customer = User::create([
            'fullname' => 'Test Customer',
            'email' => 'testcustomer2@example.com',
            'phone' => '0912345678',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
        ]);

        $category = \App\Models\Category::create([
            'category_name' => 'Test Category',
            'description' => 'Test Description'
        ]);

        $dish = \App\Models\Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Pho Test',
            'price' => 50000,
            'is_available' => true,
        ]);

        $coupon = \App\Models\Coupon::create([
            'coupon_code' => 'TESTCOUPON10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_value' => 40000,
            'start_date' => Carbon::now()->subDay()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDay()->format('Y-m-d'),
            'usage_limit' => 10,
        ]);

        // 1. Test validateCoupon API - Valid request
        $response = $this->postJson(route('api.coupon.validate'), [
            'code' => 'TESTCOUPON10',
            'total_amount' => 50000
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'discount_amount' => 5000,
                'final_amount' => 45000
            ]);

        // 2. Test validateCoupon API - Min order value violation
        $responseErr = $this->postJson(route('api.coupon.validate'), [
            'code' => 'TESTCOUPON10',
            'total_amount' => 30000
        ]);

        $responseErr->assertStatus(200)
            ->assertJson([
                'success' => false,
            ]);

        // 3. Test processCheckout applying coupon
        $cartItems = json_encode([
            ['id' => $dish->id, 'quantity' => 1]
        ]);

        // Log in the customer
        $this->actingAs($customer);

        $responseCheckout = $this->post(route('muahang.process'), [
            'cart_items' => $cartItems,
            'cart_phone' => '0912345678',
            'cart_time' => 'now',
            'province' => 'TP. Hồ Chí Minh',
            'district' => 'Tân Phú',
            'ward' => 'Tân Sơn Nhì',
            'cart_address_detail' => '12 Bau Cat',
            'cart_address' => '12 Bau Cat, Tân Sơn Nhì, Tân Phú, TP. Hồ Chí Minh',
            'cart_payment' => 'cod',
            'coupon_code' => 'TESTCOUPON10'
        ]);

        // Assert order was created in DB with correct amounts and coupon_id
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'coupon_id' => $coupon->id,
            'total_amount' => 50000,
            'final_amount' => 45000,
            'payment_method' => 'cash',
        ]);

        // Assert coupon usage limit was decremented
        $this->assertEquals(9, $coupon->fresh()->usage_limit);
    }

    /**
     * Test the point calculation logic for USD and VND amounts.
     */
    public function test_point_calculation_for_usd_and_vnd()
    {
        $customer = User::create([
            'fullname' => 'Point Test Customer',
            'email' => 'pointtest@example.com',
            'phone' => '0912345600',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
        ]);

        // 1. Test USD amount (< 1000)
        // 179.99 USD * 10 = 1800 points
        $customer->addPoints(179.99);
        $this->assertEquals(1800, $customer->fresh()->points);
        $this->assertEquals('gold', $customer->fresh()->membership);

        // Reset points
        $customer->points = 0;
        $customer->membership = 'bronze';
        $customer->save();

        // 2. Test VND amount (>= 1000)
        // 50,000 VND / 1000 = 50 points
        $customer->addPoints(50000);
        $this->assertEquals(50, $customer->fresh()->points);
        $this->assertEquals('bronze', $customer->fresh()->membership);

        // Reset points
        $customer->points = 0;
        $customer->membership = 'bronze';
        $customer->save();

        // 1,500,000 VND / 1000 = 1500 points
        $customer->addPoints(1500000);
        $this->assertEquals(1500, $customer->fresh()->points);
        $this->assertEquals('gold', $customer->fresh()->membership);

        // Add 500,000 VND / 1000 = 500 points
        $customer->addPoints(500000);
        $this->assertEquals(2000, $customer->fresh()->points);
        $this->assertEquals('diamond', $customer->fresh()->membership);
    }

    /**
     * Test the subscription:dispatch-daily Artisan command.
     */
    public function test_subscription_dispatch_daily_command()
    {
        $customer = User::create([
            'fullname' => 'Subscription Dispatch Test Customer',
            'email' => 'subdispatchtest@example.com',
            'phone' => '0912345601',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
        ]);

        $category = \App\Models\Category::create([
            'category_name' => 'Mon An Test',
            'description' => 'Test'
        ]);

        $dish = \App\Models\Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Pho Ga',
            'price' => 45000,
            'is_available' => true,
        ]);

        $package = \App\Models\ServicePackage::create([
            'package_name' => 'Gói 30 ngày',
            'price' => 1500000.00,
            'duration_days' => 30,
            'status' => true,
        ]);

        $order = Order::create([
            'user_id' => $customer->id,
            'order_type' => 'subscription',
            'total_amount' => 1500000.00,
            'final_amount' => 1500000.00,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        $subscription = \App\Models\Subscription::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'package_id' => $package->id,
            'start_date' => Carbon::today()->toDateString(),
            'end_date' => Carbon::today()->addDays(30)->toDateString(),
            'remaining_days' => 10,
            'status' => 'active',
        ]);

        // Create two schedules: one for yesterday (pending), one for today (pending), one for tomorrow (pending)
        $scheduleYesterday = \App\Models\DailySchedule::create([
            'subscription_id' => $subscription->id,
            'delivery_date' => Carbon::yesterday()->toDateString(),
            'dish_id' => $dish->id,
            'delivery_status' => 'pending',
            'is_locked' => false,
        ]);

        $scheduleToday = \App\Models\DailySchedule::create([
            'subscription_id' => $subscription->id,
            'delivery_date' => Carbon::today()->toDateString(),
            'dish_id' => $dish->id,
            'delivery_status' => 'pending',
            'is_locked' => false,
        ]);

        $scheduleTomorrow = \App\Models\DailySchedule::create([
            'subscription_id' => $subscription->id,
            'delivery_date' => Carbon::tomorrow()->toDateString(),
            'dish_id' => $dish->id,
            'delivery_status' => 'pending',
            'is_locked' => false,
        ]);

        // Run Artisan command
        $this->artisan('subscription:dispatch-daily')
            ->expectsOutput('Starting checking for subscription daily schedules to dispatch...')
            ->expectsOutput("Dispatched schedule ID {$scheduleYesterday->id} (Dish: Pho Ga) for Subscription ID {$subscription->id}")
            ->expectsOutput("Dispatched schedule ID {$scheduleToday->id} (Dish: Pho Ga) for Subscription ID {$subscription->id}")
            ->expectsOutput('Successfully dispatched 2 daily schedules.')
            ->assertExitCode(0);

        // Assert yesterday and today dispatches completed
        $this->assertEquals('delivered', $scheduleYesterday->fresh()->delivery_status);
        $this->assertTrue($scheduleYesterday->fresh()->is_locked);

        $this->assertEquals('delivered', $scheduleToday->fresh()->delivery_status);
        $this->assertTrue($scheduleToday->fresh()->is_locked);

        // Tomorrow dispatch should remain pending
        $this->assertEquals('pending', $scheduleTomorrow->fresh()->delivery_status);
        $this->assertFalse($scheduleTomorrow->fresh()->is_locked);

        // Remaining days: started with 10, dispatched 2 -> should be 8
        $this->assertEquals(8, $subscription->fresh()->remaining_days);
    }
}

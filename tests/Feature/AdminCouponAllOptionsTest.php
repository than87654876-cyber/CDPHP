<?php

namespace Tests\Feature;

use App\Jobs\SendPromoCouponEmail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\ServicePackage;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminCouponAllOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'fullname' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'status' => true,
        ]);
    }

    /**
     * Test Option 1 - Filter by Diamond Rank
     */
    public function test_send_coupon_option_diamond_rank()
    {
        Queue::fake();

        User::create([
            'fullname' => 'Diamond Customer',
            'email' => 'diamond@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
            'membership' => 'diamond',
        ]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'ranks' => ['diamond'],
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'code_length' => 6,
            'expiry_days' => 7,
            'min_order' => 50000,
            'email_subject' => 'VIP Diamond Gift',
            'message_body' => 'Hello, here is [MÃ_TỰ_SINH] for [SỐ_NGÀY] days',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
        $this->assertDatabaseHas('coupons', ['discount_value' => 20, 'discount_type' => 'percent']);
    }

    /**
     * Test Option 1 - Filter by Gold Rank
     */
    public function test_send_coupon_option_gold_rank()
    {
        Queue::fake();

        User::create([
            'fullname' => 'Gold Customer',
            'email' => 'gold@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
            'membership' => 'gold',
        ]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'ranks' => ['gold'],
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'code_length' => 8,
            'expiry_days' => 10,
            'min_order' => 0,
            'email_subject' => 'Gold Voucher',
            'message_body' => 'Code [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
    }

    /**
     * Test Option 1 - Filter by Silver Rank
     */
    public function test_send_coupon_option_silver_rank()
    {
        Queue::fake();

        User::create([
            'fullname' => 'Silver Customer',
            'email' => 'silver@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
            'membership' => 'silver',
        ]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'ranks' => ['silver'],
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'code_length' => 6,
            'expiry_days' => 5,
            'min_order' => 100000,
            'email_subject' => 'Silver Voucher',
            'message_body' => 'Code [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
    }

    /**
     * Test Option 1 - Filter by Standard Rank (mapped to Bronze)
     */
    public function test_send_coupon_option_standard_rank_maps_to_bronze()
    {
        Queue::fake();

        User::create([
            'fullname' => 'Bronze Customer',
            'email' => 'bronze@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => true,
            'membership' => 'bronze',
        ]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'ranks' => ['standard'],
            'discount_type' => 'fixed',
            'discount_value' => 25000,
            'code_length' => 6,
            'expiry_days' => 5,
            'min_order' => 50000,
            'email_subject' => 'Standard Welcome',
            'message_body' => 'Code [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
    }

    /**
     * Test Option 1 - Multiple Ranks Selected
     */
    public function test_send_coupon_option_multiple_ranks()
    {
        Queue::fake();

        User::create(['fullname' => 'G1', 'email' => 'g1@gmail.com', 'password' => 'x', 'role' => 'customer', 'status' => 1, 'membership' => 'gold']);
        User::create(['fullname' => 'D1', 'email' => 'd1@gmail.com', 'password' => 'x', 'role' => 'customer', 'status' => 1, 'membership' => 'diamond']);
        User::create(['fullname' => 'B1', 'email' => 'b1@gmail.com', 'password' => 'x', 'role' => 'customer', 'status' => 1, 'membership' => 'bronze']);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'ranks' => ['gold', 'diamond'],
            'discount_type' => 'percentage',
            'discount_value' => 25,
            'code_length' => 6,
            'expiry_days' => 7,
            'min_order' => 50000,
            'email_subject' => 'VIPs only',
            'message_body' => 'Code [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 2);
    }

    /**
     * Test Option 2 - Filter by Inactive Period (1 month)
     */
    public function test_send_coupon_option_inactive_period_1_month()
    {
        Queue::fake();

        $inactiveUser = User::create([
            'fullname' => 'Inactive User',
            'email' => 'inactive@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
            'membership' => 'bronze',
        ]);

        // Has an old order 45 days ago, none recent
        $oldOrder = Order::create([
            'user_id' => $inactiveUser->id,
            'order_type' => 'single',
            'total_amount' => 100000,
            'final_amount' => 100000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
        $oldOrder->created_at = Carbon::now()->subDays(45);
        $oldOrder->save();

        // Active user with an order yesterday
        $activeUser = User::create([
            'fullname' => 'Active User',
            'email' => 'active@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
            'membership' => 'bronze',
        ]);
        $recentOrder = Order::create([
            'user_id' => $activeUser->id,
            'order_type' => 'single',
            'total_amount' => 100000,
            'final_amount' => 100000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
        $recentOrder->created_at = Carbon::now()->subDays(1);
        $recentOrder->save();

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'inactive_period' => '1_month',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'code_length' => 6,
            'expiry_days' => 5,
            'min_order' => 50000,
            'email_subject' => 'We miss you',
            'message_body' => 'Come back! [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
    }

    /**
     * Test Option 2 - Filter by Inactive Period (6 months & 1 year)
     */
    public function test_send_coupon_option_inactive_period_6_months_and_1_year()
    {
        Queue::fake();

        $veryOldUser = User::create([
            'fullname' => 'Old Customer',
            'email' => 'oldie@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
            'membership' => 'bronze',
        ]);

        $oldOrder = Order::create([
            'user_id' => $veryOldUser->id,
            'order_type' => 'single',
            'total_amount' => 150000,
            'final_amount' => 150000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
        $oldOrder->created_at = Carbon::now()->subMonths(8);
        $oldOrder->save();

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'inactive_period' => '6_months',
            'discount_type' => 'fixed',
            'discount_value' => 40000,
            'code_length' => 8,
            'expiry_days' => 14,
            'min_order' => 100000,
            'email_subject' => 'Come back to FoodDaily',
            'message_body' => 'Special promo [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
    }

    /**
     * Test Option 3 - Filter by New Customer (7 days, 15 days, 30 days)
     */
    public function test_send_coupon_option_new_customer_range()
    {
        Queue::fake();

        $newUser = User::create([
            'fullname' => 'Brand New Customer',
            'email' => 'newbie@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
        ]);
        $newUser->created_at = Carbon::now()->subDays(3);
        $newUser->save();

        $oldUser = User::create([
            'fullname' => 'Ancient Customer',
            'email' => 'ancient@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
        ]);
        $oldUser->created_at = Carbon::now()->subDays(60);
        $oldUser->save();

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'new_customer_range' => '7_days',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'code_length' => 6,
            'expiry_days' => 7,
            'min_order' => 0,
            'email_subject' => 'Welcome Gift',
            'message_body' => 'Enjoy your first meal with [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);
    }

    /**
     * Test Option 4 - Target Email Specific
     */
    public function test_send_coupon_option_target_email_specific()
    {
        Queue::fake();

        $user = User::create([
            'fullname' => 'Specific Target',
            'email' => 'specific@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'target_email' => 'specific@gmail.com',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'code_prefix' => 'TARGET',
            'code_length' => 8,
            'expiry_days' => 10,
            'min_order' => 150000,
            'email_subject' => 'Personalized Voucher',
            'message_body' => 'Voucher [MÃ_TỰ_SINH] for you',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 1);

        $coupon = Coupon::where('coupon_code', 'like', 'TARGET-%')->first();
        $this->assertNotNull($coupon);
        $this->assertEquals(50000, $coupon->discount_value);
        $this->assertEquals('fixed', $coupon->discount_type);
    }

    /**
     * Test Option 5 - Broadcast to All (empty filters)
     */
    public function test_send_coupon_with_all_blank_filters_broadcasts()
    {
        Queue::fake();

        User::create(['fullname' => 'U1', 'email' => 'u1@gmail.com', 'password' => 'x', 'role' => 'customer', 'status' => 1]);
        User::create(['fullname' => 'U2', 'email' => 'u2@gmail.com', 'password' => 'x', 'role' => 'customer', 'status' => 1]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'inactive_period' => 'all',
            'new_customer_range' => 'all',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'code_length' => 6,
            'expiry_days' => 5,
            'min_order' => 0,
            'email_subject' => 'General Broadcast',
            'message_body' => 'All users get [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('quanly_guima'));
        Queue::assertPushed(SendPromoCouponEmail::class, 2);
    }

    /**
     * Test Graceful Handling when no customer matches filters
     */
    public function test_send_coupon_when_no_customer_matches_gracefully_redirects()
    {
        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'ranks' => ['diamond'], // No diamond users exist
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'code_length' => 6,
            'expiry_days' => 5,
            'min_order' => 0,
            'email_subject' => 'General Broadcast',
            'message_body' => 'All users get [MÃ_TỰ_SINH]',
        ]);

        $response->assertSessionHas('error', 'Không tìm thấy khách hàng nào thỏa mãn điều kiện lọc để gửi mã.');
    }

    /**
     * Test User::subscriptions relationship and whereHas('subscriptions')
     * Verifies that the previous BadMethodCallException can NEVER happen again.
     */
    public function test_user_subscriptions_relation_never_throws_exception()
    {
        $user = User::create([
            'fullname' => 'Subscriber User',
            'email' => 'sub@gmail.com',
            'password' => 'x',
            'role' => 'customer',
            'status' => 1,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_type' => 'subscription',
            'total_amount' => 199000,
            'final_amount' => 199000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);

        $pkg = ServicePackage::create([
            'package_name' => 'Monthly Health VIP',
            'duration_days' => 30,
            'price' => 199000,
        ]);

        // Creating subscription using Eloquent relation
        $sub = $user->subscriptions()->create([
            'order_id' => $order->id,
            'package_id' => $pkg->id,
            'status' => 'active',
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addMonth()->format('Y-m-d'),
        ]);

        $this->assertInstanceOf(Subscription::class, $sub);
        $this->assertEquals($user->id, $sub->user_id);

        // Test querying with whereHas('subscriptions')
        $usersWithSubs = User::whereHas('subscriptions')->get();
        $this->assertTrue($usersWithSubs->contains($user));

        // Test querying with orWhereHas('subscriptions')
        $queryResult = User::where('id', 999999)->orWhereHas('subscriptions')->get();
        $this->assertTrue($queryResult->contains($user));
    }

    /**
     * Test Promotion Standard CRUD (Create, Read, Update, Delete)
     */
    public function test_promotion_standard_crud()
    {
        // 1. Create Promotion
        $storeResponse = $this->actingAs($this->admin)->post(route('khuyenmai_them.post'), [
            'coupon_code' => 'SUPERMEGASALE',
            'discount_type' => 'percent',
            'discount_value' => 25,
            'min_order_value' => 100000,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'usage_limit' => 50,
        ]);
        $storeResponse->assertRedirect(route('quanly_khuyenmai'));
        $storeResponse->assertSessionHas('success');

        $coupon = Coupon::where('coupon_code', 'SUPERMEGASALE')->firstOrFail();
        $this->assertEquals(25, $coupon->discount_value);

        // 2. View Promotion Detail
        $detailResponse = $this->actingAs($this->admin)->get(route('khuyenmai_xem', $coupon->id));
        $detailResponse->assertStatus(200);

        // 3. Update Promotion
        $updateResponse = $this->actingAs($this->admin)->post(route('khuyenmai_chinhsua.post', $coupon->id), [
            'coupon_code' => 'SUPERMEGASALE_EDITED',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_value' => 200000,
            'start_date' => Carbon::now()->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'usage_limit' => 20,
        ]);
        $updateResponse->assertRedirect(route('quanly_khuyenmai'));
        $this->assertDatabaseHas('coupons', ['coupon_code' => 'SUPERMEGASALE_EDITED', 'discount_value' => 50000]);

        // 4. Delete Promotion
        $deleteResponse = $this->actingAs($this->admin)->post(route('khuyenmai_xoa', $coupon->id));
        $deleteResponse->assertRedirect(route('quanly_khuyenmai'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }
}

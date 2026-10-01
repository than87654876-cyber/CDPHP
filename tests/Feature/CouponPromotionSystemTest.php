<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use App\Models\UserCoupon;
use App\Jobs\SendPromoCouponEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CouponPromotionSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $admin;
    protected Dish $dish;
    protected Coupon $couponPercent;
    protected Coupon $couponFixed;
    protected Coupon $couponExpiring;
    protected Coupon $couponHighMin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->customer = User::factory()->create([
            'role' => 'customer',
            'fullname' => 'Nguyễn Văn Test',
            'email' => 'customer_test@fooddaily.com',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'fullname' => 'Quản Trị Viên',
            'email' => 'admin_test@fooddaily.com',
        ]);
        $this->admin->assignRole('admin');

        $category = Category::create([
            'category_name' => 'Món Ăn Nhanh',
            'description' => 'Fastfood',
        ]);

        $this->dish = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Burger Bò Phô Mai',
            'price' => 100000,
            'description' => 'Burger thơm ngon',
            'is_available' => true,
        ]);

        // Tạo các coupon mẫu
        // 1. Giảm 20%, đơn tối thiểu 50.000đ
        $this->couponPercent = Coupon::create([
            'coupon_code' => 'TEST_PERCENT20',
            'discount_type' => 'percent',
            'discount_value' => 20,
            'min_order_value' => 50000,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(10)->format('Y-m-d'),
            'usage_limit' => 100,
            'used_count' => 0,
        ]);

        // 2. Giảm 50.000đ, đơn tối thiểu 150.000đ
        $this->couponFixed = Coupon::create([
            'coupon_code' => 'TEST_FIXED50K',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_value' => 150000,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(10)->format('Y-m-d'),
            'usage_limit' => 50,
            'used_count' => 0,
        ]);

        // 3. Giảm 30.000đ, sắp hết hạn (còn 2 ngày), đơn tối thiểu 100.000đ
        $this->couponExpiring = Coupon::create([
            'coupon_code' => 'TEST_EXPIRING2D',
            'discount_type' => 'fixed',
            'discount_value' => 30000,
            'min_order_value' => 100000,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(2)->format('Y-m-d'),
            'usage_limit' => 20,
            'used_count' => 0,
        ]);

        // 4. Giảm 100.000đ, đơn tối thiểu 500.000đ (chưa đủ điều kiện với đơn nhỏ)
        $this->couponHighMin = Coupon::create([
            'coupon_code' => 'TEST_HIGH500K',
            'discount_type' => 'fixed',
            'discount_value' => 100000,
            'min_order_value' => 500000,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(15)->format('Y-m-d'),
            'usage_limit' => 10,
            'used_count' => 0,
        ]);
    }

    public function test_guest_cannot_validate_coupon_and_receives_require_login(): void
    {
        $response = $this->postJson(route('api.coupon.validate'), [
            'coupon_code' => $this->couponPercent->coupon_code,
            'total_amount' => 200000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'require_login' => true,
        ]);
    }

    public function test_guest_cannot_get_best_coupon_and_receives_require_login(): void
    {
        $response = $this->postJson(route('api.coupon.best'), [
            'total_amount' => 200000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'require_login' => true,
        ]);
    }

    public function test_guest_cannot_save_coupon(): void
    {
        $response = $this->postJson(route('api.coupons.save'), [
            'coupon_id' => $this->couponPercent->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'require_login' => true,
        ]);
    }

    public function test_authenticated_user_can_save_coupon(): void
    {
        $response = $this->actingAs($this->customer)->postJson(route('api.coupons.save'), [
            'coupon_id' => $this->couponFixed->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'saved' => true,
        ]);

        $this->assertDatabaseHas('user_coupons', [
            'user_id' => $this->customer->id,
            'coupon_id' => $this->couponFixed->id,
            'is_used' => false,
        ]);
    }

    public function test_authenticated_user_can_save_all_coupons(): void
    {
        $response = $this->actingAs($this->customer)->postJson(route('api.coupons.save_all'), []);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertTrue(
            UserCoupon::where('user_id', $this->customer->id)->count() >= 4
        );
    }

    public function test_api_coupons_my_returns_correct_expiring_and_saved_status(): void
    {
        // User lưu 1 coupon
        UserCoupon::create([
            'user_id' => $this->customer->id,
            'coupon_id' => $this->couponExpiring->id,
            'is_used' => false,
        ]);

        $response = $this->actingAs($this->customer)->getJson(route('api.coupons.my'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_logged_in' => true,
            'saved_count' => 1,
        ]);

        $coupons = collect($response->json('coupons'));

        // Kiểm tra mã TEST_EXPIRING2D có is_expiring_soon = true và is_saved = true
        $expiringCouponData = $coupons->firstWhere('coupon_code', 'TEST_EXPIRING2D');
        $this->assertNotNull($expiringCouponData);
        $this->assertTrue($expiringCouponData['is_expiring_soon']);
        $this->assertTrue($expiringCouponData['is_saved']);
        $this->assertLessThanOrEqual(3, $expiringCouponData['days_remaining']);
    }

    public function test_auto_apply_best_coupon_selects_highest_discount(): void
    {
        // Xóa các coupon khác để chỉ còn các coupon của test
        Coupon::whereNotIn('id', [
            $this->couponPercent->id,
            $this->couponFixed->id,
            $this->couponExpiring->id,
            $this->couponHighMin->id,
        ])->delete();

        // Với đơn hàng 200.000đ:
        // - TEST_PERCENT20: 20% của 200k = 40.000đ
        // - TEST_FIXED50K: 50.000đ
        // - TEST_EXPIRING2D: 30.000đ
        // - TEST_HIGH500K: Đơn tối thiểu 500k -> không đủ điều kiện
        // => Mã tốt nhất phải là TEST_FIXED50K (tiết kiệm 50.000đ)

        $response = $this->actingAs($this->customer)->postJson(route('api.coupon.best'), [
            'total_amount' => 200000,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'has_coupon' => true,
            'best_coupon' => [
                'coupon_code' => 'TEST_FIXED50K',
                'discount_amount' => 50000,
            ],
        ]);

        $allCoupons = collect($response->json('all_coupons'));
        $highMinCoupon = $allCoupons->firstWhere('coupon_code', 'TEST_HIGH500K');
        $this->assertNotNull($highMinCoupon);
        $this->assertFalse($highMinCoupon['is_eligible']);
        $this->assertStringContainsString('300.000', $highMinCoupon['ineligible_reason']);
    }

    public function test_guest_cannot_checkout_with_coupon_code(): void
    {
        $response = $this->from(route('muahang'))->post(route('muahang.process'), [
            'cart_fullname' => 'Guest User',
            'cart_phone' => '0901234567',
            'cart_email' => 'guest@example.com',
            'cart_address' => '123 Đường ABC, Quận 1',
            'cart_time' => 'Giao siêu tốc 20 phút',
            'cart_payment' => 'cod',
            'coupon_code' => 'TEST_FIXED50K',
            'cart_items' => json_encode([
                [
                    'id' => $this->dish->id,
                    'quantity' => 2,
                ]
            ]),
        ]);

        // Khách vãng lai dùng coupon phải bị chặn, redirect back với lỗi coupon_code
        $response->assertRedirect(route('muahang'));
        $response->assertSessionHasErrors(['coupon_code']);
        $this->assertDatabaseMissing('orders', [
            'coupon_id' => $this->couponFixed->id,
        ]);
    }

    public function test_authenticated_user_can_checkout_with_coupon_and_records_discount(): void
    {
        // Cart gồm 2 burger = 200.000đ
        // Dùng mã TEST_FIXED50K (giảm 50.000đ)
        // Tiền cuối cùng = 200.000 - 50.000 = 150.000đ
        $response = $this->actingAs($this->customer)->post(route('muahang.process'), [
            'cart_phone' => '0901234567',
            'cart_address' => '456 Lê Lợi, Quận 1',
            'cart_time' => 'Giao siêu tốc 20 phút',
            'cart_payment' => 'cod',
            'coupon_code' => 'TEST_FIXED50K',
            'cart_items' => json_encode([
                [
                    'id' => $this->dish->id,
                    'quantity' => 2,
                ]
            ]),
        ]);

        $order = Order::where('user_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(200000, (float) $order->total_amount);
        $this->assertEquals(150000, (float) $order->final_amount);
        $this->assertEquals($this->couponFixed->id, $order->coupon_id);

        // Kiểm tra usage_limit của coupon đã giảm 1
        $this->couponFixed->refresh();
        $this->assertEquals(49, $this->couponFixed->usage_limit);

        // Kiểm tra trong user_coupons đã đánh dấu is_used = true
        $this->assertDatabaseHas('user_coupons', [
            'user_id' => $this->customer->id,
            'coupon_id' => $this->couponFixed->id,
            'is_used' => true,
        ]);
    }

    public function test_pages_contain_required_coupon_sections_and_modals(): void
    {
        // 1. Trang chủ
        $homeResponse = $this->get(route('trangchu'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Kho Ưu Đãi');
        $homeResponse->assertSee('voucherModal');
        $homeResponse->assertSee('globalUserVoucherModal');
        $homeResponse->assertSee('TẶNG BẠN KHO MÃ GIẢM GIÁ ĐỘC QUYỀN');

        // 2. Trang đặt món (muahang) khi chưa đăng nhập
        $buyGuestResponse = $this->get(route('muahang'));
        $buyGuestResponse->assertStatus(200);
        $buyGuestResponse->assertSee('Mã khuyến mãi chỉ sử dụng được sau khi đăng nhập thành công');

        // 3. Trang đặt món khi đã đăng nhập
        $buyAuthResponse = $this->actingAs($this->customer)->get(route('muahang'));
        $buyAuthResponse->assertStatus(200);
        $buyAuthResponse->assertSee('couponSelectorModal');
        $buyAuthResponse->assertSee('auto-coupon-banner');
    }

    public function test_admin_can_view_create_coupon_page_with_shopee_and_special_presets(): void
    {
        $response = $this->actingAs($this->admin)->get(route('khuyenmai_them'));

        $response->assertStatus(200);
        $response->assertSee('Shopee Siêu Sale Ngày Đôi');
        $response->assertSee('10.10 Mega Sale');
        $response->assertSee('11.11 Độc Thân');
        $response->assertSee('Tết Nguyên Đán');
        $response->assertSee('Black Friday');
        $response->assertSee('Gửi email hàng loạt tới các tài khoản khách hàng qua Gmail');
    }

    public function test_admin_can_create_shopee_double_day_coupon_and_bulk_broadcast_email_to_gmail_customers(): void
    {
        Queue::fake();

        // Tạo 2 khách hàng sử dụng Gmail và 1 khách hàng không phải Gmail
        $gmailCustomer1 = User::factory()->create([
            'role' => 'customer',
            'fullname' => 'Khách Hàng Gmail 1',
            'email' => 'customer_shopee1@gmail.com',
            'status' => true,
        ]);

        $gmailCustomer2 = User::factory()->create([
            'role' => 'customer',
            'fullname' => 'Khách Hàng Gmail 2',
            'email' => 'customer_shopee2@gmail.com',
            'status' => true,
        ]);

        $nonGmailCustomer = User::factory()->create([
            'role' => 'customer',
            'fullname' => 'Khách Hàng Yahoo',
            'email' => 'customer_other@yahoo.com',
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('khuyenmai_them.post'), [
            'coupon_code' => 'SHOPEE_1111_MEGA',
            'discount_type' => 'percent',
            'discount_value' => 50,
            'min_order_value' => 120000,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addDays(3)->format('Y-m-d'),
            'usage_limit' => 500,
            'send_email_now' => 1,
            'auto_save_wallet' => 1,
            'email_subject' => '[FOODDAILY] ⚡ Bùng nổ siêu sale 11.11!',
            'email_message' => 'Ưu đãi 50% chỉ trong dịp 11.11!',
        ]);

        $response->assertRedirect(route('quanly_khuyenmai'));
        $this->assertDatabaseHas('coupons', [
            'coupon_code' => 'SHOPEE_1111_MEGA',
            'discount_value' => 50,
        ]);

        $createdCoupon = Coupon::where('coupon_code', 'SHOPEE_1111_MEGA')->first();
        $this->assertNotNull($createdCoupon);

        // Kiểm tra Queue đã nhận đúng 2 job gửi email cho 2 khách hàng Gmail
        Queue::assertPushed(SendPromoCouponEmail::class, 2);

        // Kiểm tra mã đã được tự động lưu vào ví ưu đãi (user_coupons) của 2 khách hàng Gmail
        $this->assertDatabaseHas('user_coupons', [
            'user_id' => $gmailCustomer1->id,
            'coupon_id' => $createdCoupon->id,
            'is_used' => false,
        ]);

        $this->assertDatabaseHas('user_coupons', [
            'user_id' => $gmailCustomer2->id,
            'coupon_id' => $createdCoupon->id,
            'is_used' => false,
        ]);

        // Khách hàng không dùng Gmail không được gửi email
        $this->assertDatabaseMissing('user_coupons', [
            'user_id' => $nonGmailCustomer->id,
            'coupon_id' => $createdCoupon->id,
        ]);
    }

    public function test_admin_can_broadcast_existing_coupon_to_gmail_customers(): void
    {
        Queue::fake();

        $gmailCustomer = User::factory()->create([
            'role' => 'customer',
            'fullname' => 'Khách Hàng Broadcast',
            'email' => 'broadcast_user@gmail.com',
            'status' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('khuyenmai_guimail', $this->couponFixed->id), [
            'only_gmail' => 1,
            'auto_save_wallet' => 1,
            'email_subject' => 'Ưu đãi đặc biệt gửi tới bạn',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Queue::assertPushed(SendPromoCouponEmail::class);

        $this->assertDatabaseHas('user_coupons', [
            'user_id' => $gmailCustomer->id,
            'coupon_id' => $this->couponFixed->id,
            'is_used' => false,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user to perform the actions
        $this->admin = User::factory()->create([
            'fullname' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'status' => true,
        ]);
    }

    /**
     * Test Category CRUD
     */
    public function test_category_crud_operations(): void
    {
        // 1. Create category
        $response = $this->actingAs($this->admin)->post(route('danhmuc_them.post'), [
            'category_name' => 'Khai Vi',
            'description' => 'Cac mon khai vi nhe nhang',
        ]);
        $response->assertRedirect(route('quanly_danhmuc'));
        $this->assertDatabaseHas('categories', ['category_name' => 'Khai Vi']);

        $category = Category::where('category_name', 'Khai Vi')->first();

        // 2. Read category list & detail
        $response = $this->actingAs($this->admin)->get(route('quanly_danhmuc'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('danhmuc_xem', $category->id));
        $response->assertStatus(200);

        // 3. Edit category
        $response = $this->actingAs($this->admin)->post(route('danhmuc_chinhsua.post', $category->id), [
            'category_name' => 'Khai Vi Updated',
            'description' => 'Updated description',
        ]);
        $response->assertRedirect(route('quanly_danhmuc'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'category_name' => 'Khai Vi Updated']);

        // 4. Delete category
        $response = $this->actingAs($this->admin)->post(route('danhmuc_xoa', $category->id));
        $response->assertRedirect(route('quanly_danhmuc'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test Dish CRUD
     */
    public function test_dish_crud_operations(): void
    {
        $category = Category::create([
            'category_name' => 'Main',
            'status' => true,
        ]);

        // 1. Create Dish
        $response = $this->actingAs($this->admin)->post(route('monandon_them.post'), [
            'dish_name' => 'Sup Cua',
            'category_id' => $category->id,
            'price' => 30000,
            'description' => 'Sup cua bien thom ngon',
            'is_available' => '1',
        ]);
        $response->assertRedirect(route('quanly_monandon'));
        $this->assertDatabaseHas('dishes', ['dish_name' => 'Sup Cua', 'category_id' => $category->id, 'price' => 30000]);

        $dish = Dish::where('dish_name', 'Sup Cua')->first();

        // 2. Read Dish list & detail
        $response = $this->actingAs($this->admin)->get(route('quanly_monandon'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('monandon_xem', $dish->id));
        $response->assertStatus(200);

        // 3. Edit Dish
        $response = $this->actingAs($this->admin)->post(route('monandon_chinhsua.post', $dish->id), [
            'dish_name' => 'Sup Cua Updated',
            'category_id' => $category->id,
            'price' => 35000,
            'description' => 'Updated desc',
        ]);
        $response->assertRedirect(route('quanly_monandon'));
        $this->assertDatabaseHas('dishes', ['id' => $dish->id, 'dish_name' => 'Sup Cua Updated', 'price' => 35000]);

        // 4. Delete Dish
        $response = $this->actingAs($this->admin)->post(route('monandon_xoa', $dish->id));
        $response->assertRedirect(route('quanly_monandon'));
        $this->assertDatabaseMissing('dishes', ['id' => $dish->id]);
    }

    /**
     * Test Coupon CRUD & Sending Coupons
     */
    public function test_coupon_crud_and_sending(): void
    {
        // 1. Create Coupon
        $response = $this->actingAs($this->admin)->post(route('khuyenmai_them.post'), [
            'coupon_code' => 'TEST50',
            'discount_type' => 'fixed',
            'discount_value' => 50000,
            'min_order_value' => 100000,
            'start_date' => '2026-06-01',
            'end_date' => '2026-07-01',
            'usage_limit' => 100,
        ]);
        $response->assertRedirect(route('quanly_khuyenmai'));
        $this->assertDatabaseHas('coupons', ['coupon_code' => 'TEST50', 'discount_value' => 50000]);

        $coupon = Coupon::where('coupon_code', 'TEST50')->first();

        // 2. Edit Coupon
        $response = $this->actingAs($this->admin)->post(route('khuyenmai_chinhsua.post', $coupon->id), [
            'coupon_code' => 'TEST50_UPDATED',
            'discount_type' => 'fixed',
            'discount_value' => 60000,
            'min_order_value' => 120000,
            'start_date' => '2026-06-01',
            'end_date' => '2026-07-01',
            'usage_limit' => 150,
        ]);
        $response->assertRedirect(route('quanly_khuyenmai'));
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'coupon_code' => 'TEST50_UPDATED', 'discount_value' => 60000]);

        // 3. Send Coupon to users (Advanced generator test)
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => true,
            'membership' => 'gold',
            'email' => 'testcustomer@gmail.com',
        ]);

        $response = $this->actingAs($this->admin)->post(route('quanly_guima.post'), [
            'discount_value' => 10,
            'discount_type' => 'percentage',
            'code_length' => 8,
            'expiry_days' => 7,
            'min_order' => 50000,
            'email_subject' => 'Chao ban',
            'message_body' => 'Ma cua ban la [MÃ_TỰ_SINH] han dung [SỐ_NGÀY] ngay',
            'ranks' => ['gold'],
        ]);

        $response->assertRedirect(route('quanly_guima'));
        $this->assertDatabaseHas('coupons', [
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order_value' => 50000,
        ]);

        // 4. Delete Coupon
        $response = $this->actingAs($this->admin)->post(route('khuyenmai_xoa', $coupon->id));
        $response->assertRedirect(route('quanly_khuyenmai'));
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    /**
     * Test Employee CRUD
     */
    public function test_employee_crud_operations(): void
    {
        // 1. Create Employee
        $response = $this->actingAs($this->admin)->post(route('nhanvien_them.post'), [
            'fullname' => 'Nhan Vien B',
            'phone' => '0911222333',
            'email' => 'nhanvien_b@example.com',
            'password' => 'nhanvienpass',
            'role' => 'staff',
            'status' => '1',
            'notes' => 'Giao hang phan khu A',
        ]);
        $response->assertRedirect(route('quanly_nhanvien'));
        $this->assertDatabaseHas('users', ['email' => 'nhanvien_b@example.com', 'role' => 'staff']);

        $employee = User::where('email', 'nhanvien_b@example.com')->first();

        // 2. Read list & detail
        $response = $this->actingAs($this->admin)->get(route('quanly_nhanvien'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('nhanvien_xem', $employee->id));
        $response->assertStatus(200);

        // 3. Edit Employee
        $response = $this->actingAs($this->admin)->post(route('nhanvien_chinhsua.post', $employee->id), [
            'fullname' => 'Nhan Vien B Updated',
            'phone' => '0911222333',
            'email' => 'nhanvien_b@example.com',
            'role' => 'staff',
            'status' => '0',
            'notes' => 'Updated notes',
        ]);
        $response->assertRedirect(route('quanly_nhanvien'));
        $this->assertDatabaseHas('users', ['id' => $employee->id, 'fullname' => 'Nhan Vien B Updated', 'status' => 0]);

        // 4. Delete Employee
        $response = $this->actingAs($this->admin)->post(route('nhanvien_xoa', $employee->id));
        $response->assertRedirect(route('quanly_nhanvien'));
        $this->assertDatabaseMissing('users', ['id' => $employee->id]);
    }

    /**
     * Test Staff Role Permissions restriction
     */
    public function test_staff_role_permissions_restricted(): void
    {
        $staffUser = User::factory()->create([
            'fullname' => 'Staff A',
            'email' => 'staff_a@example.com',
            'role' => 'staff',
            'status' => true,
        ]);

        // 1. Staff tries to access admin-only pages, should redirect to /quanly_banlamviec with error
        $response = $this->actingAs($staffUser)->get(route('quanly'));
        $response->assertRedirect(route('quanly_banlamviec'));
        $response->assertSessionHasErrors(['role']);

        $response = $this->actingAs($staffUser)->get(route('quanly_nhanvien'));
        $response->assertRedirect(route('quanly_banlamviec'));
        $response->assertSessionHasErrors(['role']);

        $response = $this->actingAs($staffUser)->get(route('quanly_khuyenmai'));
        $response->assertRedirect(route('quanly_banlamviec'));
        $response->assertSessionHasErrors(['role']);

        // 2. Staff accesses shared pages and workspace successfully
        $response = $this->actingAs($staffUser)->get(route('quanly_banlamviec'));
        $response->assertStatus(200);

        $response = $this->actingAs($staffUser)->get(route('quanly_donhang'));
        $response->assertStatus(200);

        $response = $this->actingAs($staffUser)->get(route('quanly_danhmuc'));
        $response->assertStatus(200);
    }

    /**
     * Test fixes for subscription cancellation refund amount, empty checkout validation, and order delete cascade.
     */
    public function test_system_optimizations_and_fixes(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Healthy Bowl Fix',
            'status' => true,
        ]);

        $dish = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Dish Test Fix',
            'price' => 50000,
            'is_available' => true,
        ]);

        // 1. Test empty items checkout
        $response = $this->actingAs($customer)->post(route('muahang.process'), [
            'cart_phone' => '0987654321',
            'cart_address' => '123 Test St',
            'cart_time' => '12:00',
            'cart_payment' => 'cod',
            'cart_items' => json_encode([]),
        ]);
        $response->assertSessionHasErrors(['cart_items']);

        // 2. Test order delete cascade
        $order = Order::create([
            'user_id' => $customer->id,
            'order_type' => 'single',
            'total_amount' => 100000,
            'final_amount' => 100000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $dish->id,
            'quantity' => 2,
            'price' => 50000,
        ]);

        // Admin deletes the order
        $response = $this->actingAs($this->admin)->post(route('donhang_xoa', $order->id));
        $response->assertRedirect(route('quanly_donhang'));

        // Verify order is deleted
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
        $this->assertDatabaseMissing('order_items', ['order_id' => $order->id]);
    }

    /**
     * Test successful checkout flow and simulated payment redirects.
     */
    public function test_valid_checkout_flow(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Menu Item',
            'status' => true,
        ]);

        $dish = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Test Pho',
            'price' => 45000,
            'is_available' => true,
        ]);

        // Post checkout form
        $response = $this->actingAs($customer)->post(route('muahang.process'), [
            'cart_phone' => '0912345678',
            'cart_address' => '123 ABC St',
            'cart_time' => 'now',
            'cart_payment' => 'momo',
            'cart_items' => json_encode([
                [
                    'id' => $dish->id,
                    'quantity' => 2,
                ],
            ]),
        ]);

        // Assert redirect to momo simulated page
        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('thanhtoan_momo', ['order_id' => $order->id, 'amount' => 90000]));

        // Visit the simulator route to verify it returns the transfer payment page correctly without 404
        $simulatorResponse = $this->actingAs($customer)->get(route('thanhtoan_momo', ['order_id' => $order->id, 'amount' => 90000]));
        $simulatorResponse->assertStatus(200);
        $simulatorResponse->assertViewIs('client.transfer_payment');
    }

    /**
     * Test success notifications are flashed and present in the session on successful purchase or package subscription.
     */
    public function test_purchase_notifications_success(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Menu Item',
            'status' => true,
        ]);

        $dish = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Test Pho',
            'price' => 45000,
            'is_available' => true,
        ]);

        // 1. Check COD checkout success message
        $response = $this->actingAs($customer)->post(route('muahang.process'), [
            'cart_phone' => '0912345678',
            'cart_address' => '123 ABC St',
            'cart_time' => 'now',
            'cart_payment' => 'cod',
            'cart_items' => json_encode([
                [
                    'id' => $dish->id,
                    'quantity' => 2,
                ],
            ]),
        ]);

        $orderCod = Order::where('user_id', $customer->id)->where('payment_method', 'cash')->first();
        $this->assertNotNull($orderCod);
        $response->assertRedirect(route('giohang'));
        $response->assertSessionHas('success', 'Đơn hàng FDL-'.$orderCod->id.' đã được đặt thành công!');

        // 2. Check MoMo / ATM complete payment success message
        $response = $this->actingAs($customer)->post(route('muahang.process'), [
            'cart_phone' => '0912345678',
            'cart_address' => '123 ABC St',
            'cart_time' => 'now',
            'cart_payment' => 'momo',
            'cart_items' => json_encode([
                [
                    'id' => $dish->id,
                    'quantity' => 1,
                ],
            ]),
        ]);

        $orderMomo = Order::where('user_id', $customer->id)->where('payment_method', 'momo')->first();
        $this->assertNotNull($orderMomo);

        // Simulated complete payment
        $responseComplete = $this->actingAs($customer)->get(route('thanhtoan_hoantat', $orderMomo->id));
        $responseComplete->assertRedirect(route('giohang'));
        $responseComplete->assertSessionHas('success', 'Thanh toán đơn hàng FDL-'.$orderMomo->id.' thành công! Đơn hàng đang được nhà hàng chuẩn bị.');

    }
}


<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FullSystemWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test client registration, login, checkout, and staff/admin operations.
     */
    public function test_complete_customer_to_staff_workflow(): void
    {
        // 1. Setup initial DB seeds manually or programmatically
        $category = Category::create([
            'category_name' => 'Mon Chinh',
            'description' => 'Cac mon chinh an kem com',
            'icon' => 'dish.png',
            'status' => true,
        ]);

        $dish1 = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Com ga hai nam',
            'price' => 50000,
            'is_available' => true,
            'description' => 'Com ga ngon dam da',
            'image_url' => 'comga.jpg',
        ]);

        $dish2 = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Com tam suon bi cha',
            'price' => 45000,
            'is_available' => true,
            'description' => 'Com tam dac san',
            'image_url' => 'comtam.jpg',
        ]);



        // 2. Test Customer registration
        $registrationData = [
            'fullname' => 'Nguyen Van A',
            'email' => 'customer_a@example.com',
            'phone' => '0987654321',
            'address' => '123 Ly Thuong Kiet, Q10, HCMC',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => 'on',
        ];

        $response = $this->post(route('trangchu/dangky.post'), $registrationData);
        $response->assertRedirect(route('trangchu'));

        $this->assertDatabaseHas('users', [
            'email' => 'customer_a@example.com',
            'phone' => '0987654321',
            'role' => 'customer',
        ]);

        $user = User::where('email', 'customer_a@example.com')->first();
        $this->assertStringContainsString('123 Ly Thuong Kiet', $user->notes);

        // 3. Test Customer login
        $loginData = [
            'login_input' => '0987654321',
            'password' => 'password123',
        ];

        $response = $this->post(route('trangchu/dangnhap.post'), $loginData);
        $response->assertRedirect(route('trangchu'));
        $this->assertAuthenticatedAs($user);

        // 4. Test Cart Checkout
        // Simulating the localStorage format submitted as a JSON string
        $cartItems = json_encode([
            ['id' => $dish1->id, 'quantity' => 2],
            ['id' => $dish2->id, 'quantity' => 1],
        ]);

        $checkoutData = [
            'cart_phone' => '0987654321',
            'cart_address' => '123 Ly Thuong Kiet, HCMC',
            'cart_time' => '12:00',
            'cart_payment' => 'cod', // cash
            'cart_items' => $cartItems,
        ];

        $response = $this->actingAs($user)->post(route('muahang.process'), $checkoutData);
        $response->assertRedirect(route('giohang'));

        // Total price is: (50000 * 2) + 45000 = 145000
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'order_type' => 'single',
            'total_amount' => 145000,
            'payment_method' => 'cash',
            'order_status' => 'pending',
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'dish_id' => $dish1->id,
            'quantity' => 2,
            'price' => 50000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'dish_id' => $dish2->id,
            'quantity' => 1,
            'price' => 45000,
        ]);

        // 5. Test Staff access and order status transition
        $staff = User::factory()->create([
            'fullname' => 'Staff Member',
            'email' => 'staff_member@example.com',
            'role' => 'staff',
            'status' => true,
        ]);

        // Staff/Admin updates order status from pending to preparing/delivering/completed
        $response = $this->actingAs($staff)->post(route('donhang_chinhsua.post', $order->id), [
            'order_status' => 'delivering',
            'payment_status' => 'pending',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'delivering',
        ]);

        // Staff/Admin marks as completed
        $response = $this->actingAs($staff)->post(route('donhang_chinhsua.post', $order->id), [
            'order_status' => 'completed',
            'payment_status' => 'paid',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }
}

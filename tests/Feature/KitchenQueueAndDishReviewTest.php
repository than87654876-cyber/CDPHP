<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitchenQueueAndDishReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Category $category;
    protected Dish $dishA;
    protected Dish $dishB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => true,
        ]);

        $this->customer = User::factory()->create([
            'fullname' => 'Trần Văn Khách',
            'phone' => '0901234567',
            'role' => 'customer',
            'status' => true,
        ]);

        $this->category = Category::create([
            'category_name' => 'Món Ăn Nhanh',
            'status' => true,
        ]);

        $this->dishA = Dish::create([
            'category_id' => $this->category->id,
            'dish_name' => 'Gà Rán Giòn Rụm (2 Miếng)',
            'price' => 69000,
            'is_available' => true,
        ]);

        $this->dishB = Dish::create([
            'category_id' => $this->category->id,
            'dish_name' => 'Burger Bò Phô Mai Nướng',
            'price' => 75000,
            'is_available' => true,
        ]);
    }

    /**
     * Test kitchen queue shows order with multiple dishes grouped under the order code in FIFO sequence.
     */
    public function test_kitchen_queue_groups_multiple_dishes_under_order_code_fifo(): void
    {
        // Order 1 placed first with 2 dishes (Gà Rán x2, Burger x1)
        $order1 = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 213000,
            'final_amount' => 213000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'health_notes' => 'Không tương ớt, cho nhiều tương cà',
            'created_at' => now()->subMinutes(15),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'dish_id' => $this->dishA->id,
            'quantity' => 2,
            'price' => 69000,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'dish_id' => $this->dishB->id,
            'quantity' => 1,
            'price' => 75000,
        ]);

        // Order 2 placed later with 1 dish
        $order2 = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 69000,
            'final_amount' => 69000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'created_at' => now()->subMinutes(5),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'dish_id' => $this->dishA->id,
            'quantity' => 1,
            'price' => 69000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('quanly_bep'));
        $response->assertStatus(200);

        // Check Order 1 ticket
        $response->assertSee('#FDL-' . $order1->id);
        $response->assertSee('Gà Rán Giòn Rụm (2 Miếng)');
        $response->assertSee('2 suất');
        $response->assertSee('Burger Bò Phô Mai Nướng');
        $response->assertSee('1 suất');
        $response->assertSee('Không tương ớt, cho nhiều tương cà');
        $response->assertSee('Bắt đầu nấu đơn này');

        // Check Order 2 ticket
        $response->assertSee('#FDL-' . $order2->id);

        // Verify FIFO order: Order 1 text appears before Order 2 in the output
        $content = $response->getContent();
        $pos1 = strpos($content, '#FDL-' . $order1->id);
        $pos2 = strpos($content, '#FDL-' . $order2->id);
        $this->assertTrue($pos1 < $pos2, 'Order 1 placed earlier must appear before Order 2 placed later in the kitchen queue (FIFO).');
    }

    /**
     * Test reviewing an order automatically creates reviews for all dishes in that order.
     */
    public function test_user_can_review_order_and_evaluates_dishes(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 144000,
            'final_amount' => 144000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dishA->id,
            'quantity' => 1,
            'price' => 69000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dishB->id,
            'quantity' => 1,
            'price' => 75000,
        ]);

        // Submit review for order
        $response = $this->actingAs($this->customer)->post(route('order.review'), [
            'order_id' => $order->id,
            'rating_stars' => 5,
            'review_comment' => 'Món ăn giao nhanh, gà rất giòn rụm và burger phô mai cực thơm ngon!',
        ]);

        $response->assertRedirect(route('giohang'));
        $response->assertSessionHas('success');

        // Verify review for order created
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'dish_id' => null,
            'rating' => 5,
        ]);

        // Verify reviews for each dish in order created
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'dish_id' => $this->dishA->id,
            'rating' => 5,
        ]);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'order_id' => $order->id,
            'dish_id' => $this->dishB->id,
            'rating' => 5,
        ]);

        // Verify dish detail page shows the review
        $detailResponse = $this->get(route('dish.detail', $this->dishA->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Món ăn giao nhanh, gà rất giòn rụm và burger phô mai cực thơm ngon!');
        $detailResponse->assertSee($this->customer->fullname);
    }

    /**
     * Test direct dish review on dish detail page.
     */
    public function test_user_can_submit_review_directly_for_dish(): void
    {
        $response = $this->actingAs($this->customer)->post(route('dish.review', $this->dishA->id), [
            'rating' => 5,
            'comment' => 'Gà rán ngon nhất từng ăn, nước chấm đậm đà xuất sắc!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'dish_id' => $this->dishA->id,
            'rating' => 5,
            'comment' => 'Gà rán ngon nhất từng ăn, nước chấm đậm đà xuất sắc!',
        ]);

        // View dish detail
        $detailResponse = $this->get(route('dish.detail', $this->dishA->id));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Gà rán ngon nhất từng ăn, nước chấm đậm đà xuất sắc!');
    }

    /**
     * Test Kitchen Display System toolbar features: search input, 3-order focus mode, and AJAX completion.
     */
    public function test_kitchen_display_system_toolbar_search_and_ajax_auto_advance(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 150000,
            'final_amount' => 150000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'preparing',
        ]);

        $response = $this->actingAs($this->admin)->get(route('quanly_bep'));
        $response->assertStatus(200);

        // Verify search input & controls are present
        $response->assertSee('id="kitchen-search-input"', false);
        $response->assertSee('id="btn-view-limit-3"', false);
        $response->assertSee('id="chk-auto-advance"', false);
        $response->assertSee('id="completedOrdersModal"', false);
        $response->assertSee('id="modal-search-completed"', false);
        $response->assertSee('data-phone=', false);
        $response->assertSee('data-dishes-json=', false);

        // Test AJAX completion of the order (Kitchen ticking done -> Delivering)
        $ajaxResponse = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('donhang_chinhsua.post', $order->id), [
                'order_status' => 'delivering',
                'payment_status' => 'paid',
                'admin_notes' => 'Đầu bếp đã nấu xong, tự động chuyển đơn mới lên',
            ]);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'delivering',
        ]);
    }
}


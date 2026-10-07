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

class OrderDeliveryReviewPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected Category $category;
    protected Dish $dish1;
    protected Dish $dish2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'fullname' => 'Nguyễn Thị Khách',
            'phone' => '0912345678',
            'role' => 'customer',
            'status' => true,
        ]);

        $this->category = Category::create([
            'category_name' => 'Cơm Trưa Văn Phòng',
            'status' => true,
        ]);

        $this->dish1 = Dish::create([
            'category_id' => $this->category->id,
            'dish_name' => 'Cơm Tấm Sườn Bì Chả Đặc Biệt',
            'price' => 65000,
            'is_available' => true,
        ]);

        $this->dish2 = Dish::create([
            'category_id' => $this->category->id,
            'dish_name' => 'Canh Rong Biển Thịt Bằm',
            'price' => 25000,
            'is_available' => true,
        ]);
    }

    /**
     * Test order review page displays after delivery completion with options to review or skip.
     */
    public function test_order_review_page_displays_with_review_and_skip_options(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 90000,
            'final_amount' => 90000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed', // Đã giao hàng thành công
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dish1->id,
            'quantity' => 1,
            'price' => 65000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dish2->id,
            'quantity' => 1,
            'price' => 25000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('donhang.danhgia', $order->id));
        $response->assertStatus(200);

        // Check header & order info
        $response->assertSee('#FDL-' . $order->id);
        $response->assertSee('Giao Đơn Hàng Thành Công');
        $response->assertSee('Cơm Tấm Sườn Bì Chả Đặc Biệt');
        $response->assertSee('Canh Rong Biển Thịt Bằm');

        // Check both options: Review option & Skip option
        $response->assertSee('GỬI ĐÁNH GIÁ MÓN ĂN');
        $response->assertSee('Bỏ qua, không đánh giá');
    }

    /**
     * Test user can submit review from review page.
     */
    public function test_user_can_submit_review_from_review_page(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 65000,
            'final_amount' => 65000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dish1->id,
            'quantity' => 1,
            'price' => 65000,
        ]);

        $response = $this->actingAs($this->customer)->post(route('order.review'), [
            'order_id' => $order->id,
            'redirect_to' => 'review_page',
            'rating_stars' => 5,
            'review_comment' => 'Cơm tấm sườn nướng siêu thơm, nóng sốt và giao rất nhanh!',
        ]);

        $response->assertRedirect(route('donhang.danhgia', $order->id));
        $response->assertSessionHas('success');

        // Verify review created for order (Internal for admin)
        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'dish_id' => null,
            'rating' => 5,
        ]);

        $this->assertDatabaseMissing('reviews', [
            'order_id' => $order->id,
            'dish_id' => $this->dish1->id,
        ]);

        // Review page now shows existing review
        $pageResponse = $this->actingAs($this->customer)->get(route('donhang.danhgia', $order->id));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Bạn đã gửi đánh giá cho đơn hàng này');
        $pageResponse->assertSee('Cơm tấm sườn nướng siêu thơm, nóng sốt và giao rất nhanh!');
    }

    /**
     * Test track order page shows review button/banner when delivery is completed.
     */
    public function test_track_order_shows_review_banner_when_completed(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_type' => 'single',
            'total_amount' => 65000,
            'final_amount' => 65000,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dish1->id,
            'quantity' => 1,
            'price' => 65000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('tracuu', ['order_id' => 'FDL-' . $order->id]));
        $response->assertStatus(200);
        $response->assertSee('Mở trang đánh giá món ăn');
    }
}

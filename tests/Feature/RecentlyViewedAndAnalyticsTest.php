<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecentlyViewedAndAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Dish $dishA;
    protected Dish $dishB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@fooddaily.com',
        ]);
        $this->admin->assignRole('admin');

        $category = Category::create([
            'category_name' => 'Món Cơm Trưa',
            'description' => 'Cơm trưa dinh dưỡng',
        ]);

        $this->dishA = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Cơm Tấm Sườn Bì Chả',
            'price' => 55000,
            'description' => 'Cơm tấm sườn nướng thơm ngon',
            'is_available' => true,
        ]);

        $this->dishB = Dish::create([
            'category_id' => $category->id,
            'dish_name' => 'Cơm Gà Xối Mỡ Giòn',
            'price' => 60000,
            'description' => 'Cơm gà giòn rụm',
            'is_available' => true,
        ]);
    }

    public function test_viewing_dish_detail_adds_to_recently_viewed_session(): void
    {
        // 1. Visit Dish A
        $response = $this->get(route('dish.detail', $this->dishA->id));
        $response->assertStatus(200);
        $response->assertSessionHas('recently_viewed_dishes', [$this->dishA->id]);

        // 2. Visit Dish B
        $response = $this->get(route('dish.detail', $this->dishB->id));
        $response->assertStatus(200);
        // Dish B should be at the beginning (LIFO)
        $response->assertSessionHas('recently_viewed_dishes', [$this->dishB->id, $this->dishA->id]);
    }

    public function test_homepage_displays_recently_viewed_dishes(): void
    {
        // Simulate session with viewed dishes
        $response = $this->withSession(['recently_viewed_dishes' => [$this->dishA->id]])
            ->get(route('trangchu'));

        $response->assertStatus(200);
        $response->assertSee('Món Ăn Bạn Đã Xem Gần Đây');
        $response->assertSee('Cơm Tấm Sườn Bì Chả');
    }

    public function test_clear_recently_viewed_dishes(): void
    {
        $response = $this->withSession(['recently_viewed_dishes' => [$this->dishA->id, $this->dishB->id]])
            ->post(route('api.recently_viewed.clear'));

        $response->assertSessionMissing('recently_viewed_dishes');
    }

    public function test_admin_dashboard_shows_analytics_best_sellers_and_slow_moving(): void
    {
        // Create an order with Dish A (sold 10 units)
        $order = Order::create([
            'user_id' => $this->admin->id,
            'order_type' => 'single',
            'total_amount' => 550000,
            'final_amount' => 550000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'dish_id' => $this->dishA->id,
            'quantity' => 10,
            'price' => 55000,
        ]);

        // Dish B is NOT ordered (unsold / slow-moving)

        $response = $this->actingAs($this->admin)->get(route('quanly'));
        $response->assertStatus(200);

        // Check metrics & sections
        $response->assertSee('Tổng số món đã đặt');
        $response->assertSee('10'); // Total 10 dishes ordered
        $response->assertSee('Bảng thống kê chi tiết doanh thu 12 tháng');
        $response->assertSee('Top Món Ăn Bán Chạy Nhất');
        $response->assertSee('Cơm Tấm Sườn Bì Chả');
        $response->assertSee('Món Tồn & Bán Chậm Cần Chú Ý', false);
        $response->assertSee('Cơm Gà Xối Mỡ Giòn');
        $response->assertSee('0 suất');
    }

    public function test_kitchen_report_shows_newly_placed_orders_with_tracking_codes(): void
    {
        $customer = User::factory()->create(['fullname' => 'Nguyễn Văn A', 'phone' => '0987654321']);

        // 1. User places an order (status = pending)
        $order1 = Order::create([
            'user_id' => $customer->id,
            'order_type' => 'single',
            'total_amount' => 110000,
            'final_amount' => 110000,
            'payment_method' => 'cash',
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'health_notes' => 'Không ăn cay, giao tận phòng',
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'dish_id' => $this->dishA->id,
            'quantity' => 2,
            'price' => 55000,
        ]);

        // 2. Access kitchen report as Admin
        $response = $this->actingAs($this->admin)->get(route('quanly_bep'));
        $response->assertStatus(200);

        // 3. Verify dish and tracking code appear
        $response->assertSee('Bảng Chuẩn Bị Món Ăn Bếp Nấu');
        $response->assertSee('Món Ăn Cho Đơn Hàng Lẻ');
        $response->assertSee($this->dishA->dish_name);
        $response->assertSee('#FDL-' . $order1->id);
        $response->assertSee('2 suất');
        $response->assertSee('Phiếu Gọi Món Theo Mã Vận Đơn');
        $response->assertSee('Không ăn cay, giao tận phòng');
    }
}


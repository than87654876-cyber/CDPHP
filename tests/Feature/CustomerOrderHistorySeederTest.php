<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerOrderHistorySeeder;
use Database\Seeders\DishSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CustomerOrderHistorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_order_history_seeder_generates_valid_and_consistent_data()
    {
        $this->seed(CategorySeeder::class);
        $this->seed(DishSeeder::class);
        $this->seed(CustomerOrderHistorySeeder::class);

        // 1. Kiểm tra chính xác 100 khách hàng test
        $testCustomers = User::where('email', 'like', 'customer%@test.com')->get();
        $this->assertCount(100, $testCustomers);

        // 2. Kiểm tra không trùng email
        $emails = $testCustomers->pluck('email')->toArray();
        $this->assertCount(100, array_unique($emails));

        // 3. Kiểm tra mỗi khách hàng có từ 3 đến 10 đơn hàng
        foreach ($testCustomers as $customer) {
            $orderCount = $customer->orders()->count();
            $this->assertGreaterThanOrEqual(3, $orderCount);
            $this->assertLessThanOrEqual(10, $orderCount);
            $this->assertEquals('customer', $customer->role);
            $this->assertTrue((bool)$customer->status);
        }

        // 4. Kiểm tra đơn hàng và chi tiết món ăn
        $testCustomerIds = $testCustomers->pluck('id')->toArray();
        $orders = Order::whereIn('user_id', $testCustomerIds)->with('orderItems.dish')->get();

        $this->assertGreaterThanOrEqual(300, $orders->count());
        $this->assertLessThanOrEqual(1000, $orders->count());

        foreach ($orders as $order) {
            // Không có order mồ côi
            $this->assertTrue(in_array($order->user_id, $testCustomerIds));

            // Mỗi đơn có từ 2 đến 6 món
            $itemCount = $order->orderItems->count();
            $this->assertGreaterThanOrEqual(2, $itemCount);
            $this->assertLessThanOrEqual(6, $itemCount);

            // Kiểm tra tổng tiền đơn hàng khớp chính xác với tổng tiền các order_items
            $calculatedTotal = 0;
            foreach ($order->orderItems as $item) {
                $this->assertNotNull($item->dish);
                $this->assertGreaterThan(0, $item->quantity);
                $this->assertGreaterThan(0, $item->price);
                $calculatedTotal += $item->price * $item->quantity;
            }

            $this->assertEquals($calculatedTotal, (float)$order->total_amount);
            $this->assertEquals($calculatedTotal, (float)$order->final_amount);

            // Kiểm tra trạng thái thanh toán hợp lệ
            if (in_array($order->order_status, ['completed', 'delivering', 'preparing', 'confirmed'])) {
                $this->assertEquals('paid', $order->payment_status);
            }
        }

        // 5. Kiểm tra tính năng đồng mua (co-occurrence) phục vụ gợi ý món ăn
        $coOccurrences = DB::table('order_items as a')
            ->join('order_items as b', 'a.order_id', '=', 'b.order_id')
            ->whereColumn('a.dish_id', '<', 'b.dish_id')
            ->select('a.dish_id as dish_a', 'b.dish_id as dish_b', DB::raw('COUNT(*) as pair_count'))
            ->groupBy('a.dish_id', 'b.dish_id')
            ->having('pair_count', '>=', 2)
            ->get();

        $this->assertNotEmpty($coOccurrences);
        $this->assertGreaterThan(20, $coOccurrences->count());

        $totalItems = OrderItem::whereIn('order_id', $orders->pluck('id'))->count();
        $avgItemsPerOrder = round($totalItems / $orders->count(), 2);
        $avgOrdersPerCustomer = round($orders->count() / $testCustomers->count(), 2);

        fwrite(STDOUT, "\n=== KẾT QUẢ KIỂM TRA DỮ LIỆU SEED ===\n");
        fwrite(STDOUT, "1. Tổng customers: " . $testCustomers->count() . "\n");
        fwrite(STDOUT, "2. Tổng orders: " . $orders->count() . "\n");
        fwrite(STDOUT, "3. Tổng order_items: " . $totalItems . "\n");
        fwrite(STDOUT, "4. Số món trung bình / order: " . $avgItemsPerOrder . "\n");
        fwrite(STDOUT, "5. Số order trung bình / customer: " . $avgOrdersPerCustomer . "\n");
        fwrite(STDOUT, "6. Số cặp món đồng mua (Co-occurrences >= 2 lần): " . $coOccurrences->count() . "\n");
        fwrite(STDOUT, "=====================================\n");
    }
}


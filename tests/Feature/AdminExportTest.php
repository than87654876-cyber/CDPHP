<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'fullname' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'status' => true,
        ]);
    }

    public function test_export_orders_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('baocao_xuat_orders'));
        $response->assertStatus(200);
        $this->assertTrue($response->isOk());
    }

    public function test_export_customers_excel(): void
    {
        User::factory()->create(['role' => 'customer', 'fullname' => 'Nguyen Van A']);
        $response = $this->actingAs($this->admin)->get(route('baocao_xuat_customers'));
        $response->assertStatus(200);
        $this->assertTrue($response->isOk());
    }

    public function test_export_dishes_csv(): void
    {
        $cat = Category::create(['category_name' => 'Món chính', 'description' => 'Test']);
        Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Cơm tấm Sườn Bì',
            'price' => 45000,
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('baocao_xuat_dishes'));
        $response->assertStatus(200);
        $this->assertTrue($response->isOk());
    }
}

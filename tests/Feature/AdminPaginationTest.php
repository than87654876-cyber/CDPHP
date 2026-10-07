<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPaginationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create([
            'fullname' => 'Admin Tester',
            'email' => 'admin_tester@example.com',
            'role' => 'admin',
            'status' => true,
        ]);
    }

    public function test_orders_page_is_paginated(): void
    {
        $response = $this->actingAs($this->admin)->get(route('quanly_donhang'));

        $response->assertStatus(200);
        $response->assertViewHas('orders');
        $orders = $response->viewData('orders');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $orders);
        $this->assertEquals(10, $orders->perPage());
    }

    public function test_reviews_page_is_paginated_and_maintains_global_statistics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('quanly_reviews'));

        $response->assertStatus(200);
        $response->assertViewHas('reviews');
        $response->assertViewHas('totalReviewsCount');
        $response->assertViewHas('avgRating');

        $reviews = $response->viewData('reviews');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $reviews);
        $this->assertEquals(10, $reviews->perPage());
    }

    public function test_customers_page_is_paginated(): void
    {
        $response = $this->actingAs($this->admin)->get(route('quanly_khachhang'));

        $response->assertStatus(200);
        $response->assertViewHas('customers');
        $customers = $response->viewData('customers');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $customers);
        $this->assertEquals(10, $customers->perPage());
    }

    public function test_kitchen_page_renders_kds_pagination_bar(): void
    {
        $response = $this->actingAs($this->admin)->get(route('quanly_bep'));

        $response->assertStatus(200);
        $response->assertSee('kitchen-pagination-bar', false);
        $response->assertSee('btn-kds-prev', false);
        $response->assertSee('btn-kds-next', false);
        $response->assertSee('btn-sd-limit-5', false);
        $response->assertSee('btn-sd-limit-10', false);
    }
}

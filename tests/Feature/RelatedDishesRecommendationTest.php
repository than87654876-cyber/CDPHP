<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatedDishesRecommendationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_get_related_dishes_returns_same_category_only()
    {
        $cat1 = Category::create(['category_name' => 'Trà sữa', 'description' => 'Các loại trà sữa']);
        $cat2 = Category::create(['category_name' => 'Cà phê', 'description' => 'Các loại cà phê']);

        $currentDish = Dish::create([
            'category_id' => $cat1->id,
            'dish_name' => 'Trà sữa truyền thống',
            'price' => 30000,
            'is_available' => true,
        ]);

        $sameCategoryDish1 = Dish::create([
            'category_id' => $cat1->id,
            'dish_name' => 'Trà sữa matcha',
            'price' => 35000,
            'is_available' => true,
        ]);

        $sameCategoryDish2 = Dish::create([
            'category_id' => $cat1->id,
            'dish_name' => 'Trà sữa socola',
            'price' => 35000,
            'is_available' => true,
        ]);

        $differentCategoryDish = Dish::create([
            'category_id' => $cat2->id,
            'dish_name' => 'Cà phê đen',
            'price' => 20000,
            'is_available' => true,
        ]);

        $related = $currentDish->getRelatedDishes(6);

        $this->assertCount(2, $related);
        $this->assertTrue($related->contains('id', $sameCategoryDish1->id));
        $this->assertTrue($related->contains('id', $sameCategoryDish2->id));
        $this->assertFalse($related->contains('id', $differentCategoryDish->id));
    }

    public function test_get_related_dishes_excludes_current_dish()
    {
        $cat = Category::create(['category_name' => 'Đồ ăn', 'description' => 'Đồ ăn']);

        $currentDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Gà Rán Giòn Rụm',
            'price' => 38000,
            'is_available' => true,
        ]);

        $otherDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Gà Sốt Cay',
            'price' => 45000,
            'is_available' => true,
        ]);

        $related = $currentDish->getRelatedDishes(6);

        $this->assertCount(1, $related);
        $this->assertFalse($related->contains('id', $currentDish->id));
        $this->assertTrue($related->contains('id', $otherDish->id));
    }

    public function test_get_related_dishes_excludes_unavailable_dishes()
    {
        $cat = Category::create(['category_name' => 'Pizza', 'description' => 'Pizza']);

        $currentDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Pizza Hải Sản',
            'price' => 89000,
            'is_available' => true,
        ]);

        $availableDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Pizza Phô Mai',
            'price' => 79000,
            'is_available' => true,
        ]);

        $unavailableDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Pizza Bò Dứa (Hết hàng)',
            'price' => 85000,
            'is_available' => false,
        ]);

        $related = $currentDish->getRelatedDishes(6);

        $this->assertCount(1, $related);
        $this->assertTrue($related->contains('id', $availableDish->id));
        $this->assertFalse($related->contains('id', $unavailableDish->id));
    }

    public function test_get_related_dishes_limits_to_six()
    {
        $cat = Category::create(['category_name' => 'Tráng miệng', 'description' => 'Tráng miệng']);

        $currentDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Chè Thái',
            'price' => 25000,
            'is_available' => true,
        ]);

        for ($i = 1; $i <= 10; $i++) {
            Dish::create([
                'category_id' => $cat->id,
                'dish_name' => 'Món tráng miệng ' . $i,
                'price' => 20000 + $i * 1000,
                'is_available' => true,
            ]);
        }

        $related = $currentDish->getRelatedDishes(6);

        $this->assertCount(6, $related);
    }

    public function test_category_with_no_other_dishes_returns_empty()
    {
        $cat = Category::create(['category_name' => 'Đặc sản hiếm', 'description' => 'Món đơn lẻ']);

        $loneDish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Món duy nhất',
            'price' => 150000,
            'is_available' => true,
        ]);

        $related = $loneDish->getRelatedDishes(6);

        $this->assertTrue($related->isEmpty());
    }

    public function test_dish_detail_route_loads_and_displays_related_dishes()
    {
        $cat = Category::create(['category_name' => 'Đồ uống', 'description' => 'Đồ uống']);

        $dish1 = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Coca Cola',
            'price' => 15000,
            'is_available' => true,
        ]);

        $dish2 = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Pepsi',
            'price' => 15000,
            'is_available' => true,
        ]);

        $response = $this->get(route('dish.detail', $dish1->id));

        $response->assertStatus(200);
        $response->assertSee('Coca Cola');
        $response->assertSee('Pepsi');
        $response->assertSee('Món Cùng Loại Bạn Có Thể Thích');
        $response->assertSessionHas('selected_dish_id', $dish1->id);
    }

    public function test_home_page_displays_related_dishes_section()
    {
        $cat = Category::create(['category_name' => 'Gà rán', 'description' => 'Gà rán']);

        $dish1 = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Gà Giòn Cay',
            'price' => 38000,
            'is_available' => true,
        ]);

        $dish2 = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Gà Sốt Mật Ong',
            'price' => 45000,
            'is_available' => true,
        ]);

        $response = $this->get(route('trangchu', ['dish_id' => $dish1->id]));

        $response->assertStatus(200);
        $response->assertSee('Món Cùng Loại Bạn Có Thể Thích');
        $response->assertSee('Gà Sốt Mật Ong');
    }

    public function test_adding_dish_to_cart_sets_selected_dish_id_in_session()
    {
        $cat = Category::create(['category_name' => 'Cơm tấm', 'description' => 'Cơm tấm']);

        $dish = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Cơm Tấm Sườn Bì Chả',
            'price' => 45000,
            'is_available' => true,
        ]);

        $response = $this->post(route('giohang.add'), [
            'dish_id' => $dish->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('muahang'));
        $response->assertSessionHas('selected_dish_id', $dish->id);
    }

    public function test_checkout_page_displays_related_dishes_when_dish_in_session()
    {
        $cat = Category::create(['category_name' => 'Tráng miệng', 'description' => 'Tráng miệng']);

        $dish1 = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Bánh Flan',
            'price' => 15000,
            'is_available' => true,
        ]);

        $dish2 = Dish::create([
            'category_id' => $cat->id,
            'dish_name' => 'Kem Trái Cây',
            'price' => 20000,
            'is_available' => true,
        ]);

        $response = $this->withSession(['selected_dish_id' => $dish1->id])
            ->get(route('muahang'));

        $response->assertStatus(200);
        $response->assertSee('Có thể bạn cũng thích');
        $response->assertSee('Kem Trái Cây');
    }
}


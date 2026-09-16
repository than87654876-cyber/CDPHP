<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'category_name' => 'Đồ ăn', 'description' => 'Món ngon chiên xào, gà rán, bò lúc lắc đậm đà hương vị'],
            ['id' => 2, 'category_name' => 'Đồ uống', 'description' => 'Trà sữa, trà trái cây giải nhiệt, cà phê và nước ép tươi nguyên chất'],
            ['id' => 3, 'category_name' => 'Đồ chay', 'description' => 'Món chay thanh đạm, giàu dinh dưỡng từ rau củ và nấm tươi'],
            ['id' => 4, 'category_name' => 'Bánh kem', 'description' => 'Bánh kem sinh nhật, bánh mousse, tiramisu ngọt ngào cao cấp'],
            ['id' => 5, 'category_name' => 'Tráng miệng', 'description' => 'Chè ngọt thanh, kem mát lạnh, bánh flan và mochi dẻo mềm'],
            ['id' => 6, 'category_name' => 'Pizza/Burger', 'description' => 'Pizza nướng phô mai kéo sợi và burger thịt nướng thơm lừng'],
            ['id' => 7, 'category_name' => 'Món lẩu', 'description' => 'Lẩu thái chua cay, lẩu bò, lẩu hải sản nóng hổi thơm ngon'],
            ['id' => 8, 'category_name' => 'Sushi', 'description' => 'Sushi cá hồi tươi, maki và cơm cuộn chuẩn phong vị Nhật Bản'],
            ['id' => 9, 'category_name' => 'Mì phở', 'description' => 'Phở truyền thống nước dùng đậm đà, mì xào và hủ tiếu thơm lừng'],
            ['id' => 10, 'category_name' => 'Cơm hộp', 'description' => 'Cơm sườn nướng, cơm gà xối mỡ, cơm tấm hộp văn phòng đầy đặn'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['id' => $category['id']], $category);
        }
    }
}

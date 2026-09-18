<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dish;
use Illuminate\Http\Request;

class AdminDishController extends Controller
{
    // List all dishes
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $categories = Category::all();

        if (!$categoryId && $categories->isNotEmpty()) {
            $categoryId = $categories->first()->id;
        }

        $query = Dish::with('category');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dish_name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $dishes = $query->get();

        return view('admin.single_dishes', compact('dishes', 'categories', 'search', 'categoryId'));
    }

    // View dish details
    public function show($id)
    {
        $dish = Dish::with('category')->findOrFail($id);

        return view('admin.single_dishes_detail', compact('dish'));
    }

    // Show create form
    public function create(Request $request)
    {
        $categories = Category::all();
        $selectedCategoryId = $request->query('category_id') ?? 1;

        return view('admin.single_dishes_add', compact('categories', 'selectedCategoryId'));
    }

    // Save new dish
    public function store(Request $request)
    {
        $request->validate([
            'dish_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $data = $request->only('dish_name', 'category_id', 'price', 'description');
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            $cloudinary = app(\App\Services\CloudinaryService::class);
            $imageUrl = $cloudinary->upload($request->file('image'));
            if ($imageUrl) {
                $data['image_url'] = $imageUrl;
            }
        }

        $dish = Dish::create($data);

        return redirect()->route('quanly_monandon')->with('success', 'Đã thêm món ăn mới thành công!');
    }

    // Show edit form
    public function edit($id)
    {
        $dish = Dish::findOrFail($id);
        $categories = Category::all();

        return view('admin.single_dishes_edit', compact('dish', 'categories'));
    }

    // Update dish
    public function update(Request $request, $id)
    {
        $request->validate([
            'dish_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
        ]);

        $dish = Dish::findOrFail($id);
        $data = $request->only('dish_name', 'category_id', 'price', 'description');
        $data['is_available'] = $request->has('is_available');

        if ($request->hasFile('image')) {
            // Delete old local image if exists
            if ($dish->image_url && !str_starts_with($dish->image_url, 'http') && file_exists(public_path($dish->image_url))) {
                @unlink(public_path($dish->image_url));
            }

            $cloudinary = app(\App\Services\CloudinaryService::class);
            $imageUrl = $cloudinary->upload($request->file('image'));
            if ($imageUrl) {
                $data['image_url'] = $imageUrl;
            }
        }

        $dish->update($data);

        return redirect()->route('quanly_monandon')->with('success', 'Đã cập nhật món ăn thành công!');
    }

    // Delete dish
    public function destroy($id)
    {
        $dish = Dish::findOrFail($id);

        // Delete image file if exists
        if ($dish->image_url && file_exists(public_path($dish->image_url))) {
            @unlink(public_path($dish->image_url));
        }

        $dish->delete();

        return redirect()->route('quanly_monandon')->with('success', 'Đã xóa món ăn thành công!');
    }

    // Xuất báo cáo món ăn bán chạy (CSV UTF-8 BOM)
    public function exportDishesCsv()
    {
        $headers = ['Mã món', 'Danh mục', 'Tên món ăn', 'Đơn giá (VNĐ)', 'Trạng thái', 'Số lượng bán lẻ đã bán', 'Ngày tạo'];
        $dishes = Dish::with('category')
            ->withSum(['orderItems as total_sold' => function($q) {
                $q->whereHas('order', function($sub) {
                    $sub->where('payment_status', 'paid');
                });
            }], 'quantity')
            ->get()
            ->sortByDesc('total_sold');

        return $this->exportCsvStream('mon-an-ban-chay.csv', $headers, $dishes, function ($dish) {
            $statusText = $dish->is_available ? 'Còn hàng' : 'Hết hàng';
            return [
                'MON-' . sprintf('%03d', $dish->id),
                $dish->category ? $dish->category->category_name : 'Không phân loại',
                $dish->dish_name,
                $dish->price,
                $statusText,
                $dish->total_sold ?? 0,
                $dish->created_at ? $dish->created_at->format('d/m/Y H:i') : 'N/A',
            ];
        });
    }
}

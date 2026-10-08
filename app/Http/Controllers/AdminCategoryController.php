<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::withCount('dishes')->get();

        return view('admin.category', compact('categories'));
    }

    // View category details
    public function show($id)
    {
        $category = Category::with('dishes')->findOrFail($id);

        return view('admin.category_detail', compact('category'));
    }

    // Show create form
    public function create()
    {
        return view('admin.category_add');
    }

    // Save new category
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($request->only('category_name', 'description'));
        \Illuminate\Support\Facades\Cache::forever('dishes_last_mutation_timestamp', microtime(true));
        \Illuminate\Support\Facades\Cache::forget('gemini_dishes_menu');

        return redirect()->route('quanly_danhmuc')->with('success', 'Đã thêm danh mục mới thành công!');
    }

    // Show edit form
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return view('admin.category_edit', compact('category'));
    }

    // Update category
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->only('category_name', 'description'));
        \Illuminate\Support\Facades\Cache::forever('dishes_last_mutation_timestamp', microtime(true));
        \Illuminate\Support\Facades\Cache::forget('gemini_dishes_menu');

        return redirect()->route('quanly_danhmuc')->with('success', 'Đã cập nhật danh mục thành công!');
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Prevent deletion if there are dishes in this category
        if ($category->dishes()->count() > 0) {
            return redirect()->route('quanly_danhmuc')->with('error', 'Không thể xóa danh mục này vì vẫn còn chứa món ăn.');
        }

        $category->delete();
        \Illuminate\Support\Facades\Cache::forever('dishes_last_mutation_timestamp', microtime(true));
        \Illuminate\Support\Facades\Cache::forget('gemini_dishes_menu');

        return redirect()->route('quanly_danhmuc')->with('success', 'Đã xóa danh mục thành công!');
    }
}

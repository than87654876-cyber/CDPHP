<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\ServicePackage;
use Illuminate\Http\Request;

class AdminPackageController extends Controller
{
    // Danh sách tất cả các gói dịch vụ
    public function index()
    {
        $packages = ServicePackage::with('dishes')->get();

        return view('admin.packages', compact('packages'));
    }

    // Xem chi tiết gói dịch vụ và các món ăn đi kèm
    public function show($id)
    {
        $package = ServicePackage::with('dishes')->findOrFail($id);

        return view('admin.packages_detail', compact('package'));
    }

    // Giao diện thêm gói dịch vụ mới
    public function create()
    {
        $dishes = Dish::where('is_available', true)->get();

        return view('admin.packages_add', compact('dishes'));
    }

    // Lưu gói dịch vụ mới vào DB
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'description' => 'nullable|string',
            'dishes' => 'nullable|array',
            'dishes.*' => 'exists:dishes,id',
        ]);

        $package = new ServicePackage();
        $package->package_name = $request->package_name;
        $package->duration_days = $request->duration_days;
        $package->price = $request->price;
        $package->status = $request->status === 'active';
        $package->description = $request->description;
        $package->save();

        if ($request->has('dishes')) {
            $package->dishes()->sync($request->dishes);
        }

        return redirect()->route('quanly_goidichvu')->with('success', 'Đã thêm gói dịch vụ mới thành công!');
    }

    // Giao diện chỉnh sửa thông tin gói
    public function edit($id)
    {
        $package = ServicePackage::with('dishes')->findOrFail($id);
        $dishes = Dish::where('is_available', true)->get();

        return view('admin.packages_edit', compact('package', 'dishes'));
    }

    // Cập nhật thông tin gói dịch vụ
    public function update(Request $request, $id)
    {
        $request->validate([
            'package_name' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'description' => 'nullable|string',
            'dishes' => 'nullable|array',
            'dishes.*' => 'exists:dishes,id',
        ]);

        $package = ServicePackage::findOrFail($id);
        $package->package_name = $request->package_name;
        $package->duration_days = $request->duration_days;
        $package->price = $request->price;
        $package->status = $request->status === 'active';
        $package->description = $request->description;
        $package->save();

        $dishesToSync = $request->input('dishes', []);
        $package->dishes()->sync($dishesToSync);

        return redirect()->route('quanly_goidichvu')->with('success', 'Đã cập nhật gói dịch vụ thành công!');
    }

    // Xóa gói dịch vụ
    public function destroy($id)
    {
        $package = ServicePackage::findOrFail($id);

        // Ngăn chặn xóa nếu có khách hàng đang đăng ký sử dụng gói này ở trạng thái active hoặc paused
        if ($package->subscriptions()->whereIn('status', ['active', 'paused'])->count() > 0) {
            return redirect()->route('quanly_goidichvu')->with('error', 'Không thể xóa gói dịch vụ này vì đang có khách hàng sử dụng dịch vụ.');
        }

        // Xóa các liên kết món ăn và xóa gói
        $package->dishes()->detach();
        $package->delete();

        return redirect()->route('quanly_goidichvu')->with('success', 'Đã xóa gói dịch vụ thành công!');
    }
}

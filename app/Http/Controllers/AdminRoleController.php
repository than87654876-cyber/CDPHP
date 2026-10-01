<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminRoleController extends Controller
{
    /**
     * Danh sách vai trò và phân quyền trong hệ thống
     */
    public function index()
    {
        $roles = Role::where('name', '!=', 'customer')
            ->withCount('users')
            ->with('permissions')
            ->orderBy('id', 'asc')
            ->get();

        $totalPermissions = Permission::count();
        $modules = self::getPermissionModules();

        return view('admin.roles', compact('roles', 'totalPermissions', 'modules'));
    }

    /**
     * Form thêm vai trò mới
     */
    public function create()
    {
        $modules = self::getPermissionModules();
        return view('admin.roles_form', [
            'role' => new Role(),
            'modules' => $modules,
            'isEdit' => false,
            'assignedPermissions' => [],
        ]);
    }

    /**
     * Lưu vai trò mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-zA-Z0-9_\-]+$/',
            'display_name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'Vui lòng nhập mã định danh vai trò.',
            'name.unique' => 'Mã vai trò này đã tồn tại trong hệ thống.',
            'name.regex' => 'Mã vai trò chỉ được chứa chữ cái không dấu, số, dấu gạch nối (-) hoặc gạch dưới (_).',
        ]);

        $role = Role::create([
            'name' => strtolower($request->name),
            'guard_name' => 'web',
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('quanly_vaitro')->with('success', "Đã tạo thành công vai trò '{$role->name}' và thiết lập quyền hạn!");
    }

    /**
     * Form chỉnh sửa vai trò
     */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        if ($role->name === 'customer') {
            return redirect()->route('quanly_vaitro')->with('error', 'Không thể chỉnh sửa vai trò Khách hàng mặc định.');
        }

        $modules = self::getPermissionModules();
        $assignedPermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles_form', [
            'role' => $role,
            'modules' => $modules,
            'isEdit' => true,
            'assignedPermissions' => $assignedPermissions,
        ]);
    }

    /**
     * Cập nhật vai trò và các quyền hạn
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'customer') {
            return redirect()->route('quanly_vaitro')->with('error', 'Không thể chỉnh sửa vai trò Khách hàng mặc định.');
        }

        $rules = [
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];

        // Không cho phép đổi mã vai trò của superadmin
        if ($role->name !== 'superadmin') {
            $rules['name'] = 'required|string|max:50|unique:roles,name,' . $role->id . '|regex:/^[a-zA-Z0-9_\-]+$/';
        }

        $request->validate($rules, [
            'name.required' => 'Vui lòng nhập mã định danh vai trò.',
            'name.unique' => 'Mã vai trò này đã tồn tại trong hệ thống.',
            'name.regex' => 'Mã vai trò chỉ được chứa chữ cái không dấu, số, dấu gạch nối (-) hoặc gạch dưới (_).',
        ]);

        if ($role->name !== 'superadmin' && $request->has('name')) {
            $role->name = strtolower($request->name);
            $role->save();
        }

        // Superadmin luôn có full quyền
        if ($role->name === 'superadmin') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions($request->permissions ?? []);
        }

        // Reset cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('quanly_vaitro')->with('success', "Đã cập nhật vai trò '{$role->name}' và đồng bộ các quyền hạn thành công!");
    }

    /**
     * Xóa vai trò
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $protectedRoles = ['superadmin', 'admin', 'staff', 'kitchen', 'shipper', 'customer'];
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->route('quanly_vaitro')->with('error', "Không thể xóa vai trò hệ thống mặc định '{$role->name}'.");
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('quanly_vaitro')->with('error', "Không thể xóa vai trò này vì đang có {$role->users()->count()} nhân viên được gán vai trò này.");
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('quanly_vaitro')->with('success', "Đã xóa vai trò '{$roleName}' thành công!");
    }

    /**
     * Định nghĩa các nhóm quyền Module trong hệ thống
     */
    public static function getPermissionModules(): array
    {
        return [
            'Bảng điều khiển & Thống kê' => [
                'icon' => 'fas fa-chart-pie',
                'color' => 'indigo',
                'items' => [
                    'dashboard.view' => 'Xem Dashboard doanh thu, biểu đồ & thống kê',
                ]
            ],
            'Quản lý Đơn hàng' => [
                'icon' => 'fas fa-file-invoice-dollar',
                'color' => 'blue',
                'items' => [
                    'orders.view' => 'Xem danh sách và chi tiết đơn hàng',
                    'orders.process' => 'Duyệt đơn & Cập nhật trạng thái đơn hàng',
                    'orders.refund' => 'Xử lý yêu cầu hoàn tiền của khách',
                    'orders.delete' => 'Xóa đơn hàng khỏi hệ thống',
                ]
            ],
            'Nghiệp vụ Nhà bếp' => [
                'icon' => 'fas fa-fire-burner',
                'color' => 'amber',
                'items' => [
                    'kitchen.view' => 'Xem màn hình Bếp & số lượng món cần làm trong ngày',
                    'kitchen.update_status' => 'Cập nhật trạng thái hoàn thành món nấu',
                ]
            ],
            'Nghiệp vụ Giao hàng (Shipper)' => [
                'icon' => 'fas fa-truck-fast',
                'color' => 'emerald',
                'items' => [
                    'delivery.view' => 'Xem danh sách các đơn cần đi giao',
                    'delivery.update_status' => 'Cập nhật trạng thái giao hàng thành công / thất bại',
                ]
            ],
            'Thực đơn & Món ăn' => [
                'icon' => 'fas fa-bowl-food',
                'color' => 'orange',
                'items' => [
                    'dishes.view' => 'Xem danh sách món ăn',
                    'dishes.create' => 'Thêm món ăn mới vào thực đơn',
                    'dishes.edit' => 'Chỉnh sửa thông tin, giá bán và hình ảnh món',
                    'dishes.delete' => 'Xóa món ăn',
                ]
            ],
            'Danh mục món ăn' => [
                'icon' => 'fas fa-list',
                'color' => 'lime',
                'items' => [
                    'categories.manage' => 'Toàn quyền thêm, sửa, xóa danh mục món',
                ]
            ],
            'Gói Combo & Suất ăn định kỳ' => [
                'icon' => 'fas fa-box-open',
                'color' => 'rose',
                'items' => [
                    'packages.manage' => 'Quản lý các gói ăn combo theo tuần/tháng & lịch trình',
                ]
            ],
            'Khuyến mãi & Tiếp thị' => [
                'icon' => 'fas fa-ticket',
                'color' => 'pink',
                'items' => [
                    'promotions.manage' => 'Tạo & quản lý mã ưu đãi, voucher giảm giá',
                    'coupons.send' => 'Gửi mã coupon trực tiếp cho khách hàng',
                ]
            ],
            'Đánh giá & Phản hồi' => [
                'icon' => 'fas fa-star',
                'color' => 'yellow',
                'items' => [
                    'reviews.manage' => 'Quản lý và kiểm duyệt / xóa đánh giá món ăn',
                ]
            ],
            'Khách hàng' => [
                'icon' => 'fas fa-users',
                'color' => 'cyan',
                'items' => [
                    'customers.view' => 'Xem danh sách khách hàng, lịch sử mua hàng & điểm',
                    'customers.backup' => 'Xuất file Excel & Sao lưu/phục hồi dữ liệu khách',
                ]
            ],
            'Nhân sự & Phân quyền' => [
                'icon' => 'fas fa-user-shield',
                'color' => 'purple',
                'items' => [
                    'employees.manage' => 'Quản lý nhân viên (thêm, sửa, xóa tài khoản)',
                    'roles.manage' => 'Toàn quyền quản lý Vai trò & Phân quyền RBAC',
                ]
            ],
            'Cấu hình hệ thống' => [
                'icon' => 'fas fa-sliders',
                'color' => 'slate',
                'items' => [
                    'settings.manage' => 'Cài đặt trang chủ, banner và thông tin hệ thống',
                ]
            ],
        ];
    }
}

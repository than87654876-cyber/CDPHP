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

        $protectedRoles = ['superadmin', 'admin', 'manager', 'accountant', 'warehouse', 'staff', 'kitchen', 'shipper', 'customer'];
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
     * Định nghĩa các nhóm quyền Module trong hệ thống (RBAC Ma Trận Chi Tiết)
     */
    public static function getPermissionModules(): array
    {
        return [
            'Kho & Nhập kho (Inventory & Purchasing)' => [
                'icon' => 'fas fa-boxes-stacked',
                'color' => 'amber',
                'items' => [
                    'po.create' => 'Tạo đơn đặt hàng NCC (PO) - Quản lý / Thủ kho lập',
                    'po.approve_price' => 'Duyệt giá đơn đặt hàng NCC - Admin / Kế toán duyệt giá',
                    'grn.create' => 'Tạo phiếu nhập kho (GRN) - Ghi nhận SL thực nhận, số lô, HSD',
                    'inventory.view_cost' => 'Xem giá vốn / Giá nhập (Bảo mật: Admin, Kế toán, Quản lý)',
                    'inventory.transfer' => 'Điều chuyển kho - Tạo yêu cầu chuyển kho, xác nhận xuất/nhập',
                    'inventory.stock_count' => 'Kiểm kê kho - Nhập số lượng đếm thực tế tồn kho',
                    'inventory.balance' => 'Cân bằng kho (Điều chỉnh tồn) - Chỉ Quản lý / Admin duyệt',
                    'inventory.discard_return' => 'Hủy hàng / Xuất trả NCC - Quy trình phê duyệt 2 bước',
                ]
            ],
            'Bán hàng & Thu ngân (POS & Sales)' => [
                'icon' => 'fas fa-cash-register',
                'color' => 'emerald',
                'items' => [
                    'pos.create_order' => 'Tạo và thanh toán đơn hàng (Thu ngân, Nhân viên bán hàng)',
                    'pos.discount_limit' => 'Sửa giá / Chiết khấu tự do trong hạn mức (Tối đa 5%)',
                    'pos.discount_override' => 'Chiết khấu vượt hạn mức (> 5%) - Cần mã PIN Quản lý',
                    'pos.cancel_order' => 'Hủy hóa đơn / Trả hàng hoàn tiền - Cần Quản lý duyệt qua PIN',
                    'pos.view_stock' => 'Xem tồn kho khi bán hàng (Ẩn giá vốn và chi tiết NCC)',
                ]
            ],
            'Nhà cung cấp & Khách hàng' => [
                'icon' => 'fas fa-handshake',
                'color' => 'blue',
                'items' => [
                    'suppliers.view_contact' => 'Xem thông tin liên hệ Nhà cung cấp (Thủ kho)',
                    'suppliers.manage_debts' => 'Quản lý công nợ & điều khoản thanh toán NCC (Kế toán)',
                    'suppliers.manage' => 'Duyệt tạo / chỉnh sửa / xóa Nhà cung cấp (Admin)',
                    'customers.view_basic' => 'Xem dữ liệu khách hàng (Tên, SĐT, Điểm tích lũy cơ bản)',
                    'customers.export' => 'Xuất file Excel & Sao lưu toàn bộ dữ liệu khách hàng',
                ]
            ],
            'Phân quyền Phạm vi Dữ liệu (Data Scope)' => [
                'icon' => 'fas fa-network-wired',
                'color' => 'indigo',
                'items' => [
                    'scope.cross_branch' => 'Phạm vi Chi nhánh: Được phép thao tác chéo giữa các Kho/Chi nhánh',
                    'scope.all_shifts' => 'Phạm vi Ca làm việc: Xem tổng kết doanh thu & tiền mặt toàn bộ ca',
                    'scope.all_customers' => 'Phạm vi Nhân viên: Xem toàn bộ tệp khách và đơn hàng hệ thống',
                ]
            ],
            'An toàn & Chống gian lận (Anti-Fraud)' => [
                'icon' => 'fas fa-shield-halved',
                'color' => 'rose',
                'items' => [
                    'audit.view' => 'Nhật ký hoạt động (Audit Trail): Xem chi tiết ai sửa tồn, đổi giá, hủy đơn',
                    'approval.dual_step' => 'Quyền phê duyệt 2 bước (Dual Approval): Duyệt phiếu giá trị lớn/hủy hàng',
                    'pos.manager_pin_authorize' => 'Ủy quyền tạm thời qua mã PIN: Mở khóa quyền trên máy POS',
                ]
            ],
            'Bảng điều khiển & Đơn hàng' => [
                'icon' => 'fas fa-chart-pie',
                'color' => 'purple',
                'items' => [
                    'dashboard.view' => 'Xem Dashboard doanh thu, biểu đồ & thống kê',
                    'orders.view' => 'Xem danh sách và chi tiết đơn hàng',
                    'orders.process' => 'Duyệt đơn & Cập nhật trạng thái đơn hàng',
                    'orders.refund' => 'Xử lý hoàn tiền cho đơn lỗi thanh toán',
                    'orders.delete' => 'Xóa đơn hàng khỏi hệ thống',
                ]
            ],
            'Thực đơn, Bếp & Giao nhận' => [
                'icon' => 'fas fa-utensils',
                'color' => 'orange',
                'items' => [
                    'dishes.view' => 'Xem danh sách món ăn',
                    'dishes.create' => 'Thêm món ăn mới vào thực đơn',
                    'dishes.edit' => 'Chỉnh sửa thông tin, giá bán và hình ảnh món',
                    'dishes.delete' => 'Xóa món ăn',
                    'categories.manage' => 'Toàn quyền thêm, sửa, xóa danh mục món',
                    'packages.manage' => 'Quản lý các gói ăn combo theo tuần/tháng & lịch trình',
                    'kitchen.view' => 'Xem màn hình Bếp & số lượng món cần làm trong ngày',
                    'kitchen.update_status' => 'Cập nhật trạng thái hoàn thành món nấu',
                    'delivery.view' => 'Xem danh sách các đơn cần đi giao',
                    'delivery.update_status' => 'Cập nhật trạng thái giao hàng thành công / thất bại',
                ]
            ],
            'Khuyến mãi & Đánh giá' => [
                'icon' => 'fas fa-ticket',
                'color' => 'pink',
                'items' => [
                    'promotions.manage' => 'Tạo & quản lý mã ưu đãi, voucher giảm giá',
                    'coupons.send' => 'Gửi mã coupon trực tiếp cho khách hàng',
                    'reviews.manage' => 'Quản lý và kiểm duyệt / xóa đánh giá món ăn',
                ]
            ],
            'Nhân sự, Phân quyền & Cài đặt' => [
                'icon' => 'fas fa-user-gear',
                'color' => 'slate',
                'items' => [
                    'employees.manage' => 'Quản lý nhân viên (thêm, sửa, xóa tài khoản)',
                    'roles.manage' => 'Toàn quyền quản lý Vai trò & Phân quyền RBAC',
                    'settings.manage' => 'Cài đặt trang chủ, banner và thông tin hệ thống',
                ]
            ],
        ];
    }
}

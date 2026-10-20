<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Khởi tạo Chi nhánh / Kho hàng mẫu (Branches & Warehouses Data Scope)
        $branchMain = Branch::updateOrCreate(
            ['code' => 'CN-Q1'],
            [
                'name' => 'Chi nhánh Quận 1 (Kho Trung Tâm)',
                'phone' => '02838123456',
                'address' => '68 Lê Lợi, Phường Bến Nghé, Quận 1, TP.HCM',
                'is_main' => true,
                'is_active' => true,
            ]
        );

        $branchSub = Branch::updateOrCreate(
            ['code' => 'CN-Q3'],
            [
                'name' => 'Chi nhánh Quận 3 (Kho Vệ Tinh)',
                'phone' => '02838987654',
                'address' => '250 Nguyễn Thị Minh Khai, Quận 3, TP.HCM',
                'is_main' => false,
                'is_active' => true,
            ]
        );

        // Xóa cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Danh sách Permissions chuẩn hóa theo Ma trận RBAC chi tiết (CRUD + Nghiệp vụ đặc thù)
        $permissions = [
            // Nghiệp vụ Kho & Nhập kho (Inventory & Purchasing)
            'po.create',                  // Tạo đơn đặt hàng NCC (PO)
            'po.approve_price',            // Duyệt giá đơn đặt hàng (Admin / Kế toán)
            'grn.create',                 // Tạo phiếu nhập kho (GRN) số lượng, số lô, HSD
            'inventory.view_cost',        // Xem giá vốn / Giá nhập (Giới hạn Admin, Kế toán, Quản lý)
            'inventory.transfer',         // Điều chuyển kho (tạo yêu cầu, xuất, nhập)
            'inventory.stock_count',      // Kiểm kê kho (nhập số lượng đếm thực tế)
            'inventory.balance',          // Cân bằng kho (điều chỉnh tồn - Chỉ Quản lý / Admin)
            'inventory.discard_return',   // Hủy hàng / Xuất trả NCC (Phê duyệt 2 bước)

            // Nghiệp vụ Bán hàng & Thu ngân (POS & Sales)
            'pos.create_order',           // Tạo và thanh toán đơn hàng POS
            'pos.discount_limit',         // Chiết khấu tự do trong hạn mức <= 5%
            'pos.discount_override',      // Chiết khấu > 5% (cần mã PIN Quản lý)
            'pos.cancel_order',           // Hủy hóa đơn / Trả hàng hoàn tiền (cần PIN Quản lý)
            'pos.view_stock',             // Xem tồn kho khi bán hàng (ẩn giá vốn & NCC)

            // Nghiệp vụ Nhà cung cấp & Khách hàng
            'suppliers.view_contact',     // Xem thông tin liên hệ NCC
            'suppliers.manage_debts',     // Quản lý công nợ & điều khoản thanh toán
            'suppliers.manage',           // Duyệt tạo, sửa, xóa NCC
            'customers.view_basic',       // Xem thông tin khách hàng (Tên, SĐT, Điểm)
            'customers.export',           // Xuất file Excel dữ liệu khách hàng

            // Phân quyền Phạm vi Dữ liệu (Data Scope)
            'scope.cross_branch',         // Quyền thao tác chéo giữa các Chi nhánh / Kho
            'scope.all_shifts',           // Quyền xem tổng kết doanh thu toàn bộ ca làm việc
            'scope.all_customers',        // Quyền xem tệp khách hàng & đơn hàng toàn hệ thống

            // Cơ chế An toàn & Chống gian lận (Security & Anti-Fraud)
            'audit.view',                 // Xem Nhật ký hoạt động (Audit Trail / Activity Log)
            'approval.dual_step',         // Quyền duyệt bước trong quy trình Phê duyệt 2 bước
            'pos.manager_pin_authorize',  // Quyền cấp / sử dụng mã PIN ủy quyền tại quầy

            // Bảng điều khiển & Thống kê
            'dashboard.view',

            // Đơn hàng (Orders)
            'orders.view',
            'orders.process',
            'orders.refund',
            'orders.delete',

            // Nhà bếp (Kitchen)
            'kitchen.view',
            'kitchen.update_status',

            // Giao hàng (Delivery)
            'delivery.view',
            'delivery.update_status',

            // Món ăn & Thực đơn
            'dishes.view',
            'dishes.create',
            'dishes.edit',
            'dishes.delete',

            // Danh mục & Combo
            'categories.manage',
            'packages.manage',

            // Khuyến mãi & Đánh giá
            'promotions.manage',
            'coupons.send',
            'reviews.manage',

            // Khách hàng & Nhân sự
            'customers.view',
            'customers.backup',
            'employees.manage',
            'roles.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 2. Thiết lập các Vai trò chuẩn hóa (Roles Matrix)

        // Vai trò 1: SUPERADMIN - Toàn quyền tối cao
        $superAdminRole = Role::findOrCreate('superadmin', 'web');
        $superAdminRole->syncPermissions(Permission::all());

        // Vai trò 2: ADMIN (Ban giám đốc / Giám đốc chi nhánh)
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions([
            'dashboard.view',
            'po.create', 'po.approve_price', 'grn.create', 'inventory.view_cost',
            'inventory.transfer', 'inventory.stock_count', 'inventory.balance', 'inventory.discard_return',
            'pos.create_order', 'pos.discount_limit', 'pos.discount_override', 'pos.cancel_order', 'pos.view_stock',
            'suppliers.view_contact', 'suppliers.manage_debts', 'suppliers.manage',
            'customers.view_basic', 'customers.export', 'customers.view', 'customers.backup',
            'scope.cross_branch', 'scope.all_shifts', 'scope.all_customers',
            'audit.view', 'approval.dual_step', 'pos.manager_pin_authorize',
            'orders.view', 'orders.process', 'orders.refund', 'orders.delete',
            'dishes.view', 'dishes.create', 'dishes.edit', 'dishes.delete',
            'categories.manage', 'packages.manage',
            'kitchen.view', 'delivery.view',
            'promotions.manage', 'coupons.send', 'reviews.manage',
            'employees.manage', 'settings.manage',
        ]);

        // Vai trò 3: MANAGER (Quản lý cửa hàng / Trưởng ca)
        $managerRole = Role::findOrCreate('manager', 'web');
        $managerRole->syncPermissions([
            'dashboard.view',
            'po.create', // Lập đơn đặt hàng NCC
            'grn.create',
            'inventory.view_cost', // Xem giá vốn
            'inventory.transfer',
            'inventory.stock_count',
            'inventory.balance', // Quyền bấm nút Cân bằng kho
            'inventory.discard_return',
            'pos.create_order',
            'pos.discount_limit',
            'pos.discount_override', // Ủy quyền chiết khấu > 5%
            'pos.cancel_order',      // Duyệt hủy hóa đơn
            'pos.view_stock',
            'suppliers.view_contact',
            'customers.view_basic',
            'scope.all_shifts',
            'audit.view',
            'approval.dual_step',
            'pos.manager_pin_authorize',
            'orders.view', 'orders.process', 'orders.refund',
            'kitchen.view', 'delivery.view',
            'dishes.view',
        ]);

        // Vai trò 4: ACCOUNTANT (Kế toán)
        $accountantRole = Role::findOrCreate('accountant', 'web');
        $accountantRole->syncPermissions([
            'dashboard.view',
            'po.approve_price',      // Duyệt giá đơn đặt hàng NCC
            'inventory.view_cost',   // Xem giá vốn / Giá nhập
            'suppliers.view_contact',
            'suppliers.manage_debts', // Quản lý công nợ & điều khoản thanh toán
            'customers.view_basic',
            'customers.export',
            'scope.all_shifts',
            'audit.view',
            'approval.dual_step',
            'orders.view',
        ]);

        // Vai trò 5: WAREHOUSE (Thủ kho / Nhân viên kho)
        $warehouseRole = Role::findOrCreate('warehouse', 'web');
        $warehouseRole->syncPermissions([
            'po.create',             // Tạo đơn đặt hàng NCC (PO)
            'grn.create',            // Tạo phiếu nhập kho (GRN) số lô, HSD, SL thực nhận
            'inventory.transfer',    // Điều chuyển kho (xác nhận xuất/nhập)
            'inventory.stock_count', // Nhập số lượng đếm thực tế khi kiểm kê
            // KHÔNG CÓ inventory.view_cost (Ẩn giá vốn)
            // KHÔNG CÓ inventory.balance (Chỉ Quản lý mới được Cân bằng kho)
            'suppliers.view_contact', // Xem thông tin liên hệ nhà cung cấp
            'pos.view_stock',
            'dishes.view',
        ]);

        // Vai trò 6: STAFF (Nhân viên bán hàng / Thu ngân)
        $staffRole = Role::findOrCreate('staff', 'web');
        $staffRole->syncPermissions([
            'pos.create_order',      // Tạo và thanh toán đơn hàng
            'pos.discount_limit',    // Chiết khấu tối đa 5%
            'pos.view_stock',        // Xem tồn kho để tư vấn (Ẩn giá vốn & NCC)
            'customers.view_basic',  // Xem tên, SĐT, điểm tích lũy (Cấm xuất Excel)
            'orders.view',
            'orders.process',
            'dishes.view',
            'kitchen.view',
            'delivery.view',
            'reviews.manage',
        ]);

        // Vai trò 7: KITCHEN (Bếp trưởng / Phụ bếp)
        $kitchenRole = Role::findOrCreate('kitchen', 'web');
        $kitchenRole->syncPermissions([
            'kitchen.view',
            'kitchen.update_status',
            'pos.view_stock',
            'dishes.view',
        ]);

        // Vai trò 8: SHIPPER (Nhân viên giao nhận)
        $shipperRole = Role::findOrCreate('shipper', 'web');
        $shipperRole->syncPermissions([
            'delivery.view',
            'delivery.update_status',
            'orders.view',
        ]);

        // Vai trò 9: CUSTOMER (Khách hàng)
        $customerRole = Role::findOrCreate('customer', 'web');

        // 3. Cập nhật mã PIN quản lý mặc định và gán Chi nhánh cho nhân sự
        $users = User::all();
        foreach ($users as $user) {
            // Gán chi nhánh mặc định nếu chưa có
            if (!$user->branch_id) {
                $user->branch_id = $branchMain->id;
            }

            // Cài đặt mã PIN Quản lý cho Admin & Superadmin (Mặc định: 123456)
            if (in_array($user->role, ['superadmin', 'admin', 'manager'])) {
                if (empty($user->manager_pin)) {
                    $user->manager_pin = Hash::make('123456');
                }
            }

            $user->save();

            // Đồng bộ vai trò Spatie Role
            if ($user->role && Role::where('name', $user->role)->exists()) {
                $user->syncRoles([$user->role]);
            } else {
                $user->syncRoles(['customer']);
            }
        }
    }
}

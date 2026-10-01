<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
        // Xóa cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Danh sách Permissions theo từng Module
        $permissions = [
            // Dashboard
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

            // Món ăn (Dishes)
            'dishes.view',
            'dishes.create',
            'dishes.edit',
            'dishes.delete',

            // Danh mục (Categories)
            'categories.manage',

            // Gói combo & Subscriptions
            'packages.manage',

            // Khuyến mãi & Marketing
            'promotions.manage',
            'coupons.send',

            // Đánh giá
            'reviews.manage',

            // Khách hàng
            'customers.view',
            'customers.backup',

            // Nhân sự & Phân quyền
            'employees.manage',
            'roles.manage',

            // Cài đặt hệ thống
            'settings.manage',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 2. Tạo các Roles mặc định và gán quyền
        
        // Super Admin: Toàn quyền
        $superAdminRole = Role::findOrCreate('superadmin', 'web');
        $superAdminRole->syncPermissions(Permission::all());

        // Admin (Quản lý): Quản lý hầu hết các mảng, trừ phân quyền cấp cao và backup
        $adminRole = Role::findOrCreate('admin', 'web');
        $adminRole->syncPermissions([
            'dashboard.view',
            'orders.view',
            'orders.process',
            'orders.refund',
            'kitchen.view',
            'delivery.view',
            'dishes.view',
            'dishes.create',
            'dishes.edit',
            'dishes.delete',
            'categories.manage',
            'packages.manage',
            'promotions.manage',
            'coupons.send',
            'reviews.manage',
            'customers.view',
            'settings.manage',
        ]);

        // CSKH / Thu ngân (Staff)
        $staffRole = Role::findOrCreate('staff', 'web');
        $staffRole->syncPermissions([
            'orders.view',
            'orders.process',
            'orders.refund',
            'kitchen.view',
            'delivery.view',
            'customers.view',
            'reviews.manage',
        ]);

        // Bếp (Kitchen)
        $kitchenRole = Role::findOrCreate('kitchen', 'web');
        $kitchenRole->syncPermissions([
            'kitchen.view',
            'kitchen.update_status',
        ]);

        // Shipper (Giao hàng)
        $shipperRole = Role::findOrCreate('shipper', 'web');
        $shipperRole->syncPermissions([
            'delivery.view',
            'delivery.update_status',
            'orders.view',
        ]);

        // Khách hàng
        $customerRole = Role::findOrCreate('customer', 'web');

        // 3. Đồng bộ gán Role cho các tài khoản User hiện có trong hệ thống
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role && Role::where('name', $user->role)->exists()) {
                $user->syncRoles([$user->role]);
            } else {
                $user->syncRoles(['customer']);
            }
        }
    }
}

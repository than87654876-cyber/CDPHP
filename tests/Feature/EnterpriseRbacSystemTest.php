<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DualApproval;
use App\Models\InventoryItem;
use App\Models\User;
use App\Models\WorkShift;
use App\Services\AuditLogService;
use App\Services\DualApprovalService;
use App\Services\ManagerPinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnterpriseRbacSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Chạy Roles & Permissions Seeder
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /**
     * Test danh sách quyền và vai trò được nạp đầy đủ theo Ma trận RBAC.
     */
    public function test_rbac_permissions_matrix_seeded_and_roles_assigned(): void
    {
        $this->assertDatabaseHas('roles', ['name' => 'superadmin']);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'manager']);
        $this->assertDatabaseHas('roles', ['name' => 'accountant']);
        $this->assertDatabaseHas('roles', ['name' => 'warehouse']);
        $this->assertDatabaseHas('roles', ['name' => 'staff']);

        $this->assertDatabaseHas('permissions', ['name' => 'inventory.view_cost']);
        $this->assertDatabaseHas('permissions', ['name' => 'inventory.balance']);
        $this->assertDatabaseHas('permissions', ['name' => 'pos.discount_override']);
        $this->assertDatabaseHas('permissions', ['name' => 'pos.manager_pin_authorize']);
        $this->assertDatabaseHas('permissions', ['name' => 'approval.dual_step']);
    }

    /**
     * Test bảo mật giá vốn: Nhân viên bán hàng & thủ kho không có quyền xem giá vốn.
     */
    public function test_cost_price_security_permissions(): void
    {
        $staffRole = Role::findByName('staff');
        $this->assertFalse($staffRole->hasPermissionTo('inventory.view_cost'));

        $warehouseRole = Role::findByName('warehouse');
        $this->assertFalse($warehouseRole->hasPermissionTo('inventory.view_cost'));

        $managerRole = Role::findByName('manager');
        $this->assertTrue($managerRole->hasPermissionTo('inventory.view_cost'));

        $accountantRole = Role::findByName('accountant');
        $this->assertTrue($accountantRole->hasPermissionTo('inventory.view_cost'));
    }

    /**
     * Test quyền cân bằng kho và tự động ghi nhật ký Audit Trail.
     */
    public function test_stock_adjustment_requires_inventory_balance_permission_and_writes_audit_log(): void
    {
        $item = InventoryItem::create([
            'item_code' => 'NL-TEST',
            'name' => 'Thịt bò tươi',
            'unit' => 'kg',
            'current_stock' => 50,
            'min_stock' => 10,
            'cost_price' => 200000,
        ]);

        $admin = User::create([
            'fullname' => 'Admin Manager',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 1,
        ]);
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $response = $this->post(route('kho_dieuchinh', $item->id), [
            'new_stock' => 75,
            'reason' => 'Kiểm kê thực tế thừa 25kg',
        ]);

        $response->assertSessionHas('success');

        $item->refresh();
        $this->assertEquals(75, $item->current_stock);

        // Kiểm tra Audit Log đã ghi lại hành vi
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'inventory_adjusted',
            'auditable_type' => InventoryItem::class,
            'auditable_id' => $item->id,
        ]);
    }

    /**
     * Test ủy quyền tạm thời qua mã PIN Quản lý (Supervisor PIN Override).
     */
    public function test_manager_pin_verification_succeeds_and_creates_audit_log(): void
    {
        $manager = User::create([
            'fullname' => 'Trưởng Ca Quản Lý',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
            'role' => 'manager',
            'manager_pin' => Hash::make('889900'),
            'status' => 1,
        ]);
        $manager->assignRole('manager');

        $cashier = User::create([
            'fullname' => 'Thu Ngân 01',
            'email' => 'cashier@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 1,
        ]);
        $cashier->assignRole('staff');

        $this->actingAs($cashier);

        // Gọi API kiểm tra mã PIN
        $response = $this->postJson(route('api.pos.verify_pin'), [
            'pin' => '889900',
            'action_type' => 'discount_override',
            'notes' => 'Chiết khấu 15% cho khách VIP theo ủy quyền',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'manager' => [
                'id' => $manager->id,
                'fullname' => 'Trưởng Ca Quản Lý',
            ],
        ]);

        // Kiểm tra Audit Log đã ghi lại
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'manager_pin_override',
        ]);
    }

    /**
     * Test nhập sai mã PIN Quản lý sẽ bị chặn với mã 403.
     */
    public function test_manager_pin_verification_fails_with_invalid_pin(): void
    {
        $cashier = User::create([
            'fullname' => 'Thu Ngân 01',
            'email' => 'cashier2@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 1,
        ]);

        $this->actingAs($cashier);

        $response = $this->postJson(route('api.pos.verify_pin'), [
            'pin' => '000000', // PIN sai
            'action_type' => 'order_cancellation',
        ]);

        $response->assertStatus(403);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test quy trình phê duyệt 2 bước độc lập (Dual Approval).
     */
    public function test_dual_approval_workflow_requires_two_independent_approvers(): void
    {
        $requester = User::create(['fullname' => 'Thủ kho A', 'email' => 'u1@test.com', 'password' => '123', 'role' => 'warehouse', 'status' => 1]);
        $approver1 = User::create(['fullname' => 'Kế toán B', 'email' => 'u2@test.com', 'password' => '123', 'role' => 'accountant', 'status' => 1]);
        $approver2 = User::create(['fullname' => 'Giám đốc C', 'email' => 'u3@test.com', 'password' => '123', 'role' => 'admin', 'status' => 1]);

        $item = InventoryItem::create(['item_code' => 'SP-01', 'name' => 'Hàng hóa A', 'unit' => 'cái', 'current_stock' => 10, 'min_stock' => 1, 'cost_price' => 100000]);

        // 1. Tạo yêu cầu duyệt 2 bước cho lô hàng 50 triệu
        $approval = DualApprovalService::createRequest($item, 'large_goods_receipt', 50000000, 'Nhập lô hàng giá trị lớn', $requester->id);
        $this->assertEquals('pending_step_1', $approval->status);

        // 2. Người tạo không được tự duyệt
        $this->expectException(\InvalidArgumentException::class);
        DualApprovalService::approve($approval, $requester->id);
    }

    /**
     * Test hoàn tất 2 bước duyệt độc lập thành công.
     */
    public function test_dual_approval_completes_with_two_distinct_approvers(): void
    {
        $requester = User::create(['fullname' => 'Thủ kho A', 'email' => 'req@test.com', 'password' => '123', 'role' => 'warehouse', 'status' => 1]);
        $approver1 = User::create(['fullname' => 'Kế toán B', 'email' => 'app1@test.com', 'password' => '123', 'role' => 'accountant', 'status' => 1]);
        $approver2 = User::create(['fullname' => 'Giám đốc C', 'email' => 'app2@test.com', 'password' => '123', 'role' => 'admin', 'status' => 1]);

        $item = InventoryItem::create(['item_code' => 'SP-02', 'name' => 'Hàng hóa B', 'unit' => 'cái', 'current_stock' => 10, 'min_stock' => 1, 'cost_price' => 100000]);

        $approval = DualApprovalService::createRequest($item, 'stock_discard', 25000000, 'Hủy hàng hư hỏng', $requester->id);

        // Bước 1: Kế toán duyệt
        DualApprovalService::approve($approval, $approver1->id, 'Đồng ý bước 1');
        $approval->refresh();
        $this->assertEquals('pending_step_2', $approval->status);
        $this->assertEquals($approver1->id, $approval->first_approver_id);

        // Bước 2: Không được cùng 1 người duyệt bước 2
        try {
            DualApprovalService::approve($approval, $approver1->id, 'Cố tình duyệt bước 2');
            $this->fail('Phải ném ngoại lệ khi cùng 1 người duyệt 2 bước');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('tài khoản độc lập khác', $e->getMessage());
        }

        // Bước 2: Giám đốc duyệt hợp lệ
        DualApprovalService::approve($approval, $approver2->id, 'Phê duyệt hoàn tất');
        $approval->refresh();
        $this->assertEquals('approved', $approval->status);
        $this->assertEquals($approver2->id, $approval->second_approver_id);
    }

    /**
     * Test cấu trúc phân quyền theo phạm vi dữ liệu (Data Scope).
     */
    public function test_data_scope_branches_and_work_shifts_relations(): void
    {
        $branch = Branch::first();
        $user = User::create([
            'fullname' => 'Thu ngân Chi nhánh',
            'email' => 'shift_cashier@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'branch_id' => $branch->id,
            'status' => 1,
        ]);

        $shift = WorkShift::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'shift_name' => 'Ca Sáng (08:00 - 16:00)',
            'opened_at' => now(),
            'opening_cash' => 2000000,
            'status' => 'open',
        ]);

        $this->assertEquals($branch->id, $user->branch->id);
        $this->assertEquals($user->id, $shift->user->id);
        $this->assertEquals(2000000, $shift->opening_cash);
    }
}

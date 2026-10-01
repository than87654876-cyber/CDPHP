<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionRbacTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles & permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'superadmin@fooddaily.com',
        ]);
        $this->superadmin->assignRole('superadmin');
    }

    public function test_superadmin_can_view_roles_index(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('quanly_vaitro'));
        $response->assertStatus(200);
        $response->assertSee('Ma Trận Phân Quyền');
        $response->assertSee('superadmin');
        $response->assertSee('kitchen');
        $response->assertSee('shipper');
    }

    public function test_superadmin_can_view_create_role_page(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('vaitro_them'));
        $response->assertStatus(200);
        $response->assertSee('Tạo Vai Trò & Phân Quyền Mới');
        $response->assertSee('kitchen.view');
        $response->assertSee('orders.refund');
    }

    public function test_superadmin_can_store_custom_role(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('vaitro_them.post'), [
            'name' => 'parttime_chef',
            'permissions' => ['kitchen.view', 'kitchen.update_status'],
        ]);

        $response->assertRedirect(route('quanly_vaitro'));
        $this->assertDatabaseHas('roles', ['name' => 'parttime_chef']);

        $role = Role::findByName('parttime_chef', 'web');
        $this->assertTrue($role->hasPermissionTo('kitchen.view'));
        $this->assertFalse($role->hasPermissionTo('orders.delete'));
    }

    public function test_core_roles_cannot_be_deleted(): void
    {
        $kitchenRole = Role::findByName('kitchen', 'web');

        $response = $this->actingAs($this->superadmin)->post(route('vaitro_xoa', $kitchenRole->id));
        $response->assertRedirect(route('quanly_vaitro'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'kitchen']);
    }

    public function test_superadmin_can_assign_new_role_to_employee(): void
    {
        $response = $this->actingAs($this->superadmin)->post(route('nhanvien_them.post'), [
            'fullname' => 'Đầu Bếp Thử Nghiệm',
            'email' => 'chef_test@fooddaily.com',
            'phone' => '0988776655',
            'password' => 'password123',
            'role' => 'kitchen',
            'status' => 1,
            'notes' => 'Bếp ca chiều',
        ]);

        $response->assertRedirect(route('quanly_nhanvien'));
        
        $newEmployee = User::where('email', 'chef_test@fooddaily.com')->first();
        $this->assertNotNull($newEmployee);
        $this->assertTrue($newEmployee->hasRole('kitchen'));
        $this->assertTrue($newEmployee->can('kitchen.view'));
        $this->assertFalse($newEmployee->can('orders.delete'));

        // Cleanup
        $newEmployee->delete();
    }
}

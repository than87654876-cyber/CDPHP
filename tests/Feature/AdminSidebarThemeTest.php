<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_renders_light_theme_successfully(): void
    {
        $superadmin = User::create([
            'fullname' => 'Super Admin',
            'email' => 'superadmin_theme@test.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'status' => 1,
        ]);

        $response = $this->actingAs($superadmin)->get(route('quanly'));

        $response->assertStatus(200);
        // Kiểm tra các class sáng màu mới trong sidebar
        $response->assertSee('admin-sidebar', false);
        $response->assertSee('bg-white text-slate-700', false);
        $response->assertSee('bg-[#f8fafc]', false);
        $response->assertSee('Xem Trang Chủ', false);
        $response->assertSee('Báo cáo doanh thu', false);
        $response->assertSee('Dịch vụ & Thực đơn', false);
        $response->assertSee('Đơn hàng & Khách hàng', false);
        $response->assertSee('Nghiệp vụ Vận hành', false);
        $response->assertSee('Kho Vận & Nhập Hàng', false);
        $response->assertSee('Tài khoản & Hệ thống', false);

        // Kiểm tra khối Tổng quan Nhập kho và Lợi nhuận mới bổ sung trên trang Admin
        $response->assertSee('Chi phí Nhập kho Tháng', false);
        $response->assertSee('Lợi nhuận gộp ước tính', false);
        $response->assertSee('Lịch Sử Nhập Kho Gần Đây', false);
        $response->assertSee('Lập Phiếu Nhập', false);
    }
}

<?php

namespace Tests\Feature;

use App\Models\GoodsReceipt;
use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementAndInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (class_exists(\Database\Seeders\RolesAndPermissionsSeeder::class)) {
            $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        }
    }

    protected function getAdminUser()
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'email' => 'admin_test_' . uniqid() . '@example.com',
            'phone' => '0987654321',
        ]);
        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('superadmin');
            } catch (\Throwable $e) {}
        }
        return $user;
    }

    public function test_admin_can_view_inventory_list_with_metrics(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('quanly_kho'));

        $response->assertStatus(200);
        $response->assertSee('Quản Lý Kho &amp; Hàng Tồn Thực Tế', false);
        $response->assertSee('Tổng mặt hàng trong kho');
    }

    public function test_admin_can_view_suppliers_list(): void
    {
        $admin = $this->getAdminUser();

        $response = $this->actingAs($admin)->get(route('quanly_nhacungcap'));

        $response->assertStatus(200);
        $response->assertSee('Nhà Cung Cấp (Nguồn Nhập Hàng)', false);
    }

    public function test_goods_receipt_creation_automatically_increments_inventory_stock(): void
    {
        $admin = $this->getAdminUser();

        $supplier = Supplier::first() ?? Supplier::create([
            'supplier_code' => 'TEST-SUP-' . uniqid(),
            'name' => 'NCC Kiểm Thử Tự Động',
            'phone' => '0909999888',
            'status' => 'active',
        ]);

        $item = InventoryItem::create([
            'item_code' => 'TEST-ITEM-' . uniqid(),
            'name' => 'Mặt Hàng Test Tự Động',
            'unit' => 'kg',
            'current_stock' => 10.0,
            'min_stock' => 5.0,
            'cost_price' => 50000,
            'supplier_id' => $supplier->id,
            'status' => 'active',
        ]);

        $receiptCode = 'PNK-TEST-' . uniqid();
        $importQty = 25.5;
        $importPrice = 52000;

        $response = $this->actingAs($admin)->post(route('nhaphang_them.post'), [
            'receipt_code' => $receiptCode,
            'supplier_id' => $supplier->id,
            'receipt_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'payment_status' => 'paid',
            'notes' => 'Kiểm thử nhập kho tự động tăng tồn',
            'items' => [
                [
                    'inventory_item_id' => $item->id,
                    'quantity' => $importQty,
                    'unit_price' => $importPrice,
                    'expiry_date' => Carbon::now()->addDays(30)->toDateString(),
                    'notes' => 'Hàng mới date xa',
                ]
            ],
        ]);

        // Kiểm tra tạo phiếu thành công
        $this->assertDatabaseHas('goods_receipts', [
            'receipt_code' => $receiptCode,
            'supplier_id' => $supplier->id,
            'payment_status' => 'paid',
        ]);

        // Kiểm tra TỒN KHO TỰ ĐỘNG TĂNG LÊN (10 + 25.5 = 35.5)
        $item->refresh();
        $this->assertEquals(35.5, (float) $item->current_stock);
        $this->assertEquals(52000, (float) $item->cost_price);

        $receipt = GoodsReceipt::where('receipt_code', $receiptCode)->first();
        $response->assertRedirect(route('nhaphang_xem', $receipt->id));

        // Kiểm tra trang xem chi tiết phiếu
        $viewResponse = $this->actingAs($admin)->get(route('nhaphang_xem', $receipt->id));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($receiptCode);
        $viewResponse->assertSee('NCC Kiểm Thử Tự Động');
    }

    public function test_admin_can_adjust_stock_with_audit_reason(): void
    {
        $admin = $this->getAdminUser();

        $item = InventoryItem::first();
        $this->assertNotNull($item);

        $newStock = 77.5;
        $response = $this->actingAs($admin)->post(route('kho_dieuchinh', $item->id), [
            'new_stock' => $newStock,
            'reason' => 'Kiểm kê định kỳ cuối tháng',
        ]);

        $response->assertSessionHas('success');
        $item->refresh();
        $this->assertEquals($newStock, (float) $item->current_stock);
        $this->assertStringContainsString('Kiểm kê định kỳ cuối tháng', $item->notes);
    }
}

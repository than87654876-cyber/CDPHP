<?php

namespace Database\Seeders;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo các Nhà Cung Cấp đối tác uy tín
        $suppliersData = [
            [
                'supplier_code' => 'NCC-CPFOOD',
                'name' => 'Công ty Cổ phần Chăn nuôi C.P. Việt Nam',
                'contact_person' => 'Nguyễn Văn Tuấn (Phụ trách khu vực)',
                'phone' => '0903123456',
                'email' => 'contact@cp.com.vn',
                'address' => 'Số 2, Đường 2A, KCN Biên Hòa 2, Đồng Nai',
                'supply_category' => 'Thịt tươi sống & Trứng gà sạch',
                'status' => 'active',
                'notes' => 'Giao hàng 5h30 sáng hàng ngày. Hàng đạt chuẩn VietGAP.',
            ],
            [
                'supplier_code' => 'NCC-VISSAN',
                'name' => 'Công ty Cổ phần Việt Nam Kỹ Nghệ Súc Sản (VISSAN)',
                'contact_person' => 'Trần Thị Mai (Kinh doanh F&B)',
                'phone' => '0918765432',
                'email' => 'kinhdoanh@vissan.com.vn',
                'address' => '420 Nơ Trang Long, P. 13, Q. Bình Thạnh, TP.HCM',
                'supply_category' => 'Thịt bò, Thịt heo & Thực phẩm chế biến',
                'status' => 'active',
                'notes' => 'Đối tác cung ứng thịt bò và xúc xích sạch.',
            ],
            [
                'supplier_code' => 'NCC-DALATFARM',
                'name' => 'Hợp tác xã Nông sản Sạch Rau Củ Đà Lạt',
                'contact_person' => 'Lê Hoàng Long',
                'phone' => '0987112233',
                'email' => 'sales@dalatfarmorganic.vn',
                'address' => 'Thôn Đa Quý, Xã Xuân Thọ, TP. Đà Lạt, Lâm Đồng',
                'supply_category' => 'Rau củ quả tươi & Nấm',
                'status' => 'active',
                'notes' => 'Rau sạch hữu cơ thu hoạch trong ngày giao xe lạnh.',
            ],
            [
                'supplier_code' => 'NCC-BEVERAGE',
                'name' => 'Đại lý Phân phối Nước giải khát Hưng Thịnh',
                'contact_person' => 'Phạm Quốc Hùng',
                'phone' => '0934556677',
                'email' => 'hungthinh.beverage@gmail.com',
                'address' => '128 Tô Hiến Thành, P. 15, Quận 10, TP.HCM',
                'supply_category' => 'Nước giải khát đóng lon, Trà & Nước khoáng',
                'status' => 'active',
                'notes' => 'Cung cấp lon Coca, Pepsi, Lavie, Trà Ô long giá sỉ đại lý.',
            ],
        ];

        $createdSuppliers = [];
        foreach ($suppliersData as $sup) {
            $createdSuppliers[$sup['supplier_code']] = Supplier::updateOrCreate(
                ['supplier_code' => $sup['supplier_code']],
                $sup
            );
        }

        // 2. Tạo Danh mục Hàng tồn kho / Nguyên liệu
        $itemsData = [
            [
                'item_code' => 'NL-HEOBD',
                'name' => 'Thịt heo ba chỉ rút sườn',
                'category' => 'Thịt tươi sống',
                'unit' => 'kg',
                'current_stock' => 18.5,
                'min_stock' => 10.0,
                'cost_price' => 135000,
                'supplier_id' => $createdSuppliers['NCC-CPFOOD']->id,
                'status' => 'active',
                'notes' => 'Nguyên liệu cho món cơm sườn nướng, ba chỉ kho trứng.',
            ],
            [
                'item_code' => 'NL-UCGA',
                'name' => 'Ức gà phi lê tươi',
                'category' => 'Thịt tươi sống',
                'unit' => 'kg',
                'current_stock' => 4.2, // Sắp hết
                'min_stock' => 10.0,
                'cost_price' => 75000,
                'supplier_id' => $createdSuppliers['NCC-CPFOOD']->id,
                'status' => 'active',
                'notes' => 'Dùng làm cơm gà áp chảo, salad gà xé.',
            ],
            [
                'item_code' => 'NL-THITBO',
                'name' => 'Thịt bò bắp & thăn loại 1',
                'category' => 'Thịt tươi sống',
                'unit' => 'kg',
                'current_stock' => 0.0, // Hết hàng cần nhập gấp
                'min_stock' => 8.0,
                'cost_price' => 240000,
                'supplier_id' => $createdSuppliers['NCC-VISSAN']->id,
                'status' => 'active',
                'notes' => 'Bò xào lúc lắc, phở bò, bò sốt tiêu.',
            ],
            [
                'item_code' => 'NL-TRUNGGA',
                'name' => 'Trứng gà ta chuẩn sạch',
                'category' => 'Thực phẩm khô & Trứng',
                'unit' => 'quả',
                'current_stock' => 120,
                'min_stock' => 50,
                'cost_price' => 3200,
                'supplier_id' => $createdSuppliers['NCC-CPFOOD']->id,
                'status' => 'active',
                'notes' => 'Dùng cho món ốp la, cơm chiên, sốt trứng.',
            ],
            [
                'item_code' => 'NL-GAOST25',
                'name' => 'Gạo thơm đặc sản ST25',
                'category' => 'Lương thực',
                'unit' => 'kg',
                'current_stock' => 85.0,
                'min_stock' => 30.0,
                'cost_price' => 28000,
                'supplier_id' => $createdSuppliers['NCC-DALATFARM']->id,
                'status' => 'active',
                'notes' => 'Gạo nấu cơm dẻo cho toàn bộ suất ăn văn phòng.',
            ],
            [
                'item_code' => 'NL-XALACH',
                'name' => 'Xà lách mỡ Đà Lạt sạch',
                'category' => 'Rau củ quả',
                'unit' => 'kg',
                'current_stock' => 3.5, // Sắp hết
                'min_stock' => 8.0,
                'cost_price' => 32000,
                'supplier_id' => $createdSuppliers['NCC-DALATFARM']->id,
                'status' => 'active',
                'notes' => 'Rau ăn kèm và làm món salad healthy.',
            ],
            [
                'item_code' => 'NL-CACHUA',
                'name' => 'Cà chua cherry bi đỏ',
                'category' => 'Rau củ quả',
                'unit' => 'kg',
                'current_stock' => 15.0,
                'min_stock' => 5.0,
                'cost_price' => 45000,
                'supplier_id' => $createdSuppliers['NCC-DALATFARM']->id,
                'status' => 'active',
                'notes' => 'Trang trí món ăn và bổ sung vitamin.',
            ],
            [
                'item_code' => 'SP-COCA',
                'name' => 'Nước ngọt Coca-Cola Sleek 320ml',
                'category' => 'Đồ uống đóng lon',
                'unit' => 'lon',
                'current_stock' => 48,
                'min_stock' => 24,
                'cost_price' => 8500,
                'supplier_id' => $createdSuppliers['NCC-BEVERAGE']->id,
                'status' => 'active',
                'notes' => 'Mặt hàng thương mại bán trực tiếp kèm combo.',
            ],
            [
                'item_code' => 'SP-LAVIE',
                'name' => 'Nước khoáng thiên nhiên LaVie 500ml',
                'category' => 'Đồ uống đóng lon',
                'unit' => 'chai',
                'current_stock' => 5, // Sắp hết
                'min_stock' => 24,
                'cost_price' => 5000,
                'supplier_id' => $createdSuppliers['NCC-BEVERAGE']->id,
                'status' => 'active',
                'notes' => 'Nước uống phục vụ khách ăn tại chỗ và mang về.',
            ],
            [
                'item_code' => 'SP-TRAOLONG',
                'name' => 'Trà Ô Long Tea+ Plus 455ml',
                'category' => 'Đồ uống đóng lon',
                'unit' => 'chai',
                'current_stock' => 0, // Hết hàng
                'min_stock' => 20,
                'cost_price' => 9000,
                'supplier_id' => $createdSuppliers['NCC-BEVERAGE']->id,
                'status' => 'active',
                'notes' => 'Mặt hàng giải nhiệt bán chạy.',
            ],
        ];

        $createdItems = [];
        foreach ($itemsData as $item) {
            $createdItems[$item['item_code']] = InventoryItem::updateOrCreate(
                ['item_code' => $item['item_code']],
                $item
            );
        }

        // 3. Tạo một số Phiếu Nhập Hàng mẫu để minh họa lịch sử nhập kho thực tế
        $adminUser = User::whereIn('role', ['superadmin', 'admin'])->first() ?? User::first();
        if ($adminUser) {
            // Phiếu 1: Nhập từ CP Food 2 ngày trước
            $receipt1 = GoodsReceipt::updateOrCreate(
                ['receipt_code' => 'PNK-' . Carbon::now()->subDays(2)->format('Ymd') . '-001'],
                [
                    'supplier_id' => $createdSuppliers['NCC-CPFOOD']->id,
                    'user_id' => $adminUser->id,
                    'receipt_date' => Carbon::now()->subDays(2)->setTime(6, 15),
                    'total_amount' => 4565000,
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'notes' => 'Nhập nguyên liệu tươi đợt đầu tuần, hóa đơn số HD-CP-8841. Hàng đạt chất lượng.',
                ]
            );

            GoodsReceiptItem::updateOrCreate(
                [
                    'goods_receipt_id' => $receipt1->id,
                    'inventory_item_id' => $createdItems['NL-HEOBD']->id,
                ],
                [
                    'quantity' => 25.0,
                    'unit_price' => 135000,
                    'subtotal' => 3375000,
                    'expiry_date' => Carbon::now()->addDays(5)->toDateString(),
                    'notes' => 'Thịt tươi đóng khay hút chân không.',
                ]
            );

            GoodsReceiptItem::updateOrCreate(
                [
                    'goods_receipt_id' => $receipt1->id,
                    'inventory_item_id' => $createdItems['NL-UCGA']->id,
                ],
                [
                    'quantity' => 15.0,
                    'unit_price' => 75000,
                    'subtotal' => 1125000,
                    'expiry_date' => Carbon::now()->addDays(4)->toDateString(),
                    'notes' => 'Ức gà phi lê đông mát.',
                ]
            );

            // Phiếu 2: Nhập từ Hưng Thịnh Beverage ngày hôm qua
            $receipt2 = GoodsReceipt::updateOrCreate(
                ['receipt_code' => 'PNK-' . Carbon::now()->subDays(1)->format('Ymd') . '-002'],
                [
                    'supplier_id' => $createdSuppliers['NCC-BEVERAGE']->id,
                    'user_id' => $adminUser->id,
                    'receipt_date' => Carbon::now()->subDays(1)->setTime(9, 30),
                    'total_amount' => 1224000,
                    'payment_status' => 'paid',
                    'status' => 'completed',
                    'notes' => 'Nhập 5 thùng nước ngọt lon bán lẻ kèm combo ăn trưa.',
                ]
            );

            GoodsReceiptItem::updateOrCreate(
                [
                    'goods_receipt_id' => $receipt2->id,
                    'inventory_item_id' => $createdItems['SP-COCA']->id,
                ],
                [
                    'quantity' => 72, // 3 thùng
                    'unit_price' => 8500,
                    'subtotal' => 612000,
                    'expiry_date' => Carbon::now()->addMonths(9)->toDateString(),
                    'notes' => '3 thùng lon date mới 2027.',
                ]
            );

            GoodsReceiptItem::updateOrCreate(
                [
                    'goods_receipt_id' => $receipt2->id,
                    'inventory_item_id' => $createdItems['SP-TRAOLONG']->id,
                ],
                [
                    'quantity' => 68,
                    'unit_price' => 9000,
                    'subtotal' => 612000,
                    'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
                    'notes' => 'Thùng nguyên đai nguyên kiện.',
                ]
            );
        }
    }
}

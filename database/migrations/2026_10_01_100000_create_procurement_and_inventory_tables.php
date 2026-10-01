<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bảng Nhà Cung Cấp / Nguồn Hàng Đối Tác
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code', 50)->unique();
            $table->string('name', 255);
            $table->string('contact_person', 255)->nullable();
            $table->string('phone', 50);
            $table->string('email', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('supply_category', 255)->nullable(); // Ngành hàng: Thịt tươi, Nước ngọt, Rau củ quả...
            $table->string('status', 50)->default('active'); // active, inactive
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Bảng Danh Mục Mặt Hàng / Nguyên Liệu Kho
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code', 50)->unique();
            $table->string('name', 255);
            $table->string('category', 100)->nullable(); // Thịt tươi, Đồ uống đóng chai, Rau củ, Đồ khô...
            $table->string('unit', 50)->default('kg'); // kg, lon, chai, bao, bó, vỉ...
            $table->decimal('current_stock', 12, 2)->default(0.00); // Tồn kho thực tế
            $table->decimal('min_stock', 12, 2)->default(10.00); // Ngưỡng an toàn tối thiểu (cảnh báo nhập hàng)
            $table->decimal('cost_price', 14, 2)->default(0.00); // Giá nhập tham khảo / gần nhất
            $table->unsignedBigInteger('supplier_id')->nullable(); // Nguồn nhập ưu tiên
            $table->unsignedBigInteger('dish_id')->nullable(); // Gắn với món trên web (nếu là mặt hàng bán liền như lon nước)
            $table->string('status', 50)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('set null');
        });

        // 3. Bảng Phiếu Nhập Hàng (Goods Receipts)
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_code', 50)->unique(); // Mã phiếu: PNK-20261001-001
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('user_id'); // Nhân viên / Admin tiếp nhận nhập kho
            $table->dateTime('receipt_date');
            $table->decimal('total_amount', 14, 2)->default(0.00); // Tổng giá trị phiếu nhập
            $table->string('payment_status', 50)->default('paid'); // paid (đã thanh toán), pending (chưa thanh toán), debt (ghi nợ)
            $table->string('status', 50)->default('completed'); // completed, cancelled
            $table->text('notes')->nullable(); // Hóa đơn số, ghi chú xe giao hàng...
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        // 4. Bảng Chi Tiết Phiếu Nhập Hàng (Goods Receipt Items)
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('goods_receipt_id');
            $table->unsignedBigInteger('inventory_item_id');
            $table->decimal('quantity', 12, 2); // Số lượng thực tế nhập
            $table->decimal('unit_price', 14, 2); // Đơn giá nhập của đợt này
            $table->decimal('subtotal', 14, 2); // Thành tiền
            $table->date('expiry_date')->nullable(); // Hạn sử dụng của lô
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('goods_receipt_id')->references('id')->on('goods_receipts')->onDelete('cascade');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('suppliers');
    }
};

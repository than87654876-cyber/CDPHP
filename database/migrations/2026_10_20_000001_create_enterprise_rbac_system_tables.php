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
        // 1. Bảng Chi nhánh / Kho hàng (Branch & Warehouse Data Scope)
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('code', 50)->unique();
                $table->string('name', 150);
                $table->string('phone', 20)->nullable();
                $table->string('address', 255)->nullable();
                $table->boolean('is_main')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Thêm branch_id và manager_pin vào bảng users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('role');
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'manager_pin')) {
                $table->string('manager_pin', 100)->nullable()->after('branch_id'); // Mã PIN ủy quyền Quản lý
            }
        });

        // 3. Bảng Ca làm việc của Thu ngân / Nhân viên bán hàng (Work Shift Data Scope)
        if (!Schema::hasTable('work_shifts')) {
            Schema::create('work_shifts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->string('shift_name', 100);
                $table->dateTime('opened_at');
                $table->dateTime('closed_at')->nullable();
                $table->decimal('opening_cash', 12, 2)->default(0);
                $table->decimal('closing_cash', 12, 2)->nullable();
                $table->decimal('actual_cash', 12, 2)->nullable();
                $table->decimal('difference', 12, 2)->nullable();
                $table->enum('status', ['open', 'closed'])->default('open');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            });
        }

        // 4. Bảng Đơn đặt hàng Nhà cung cấp (Purchase Order - PO)
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_code', 50)->unique();
                $table->unsignedBigInteger('supplier_id');
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('created_by'); // Quản lý hoặc Thủ kho lập
                $table->unsignedBigInteger('price_approved_by')->nullable(); // Admin / Kế toán duyệt giá
                $table->dateTime('price_approved_at')->nullable();
                $table->enum('status', ['draft', 'price_pending', 'approved', 'received', 'cancelled'])->default('draft');
                $table->decimal('total_estimated_amount', 12, 2)->default(0);
                $table->decimal('total_approved_amount', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('price_approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // Bảng Chi tiết Đơn đặt hàng PO
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_order_id');
                $table->unsignedBigInteger('inventory_item_id');
                $table->decimal('quantity_ordered', 10, 2);
                $table->decimal('estimated_unit_price', 12, 2)->default(0);
                $table->decimal('approved_unit_price', 12, 2)->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
                $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
            });
        }

        // 5. Cập nhật bảng goods_receipt_items (số lô, hạn dùng, số lượng thực nhận)
        Schema::table('goods_receipt_items', function (Blueprint $table) {
            if (!Schema::hasColumn('goods_receipt_items', 'batch_number')) {
                $table->string('batch_number', 50)->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('goods_receipt_items', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_number');
            }
            if (!Schema::hasColumn('goods_receipt_items', 'actual_quantity')) {
                $table->decimal('actual_quantity', 10, 2)->nullable()->after('quantity');
            }
        });

        // 6. Bảng Điều chuyển kho (Stock Transfers)
        if (!Schema::hasTable('stock_transfers')) {
            Schema::create('stock_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('transfer_code', 50)->unique();
                $table->unsignedBigInteger('from_branch_id');
                $table->unsignedBigInteger('to_branch_id');
                $table->unsignedBigInteger('created_by'); // Người tạo yêu cầu
                $table->unsignedBigInteger('dispatched_by')->nullable(); // Xác nhận xuất kho
                $table->dateTime('dispatched_at')->nullable();
                $table->unsignedBigInteger('received_by')->nullable(); // Xác nhận nhập kho đích
                $table->dateTime('received_at')->nullable();
                $table->enum('status', ['pending', 'in_transit', 'completed', 'cancelled'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('from_branch_id')->references('id')->on('branches')->cascadeOnDelete();
                $table->foreign('to_branch_id')->references('id')->on('branches')->cascadeOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('dispatched_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('stock_transfer_items')) {
            Schema::create('stock_transfer_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_transfer_id');
                $table->unsignedBigInteger('inventory_item_id');
                $table->decimal('quantity', 10, 2);
                $table->string('batch_number', 50)->nullable();
                $table->date('expiry_date')->nullable();
                $table->timestamps();

                $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->cascadeOnDelete();
                $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
            });
        }

        // 7. Bảng Kiểm kê & Cân bằng kho (Stock Audits)
        if (!Schema::hasTable('stock_audits')) {
            Schema::create('stock_audits', function (Blueprint $table) {
                $table->id();
                $table->string('audit_code', 50)->unique();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('counted_by'); // Nhân viên kho nhập số lượng đếm thực tế
                $table->unsignedBigInteger('balanced_by')->nullable(); // Chỉ Quản lý / Admin mới có quyền Cân bằng kho
                $table->dateTime('balanced_at')->nullable();
                $table->enum('status', ['counting', 'balanced', 'cancelled'])->default('counting');
                $table->decimal('total_variance_qty', 10, 2)->default(0);
                $table->decimal('total_variance_value', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
                $table->foreign('counted_by')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('balanced_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('stock_audit_items')) {
            Schema::create('stock_audit_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('stock_audit_id');
                $table->unsignedBigInteger('inventory_item_id');
                $table->decimal('system_stock', 10, 2);
                $table->decimal('actual_stock', 10, 2);
                $table->decimal('variance_stock', 10, 2);
                $table->decimal('cost_price', 12, 2)->default(0);
                $table->decimal('variance_value', 12, 2)->default(0);
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->foreign('stock_audit_id')->references('id')->on('stock_audits')->cascadeOnDelete();
                $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->cascadeOnDelete();
            });
        }

        // 8. Bảng Hủy hàng / Xuất trả NCC (Stock Discards - Yêu cầu phê duyệt 2 bước)
        if (!Schema::hasTable('stock_discards')) {
            Schema::create('stock_discards', function (Blueprint $table) {
                $table->id();
                $table->string('discard_code', 50)->unique();
                $table->unsignedBigInteger('branch_id')->nullable();
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->enum('type', ['discard', 'return_supplier'])->default('discard');
                $table->unsignedBigInteger('requested_by');
                $table->text('reason');
                $table->decimal('total_loss_value', 12, 2)->default(0);
                $table->enum('status', ['pending_step_1', 'pending_step_2', 'approved', 'rejected'])->default('pending_step_1');
                $table->unsignedBigInteger('first_approved_by')->nullable();
                $table->dateTime('first_approved_at')->nullable();
                $table->unsignedBigInteger('second_approved_by')->nullable();
                $table->dateTime('second_approved_at')->nullable();
                $table->timestamps();

                $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
                $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
                $table->foreign('requested_by')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('first_approved_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('second_approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 9. Bảng Nhật ký hoạt động (Audit Trail / Activity Log)
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 100); // e.g: inventory_adjusted, price_changed, discount_override, order_cancelled
                $table->string('auditable_type', 150)->nullable();
                $table->unsignedBigInteger('auditable_id')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                $table->index(['action', 'created_at']);
                $table->index(['auditable_type', 'auditable_id']);
            });
        }

        // 10. Bảng Quản lý Phê duyệt 2 bước (Dual Approval Engine)
        if (!Schema::hasTable('dual_approvals')) {
            Schema::create('dual_approvals', function (Blueprint $table) {
                $table->id();
                $table->string('approvable_type', 150);
                $table->unsignedBigInteger('approvable_id');
                $table->string('action_type', 100); // e.g: large_goods_receipt, large_stock_variance, stock_discard
                $table->decimal('amount_value', 12, 2)->default(0);
                $table->unsignedBigInteger('requested_by');
                $table->unsignedBigInteger('first_approver_id')->nullable();
                $table->dateTime('first_approved_at')->nullable();
                $table->unsignedBigInteger('second_approver_id')->nullable();
                $table->dateTime('second_approved_at')->nullable();
                $table->enum('status', ['pending_step_1', 'pending_step_2', 'approved', 'rejected'])->default('pending_step_1');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('requested_by')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('first_approver_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('second_approver_id')->references('id')->on('users')->nullOnDelete();
                $table->index(['approvable_type', 'approvable_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dual_approvals');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('stock_discards');
        Schema::dropIfExists('stock_audit_items');
        Schema::dropIfExists('stock_audits');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        
        if (Schema::hasTable('goods_receipt_items')) {
            Schema::table('goods_receipt_items', function (Blueprint $table) {
                $table->dropColumn(['batch_number', 'expiry_date', 'actual_quantity']);
            });
        }

        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('work_shifts');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn(['branch_id', 'manager_pin']);
            });
        }

        Schema::dropIfExists('branches');
    }
};

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
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['order_status', 'payment_status'], 'idx_orders_status_payment');
            $table->index(['user_id', 'created_at'], 'idx_orders_user_created');
            $table->index('created_at', 'idx_orders_created_at');
        });

        Schema::table('dishes', function (Blueprint $table) {
            $table->index(['category_id', 'is_available'], 'idx_dishes_category_available');
            $table->index('is_available', 'idx_dishes_is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status_payment');
            $table->dropIndex('idx_orders_user_created');
            $table->dropIndex('idx_orders_created_at');
        });

        Schema::table('dishes', function (Blueprint $table) {
            $table->dropIndex('idx_dishes_category_available');
            $table->dropIndex('idx_dishes_is_available');
        });
    }
};

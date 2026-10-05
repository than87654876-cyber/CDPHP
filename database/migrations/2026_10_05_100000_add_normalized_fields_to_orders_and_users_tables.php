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
            $table->string('delivery_address', 500)->nullable()->after('health_notes');
            $table->string('delivery_phone', 20)->nullable()->after('delivery_address');
            $table->string('delivery_time', 50)->nullable()->after('delivery_phone');
            $table->string('cancellation_reason', 255)->nullable()->after('delivery_time');
            $table->text('admin_notes')->nullable()->after('cancellation_reason');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('address', 500)->nullable()->after('phone');
            $table->string('gender', 20)->nullable()->after('address');
            $table->date('birthday')->nullable()->after('gender');
            $table->timestamp('last_login')->nullable()->after('birthday');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_address', 'delivery_phone', 'delivery_time', 'cancellation_reason', 'admin_notes']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'gender', 'birthday', 'last_login']);
        });
    }
};

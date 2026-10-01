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
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->nullable()->change();
            if (!Schema::hasColumn('reviews', 'dish_id')) {
                $table->unsignedBigInteger('dish_id')->nullable()->after('order_id');
                $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['dish_id']);
            $table->dropColumn('dish_id');
        });
    }
};

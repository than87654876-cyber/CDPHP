<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_visible')->default(false)->after('image_url')->comment('0: Ẩn/Chờ duyệt, 1: Hiển thị công khai');
        });

        // Set existing reviews to visible = 1 so demo data remains intact
        DB::table('reviews')->update(['is_visible' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });
    }
};

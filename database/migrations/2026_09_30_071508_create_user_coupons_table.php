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
        Schema::create('user_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coupon_id');
            $table->boolean('is_used')->default(false);
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->unique(['user_id', 'coupon_id']);
        });

        // Insert / Update rich promotional coupons for users
        $coupons = [
            [
                'coupon_code' => 'FOODDAILY30',
                'discount_type' => 'percent',
                'discount_value' => 30.00,
                'min_order_value' => 80000.00,
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-31',
                'usage_limit' => 500,
            ],
            [
                'coupon_code' => 'CHAOBANMOI',
                'discount_type' => 'fixed',
                'discount_value' => 50000.00,
                'min_order_value' => 100000.00,
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-31',
                'usage_limit' => 200,
            ],
            [
                'coupon_code' => 'FREESHIP',
                'discount_type' => 'fixed',
                'discount_value' => 20000.00,
                'min_order_value' => 50000.00,
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-25',
                'usage_limit' => 1000,
            ],
            [
                'coupon_code' => 'FLASHDEAL50',
                'discount_type' => 'percent',
                'discount_value' => 50.00,
                'min_order_value' => 70000.00,
                'start_date' => '2026-09-20',
                'end_date' => '2026-10-02', // Sắp hết hạn trong 2 ngày
                'usage_limit' => 100,
            ],
            [
                'coupon_code' => 'CUOITUAN35',
                'discount_type' => 'fixed',
                'discount_value' => 35000.00,
                'min_order_value' => 120000.00,
                'start_date' => '2026-09-20',
                'end_date' => '2026-10-03', // Sắp hết hạn trong 3 ngày
                'usage_limit' => 150,
            ],
            [
                'coupon_code' => 'COMTRUA15',
                'discount_type' => 'percent',
                'discount_value' => 15.00,
                'min_order_value' => 60000.00,
                'start_date' => '2026-09-01',
                'end_date' => '2026-10-30',
                'usage_limit' => 300,
            ],
            [
                'coupon_code' => 'TIECVUI100',
                'discount_type' => 'fixed',
                'discount_value' => 100000.00,
                'min_order_value' => 300000.00,
                'start_date' => '2026-09-15',
                'end_date' => '2026-10-02', // Sắp hết hạn trong 2 ngày
                'usage_limit' => 80,
            ],
        ];

        foreach ($coupons as $c) {
            \Illuminate\Support\Facades\DB::table('coupons')->updateOrInsert(
                ['coupon_code' => $c['coupon_code']],
                array_merge($c, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_coupons');
    }
};

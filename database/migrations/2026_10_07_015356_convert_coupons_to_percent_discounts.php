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
        // 1. Cập nhật các mã mặc định phổ biến sang chiết khấu phần trăm (%)
        $mappings = [
            'CHAOBANMOI' => ['discount_type' => 'percent', 'discount_value' => 20.00, 'min_order_value' => 100000.00],
            'FREESHIP' => ['discount_type' => 'percent', 'discount_value' => 15.00, 'min_order_value' => 50000.00],
            'CUOITUAN35' => ['discount_type' => 'percent', 'discount_value' => 25.00, 'min_order_value' => 120000.00],
            'TIECVUI100' => ['discount_type' => 'percent', 'discount_value' => 30.00, 'min_order_value' => 300000.00],
            'FLAT20' => ['discount_type' => 'percent', 'discount_value' => 10.00, 'min_order_value' => 100000.00],
        ];

        foreach ($mappings as $code => $data) {
            DB::table('coupons')->where('coupon_code', $code)->update($data);
        }

        // 2. Chuyển toàn bộ các mã tự sinh (CODE-*) sang giảm 15% để tránh đơn hàng bị giảm xuống 0đ
        DB::table('coupons')
            ->where('coupon_code', 'like', 'CODE-%')
            ->update([
                'discount_type' => 'percent',
                'discount_value' => 15.00,
            ]);

        // 3. Chuyển bất kỳ mã fixed nào còn lại trong database sang percent
        $remainingFixed = DB::table('coupons')->where('discount_type', 'fixed')->get();
        foreach ($remainingFixed as $c) {
            $newPercent = 15.00;
            if ($c->discount_value > 0 && $c->discount_value <= 50) {
                $newPercent = (float) $c->discount_value;
            } elseif ($c->min_order_value > 0) {
                $newPercent = round(($c->discount_value / $c->min_order_value) * 100);
                $newPercent = min(max(5, $newPercent), 50);
            }
            DB::table('coupons')->where('id', $c->id)->update([
                'discount_type' => 'percent',
                'discount_value' => $newPercent,
            ]);
        }

        // 4. Giới hạn tối đa 90% cho các mã có mức giảm bất thường (>90)
        DB::table('coupons')->where('discount_value', '>', 90)->update([
            'discount_value' => 15.00,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Có thể rollback nếu cần thiết
    }
};

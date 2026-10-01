<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'start_date',
        'end_date',
        'usage_limit',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the orders that use this coupon.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function userCoupons(): HasMany
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_coupons')
            ->withPivot('is_used', 'used_at')
            ->withTimestamps();
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percent') {
            return 'Giảm ' . (int) $this->discount_value . '%';
        }
        return 'Giảm ' . number_format($this->discount_value, 0, ',', '.') . 'đ';
    }

    public function getFormattedMinOrderAttribute(): string
    {
        if (!$this->min_order_value || $this->min_order_value <= 0) {
            return 'Đơn từ 0đ';
        }
        return 'Đơn từ ' . number_format($this->min_order_value, 0, ',', '.') . 'đ';
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }
        $today = now()->startOfDay();
        $end = $this->end_date->copy()->startOfDay();
        return (int) $today->diffInDays($end, false);
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        $days = $this->days_remaining;
        return $days !== null && $days >= 0 && $days <= 3;
    }

    public function calculateDiscount(float $totalAmount): float
    {
        if ($this->discount_type === 'percent') {
            $discount = $totalAmount * ($this->discount_value / 100);
        } else {
            $discount = (float) $this->discount_value;
        }
        return min($discount, $totalAmount);
    }

    public function isValidFor(float $totalAmount): array
    {
        $now = now()->format('Y-m-d');
        if ($this->start_date && $now < $this->start_date->format('Y-m-d')) {
            return ['valid' => false, 'message' => 'Chương trình khuyến mãi chưa bắt đầu.'];
        }
        if ($this->end_date && $now > $this->end_date->format('Y-m-d')) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết hạn sử dụng.'];
        }
        if ($this->usage_limit !== null && $this->usage_limit <= 0) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết lượt sử dụng.'];
        }
        if ($this->min_order_value && $totalAmount < $this->min_order_value) {
            $diff = $this->min_order_value - $totalAmount;
            return [
                'valid' => false, 
                'message' => 'Cần mua thêm ' . number_format($diff, 0, ',', '.') . 'đ để áp dụng mã này.',
                'need_more' => $diff
            ];
        }
        return ['valid' => true, 'message' => 'Mã hợp lệ!'];
    }
}

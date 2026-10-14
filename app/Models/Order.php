<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'order_type',
        'total_amount',
        'final_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'health_notes',
        'delivery_address',
        'delivery_phone',
        'delivery_time',
        'cancellation_reason',
        'admin_notes',
        'points_accumulated',
        'payment_transaction_id',
        'payment_bank_reference',
        'payment_bank',
        'payment_amount',
        'payment_content',
        'payment_paid_at',
    ];

    /**
     * Get delivery phone with fallback to health_notes regex
     */
    public function getResolvedPhoneAttribute(): string
    {
        if (!empty($this->delivery_phone)) {
            return $this->delivery_phone;
        }
        if (!empty($this->health_notes) && preg_match('/SĐT:\s*([0-9\+\.\s]+)/i', $this->health_notes, $m)) {
            return trim($m[1]);
        }
        return $this->user->phone ?? '';
    }

    /**
     * Get delivery address with fallback to health_notes regex
     */
    public function getResolvedAddressAttribute(): string
    {
        if (!empty($this->delivery_address)) {
            return $this->delivery_address;
        }
        if (!empty($this->health_notes) && preg_match('/(?:Địa chỉ|Giao):\s*([^.\n]+)/i', $this->health_notes, $m)) {
            return trim($m[1]);
        }
        return $this->user->address ?? '';
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saving(function ($order) {
            // Auto-confirm order when payment is successful.
            if ($order->payment_status === 'paid' && $order->order_status === 'pending') {
                $order->order_status = 'confirmed';
            }

            // Auto-cancel order when payment is refunded.
            if ($order->payment_status === 'refunded' && $order->order_status !== 'cancelled') {
                $order->order_status = 'cancelled';
            }
        });

        static::saved(function ($order) {
            // Check if the order is paid and points have not been accumulated yet
            if ($order->payment_status === 'paid' && !$order->points_accumulated) {
                $user = $order->user;
                if ($user) {
                    $user->addPoints($order->final_amount);
                    
                    // Mark points as accumulated and save quietly to prevent infinite loop
                    $order->points_accumulated = true;
                    $order->saveQuietly();
                }
            }
        });
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coupon applied to the order.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the reviews for the order.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

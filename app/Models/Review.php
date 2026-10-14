<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'dish_id',
        'rating',
        'comment',
        'image_url',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
    ];

    /**
     * Scope lấy các đánh giá đã duyệt hiển thị công khai
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope lấy các đánh giá đang ẩn hoặc chờ duyệt
     */
    public function scopePending($query)
    {
        return $query->where('is_visible', false);
    }

    /**
     * Get the user who made the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order being reviewed.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the dish being reviewed.
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }
}

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
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

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

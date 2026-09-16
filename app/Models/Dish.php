<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'dish_name',
        'image_url',
        'price',
        'description',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'rating_score',
        'reviews_count',
    ];

    /**
     * Accessor for legacy image property
     */
    public function getImageAttribute(): ?string
    {
        return $this->image_url;
    }

    /**
     * Accessor for display image URL with fallback
     */
    public function getDisplayImageAttribute(): string
    {
        if ($this->image_url) {
            return \Illuminate\Support\Str::startsWith($this->image_url, 'http') 
                ? $this->image_url 
                : asset($this->image_url);
        }
        return asset('logo.jpg');
    }

    /**
     * Get average rating score (e.g. 5.0, 4.9, 4.8, 4.7)
     */
    public function getRatingScoreAttribute(): float
    {
        if (isset($this->attributes['rating_score'])) {
            return (float) $this->attributes['rating_score'];
        }

        // 1. Check real reviews linked through order_items
        $realAvg = \App\Models\Review::whereHas('order.orderItems', function ($q) {
            $q->where('dish_id', $this->id);
        })->avg('rating');

        if ($realAvg && $realAvg > 0) {
            return round((float)$realAvg, 1);
        }

        // 2. Deterministic realistic rating score based on dish id (from 4.5 to 5.0)
        $scores = [5.0, 4.9, 4.8, 5.0, 4.7, 4.9, 5.0, 4.8, 4.6, 4.9, 5.0, 4.8];
        return $scores[$this->id % count($scores)];
    }

    /**
     * Get formatted reviews count (e.g. 128, 95)
     */
    public function getReviewsCountAttribute(): int
    {
        if (isset($this->attributes['reviews_count'])) {
            return (int) $this->attributes['reviews_count'];
        }

        $realCount = \App\Models\Review::whereHas('order.orderItems', function ($q) {
            $q->where('dish_id', $this->id);
        })->count();

        if ($realCount > 0) {
            return $realCount + 20;
        }

        return 35 + (($this->id * 17) % 180);
    }

    /**
     * Get the category that owns the dish.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the packages that contain this dish.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(ServicePackage::class, 'package_dishes', 'dish_id', 'package_id');
    }

    /**
     * Get the order items for this dish.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the daily schedules for this dish.
     */
    public function dailySchedules(): HasMany
    {
        return $this->hasMany(DailySchedule::class);
    }

    /**
     * Get related dishes in the same category, excluding the current dish.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRelatedDishes(int $limit = 6)
    {
        return self::where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->where('is_available', true)
            ->with('category')
            ->orderBy('id', 'desc')
            ->take($limit)
            ->get();
    }
}

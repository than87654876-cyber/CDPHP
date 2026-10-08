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
        'display_image',
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
     * Quan hệ lấy danh sách các đánh giá chung của món ăn này (chỉ hiển thị công khai các đánh giá chung, không lấy đánh giá từng đơn)
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->whereNull('order_id')->with('user')->latest();
    }

    /**
     * Quan hệ lấy các đánh giá chung công khai
     */
    public function generalReviews(): HasMany
    {
        return $this->reviews();
    }

    /**
     * Quan hệ lấy toàn bộ đánh giá (dành riêng cho nội bộ admin nếu cần)
     */
    public function allReviews(): HasMany
    {
        return $this->hasMany(Review::class)->with('user')->latest();
    }

    /**
     * Statically cached reviews summary across requests/models
     * @var array<int, array{avg: float, count: int}>|null
     */
    protected static ?array $reviewsSummaryCache = null;

    public static function clearReviewsSummaryCache(): void
    {
        self::$reviewsSummaryCache = null;
        \Illuminate\Support\Facades\Cache::forget('dishes_reviews_summary_cache');
    }

    protected static function loadReviewsSummary(): void
    {
        if (self::$reviewsSummaryCache !== null) {
            return;
        }

        self::$reviewsSummaryCache = \Illuminate\Support\Facades\Cache::remember('dishes_reviews_summary_cache', 600, function () {
            $summary = [];
            try {
                // Chỉ lấy các đánh giá chung cho món ăn (order_id IS NULL)
                // Đánh giá từng đơn (order_id IS NOT NULL) là nội bộ dành cho Admin
                $directReviews = \Illuminate\Support\Facades\DB::table('reviews')
                    ->whereNotNull('dish_id')
                    ->whereNull('order_id')
                    ->select(
                        'dish_id',
                        \Illuminate\Support\Facades\DB::raw('AVG(rating) as avg_rating'),
                        \Illuminate\Support\Facades\DB::raw('COUNT(id) as total_reviews')
                    )
                    ->groupBy('dish_id')
                    ->get();

                foreach ($directReviews as $row) {
                    $summary[(int) $row->dish_id] = [
                        'avg' => round((float) $row->avg_rating, 1),
                        'count' => (int) $row->total_reviews,
                    ];
                }
            } catch (\Throwable $e) {
                // Silently continue with fallbacks if tables don't exist yet
            }
            return $summary;
        });
    }

    /**
     * Get average rating score (e.g. 5.0, 4.9, 4.8, 4.7)
     */
    public function getRatingScoreAttribute(): float
    {
        if (isset($this->attributes['rating_score'])) {
            return (float) $this->attributes['rating_score'];
        }

        self::loadReviewsSummary();
        if (isset(self::$reviewsSummaryCache[$this->id]) && self::$reviewsSummaryCache[$this->id]['avg'] > 0) {
            return self::$reviewsSummaryCache[$this->id]['avg'];
        }

        return 5.0;
    }

    /**
     * Get formatted reviews count (e.g. 128, 95)
     */
    public function getReviewsCountAttribute(): int
    {
        if (isset($this->attributes['reviews_count'])) {
            return (int) $this->attributes['reviews_count'];
        }

        self::loadReviewsSummary();
        if (isset(self::$reviewsSummaryCache[$this->id])) {
            return self::$reviewsSummaryCache[$this->id]['count'];
        }

        return 0;
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

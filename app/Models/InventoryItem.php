<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'current_stock',
        'min_stock',
        'cost_price',
        'supplier_id',
        'dish_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    protected $appends = [
        'stock_status',
        'status_badge_class',
        'status_label',
    ];

    /**
     * Nhà cung cấp ưu tiên / đối tác nhập hàng
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Món ăn liên kết trên web (nếu có)
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class, 'dish_id');
    }

    /**
     * Chi tiết các lần nhập hàng
     */
    public function receiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class, 'inventory_item_id');
    }

    /**
     * Trạng thái tồn kho: out_of_stock (hết hàng), low_stock (sắp hết / dưới min), in_stock (đủ an toàn)
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_stock <= 0) {
            return 'out_of_stock';
        }

        if ($this->current_stock <= $this->min_stock) {
            return 'low_stock';
        }

        return 'in_stock';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'Hết hàng (Cần nhập ngay)',
            'low_stock' => 'Sắp hết (Dưới mức tối thiểu)',
            default => 'Tồn kho an toàn',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->stock_status) {
            'out_of_stock' => 'bg-rose-500/10 text-rose-500 border border-rose-500/20 font-bold',
            'low_stock' => 'bg-amber-500/10 text-amber-500 border border-amber-500/20 font-bold',
            default => 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium',
        };
    }
}

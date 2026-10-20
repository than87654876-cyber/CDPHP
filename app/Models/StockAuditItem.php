<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_audit_id',
        'inventory_item_id',
        'system_stock',
        'actual_stock',
        'variance_stock',
        'cost_price',
        'variance_value',
        'notes',
    ];

    protected $casts = [
        'system_stock' => 'decimal:2',
        'actual_stock' => 'decimal:2',
        'variance_stock' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'variance_value' => 'decimal:2',
    ];

    public function stockAudit(): BelongsTo
    {
        return $this->belongsTo(StockAudit::class, 'stock_audit_id');
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}

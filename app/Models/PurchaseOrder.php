<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_code',
        'supplier_id',
        'branch_id',
        'created_by',
        'price_approved_by',
        'price_approved_at',
        'status',
        'total_estimated_amount',
        'total_approved_amount',
        'notes',
    ];

    protected $casts = [
        'price_approved_at' => 'datetime',
        'total_estimated_amount' => 'decimal:2',
        'total_approved_amount' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function priceApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'price_approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }
}

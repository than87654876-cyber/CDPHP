<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_code',
        'branch_id',
        'counted_by',
        'balanced_by',
        'balanced_at',
        'status',
        'total_variance_qty',
        'total_variance_value',
        'notes',
    ];

    protected $casts = [
        'balanced_at' => 'datetime',
        'total_variance_qty' => 'decimal:2',
        'total_variance_value' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    public function balancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'balanced_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockAuditItem::class, 'stock_audit_id');
    }
}

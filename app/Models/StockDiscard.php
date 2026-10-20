<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockDiscard extends Model
{
    use HasFactory;

    protected $fillable = [
        'discard_code',
        'branch_id',
        'supplier_id',
        'type',
        'requested_by',
        'reason',
        'total_loss_value',
        'status',
        'first_approved_by',
        'first_approved_at',
        'second_approved_by',
        'second_approved_at',
    ];

    protected $casts = [
        'total_loss_value' => 'decimal:2',
        'first_approved_at' => 'datetime',
        'second_approved_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function firstApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'first_approved_by');
    }

    public function secondApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'second_approved_by');
    }
}

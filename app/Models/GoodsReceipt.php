<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_code',
        'supplier_id',
        'user_id',
        'receipt_date',
        'total_amount',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Nhà cung cấp giao hàng
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * Nhân viên / Quản trị viên lập và nhận hàng
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Danh sách mặt hàng chi tiết trong phiếu nhập
     */
    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class, 'goods_receipt_id');
    }
}

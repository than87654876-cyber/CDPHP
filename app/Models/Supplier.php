<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'supply_category',
        'status',
        'notes',
    ];

    /**
     * Danh sách mặt hàng nguyên liệu mà nhà cung cấp này phụ trách
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'supplier_id');
    }

    /**
     * Danh sách các đợt phiếu nhập hàng từ nhà cung cấp này
     */
    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'supplier_id')->latest();
    }
}

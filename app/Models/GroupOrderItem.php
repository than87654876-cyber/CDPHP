<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_order_id',
        'member_name',
        'dish_id',
        'quantity',
        'note',
    ];

    public function groupOrder()
    {
        return $this->belongsTo(GroupOrder::class, 'group_order_id');
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class, 'dish_id');
    }
}

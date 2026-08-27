<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'host_id',
        'host_name',
        'status',
    ];

    public function items()
    {
        return $table = $this->hasMany(GroupOrderItem::class, 'group_order_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }
}

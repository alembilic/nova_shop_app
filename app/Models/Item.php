<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'order_id', 'order_id');
    }

    public function ordered_item()
    {
        return $this->hasOne(OrderedItem::class, 'order_id', 'order_id');
    }
}

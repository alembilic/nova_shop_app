<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_email', 'email');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function order_items()
    {
        return $this->hasMany(Item::class, 'order_id', 'order_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['first_purchase' => 'datetime'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_email', 'email');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}

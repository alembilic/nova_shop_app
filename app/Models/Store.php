<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            UserStoresPivot::class,
            'store_id', // Foreign key on users table...
            'id', // Foreign key on posts table...
            'id', // Local key on countries table...
            'user_id' // Local key on users table...
        );
    }
}

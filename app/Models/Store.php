<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store_products()
    {
        return $this->hasMany(StoreProduct::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function videos()
    {
        return $this->belongsToMany(Video::class,'video_categories');
    }

    public function store_products()
    {
        return $this->belongsToMany(StoreProduct::class,'product_categories');
    }
}
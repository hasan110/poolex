<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreProduct extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected $casts = [
        'files'=>'array'
    ];

    protected $with = ['store'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'product_categories');
    }
}

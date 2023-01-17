<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function user_plans()
    {
        return $this->hasMany(UserPlan::class);
    }
}

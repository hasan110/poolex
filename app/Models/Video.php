<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function episodes()
    {
        return $this->hasMany(Video::class , 'video_id');
    }

    public function serial()
    {
        return $this->belongsTo(Video::class , 'video_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class,'video_categories');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class , 'video_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Blog extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['slug' , 'date'];

    public function getSlugAttribute()
    {
        return str_replace(' ' , '-' , $this->title);
    }

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%d %B %Y');
    }
}

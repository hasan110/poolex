<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'feedback';
    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

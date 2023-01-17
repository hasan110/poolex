<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function starter()
    {
        return $this->belongsTo(User::class);
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class);
    }

    public function winner()
    {
        return $this->belongsTo(User::class);
    }
}

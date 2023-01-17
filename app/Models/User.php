<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function bank_account()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function user_plan()
    {
        return $this->hasOne(UserPlan::class);
    }

    public function user_referrals()
    {
        return $this->hasMany(UserReferral::class);
    }

    public function user_awards()
    {
        return $this->hasMany(UserAward::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function ticket_replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class);
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function store_products()
    {
        return $this->hasMany(StoreProduct::class);
    }

    public function cart_products()
    {
        return $this->belongsToMany(StoreProduct::class,'carts');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function favorite_products()
    {
        return $this->belongsToMany(StoreProduct::class,'favorites');
    }
}

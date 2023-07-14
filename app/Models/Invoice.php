<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['date' , 'status_title'];

    public function getDateAttribute()
    {
        return Jalalian::forge($this->created_at)->format('%Y/%m/%d');
    }

    public function getStatusTitleAttribute()
    {
        return $this->getStatusTitle($this->status);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class , 'seller_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function getStatusTitle($status)
    {
        $title = null;

        $titles = [
            'درحال بررسی موجودی',
            'تایید فروشنده',
            'آماده سازی سفارش',
            'آماده ارسال',
            'ارسال شده',
            'انصرافی',
        ];
        $status = (int) $status;
        if(in_array($status , [0,1,2,3,4,5])){
            $title = $titles[$status];
        }
        return $title;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = 'store_product';

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function store_product()
    {
        return $this->belongsTo(StoreProduct::class);
    }
}

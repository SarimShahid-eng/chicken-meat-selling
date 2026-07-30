<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HotelSale extends Model
{
    protected $fillable = [
        'voucher_no',
        'customer_id',
        'date',
        'total_weight',
        'total_amount',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(HotelSaleItem::class);
    }

    public function customerPayment(): HasOne
    {
        return $this->hasOne(CustomerPayment::class, 'sale_id')->where('reference', 'hotel_sale');
    }
}

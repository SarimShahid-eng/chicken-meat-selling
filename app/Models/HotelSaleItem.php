<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelSaleItem extends Model
{
    protected $fillable = [
        'hotel_sale_id',
        'product_id',
        'weight',
        'rate',
        'amount',
    ];

    public function hotelSale(): BelongsTo
    {
        return $this->belongsTo(HotelSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

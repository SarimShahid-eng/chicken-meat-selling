<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseVehicle extends Model
{
    protected $fillable = [
        'purchase_id',
        'name',
        'crate_qty',
        'total_weight',
        'weight_cut',
        'netweight'
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
    //
}

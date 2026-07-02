<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'voucher_no',
        'product_id',
        'supplier_id',
        'vehicle_no',
        'crate_qty',
        'total_weight',
        'weight_cut',
        'netweight',
        'rate',
        'rate_date',
        'total_amount',
        'date'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'product_id' => 'integer',
            'supplier_id' => 'integer',
            'rate_date' => 'date',
            'date'=>'date'
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
    public function supplierPayment(): HasOne
    {
        return $this->hasOne(SupplierPayment::class);
    }
}

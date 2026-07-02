<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'name',
        'phone_number',
        'description',
        'region_id',
        'opening_balance',
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
            'region_id' => 'integer',
            'opening_balance' => 'decimal:2',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class)->where('category','purchase');
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}

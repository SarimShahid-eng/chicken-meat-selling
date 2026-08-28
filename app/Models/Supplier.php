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
    public function getPreviousBalanceBeforePurchase(Purchase $purchase): float
    {
        $totalPurchases = $this->purchases()
            ->whereNotNull('rate')
            ->where(function ($query) use ($purchase) {
                $query->where('date', '<', $purchase->date)
                    ->orWhere(function ($q) use ($purchase) {
                        $q->where('date', '=', $purchase->date)
                            ->where('id', '<', $purchase->id);
                    });
            })
            ->sum('total_amount');

        // Total Credits before this purchase
        $credits = $this->supplierPayments()
            ->where(function ($query) use ($purchase) {
                $query->where('date', '<', $purchase->date)
                    ->orWhere(function ($q) use ($purchase) {
                        $q->where('date', '=', $purchase->date)
                            ->where('id', '<', $purchase->id);
                    });
            })
            ->where('payment_type', 'credit')
            ->sum('amount');

        // Total Debits before this purchase
        $debits = $this->supplierPayments()
            ->where(function ($query) use ($purchase) {
                $query->where('date', '<', $purchase->date)
                    ->orWhere(function ($q) use ($purchase) {
                        $q->where('date', '=', $purchase->date)
                            ->where('id', '<', $purchase->id);
                    });
            })
            ->where('payment_type', 'debit')
            ->sum('amount');

        return ($totalPurchases + $credits) - $debits;
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class)->where('category', 'purchase');
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

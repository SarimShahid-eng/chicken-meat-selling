<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone_number',
        'description',
        'region_id',
        'opening_balance',
        'category',
        'date',
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

    // * Get customer balance on or prior to a given date.
    public function getBalanceOnDate($date)
    {
        $targetDate = Carbon::parse($date)->format('Y-m-d');

        // 1. Total Sales Amount (Total Debits incurred by customer)
        $totalSales = $this->sales()
            ->where('date', '<=', $targetDate)
            ->sum('total_amount'); // Adjust column name if different (e.g., net_total)

        // 2. Payments Breakdown from customer_payments table
        // 'credit' reduces what customer owes (payments made)
        $credits = $this->customerPayments()
            ->where('date', '<=', $targetDate)
            ->where('payment_type', 'credit')
            ->sum('amount');

        // 'debit' increases balance (e.g. refunds or manual debit adjustments)
        $debits = $this->customerPayments()
            ->where('date', '<=', $targetDate)
            ->where('payment_type', 'debit')
            ->sum('amount');

        // Total Balance = Sales + Manual Debits - Credits
        return ($totalSales + $debits) - $credits;
    }

    public function getPreviousBalanceBeforeSale(Sale $sale): float
    {
        // Regular sales
        $totalSales = $this->sales()
            ->where('date', '<=', $sale->date)
            ->sum('total_amount');

        // Hotel sales - ADD THIS!
        $totalHotelSales = $this->hotelSales()
            ->where('date', '<=', $sale->date)
            ->sum('total_amount');

        // Payments - debit type (money OUT to customer, increases balance owed)
        $debits = $this->customerPayments()
            ->where('date', '<=', $sale->date)
            ->where('payment_type', 'debit')
            ->sum('amount');

        // Payments - credit type (money IN from customer, reduces balance owed)
        $credits = $this->customerPayments()
            ->where('date', '<=', $sale->date)
            ->where('payment_type', 'credit')
            ->sum('amount');


        return ($this->opening_balance ?? 0.00)
            + $totalSales
            + $totalHotelSales  // ADD hotel sales
            + $debits
            - $credits;
    }

    public function getPreviousBalanceBeforeHotelSale(HotelSale $hotelSale): float
    {
        $totalHotelSales = $this->hotelSales()
            ->whereNotNull('rate')
            ->where('date', '<=', $hotelSale->date)
            ->sum('total_amount');

        $debits = $this->customerPayments()
            ->where('date', '<=', $hotelSale->date)
            ->where('payment_type', 'debit')
            ->sum('amount');

        $credits = $this->customerPayments()
            ->where('date', '<=', $hotelSale->date)
            ->where('payment_type', 'credit')
            ->sum('amount');

        return ($this->opening_balance ?? 0.00) + ($totalHotelSales + $debits) - $credits;

    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function hotelSales(): HasMany
    {
        return $this->hasMany(HotelSale::class);
    }

    public function customerPayments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }
}

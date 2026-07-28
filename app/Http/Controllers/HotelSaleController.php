<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelSaleStoreRequest;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Product;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\DB;

class HotelSaleController extends Controller
{
    public function create()
    {
        $voucher_no = Sale::max('voucher_no') + 1;
        $products = Product::all(['id', 'name']);
        $customers = Customer::with('region')->where('category', 'hotel')->get();

        return view('hotel_sales.create', compact('products', 'customers', 'voucher_no'));
    }

    public function store(HotelSaleStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['netweight'] = $validated['total_weight'];
        $validated['weight_cut'] = 0;
        $validated['type'] = 'hotel';
        if (is_null($validated['amount_received'])) {
            $validated['amount_received'] = 0;
        }
        try {
            DB::transaction(function () use ($validated) {
                $sale = Sale::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated);

                CustomerPayment::updateOrCreate([
                    'sale_id' => $sale->id,
                ], [
                    'sale_id' => $sale->id,
                    'customer_id' => $validated['customer_id'],
                    'amount' => $validated['amount_received'],
                    'date' => $validated['date'],
                    'payment_type' => 'debit',
                    'type' => 'cash',
                ]);
            });
            $message = filled($validated['update_id']) ? 'updated' : 'created';

            return redirect()
                ->route('sales.index')
                ->with('toast_success', 'Sale has been '.$message.' successfully!');
        } catch (Exception $e) {
            return redirect()
                ->route('sales.index')
                ->with('toast_error', $e->getMessage());
        }
    }

    public function edit(Sale $sale)
    {
        $products = Product::all(['id', 'name']);
        $customers = Customer::with('region')->where('category', 'hotel')->get();

        return view('hotel_sales.create', compact('sale', 'products', 'customers'));
    }
}

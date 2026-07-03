<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalSales = Sale::sum('total_amount');
        // 2. Define your critical stock alert threshold in kilograms
        $lowStockThreshold = 5.0;

        // 3. Process entire inventory tracking dataset in a single calculation loop
        $stockCounts = Product::withSum('purchases', 'netweight')
            ->withSum('sales', 'netweight')
            ->get()
            ->reduce(function ($carry, $product) use ($lowStockThreshold) {
                $purchased = $product->purchases_sum_netweight ?? 0;
                $sold = $product->sales_sum_netweight ?? 0;
                $currentStock = $purchased - $sold;

                // Increment matching inventory data arrays dynamically
                if ($currentStock > 0) {
                    $carry['available']++;
                }
                if ($currentStock <= $lowStockThreshold) {
                    $carry['low_stock']++;
                }

                return $carry;
            }, ['available' => 0, 'low_stock' => 0]); // Initial fallback state values
        $customersCount = Customer::count();
        $suppliersCount = Supplier::count();
        // dd($customers);

        return view('dashboard', compact('stockCounts', 'totalSales','customersCount','suppliersCount'));

    }
}

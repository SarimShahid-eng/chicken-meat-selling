<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $totalSales = Sale::sum('total_amount');
        // 2. Define your critical stock alert threshold in kilograms
        $lowStockThreshold = 5.0;

        $products = Product::withSum('purchases', 'netweight')
            ->withSum('sales', 'netweight')
            ->get();
        $stockCounts = $products->reduce(function ($carry, $product) use ($lowStockThreshold) {
            $purchased = $product->purchases_sum_netweight ?? 0;
            $sold = $product->sales_sum_netweight ?? 0;
            $currentStock = $purchased - $sold;

            if ($currentStock > 0) {
                $carry['available']++;
            }
            if ($currentStock <= $lowStockThreshold) {
                $carry['low_stock']++;
            }

            return $carry;
        }, ['available' => 0, 'low_stock' => 0]);

        $stockAlerts = $products->map(function ($product) use ($lowStockThreshold) {
            $purchased = $product->purchases_sum_netweight ?? 0;
            $sold = $product->sales_sum_netweight ?? 0;

            $product->current_stock = $purchased - $sold;

            if ($product->current_stock <= 5) {
                $product->status = 'critical';
                $product->bg_color = 'bg-red-50 border-red-200';
                $product->icon_color = 'bg-red-100 text-red-600';
                $product->icon = 'fas fa-exclamation-circle';
                $product->message = "Only {$product->current_stock} kg left in stock";
            } elseif ($product->current_stock <= $lowStockThreshold) {
                $product->status = 'warning';
                $product->bg_color = 'bg-yellow-50 border-yellow-200';
                $product->icon_color = 'bg-yellow-100 text-yellow-600';
                $product->icon = 'fas fa-exclamation-triangle';
                $product->message = "Only {$product->current_stock} kg left in stock";
            } else {
                $product->status = 'normal';
                $product->bg_color = 'bg-green-50 border-green-200';
                $product->icon_color = 'bg-green-100 text-green-600';
                $product->icon = 'fas fa-check-circle';
                $product->message = "Stock level: {$product->current_stock} kg";
            }

            return $product;
        })->sortBy('current_stock')
            ->take(5)
            ->values();
        $customersCount = Customer::count();
        $suppliersCount = Supplier::count();
        // ChartData
        $salesTrendData = Sale::select('date', Sale::raw('SUM(total_amount) as total_sales'))
            ->where('date', '>=', Carbon::now()->subDays(6)->format('Y-m-d'))
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                // Ensure the date key string is formatted consistently as Y-m-d
                $formattedDate = Carbon::parse($item->date)->format('Y-m-d');

                return [$formattedDate => (float) $item->total_sales];
            });
        // Smooth data array alignment for ChartJS
        $chartLabels = [];
        $chartValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $dateString = $dateObj->format('Y-m-d');

            $chartLabels[] = $dateObj->format('D'); // 'Tue', 'Wed', etc.

            // Check if the date string exists in our keyed collection map
            $chartValues[] = $salesTrendData->has($dateString) ? $salesTrendData[$dateString] : 0;
        }
        // Only 5 Latest Sales
        $salesRecord = Sale::limit(5)
            ->latest()
            ->get();
        $purchaseTrendData = Purchase::select('date', Sale::raw('SUM(total_amount) as total_purchases'))
            ->where('date', '>=', Carbon::now()->subDays(6)->format('Y-m-d'))
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                // Ensure the date key string is formatted consistently as Y-m-d
                $formattedDate = Carbon::parse($item->date)->format('Y-m-d');

                return [$formattedDate => (float) $item->total_purchases];
            });
        // Smooth data array alignment for ChartJS
        $purchaseChartLabels = [];
        $purchaseChartValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateObj = Carbon::now()->subDays($i);
            $dateString = $dateObj->format('Y-m-d');

            $purchaseChartLabels[] = $dateObj->format('D'); // 'Tue', 'Wed', etc.

            // Check if the date string exists in our keyed collection map
            $purchaseChartValues[] = $purchaseTrendData->has($dateString) ? $purchaseTrendData[$dateString] : 0;
        }
        // Only 5 Latest Sales
        $salesRecord = Sale::limit(5)
            ->latest()
            ->get();

        return view('dashboard', compact('stockCounts',
            'stockAlerts',
            'totalSales',
            'salesRecord',
            'customersCount',
            'suppliersCount',
            'chartLabels',
            'chartValues',
            'purchaseChartLabels',
            'purchaseChartValues'
        ));

    }
}

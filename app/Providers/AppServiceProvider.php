<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('routeis', function (string|array $expression) {
            return request()->routeIs($expression);
        });
        View::composer('*', function ($view) {

            // 1. Calculate Today's Sales (Sum of totals processed today)
            $todaysSales = Sale::whereDate('date', today())->sum('total_amount')??0;
            $availableProductsCount = Product::withSum('purchases', 'netweight')
                ->withSum('sales', 'netweight')
                ->get()
                ->filter(function ($product) {        // withSum automatically names the attributes: {relation}_sum_{column}
                    $purchased = $product->purchases_sum_netweight ?? 0;
                    $sold = $product->sales_sum_netweight ?? 0;

                    return ($purchased - $sold) > 0;
                })
                ->count()??0;
            $view->with([
                'globalTodaysSales' => $todaysSales,
                'globalStockCount' => $availableProductsCount,
            ]);
        });
        // }
        //
    }
}

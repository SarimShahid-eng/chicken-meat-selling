<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerLedgerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierLedgerController;
use App\Http\Controllers\SupplierPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('login', function () {
//     // dd('ss');
//     return view('login');
// });
Route::middleware('auth')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });
    // Product
    Route::controller(ProductController::class)->name('products.')
        ->prefix('products')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{product}', 'edit')->name('edit');
        });
    // Supplier
    Route::controller(SupplierController::class)->name('suppliers.')
        ->prefix('suppliers')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{supplier}', 'edit')->name('edit');
        });
    // Supplier Payment
    Route::controller(SupplierPaymentController::class)->name('suppliersPayment.')
        ->prefix('suppliersPayment')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{payment}', 'edit')->name('edit');
        });

    // Customer
    Route::controller(CustomerController::class)->name('customers.')
        ->prefix('customers')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{customer}', 'edit')->name('edit');
        });

    // Customer Payment
    Route::controller(CustomerPaymentController::class)->name('customersPayment.')
        ->prefix('customersPayment')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{payment}', 'edit')->name('edit');
        });
    // Purchase
    Route::controller(PurchaseController::class)->name('purchases.')
        ->prefix('purchases')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{purchase}', 'edit')->name('edit');
            Route::get('show/{purchase}', 'show')->name('show');
            Route::post('update-rate/{purchase}', 'update_rate')->name('update_rate');
        });
    // Purchase
    Route::controller(SaleController::class)->name('sales.')
        ->prefix('sales')
        ->group(function () {
            Route::get('index', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('store', 'store')->name('store');
            Route::get('edit/{sale}', 'edit')->name('edit');
            Route::get('show/{sale}', 'show')->name('show');
        });
    Route::prefix('ledger')->name('ledger.')->group(function () {
        Route::controller(CustomerLedgerController::class)->group(function () {
            Route::get('customer', 'customer')->name('customer');
            Route::post('customerReport', 'customerReport')->name('customerReport');
        });
        Route::controller(SupplierLedgerController::class)->group(function () {
            Route::get('supplier', 'supplier')->name('supplier');
             Route::post('supplierReport', 'supplierReport')->name('supplierReport');
        });

    });
});
Route::controller(LoginController::class)
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('login', 'login')->name('login');
            Route::post('login', 'authenticate')->name('login.auth');
        });
        Route::get('logout', 'logout')->name('logout');
    });

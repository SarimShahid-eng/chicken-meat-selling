<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('dashboard', function () {
    return view('dashboard');
})->name('dashboard');
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

// Customer
Route::controller(CustomerController::class)->name('customers.')
    ->prefix('customers')
    ->group(function () {
        Route::get('index', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{customer}', 'edit')->name('edit');
    });

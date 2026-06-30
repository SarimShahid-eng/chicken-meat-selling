<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::withSum('purchases as total_purchased_weight', 'netweight')
            ->withSum('sales as total_sold_weight', 'netweight')
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', $searchTerm)
                        ->orWhere('description', 'LIKE', $searchTerm);
                });
            })
            ->paginate(10)

            ->through(function ($product) {
                $purchased = $product->total_purchased_weight ?? 0;
                $sold = $product->total_sold_weight ?? 0;

                // Append the stock calculation dynamically
                $product->current_stock_weight = $purchased - $sold;

                return $product;
            });

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();

        $product = Product::updateOrCreate(
            ['id' => $validated['update_id']],
            $validated);
        $message = filled($validated['update_id']) ? 'updated' : 'created';

        return redirect()
            ->route('products.index')
            ->with('toast_success', 'Product has been '.$message.' successfully!');

    }

    public function edit(Product $product)
    {
        //    dd($product->soldCreateWise, $product->is_active);
        return view('products.create', compact('product'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseStoreRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Region;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $purchases = Purchase::query()
            ->with('supplier', 'product')
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('description', 'LIKE', $searchTerm)
                        ->orWhere('type', 'LIKE', $searchTerm)

                        ->orWhereHas('supplier', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        })
                        ->orWhereHas('product', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        });
                });
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->input('date');
                $q->whereDate('date', $date);
            })
            ->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $voucher_no = Purchase::max('voucher_no') + 1;
        $products = Product::all();
        $suppliers = Supplier::with('region')->get();
        // dd(Region::all());

        return view('purchases.create', compact('products','suppliers','voucher_no'));
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseStoreRequest $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        return view('purchases.create');
        //
    }
}

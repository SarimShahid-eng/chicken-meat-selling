<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseStoreRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Region;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    $query->where('voucher_no', 'LIKE', $searchTerm)
                        ->orWhereHas('supplier', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        })
                        ->orWhereHas('product', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        });
                });
            })
            ->when($request->filled('supplier_id'), function ($q) use ($request) {
                $supplier = $request->input('supplier_id');
                $q->where(function ($query) use ($supplier) {
                    $query->WhereHas('supplier', function ($subQuery) use ($supplier) {
                        $subQuery->where('id', 'LIKE', $supplier);
                    });
                });
            })
            ->when($request->filled('product_id'), function ($q) use ($request) {
                $product = $request->input('product_id');
                $q->where(function ($query) use ($product) {
                    $query->WhereHas('product', function ($subQuery) use ($product) {
                        $subQuery->where('id', 'LIKE', $product);
                    });
                });
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->input('date');
                $q->whereDate('date', $date);
            })
            ->paginate(10);
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('purchases.index', compact('purchases', 'products', 'suppliers'));
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

        return view('purchases.create', compact('products', 'suppliers', 'voucher_no'));
        //
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['product', 'supplier.region']);
        $data = [
            'product' => [
                'name' => $purchase->product->name,
            ],
            'supplier' => [
                'name' => $purchase->supplier->name,
                'region' => [
                    'name' => $purchase->supplier->region->name,
                ],
            ],
            'voucher_no' => $purchase->voucher_no,
            'vehicle_no' => $purchase->vehicle_no,
            'crate_qty' => $purchase->crate_qty,
            'total_weight' => $purchase->total_weight,
            'weight_cut' => $purchase->weight_cut,
            'netweight' => $purchase->netweight,
            'rate' => $purchase->rate,
            'total_amount' => $purchase->total_amount,
            'created_at' => $purchase->created_at->format('d-m-Y'),
            'rate_date' => $purchase->rate_date ? $purchase->rate_date->format('Y-m-d') : 'rate not finalized',
        ];
        // dd($data);

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseStoreRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::transaction(function () use ($validated) {
                $purchase = Purchase::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated);
                SupplierPayment::create([
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $validated['supplier_id'],
                    'amount' => $validated['total_amount'],
                    'date' => $validated['date'],
                    'type' => 'cash',
                ]);
            });
            $message = filled($validated['update_id']) ? 'updated' : 'created';

            return redirect()
                ->route('purchases.index')
                ->with('toast_success', 'Purchase has been '.$message.' successfully!');
        } catch (Exception $e) {
            return redirect()
                ->route('purchases.index')
                ->with('toast_error', $e->getMessage());
        }

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

    public function update_rate(Purchase $purchase, Request $request)
    {
        $validated = $request->validate([
            'rate' => 'required',
        ]);
        $total_amount = $validated['rate'] * $purchase->netweight;
        $purchase->update([
            'rate' => $validated['rate'],
            'rate_date' => now(),
            'total_amount' => $total_amount,
        ]);
        $purchase->supplierPayment->update([
            'amount' => $total_amount,
        ]);

        return redirect()
            ->route('purchases.index')
            ->with('toast_success', 'Purchase rate has been updated successfully!');

    }
}

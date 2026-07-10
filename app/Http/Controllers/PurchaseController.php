<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseStoreRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $baseQuery = Purchase::query()
            ->with(['supplier','supplier.region', 'product'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('voucher_no', 'LIKE', $searchTerm)
                        ->orWhereHas('supplier', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        })
                        ->orWhereHas('supplier.region', function ($subQuery) use ($searchTerm) {
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
            ->when($request->filled('from_date') && $request->filled('to_date'), function ($q) use ($request) {
                $fromDate = Carbon::parse($request->input('from_date'))->startOfDay();

                // Parse the end date to the absolute end of that day (23:59:59)
                $toDate = Carbon::parse($request->input('to_date'))->endOfDay();

                // Direct, unnested execution
                $q->whereBetween('date', [$fromDate, $toDate]);
            })
            ->when($request->filled('from_date') && ! $request->filled('to_date'), function ($q) use ($request) {
                $fromDate = $request->input('from_date');
                $q->where(function ($query) use ($fromDate) {
                    $query->whereDate('date', $fromDate);
                });
            });
        $purchases = (clone $baseQuery)->paginate(10);
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            // dd('ss');
            $data = (clone $baseQuery)->get();
            $pdf = Pdf::loadView('purchases.exportPdf', compact('data'));

            return $pdf->download('purchases.pdf');
        }
        $products = Product::all(['id', 'name']);
        $suppliers = Supplier::with('region')->get();

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

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseStoreRequest $request)
    {
        $validated = $request->validated();
        $isRatePresent = filled($validated['rate']) ? true : false;
        try {
            DB::transaction(function () use ($validated, $isRatePresent) {
                $purchase = Purchase::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated);
                SupplierPayment::updateOrCreate(
                    ['purchase_id' => $purchase->id], // This links it to the specific purchase
                    [
                        'rate_finalized' => $isRatePresent,
                        'supplier_id' => $validated['supplier_id'],
                        'amount' => $validated['total_amount'],
                        'date' => $validated['date'],
                        'type' => 'cash',
                    ]
                );
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
        $products = Product::all(['id', 'name']);
        $suppliers = Supplier::with('region')->get();

        return view('purchases.create', compact('purchase', 'products', 'suppliers'));
    }

    public function update_rate(Purchase $purchase, Request $request)
    {
        $validated = $request->validate([
            'rate' => 'required|numeric',
        ]);
        if ($validated['rate'] <= 0) {
            return redirect()
                ->route('purchases.index')
                ->with('toast_error', 'Rate must be greater than 0');
        }
        $total_amount = $validated['rate'] * $purchase->netweight;
        $purchase->update([
            'rate' => $validated['rate'],
            'rate_date' => now(),
            'total_amount' => $total_amount,
        ]);
        $purchase->supplierPayment->update([
            'amount' => $total_amount,
            'rate_finalized' => true,
        ]);

        return redirect()
            ->route('purchases.index')
            ->with('toast_success', 'Purchase rate has been updated successfully!');

    }
}

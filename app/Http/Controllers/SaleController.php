<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleStoreRequest;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $baseQuery = Sale::query()
            ->with(['customer', 'customer.region', 'product'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('voucher_no', 'LIKE', $searchTerm)
                        ->orWhereHas('customer', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        })
                        ->orWhereHas('customer.region', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        })
                        ->orWhereHas('product', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
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
            })
            ->when($request->filled('customer_id'), function ($q) use ($request) {
                $customer = $request->input('customer_id');
                $q->where(function ($query) use ($customer) {
                    $query->WhereHas('customer', function ($subQuery) use ($customer) {
                        $subQuery->where('id', 'LIKE', $customer);
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
            });
        $sales = (clone $baseQuery)
            ->paginate(10);
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $data = (clone $baseQuery)->get();
            $pdf = Pdf::loadView('sales.exportPdf', compact('data'));

            return $pdf->download('sales.pdf');
        }
        $products = Product::all(['id', 'name']);
        $customers = Customer::with('region')->get();

        return view('sales.index', compact('sales', 'products', 'customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $voucher_no = Sale::max('voucher_no') + 1;
        $products = Product::all();
        $customers = Customer::with('region')->get();

        return view('sales.create', compact('products', 'customers', 'voucher_no'));
        //
    }

    public function show(Sale $sale)
    {
        $sale->load(['product', 'customer.region']);
        $data = [
            'product' => [
                'name' => $sale->product->name,
            ],
            'customer' => [
                'name' => $sale->customer->name,
                'region' => [
                    'name' => $sale->customer->region->name,
                ],
            ],
            'voucher_no' => $sale->voucher_no,
            'crate_qty' => $sale->crate_qty,
            'total_weight' => $sale->total_weight,
            'weight_cut' => $sale->weight_cut,
            'netweight' => $sale->netweight,
            'rate' => $sale->rate,
            'total_amount' => $sale->total_amount,
            'created_at' => $sale->created_at->format('d-m-Y'),
        ];

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleStoreRequest $request)
    {
        $validated = $request->validated();
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
                    'amount' => $validated['total_amount'],
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

        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $products = Product::all(['id', 'name']);
        $customers = Customer::with('region')->get();

        return view('sales.create', compact('sale', 'products', 'customers'));
    }

    public function update_rate(Sale $sale, Request $request)
    {
        $validated = $request->validate([
            'rate' => 'required',
        ]);
        $total_amount = $validated['rate'] * $sale->netweight;
        $sale->update([
            'rate' => $validated['rate'],
            'rate_date' => now(),
            'total_amount' => $total_amount,
        ]);
        $sale->customerPayment->update([
            'amount' => $total_amount,
        ]);

        return redirect()
            ->route('sales.index')
            ->with('toast_success', 'Sale rate has been updated successfully!');

    }
}

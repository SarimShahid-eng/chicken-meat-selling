<?php

namespace App\Http\Controllers;

use App\Http\Requests\HotelSaleStoreRequest;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\HotelSale;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelSaleController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = HotelSale::query()
            ->with(['customer', 'customerPayment', 'customer.region', 'items', 'items.product'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%' . $request->input('search') . '%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('voucher_no', 'LIKE', $searchTerm)
                        ->orWhereHas('customer', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        })
                        ->orWhereHas('customer.region', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        });
                    // ->orWhereHas('product', function ($subQuery) use ($searchTerm) {
                    //     $subQuery->where('name', 'LIKE', $searchTerm);
                    // });
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
                    $query->WhereHas('items.product', function ($subQuery) use ($product) {
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

        return view('hotel_sales.index', compact('sales', 'products', 'customers'));
    }

    public function create()
    {
        $voucher_no = HotelSale::max('voucher_no') + 1;
        $products = Product::all(['id', 'name']);
        $customers = Customer::with('region')->where('category', 'hotel')->get();

        return view('hotel_sales.create', compact('products', 'customers', 'voucher_no'));
    }

    public function store(HotelSaleStoreRequest $request)
    {
        $validated = $request->validated();
        if (is_null($validated['amount_received'])) {
            $validated['amount_received'] = 0;
        }
        $totalWeight = collect($validated['items'])->sum('weight');
        $totalAmount = collect($validated['items'])->sum('amount');
        $validated['total_weight'] = $totalWeight;
        $validated['total_amount'] = $totalAmount;
        $isUpdating = filled($validated['update_id']);
        try {
            $sale = DB::transaction(function () use ($validated, $isUpdating) {
                $sale = HotelSale::updateOrCreate(
                    ['id' => $validated['update_id']],
                    $validated
                );
                if ($isUpdating) {
                    $sale->items()->delete();
                }
                $sale->items()->createMany($validated['items']);
                CustomerPayment::updateOrCreate([
                    'sale_id' => $sale->id,
                    'reference' => 'hotel_sale'
                ], [
                    'sale_id' => $sale->id,
                    'reference' => 'hotel_sale',
                    'customer_id' => $validated['customer_id'],
                    'amount' => $validated['amount_received'],
                    'date' => $validated['date'],
                    'payment_type' => 'debit',
                    'type' => 'cash',
                ]);
                return $sale;
            });
            $message = $isUpdating ? 'updated' : 'created';
            return redirect()
                ->route('hotel_sales.create')
                ->with('sales_successful', 'Hotel Sale Added Successfully!')
                ->with('sale_id', $sale->id);
            // return redirect()
            // ->route('hotel_sales.create')
            //     ->with('toast_success', 'Sale has been ' . $message . ' successfully!');
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()
                ->route('hotel_sales.index')
                ->with('toast_error', $e->getMessage());
        }
    }

    public function edit(HotelSale $hotel_sale)
    {
        $sale = $hotel_sale;
        $products = Product::all(['id', 'name']);
        $customers = Customer::with('region')->where('category', 'hotel')->get();

        return view('hotel_sales.create', compact('sale', 'products', 'customers'));
    }

    public function show(HotelSale $hotelSale)
    {
        $hotelSale = $hotelSale->load([
            'customer:id,name,region_id',
            'customerPayment',
            'customer.region:id,name',
            'items:id,hotel_sale_id,product_id,amount,rate,weight',
            'items.product:id,name'
        ]);
        //    dd($hotelSale);
        $data = [
            'hotel_sale' => $hotelSale,
        ];

        return response()->json($data);
    }
    public function receipt(HotelSale $hotelSale, Request $request)
    {
        $customer = Customer::findOrFail($hotelSale->customer_id);
        $previousBalance = $customer->getPreviousBalanceBeforeHotelSale($hotelSale);
        if (filled($request->export) && $request->export === "pdf") {
            $pdf = Pdf::loadView('hotel_sale.receipt', compact('hotelSale', 'previousBalance'));
            return $pdf->download('receipt');
        }
        $hotelSale->load(['items', 'items.product']);
        // $hotelSale->items->transform(function ($item) {
        //     $item->amount = ($item->netweight ?? 0) * ($item->rate ?? 0);
        //     return $item;
        // });

        return view('hotel_sales.receipt', compact('hotelSale', 'previousBalance'));
    }
}

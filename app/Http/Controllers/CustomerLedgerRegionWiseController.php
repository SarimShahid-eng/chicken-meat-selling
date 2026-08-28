<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLedgerRegionWiseController extends Controller
{
    public function index()
    {
        $regions = Region::where('category', 'sale')->orderBy('name')->get();

        return view('ledger.customerRegionWiseReport', compact('regions'));
    }

    public function regionCustomerReport(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $regionId = $validated['region_id'];
        $regions = Region::where('category', 'sale')->orderBy('name')->get();
        // dd($regions);

        // Default dates if not supplied
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($validated['from_date'])->format('Y-m-d')
            : now()->startOfMonth()->format('Y-m-d');

        $toDate = $request->filled('to_date')
            ? Carbon::parse($validated['to_date'])->format('Y-m-d')
            : now()->format('Y-m-d');

        // Get all customer IDs linked to the selected region
        $customerIds = Customer::where('region_id', $regionId)->pluck('id');

        $ledgerEntries = collect();
        $openingBalance = 0.00;

        if ($regionId && $customerIds->isNotEmpty()) {
            // 1. Base opening balance sum for all customers in the region
            $baseOpeningBalance = Customer::where('region_id', $regionId)->sum('opening_balance');

            // Calculate prior sales and hotel sales before 'fromDate'
            $priorSales = DB::table('sales')
                ->whereIn('customer_id', $customerIds)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            $priorHotelSales = DB::table('hotel_sales')
                ->whereIn('customer_id', $customerIds)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            // Sum ALL payments received before 'fromDate'
            $priorPayments = CustomerPayment::whereIn('customer_id', $customerIds)
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            // Opening Balance Formula
            $openingBalance = $baseOpeningBalance + $priorSales + $priorHotelSales - $priorPayments;

            // 2. Build UNION queries with unique group_key and sort_order

            // Standard Sales Query (sort_order = 1)
            $sales = DB::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->select(
                    'sales.date',
                    'customers.name as customer_name',
                    DB::raw('"Regular Sale" as description'),
                    'sales.total_amount as debit',
                    DB::raw('NULL as credit'),
                    'sales.voucher_no as reference_id',
                    'sales.created_at',
                    DB::raw('"sale" as type'),
                    DB::raw('NULL as sale_id'),
                    DB::raw('NULL as reference'),
                    DB::raw('1 as sort_order'),
                    // Unique group key: e.g. cust_5_sale_10
                    DB::raw('CONCAT("cust_", sales.customer_id, "_sale_", sales.id) as group_key')
                )
                ->whereIn('sales.customer_id', $customerIds)
                ->whereBetween('sales.date', [$fromDate, $toDate]);

            // Hotel Sales Query (sort_order = 1)
            $hotelSales = DB::table('hotel_sales')
                ->join('customers', 'hotel_sales.customer_id', '=', 'customers.id')
                ->select(
                    'hotel_sales.date',
                    'customers.name as customer_name',
                    DB::raw('"Hotel Sale" as description'),
                    'hotel_sales.total_amount as debit',
                    DB::raw('NULL as credit'),
                    'hotel_sales.voucher_no as reference_id',
                    'hotel_sales.created_at',
                    DB::raw('"hotel_sale" as type'),
                    DB::raw('NULL as sale_id'),
                    DB::raw('NULL as reference'),
                    DB::raw('1 as sort_order'),
                    // Unique group key: e.g. cust_5_hotel_sale_4
                    DB::raw('CONCAT("cust_", hotel_sales.customer_id, "_hotel_sale_", hotel_sales.id) as group_key')
                )
                ->whereIn('hotel_sales.customer_id', $customerIds)
                ->whereBetween('hotel_sales.date', [$fromDate, $toDate]);

            // Customer Payments Query (sort_order = 2)
            $ledgerEntries = DB::table('customer_payments')
                ->join('customers', 'customer_payments.customer_id', '=', 'customers.id')
                ->select(
                    'customer_payments.date',
                    'customers.name as customer_name',
                    DB::raw('"Payment Received" as description'),
                    DB::raw('NULL as debit'),
                    'customer_payments.amount as credit',
                    'customer_payments.id as reference_id',
                    'customer_payments.created_at',
                    DB::raw('"payment" as type'),
                    'customer_payments.sale_id',
                    'customer_payments.reference',
                    DB::raw('2 as sort_order'),
                    // Dynamic matching group key
                    DB::raw('CASE
                    WHEN customer_payments.sale_id IS NOT NULL AND customer_payments.reference = "hotel_sale"
                        THEN CONCAT("cust_", customer_payments.customer_id, "_hotel_sale_", customer_payments.sale_id)
                    WHEN customer_payments.sale_id IS NOT NULL
                        THEN CONCAT("cust_", customer_payments.customer_id, "_sale_", customer_payments.sale_id)
                    ELSE CONCAT("cust_", customer_payments.customer_id, "_payment_", customer_payments.id)
                END as group_key')
                )
                ->whereIn('customer_payments.customer_id', $customerIds)
                ->whereBetween('customer_payments.date', [$fromDate, $toDate])
                ->union($sales)
                ->union($hotelSales)
                ->orderBy('date', 'asc')       // 1. Transaction Date
                ->orderBy('group_key', 'asc')  // 2. Pairs Sale and its Payment together per Customer
                ->orderBy('sort_order', 'asc') // 3. Guarantees Sale (1) appears BEFORE Payment (2)
                ->orderBy('created_at', 'asc') // 4. Fallback creation time
                ->get();
        }

        // PDF Export Support
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            // Give PHP enough memory and time to build a massive 2,000+ row PDF
            ini_set('memory_limit', '1024M');
            set_time_limit(300);
            $ledgerEntries->transform(function ($entry) {
                $entry->date_formatted = date('d-M-Y', strtotime($entry->date));
                return $entry;
            });
            $pdf = Pdf::loadView('ledger.regionCustomerExport', compact('ledgerEntries', 'openingBalance', 'fromDate', 'toDate', 'regionId'));

            return $pdf->download('region_customer_ledger.pdf');
        }

        return view('ledger.customerRegionWiseReport', compact('regions', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate', 'regionId'));
    }
}

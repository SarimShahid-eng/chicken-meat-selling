<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\HotelSale;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLedgerController extends Controller
{
    public function customer()
    {
        $customers = Customer::whereHas('customerPayments')->get(['id', 'name']);

        return view('ledger.customer', compact('customers'));
    }

    public function customerReport(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $customerId = $validated['customer_id'];

        // Fetch customers with any sales, hotel sales, or payment activity
        $customers = Customer::orderBy('name')
            ->where(function ($query) {
                $query->whereHas('customerPayments')
                    ->orWhereHas('sales')
                    ->orWhereHas('hotelSales');
            })
            ->get();

        $ledgerEntries = collect();
        $openingBalance = 0.00;

        if ($customerId) {
            $customer = Customer::findOrFail($customerId);

            // Determine date bounds
            if ($request->filled('from_date')) {
                $fromDate = Carbon::parse($validated['from_date'])->format('Y-m-d');
            } else {
                $firstPayment = $customer->customerPayments()->oldest('date')->first()?->date;
                $firstSale = DB::table('sales')->where('customer_id', $customerId)->min('date');
                $firstHotel = DB::table('hotel_sales')->where('customer_id', $customerId)->min('date');

                $dates = array_filter([$firstPayment ? $firstPayment->format('Y-m-d') : null, $firstSale, $firstHotel]);
                $fromDate = ! empty($dates) ? min($dates) : now()->format('Y-m-d');
            }

            $toDate = $request->filled('to_date')
                ? Carbon::parse($validated['to_date'])->format('Y-m-d')
                : now()->format('Y-m-d');

            // 1. Calculate prior baseline amounts before 'from_date'
            $priorSales = DB::table('sales')
                ->where('customer_id', $customerId)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            $priorHotelSales = DB::table('hotel_sales')
                ->where('customer_id', $customerId)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            $priorPayments = CustomerPayment::where('customer_id', $customerId)
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            // Opening balance calculation
            $openingBalance = $customer->opening_balance
                + $priorSales
                + $priorHotelSales
                - $priorPayments;

            // 2. Build UNION queries with group_id and sort_order

            $sales = DB::table('sales')
                ->select(
                    'date',
                    DB::raw('"Regular Sale" as description'),
                    'total_amount as debit',
                    DB::raw('NULL as credit'),
                    'voucher_no as reference_id',
                    'created_at',
                    DB::raw('"sale" as type'),
                    DB::raw('NULL as sale_id'),
                    DB::raw('NULL as reference'),
                    DB::raw('1 as sort_order'),
                    DB::raw('CONCAT("sale_", id) as group_key') // e.g. sale_1
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate]);

            // Hotel Sales (group_key = 'hotel_sale_{id}')
            $hotelSales = DB::table('hotel_sales')
                ->select(
                    'date',
                    DB::raw('"Hotel Sale" as description'),
                    'total_amount as debit',
                    DB::raw('NULL as credit'),
                    'voucher_no as reference_id',
                    'created_at',
                    DB::raw('"hotel_sale" as type'),
                    DB::raw('NULL as sale_id'),
                    DB::raw('NULL as reference'),
                    DB::raw('1 as sort_order'),
                    DB::raw('CONCAT("hotel_sale_", id) as group_key') // e.g. hotel_sale_1
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate]);

            // Customer Payments (group_key matches linked sale OR uses payment id)
            $ledgerEntries = DB::table('customer_payments')
                ->select(
                    'date',
                    DB::raw('"Payment Received" as description'),
                    DB::raw('NULL as debit'),
                    'amount as credit',
                    'id as reference_id',
                    'created_at',
                    DB::raw('"payment" as type'),
                    'sale_id',
                    'reference',
                    DB::raw('2 as sort_order'),
                    DB::raw('CASE
            WHEN sale_id IS NOT NULL AND reference = "hotel_sale" THEN CONCAT("hotel_sale_", sale_id)
            WHEN sale_id IS NOT NULL THEN CONCAT("sale_", sale_id)
            ELSE CONCAT("payment_", id)
        END as group_key')
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($sales)
                ->union($hotelSales)
                ->orderBy('date', 'asc')       // 1. Transaction Date
                ->orderBy('group_key', 'asc')  // 2. Groups specific Sale + its specific Linked Payment together
                ->orderBy('sort_order', 'asc') // 3. Forces Sale (1) BEFORE Payment (2)
                ->orderBy('created_at', 'asc') // 4. Fallback creation time
                ->get();

            if ($request->filled('export') && $request->input('export') === 'pdf') {
                $pdf = Pdf::loadView('ledger.customerExportPdf', compact('customers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));

                return $pdf->download('customer_ledger.pdf');
            }
        }

        return view('ledger.customer', compact('customers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));
    }

    public function customerInvoice(Customer $customer, $date)
    {
        // 1. Fetch Customer Info
        // $customer = Customer::findOrFail($customer_id);
        $customer_id = $customer->id;
        $from_date = $date;
        // 1. Fetch Customer

        // 2. Single-item Sales for this date (directly has fields like product, quantity, rate, price, total_amount)
        $sales = Sale::with('product')
            ->where('customer_id', $customer_id)
            ->whereDate('date', $from_date)
            ->get();

        // 3. Multi-item Hotel Sales for this date (has nested items relation)
        $hotelSales = HotelSale::with('items.product')
            ->where('customer_id', $customer_id)
            ->whereDate('date', $from_date)
            ->get();

        // Calculate current date sales totals
        $currentSalesTotal = $sales->sum('total_amount');

        foreach ($hotelSales as $hSale) {
            $currentSalesTotal += $hSale->items->sum('amount');
        }

        // 4. Calculate Previous Balance (strictly before $from_date)
        $prevSales = Sale::where('customer_id', $customer_id)
            ->whereDate('date', '<', $from_date)
            ->sum('total_amount');

        $prevHotelSales = HotelSale::whereHas('items')
            ->where('customer_id', $customer_id)
            ->whereDate('date', '<', $from_date)
            ->get()
            ->sum(function ($hSale) {
                return $hSale->items->sum('total');
            });

        $prevPayments = CustomerPayment::where('customer_id', $customer_id)
            ->whereDate('date', '<', $from_date)
            ->sum('amount');

        $previousBalance = ($customer->opening_balance ?? 0) + $prevSales + $prevHotelSales - $prevPayments;

        // 5. Total Payments received on current date
        $receivedToday = CustomerPayment::where('customer_id', $customer_id)
            ->whereDate('date', $from_date)
            ->sum('amount');

        $subtotal = $currentSalesTotal + $previousBalance;
        $remainingBalance = $subtotal - $receivedToday;

        // Subtotal & Net Balance Due
        $subtotal = $currentSalesTotal + $previousBalance;
        $netBalanceDue = $subtotal - $receivedToday;

        return view('customers.invoice', compact(
            'customer',
            'sales',
            'hotelSales',
            'from_date',
            'currentSalesTotal',
            'previousBalance',
            'subtotal',
            'receivedToday',
            'remainingBalance'
        ));

        // // Render PDF statement matching the ledger invoice format
        // $pdf = Pdf::loadView('invoices.customer_statement_invoice', compact(
        //     'customer',
        //     'sales',
        //     'hotelSales',
        //     'from_date',
        //     'currentSalesTotal',
        //     'previousBalance',
        //     'subtotal',
        //     'receivedToday',
        //     'netBalanceDue'
        // ));

        // return $pdf->download("Invoice_{$customer->name}_{$from_date}.pdf");
    }
}

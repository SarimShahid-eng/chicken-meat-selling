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
use Illuminate\Support\Str;

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

            $priorSales = DB::table('sales')
                ->where('customer_id', $customerId)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            $priorHotelSales = DB::table('hotel_sales')
                ->where('customer_id', $customerId)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            // Split payments by type
            $priorPaymentsCredit = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_type', 'credit')  // Money IN from customer (reduces balance)
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $priorPaymentsDebit = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_type', 'debit')   // Money OUT/refund (increases balance)
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            // Calculate opening balance correctly
            // Balance owed = Opening + Sales + HotelSales + Refunds - Payments Received
            $openingBalance = $customer->opening_balance
                + $priorSales          // Add: Customer owes for these sales
                + $priorHotelSales     // Add: Customer owes for these hotel sales
                + $priorPaymentsDebit  // Add: We gave refunds (increases amount owed)
                - $priorPaymentsCredit; // Subtract: Customer paid (reduces amount owed)

            $sales = DB::table('sales')
                ->select(
                    'id as source_id',
                    'product_id',
                    'date',
                    DB::raw('"Regular Sale" as description'),
                    'total_amount as debit', // FIXED: Sales mapped to Debit
                    DB::raw('NULL as credit'), // FIXED: NULL for Credit
                    'voucher_no as reference_id',
                    'created_at',
                    DB::raw('"sale" as type'),
                    DB::raw('NULL as sale_id'),
                    DB::raw('NULL as reference'),
                    DB::raw('1 as sort_order'),
                    DB::raw('CONCAT("sale_", id) as group_key'),
                    'crate_qty as sale_crate_qty',
                    'total_weight as sale_total_weight',
                    'weight_cut as sale_weight_cut',
                    'netweight as sale_netweight',
                    'rate as sale_rate'
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate]);

            // Hotel Sales — pad with matching NULLs
            $hotelSales = DB::table('hotel_sales')
                ->select(
                    'id as source_id',
                    DB::raw('NULL as product_id'),
                    'date',
                    DB::raw('"Hotel Sale" as description'),
                    'total_amount as debit', // FIXED: Hotel Sales mapped to Debit
                    DB::raw('NULL as credit'), // FIXED: NULL for Credit
                    'voucher_no as reference_id',
                    'created_at',
                    DB::raw('"hotel_sale" as type'),
                    DB::raw('NULL as sale_id'),
                    DB::raw('NULL as reference'),
                    DB::raw('1 as sort_order'),
                    DB::raw('CONCAT("hotel_sale_", id) as group_key'),
                    DB::raw('NULL as sale_crate_qty'),
                    DB::raw('NULL as sale_total_weight'),
                    DB::raw('NULL as sale_weight_cut'),
                    DB::raw('NULL as sale_netweight'),
                    DB::raw('NULL as sale_rate')
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate]);

            // Payments
            $ledgerEntries = DB::table('customer_payments')
                ->select(
                    DB::raw('NULL as source_id'),
                    DB::raw('NULL as product_id'),
                    'date',
                    DB::raw('"Payment" as description'),
                    DB::raw('CASE
                        WHEN payment_type = "debit" THEN amount
                        ELSE NULL
                    END as debit'), // FIXED: payment_type="debit" mapped to Debit
                    DB::raw('CASE
                        WHEN payment_type = "credit" THEN amount
                        ELSE NULL
                    END as credit'), // FIXED: payment_type="credit" mapped to Credit
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
                    END as group_key'),
                    DB::raw('NULL as sale_crate_qty'),
                    DB::raw('NULL as sale_total_weight'),
                    DB::raw('NULL as sale_weight_cut'),
                    DB::raw('NULL as sale_netweight'),
                    DB::raw('NULL as sale_rate')
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($sales)
                ->union($hotelSales)
                ->orderBy('date', 'asc')
                ->orderBy('group_key', 'asc')
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // Eager-load products for regular sales, items+products for hotel sales
            $saleIds = $ledgerEntries->where('type', 'sale')->pluck('source_id')->filter()->unique()->values();
            $hotelSaleIds = $ledgerEntries->where('type', 'hotel_sale')->pluck('source_id')->filter()->unique()->values();

            $salesWithProduct = Sale::with('product')->whereIn('id', $saleIds)->get()->keyBy('id');
            $hotelSalesWithItems = HotelSale::with('items.product')->whereIn('id', $hotelSaleIds)->get()->keyBy('id');

            $ledgerEntries = $ledgerEntries->map(function ($entry) use ($salesWithProduct, $hotelSalesWithItems) {
                $entry->product_name = null;
                $entry->items = collect();

                if ($entry->type === 'sale' && $entry->source_id && $salesWithProduct->has($entry->source_id)) {
                    $entry->product_name = $salesWithProduct->get($entry->source_id)->product->name ?? null;
                }

                if ($entry->type === 'hotel_sale' && $entry->source_id && $hotelSalesWithItems->has($entry->source_id)) {
                    $entry->items = $hotelSalesWithItems->get($entry->source_id)->items;
                }

                return $entry;
            });

            if ($request->filled('export') && $request->input('export') === 'pdf') {
                $pdf = Pdf::loadView('ledger.customerExportPdf', compact('customer', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));
                $customerName = Str::slug($customer->name ?? 'customer');
                $fileName = "{$customerName}.pdf";

                return $pdf->download($fileName);
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

    }
}

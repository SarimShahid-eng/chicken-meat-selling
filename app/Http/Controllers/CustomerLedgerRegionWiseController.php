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

        // Default dates if not supplied
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($validated['from_date'])->format('Y-m-d')
            : now()->startOfMonth()->format('Y-m-d');

        $toDate = $request->filled('to_date')
            ? Carbon::parse($validated['to_date'])->format('Y-m-d')
            : now()->format('Y-m-d');

        // Get all customer IDs linked to the selected region
        $customerIds = Customer::where('region_id', $regionId)->pluck('id');

        // 1. Base opening balance sum for all customers in the region
        $baseOpeningBalance = Customer::where('region_id', $regionId)->sum('opening_balance');

        // 2. Aggregate payments prior to $fromDate
        $priorPaymentsDebit = CustomerPayment::whereIn('customer_id', $customerIds)
            ->where('payment_type', 'debit')
            ->whereDate('date', '<', $fromDate)
            ->sum('amount');

        $priorPaymentsCredit = CustomerPayment::whereIn('customer_id', $customerIds)
            ->where('payment_type', 'credit')
            ->whereDate('date', '<', $fromDate)
            ->sum('amount');

        // Customer Formula: Opening Balance + Prior Debits - Prior Credits
        $openingBalance = $baseOpeningBalance + $priorPaymentsDebit - $priorPaymentsCredit;

        // 3. Query Sales Invoices for region's customers (Debits increase balance)
        $sales = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'sales.date',
                'customers.name as customer_name',
                DB::raw('CONCAT("Sale Invoice - ", customers.name) as description'),
                'sales.total_amount as debit',
                DB::raw('NULL as credit'),
                'sales.voucher_no as reference_id',
                'sales.created_at'
            )
            ->whereIn('sales.customer_id', $customerIds)
            ->whereBetween('sales.date', [$fromDate, $toDate]);

        // 4. Query Customer Payments for region's customers (Credits reduce balance)
        $ledgerEntries = DB::table('customer_payments')
            ->join('customers', 'customer_payments.customer_id', '=', 'customers.id')
            ->select(
                'customer_payments.date',
                'customers.name as customer_name',
                DB::raw('CONCAT("Payment (", customer_payments.payment_type, ") - ", customers.name) as description'),
                DB::raw('NULL as debit'),
                'customer_payments.amount as credit',
                'customer_payments.id as reference_id',
                'customer_payments.created_at'
            )
            ->whereIn('customer_payments.customer_id', $customerIds)
            ->whereIn('customer_payments.payment_type', ['credit'])
            ->whereBetween('customer_payments.date', [$fromDate, $toDate])
            ->union($sales)
            ->orderBy('created_at', 'asc')
            ->get();

        // PDF Export Support
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('ledger.regionCustomerExport', compact('regions', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate', 'regionId'));

            return $pdf->download('region_customer_ledger.pdf');
        }

        return view('ledger.customerRegionWiseReport', compact('regions', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate', 'regionId'));
    }
}

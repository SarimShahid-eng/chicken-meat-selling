<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
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

        $customers = Customer::orderBy('name')
            ->whereHas('customerPayments')
            ->get();
        $ledgerEntries = collect();
        $openingBalance = 0.00;

        if ($customerId) {
            $customer = Customer::findOrFail($customerId);
            if ($request->filled('from_date')) {
                $fromDate = Carbon::parse($validated['from_date'])->format('Y-m-d');
            } else {
                $firstPayment = $customer->customerPayments()->oldest()->first();
                $fromDate = $firstPayment->date->format('Y-m-d');
            }
            if ($request->filled('to_date')) {
                $toDate = Carbon::parse($validated['to_date'])->format('Y-m-d');
            } else {
                $toDate = now()->format('Y-m-d');
            }
            // 1. Calculate the initial baseline before 'from_date'
            // $priorSales = Sale::where('customer_id', $customerId)
            //     ->whereDate('date', '<', $fromDate)
            //     ->sum('total_amount'); // Changed from total_amount to reflect common sales naming

            $priorPaymentsDebit = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_type', 'debit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $priorPaymentsCredit = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_type', 'credit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            // Customer Accounting Formula: Opening Balance + Sales (Debit) + Payments Debit - Payments Credit
            // Note: If standard sales are Debits, adjust signs below based on your payment_type definitions
            $openingBalance = $customer->opening_balance + $priorPaymentsDebit - $priorPaymentsCredit;

            // 2. Fetch target range records via UNION sorted precisely by timestamp
            $sales = DB::table('sales')
                ->select(
                    'date',
                    DB::raw('"Sale Invoice" as description'),
                    'total_amount as debit', // Sales increase customer balance (Debit)
                    DB::raw('NULL as credit'),
                    'voucher_no as reference_id', // Swapped voucher_no to invoice_no
                    'created_at'
                )
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate]);

            $ledgerEntries = DB::table('customer_payments')
                ->select(
                    'date',
                    DB::raw('CONCAT("Payment (", payment_type, ")") as description'),
                    DB::raw('NULL as debit'),
                    'amount as credit', // Standard customer payments reduce balance (Credit)
                    'id as reference_id',
                    'created_at'
                )
                ->where('customer_id', $customerId)
                // If you want both debit and credit payments in the ledger timeline, use whereIn or remove this check
                ->whereIn('payment_type', ['credit'])
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($sales)
                ->orderBy('created_at', 'asc') // Keeps everything perfectly sequential by time
                ->get();
            if ($request->filled('export') && $request->input('export') === 'pdf') {
                $pdf = Pdf::loadView('ledger.customerExportPdf', compact('customers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));

                return $pdf->download('customersLedger.pdf');
            }

            return view('ledger.customer', compact('customers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));

        }
    }
}

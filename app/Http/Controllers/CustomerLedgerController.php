<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerLedgerController extends Controller
{
    public function customer()
    {
        $customers = Customer::all(['id', 'name']);

        return view('ledger.customer', compact('customers'));
    }

    public function customerReport(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);
        $supplierId = $validated['customer_id'];
        $fromDate = Carbon::parse($validated['from_date'])->format('Y-m-d');
        $toDate = Carbon::parse($validated['to_date'])->format('Y-m-d');

        $suppliers = Customer::orderBy('name')->get();
        $ledgerEntries = collect();
        $openingBalance = 0.00;

        if ($customerId) {
            $supplier = Customer::findOrFail($customerId);

            // 1. Calculate the initial baseline before 'from_date'
            $priorPurchases = Purchase::where('customer_id', $customerId)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            $priorPaymentsDebit = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_type', 'debit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $priorPaymentsCredit = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_type', 'credit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');
            // Customer Base Formula: Opening Balance + Purchases (Credit) - Payments (Debit)
            $openingBalance = $supplier->opening_balance + $priorPurchases + $priorPaymentsCredit - $priorPaymentsDebit;

            // // 2. Fetch target range records via UNION
            $purchases = DB::table('purchases')
                ->select('date', DB::raw('"Purchase" as description'), DB::raw('NULL as debit'), 'total_amount as credit', 'voucher_no as reference_id')
                ->where('customer_id', $customerId)
                ->whereBetween('date', [$fromDate, $toDate]);

            $ledgerEntries = DB::table('supplier_payments')
                ->select('date', DB::raw('CONCAT("Payment (", type, ")") as description'), 'amount as debit', DB::raw('NULL as credit'), 'id as reference_id')
                ->where('customer_id', $customerId)
                ->where('payment_type', 'debit')
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($purchases)
                ->orderBy('date', 'asc')
                ->get();

            return view('ledger.supplier', compact('suppliers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));

        }
    }
}

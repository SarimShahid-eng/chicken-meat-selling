<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierLedgerController extends Controller
{
    public function supplier()
    {
        $suppliers = Supplier::all(['id', 'name']);

        return view('ledger.supplier', compact('suppliers'));
    }

    public function supplierReport(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);
        $supplierId = $validated['supplier_id'];
        $fromDate = Carbon::parse($validated['from_date'])->format('Y-m-d');
        $toDate = Carbon::parse($validated['to_date'])->format('Y-m-d');

        $suppliers = Supplier::orderBy('name')->get();
        $ledgerEntries = collect();
        $openingBalance = 0.00;

        if ($supplierId) {
            $supplier = Supplier::findOrFail($supplierId);

            // 1. Calculate the initial baseline before 'from_date'
            $priorPurchases = Purchase::where('supplier_id', $supplierId)
                ->whereDate('date', '<', $fromDate)
                ->sum('total_amount');

            $priorPaymentsDebit = SupplierPayment::where('supplier_id', $supplierId)
                ->where('payment_type', 'debit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $priorPaymentsCredit = SupplierPayment::where('supplier_id', $supplierId)
                ->where('payment_type', 'credit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');
            // Supplier Base Formula: Opening Balance + Purchases (Credit) - Payments (Debit)
            $openingBalance = $supplier->opening_balance + $priorPurchases + $priorPaymentsCredit - $priorPaymentsDebit;

            // // 2. Fetch target range records via UNION
            $purchases = DB::table('purchases')
                ->select('date', DB::raw('"Purchase" as description'), DB::raw('NULL as debit'), 'total_amount as credit', 'voucher_no as reference_id')
                ->where('supplier_id', $supplierId)
                ->whereBetween('date', [$fromDate, $toDate]);

            $ledgerEntries = DB::table('supplier_payments')
                ->select('date', DB::raw('CONCAT("Payment (", type, ")") as description'), 'amount as debit', DB::raw('NULL as credit'), 'id as reference_id')
                ->where('supplier_id', $supplierId)
                ->where('payment_type', 'debit')
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($purchases)
                ->orderBy('date', 'asc')
                ->get();

            return view('ledger.supplier', compact('suppliers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));
        }

    }
    //
}

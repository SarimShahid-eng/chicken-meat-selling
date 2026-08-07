<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierLedgerController extends Controller
{
    public function supplier()
    {
        $suppliers = Supplier::whereHas('supplierPayments')->get(['id', 'name']);

        return view('ledger.supplier', compact('suppliers'));
    }

    public function supplierReport(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $supplierId = $validated['supplier_id'];

        $suppliers = Supplier::orderBy('name')
            ->whereHas('supplierPayments')
            ->get();

        $ledgerEntries = collect();
        $openingBalance = 0.00;

        if ($supplierId) {
            $supplier = Supplier::findOrFail($supplierId);

            if ($request->filled('from_date')) {
                $fromDate = Carbon::parse($validated['from_date'])->format('Y-m-d');
            } else {
                $firstPayment = $supplier->supplierPayments()->oldest()->first();
                $fromDate = $firstPayment ? $firstPayment->date->format('Y-m-d') : now()->format('Y-m-d');
            }

            if ($request->filled('to_date')) {
                $toDate = Carbon::parse($validated['to_date'])->format('Y-m-d');
            } else {
                $toDate = now()->format('Y-m-d');
            }

            // Calculate opening balance before $fromDate
            // Calculate opening balance before $fromDate
            $priorPaymentsDebit = SupplierPayment::where('supplier_id', $supplierId)
                ->where('payment_type', 'debit') // Change to 'type' if your DB column is named 'type'
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $priorPaymentsCredit = SupplierPayment::where('supplier_id', $supplierId)
                ->where('payment_type', 'credit') // Change to 'type' if your DB column is named 'type'
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $openingBalance = $supplier->opening_balance + $priorPaymentsCredit - $priorPaymentsDebit;
            // 1. Fetch Purchases Query (Set sort_order = 1 so Purchases come FIRST)
            $purchases = DB::table('purchases')
                ->select(
                    'date',
                    DB::raw('"Purchase" as description'),
                    DB::raw('NULL as debit'),
                    'total_amount as credit',
                    'voucher_no as reference_id',
                    'created_at',
                    DB::raw('1 as sort_order') // High Priority (First)
                )
                ->where('supplier_id', $supplierId)
                ->whereBetween('date', [$fromDate, $toDate]);

            // 2. Fetch Supplier Payments Query (Set sort_order = 2 so Payments come SECOND)
            $ledgerEntries = DB::table('supplier_payments')
                ->select(
                    'date',
                    DB::raw('CONCAT("Payment (", type, ")") as description'),
                    // Dynamic mapping based on payment_type
                    DB::raw('CASE WHEN payment_type = "debit" THEN amount ELSE NULL END as debit'),
                    DB::raw('CASE WHEN payment_type = "credit" THEN amount ELSE NULL END as credit'),
                    'id as reference_id',
                    'created_at',
                    DB::raw('2 as sort_order')
                )
                ->where('supplier_id', $supplierId)
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($purchases)
                ->orderBy('date', 'asc')
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($request->filled('export') && $request->input('export') === 'pdf') {
                $pdf = Pdf::loadView('ledger.supplierExportPdf', compact('supplier', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));

                return $pdf->download('suppliersLedger.pdf');
            }

            return view('ledger.supplier', compact('suppliers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));
        }

        return view('ledger.supplier', compact('suppliers', 'ledgerEntries', 'openingBalance'));
    }

    public function supplierInvoice(Supplier $supplier, $from_date)
    {
        // 1. Fetch Supplier
        // $supplier = Supplier::findOrFail($supplier_id);
        $supplier_id = $supplier->id;
        // $from_date=

        // 2. Get Purchases for this specific date
        $purchases = Purchase::with('product')
            ->where('supplier_id', $supplier_id)
            ->whereDate('date', $from_date)
            ->get();

        // 3. Current Purchases Total
        $currentPurchaseTotal = $purchases->sum('total_amount');

        // 4. Calculate Previous Balance (Purchases minus Payments made to Supplier before $from_date)
        $prevPurchases = Purchase::where('supplier_id', $supplier_id)
            ->whereDate('date', '<', $from_date)
            ->sum('total_amount');

        $prevPayments = SupplierPayment::where('supplier_id', $supplier_id)
            ->whereDate('date', '<', $from_date)
            ->sum('amount');

        $previousBalance = ($supplier->opening_balance ?? 0) + $prevPurchases - $prevPayments;

        // 5. Payments made to Supplier on current date
        $paidToday = SupplierPayment::where('supplier_id', $supplier_id)
            ->whereDate('date', $from_date)
            ->sum('amount');

        $subtotal = $currentPurchaseTotal + $previousBalance;
        $remainingBalance = $subtotal - $paidToday;

        // Return view directly (or load via DomPDF if exporting directly)
        return view('suppliers.invoice', compact(
            'supplier',
            'purchases',
            'from_date',
            'currentPurchaseTotal',
            'previousBalance',
            'subtotal',
            'paidToday',
            'remainingBalance'
        ));
    }
}

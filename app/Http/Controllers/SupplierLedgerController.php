<?php

namespace App\Http\Controllers;

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
        $fromDate = Carbon::parse($validated['from_date'])->format('Y-m-d');
        $toDate = Carbon::parse($validated['to_date'])->format('Y-m-d');

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
                $fromDate = $firstPayment->date->format('Y-m-d');
            }
            if ($request->filled('to_date')) {
                $toDate = Carbon::parse($validated['to_date'])->format('Y-m-d');
            } else {
                $toDate = now()->format('Y-m-d');
            }

            $priorPaymentsDebit = SupplierPayment::where('supplier_id', $supplierId)
                ->where('payment_type', 'debit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $priorPaymentsCredit = SupplierPayment::where('supplier_id', $supplierId)
                ->where('payment_type', 'credit')
                ->whereDate('date', '<', $fromDate)
                ->sum('amount');

            $openingBalance = $supplier->opening_balance + $priorPaymentsCredit - $priorPaymentsDebit;
            // 2. Fetch target range records via UNION (including created_at for chronologically exact sorting)
            $purchases = DB::table('purchases')
                ->select(
                    'date',
                    DB::raw('"Purchase" as description'),
                    DB::raw('NULL as debit'),
                    'total_amount as credit',
                    'voucher_no as reference_id',
                    'created_at' // Added here
                )
                ->where('supplier_id', $supplierId)
                ->whereBetween('date', [$fromDate, $toDate]);

            $ledgerEntries = DB::table('supplier_payments')
                ->select(
                    'date',
                    DB::raw('CONCAT("Payment (", type, ")") as description'),
                    'amount as debit',
                    DB::raw('NULL as credit'),
                    'id as reference_id',
                    'created_at' // Added here to match the union layout
                )
                ->where('supplier_id', $supplierId)
                ->where('payment_type', 'debit')
                ->whereBetween('date', [$fromDate, $toDate])
                ->union($purchases)
                ->orderBy('created_at', 'asc') // This solves the same-day sequencing issue
                ->get();
            if ($request->filled('export') && $request->input('export') === 'pdf') {
                $pdf = Pdf::loadView('ledger.supplierExportPdf', compact('suppliers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));

                return $pdf->download('suppliersLedger.pdf');
            }

            return view('ledger.supplier', compact('suppliers', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate'));
        }

    }
}

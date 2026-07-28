<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierLedgerRegionWiseController extends Controller
{
    public function index()
    {
        $regions = Region::where('category', 'purchase')->orderBy('name')->get();

        return view('ledger.supplierRegionWiseReport', compact('regions'));
    }

    public function regionSupplierReport(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
        ]);

        $regionId = $validated['region_id'];
        $regions = Region::where('category', 'purchase')->orderBy('name')->get();

        $fromDate = $request->filled('from_date')
            ? Carbon::parse($validated['from_date'])->format('Y-m-d')
            : now()->startOfMonth()->format('Y-m-d');

        $toDate = $request->filled('to_date')
            ? Carbon::parse($validated['to_date'])->format('Y-m-d')
            : now()->format('Y-m-d');

        // Get all supplier IDs in the selected region
        $supplierIds = Supplier::where('region_id', $regionId)->pluck('id');

        // 1. Calculate Combined Initial Opening Balances for all suppliers in the region
        $baseOpeningBalance = Supplier::where('region_id', $regionId)->sum('opening_balance');

        // 2. Calculate prior Debit & Credit payments before the selected 'fromDate'
        $priorPaymentsDebit = SupplierPayment::whereIn('supplier_id', $supplierIds)
            ->where('payment_type', 'debit')
            ->whereDate('date', '<', $fromDate)
            ->sum('amount');

        $priorPaymentsCredit = SupplierPayment::whereIn('supplier_id', $supplierIds)
            ->where('payment_type', 'credit')
            ->whereDate('date', '<', $fromDate)
            ->sum('amount');

        $openingBalance = $baseOpeningBalance + $priorPaymentsCredit - $priorPaymentsDebit;

        // 3. Query Purchases in date range joining supplier name
        $purchases = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'purchases.date',
                'suppliers.name as supplier_name',
                DB::raw('CONCAT("Purchase - ", suppliers.name) as description'),
                DB::raw('NULL as debit'),
                'purchases.total_amount as credit',
                'purchases.voucher_no as reference_id',
                'purchases.created_at'
            )
            ->whereIn('purchases.supplier_id', $supplierIds)
            ->whereBetween('purchases.date', [$fromDate, $toDate]);

        // 4. Query Payments in date range joining supplier name
        $ledgerEntries = DB::table('supplier_payments')
            ->join('suppliers', 'supplier_payments.supplier_id', '=', 'suppliers.id')
            ->select(
                'supplier_payments.date',
                'suppliers.name as supplier_name',
                DB::raw('CONCAT("Payment (", supplier_payments.type, ") - ", suppliers.name) as description'),
                'supplier_payments.amount as debit',
                DB::raw('NULL as credit'),
                'supplier_payments.id as reference_id',
                'supplier_payments.created_at'
            )
            ->whereIn('supplier_payments.supplier_id', $supplierIds)
            ->where('supplier_payments.payment_type', 'debit')
            ->whereBetween('supplier_payments.date', [$fromDate, $toDate])
            ->union($purchases)
            ->orderBy('created_at', 'asc')
            ->get();

        // PDF Export Support
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $pdf = Pdf::loadView('ledger.regionSupplierExport', compact('regions', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate', 'regionId'));

            return $pdf->download('region_supplier_ledger.pdf');
        }

        return view('ledger.supplierRegionWiseReport', compact('regions', 'ledgerEntries', 'openingBalance', 'fromDate', 'toDate', 'regionId'));
    }
}

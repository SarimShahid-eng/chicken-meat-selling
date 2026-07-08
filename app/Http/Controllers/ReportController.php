<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierPayment;

class ReportController extends Controller
{
    public function general()
    {
        // 1. Core Global Aggregations
        $totalSalesVolume = Sale::sum('total_amount');
        $totalPurchasesVolume = Purchase::sum('total_amount');

        $totalCustomerCollections = CustomerPayment::where('payment_type', 'credit')->sum('amount');
        $totalSupplierDisbursements = SupplierPayment::where('payment_type', 'debit')->sum('amount');

        // 2. Active Accounts Receivable Summary (Customers)
        // Formula: Opening Balance + Total Sales (Debits) - Total Payments (Credits)
        $customerSummaries = Customer::all()->map(function ($customer) {
            // $sales = DB::table('sales')->where('customer_id', $customer->id)->sum('total_amount');
            $sales = Sale::where('customer_id')->sum('total_amount');
            $payments = CustomerPayment::where('customer_id', $customer->id)
                ->where('payment_type', 'credit')
                ->sum('amount');

            $netBalance = $customer->opening_balance + $sales - $payments;

            return (object) [
                'id' => $customer->id,
                'name' => $customer->name,
                'net_balance' => $netBalance,
            ];
        })->filter(fn ($c) => abs($c->net_balance) > 0.01); // Only show active balances
// iGtT6+2)0)f2?@[}
        // 3. Active Accounts Payable Summary (Suppliers)
        // Formula: Opening Balance + Total Purchases (Credits) - Total Payments (Debits)
        $supplierSummaries = Supplier::all()->map(function ($supplier) {
            $purchases = Purchase::where('supplier_id', $supplier->id)->sum('total_amount');
            $payments = SupplierPayment::where('supplier_id', $supplier->id)
                ->where('payment_type', 'debit')
                ->sum('amount');

            $netBalance = $supplier->opening_balance + $purchases - $payments;

            return (object) [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'net_balance' => $netBalance,
            ];
        })->filter(fn ($s) => abs($s->net_balance) > 0.01);

        // 4. Calculate Net Position Metrics
        $totalReceivables = $customerSummaries->sum('net_balance');
        $totalPayables = $supplierSummaries->sum('net_balance');

        // 1. Calculate Core Income Metrics
        $grossRevenue = Sale::sum('total_amount');
        // dd($grossRevenue);
        $costOfGoods = Purchase::sum('total_amount');

        // Gross Profit Formula: Revenue - Cost of Goods Sold
        $grossProfit = $grossRevenue - $costOfGoods;

        // Calculate profit margin percentage (avoid division by zero)
        $profitMarginPercent = $grossRevenue > 0 ? ($grossProfit / $grossRevenue) * 100 : 0;

        return view('reports.general', compact(
            'totalSalesVolume',
            'totalPurchasesVolume',
            'totalCustomerCollections',
            'totalSupplierDisbursements',
            'totalReceivables',
            'totalPayables',
            'customerSummaries',
            'supplierSummaries',
            'profitMarginPercent',
            'grossProfit',
            'grossRevenue',
            'costOfGoods'
        ));
    }

    public function profit() {}
}

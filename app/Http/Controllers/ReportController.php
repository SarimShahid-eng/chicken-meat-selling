<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function general()
    {
        // 1. Core Global Revenue & Cost Aggregations (Including Hotel Sales)
        $regularSalesVolume = Sale::sum('total_amount');
        $hotelSalesVolume = DB::table('hotel_sales')->sum('total_amount');
        $totalSalesVolume = $regularSalesVolume + $hotelSalesVolume;

        $totalPurchasesVolume = Purchase::sum('total_amount');

        $totalCustomerCollections = CustomerPayment::sum('amount');
        $totalSupplierDisbursements = SupplierPayment::sum('amount');

        // 2. Optimized Active Receivables Summary (Customers)
        // Pre-aggregate sales, hotel sales, and payments to avoid N+1 query loops
        $salesByCustomer = DB::table('sales')
            ->select('customer_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('customer_id')
            ->pluck('total', 'customer_id');

        $hotelSalesByCustomer = DB::table('hotel_sales')
            ->select('customer_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('customer_id')
            ->pluck('total', 'customer_id');

        $paymentsByCustomer = DB::table('customer_payments')
            ->select('customer_id', DB::raw('SUM(amount) as total'))
            ->groupBy('customer_id')
            ->pluck('total', 'customer_id');

        $customerSummaries = Customer::all()->map(function ($customer) use ($salesByCustomer, $hotelSalesByCustomer, $paymentsByCustomer) {
            $sales = $salesByCustomer->get($customer->id, 0);
            $hotelSales = $hotelSalesByCustomer->get($customer->id, 0);
            $payments = $paymentsByCustomer->get($customer->id, 0);

            // Net Balance = Opening Balance + Total Sales + Total Hotel Sales - Payments
            $netBalance = $customer->opening_balance + $sales + $hotelSales - $payments;

            return (object) [
                'id' => $customer->id,
                'name' => $customer->name,
                'net_balance' => $netBalance,
            ];
        })->filter(fn ($c) => abs($c->net_balance) > 0.01);

        // 3. Optimized Active Payables Summary (Suppliers)
        $purchasesBySupplier = DB::table('purchases')
            ->select('supplier_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('supplier_id')
            ->pluck('total', 'supplier_id');

        $supplierPaymentsBySupplier = DB::table('supplier_payments')
            ->select('supplier_id', DB::raw('SUM(amount) as total'))
            ->groupBy('supplier_id')
            ->pluck('total', 'supplier_id');

        $supplierSummaries = Supplier::all()->map(function ($supplier) use ($purchasesBySupplier, $supplierPaymentsBySupplier) {
            $purchases = $purchasesBySupplier->get($supplier->id, 0);
            $payments = $supplierPaymentsBySupplier->get($supplier->id, 0);

            // Supplier Balance = Opening Balance + Purchases - Payments Made
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

        // 5. Income & Profitability Metrics
        $grossRevenue = $totalSalesVolume;
        $costOfGoods = $totalPurchasesVolume;
        $grossProfit = $grossRevenue - $costOfGoods;

        // Calculate profit margin percentage safely
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

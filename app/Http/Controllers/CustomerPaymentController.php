<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerPaymentRequestStore;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = CustomerPayment::query()
            ->whereDoesntHave('sale')
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    // 1. Search description from its own local table
                    $query->where('description', 'LIKE', $searchTerm)
                        ->orWhere('type', 'LIKE', $searchTerm)

                          // 2. Search name from the related 'supplier' table
                        ->orWhereHas('customer', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        });
                });
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->input('date');
                $q->whereDate('date', $date);
            });
        $customersPayments = (clone $baseQuery)
            ->paginate(10);
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            $data = (clone $baseQuery)->get();
            $pdf = Pdf::loadView('customerPayment.exportPdf', compact('data'));

            return $pdf->download('customerPayment.pdf');
        }

        return view('customerPayment.index', compact('customersPayments'));
    }

    public function create()
    {
        $customers = Customer::select(['id', 'name'])->get();

        return view('customerPayment.create', compact('customers'));
    }

    public function store(CustomerPaymentRequestStore $request)
    {
        $validated = $request->validated();
        $validated['payment_type'] = 'credit';
        CustomerPayment::updateOrCreate(
            ['id' => $validated['update_id']],
            $validated);
        $message = filled($validated['update_id']) ? 'updated' : 'created';

        return redirect()
            ->route('customersPayment.index')
            ->with('toast_success', 'Customer payment has been '.$message.' successfully!');
    }

    public function edit(CustomerPayment $payment)
    {
        $customers = Customer::select('id', 'name')->get();

        return view('customerPayment.create', compact('payment', 'customers'));
    }
    //
}

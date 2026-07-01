<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierPaymentRequestStore;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        $suppliersPayments = SupplierPayment::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    // 1. Search description from its own local table
                    $query->where('description', 'LIKE', $searchTerm)
                        ->orWhere('type', 'LIKE', $searchTerm)

                          // 2. Search name from the related 'supplier' table
                        ->orWhereHas('supplier', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        });
                });
            })
            ->when($request->filled('date'), function ($q) use ($request) {
                $date = $request->input('date');
                $q->whereDate('date', $date);
            })
            ->paginate(10);

        return view('suppliersPayment.index', compact('suppliersPayments'));
    }

    public function create()
    {
        $suppliers = Supplier::select(['id', 'name'])->get();

        return view('suppliersPayment.create', compact('suppliers'));
    }

    public function store(SupplierPaymentRequestStore $request)
    {
        $validated = $request->validated();
        SupplierPayment::updateOrCreate(
            ['id' => $validated['update_id']],
            $validated);
        $message = filled($validated['update_id']) ? 'updated' : 'created';

        return redirect()
            ->route('suppliersPayment.index')
            ->with('toast_success', 'Supplier payment has been '.$message.' successfully!');
    }

    public function edit(SupplierPayment $payment)
    {
        $suppliers = Supplier::select('id', 'name')->get();

        return view('suppliersPayment.create', compact('payment', 'suppliers'));
    }
    //
}

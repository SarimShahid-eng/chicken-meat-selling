<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Models\Region;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Supplier::query()
            ->with(['supplierPayments' => function ($q) {
                $q->where('rate_finalized', true);
            }, 'region'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', $searchTerm)
                        ->orWhere('description', 'LIKE', $searchTerm)
                        ->orWhereHas('region', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'LIKE', $searchTerm);
                        });

                });
            });
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            // dd('ss');
            $data = (clone $baseQuery)->get()
            // ->collection()
                ->map(function ($supplier) {
                    $credit = $supplier->supplierPayments->where('payment_type', 'credit')
                        ->sum('amount') ?? 0;
                    $debit = $supplier->supplierPayments->where('payment_type', 'debit')
                        ->sum('amount') ?? 0;

                    $supplier->current_balance = $supplier->opening_balance + $credit - $debit;

                    return $supplier;
                });
            $pdf = Pdf::loadView('suppliers.exportPdf', compact('data'));

            return $pdf->download('suppliers.pdf');
        }
        $suppliers = (clone $baseQuery)
            // ->when($request-)
            ->paginate(10)

            ->through(function ($supplier) {
                $credit = $supplier->supplierPayments->where('payment_type', 'credit')
                    ->sum('amount') ?? 0;
                $debit = $supplier->supplierPayments->where('payment_type', 'debit')
                    ->sum('amount') ?? 0;

                $supplier->current_balance = $supplier->opening_balance + $credit - $debit;

                return $supplier;
            });

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $regions = Region::where('category', 'purchase')->get();

        return view('suppliers.create', compact('regions'));
    }

    public function store(SupplierStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;

        Supplier::updateOrCreate(
            ['id' => $validated['update_id']],
            $validated);
        $message = filled($validated['update_id']) ? 'updated' : 'created';

        return redirect()
            ->route('suppliers.index')
            ->with('toast_success', 'Supplier has been '.$message.' successfully!');

    }

    public function edit(Supplier $supplier)
    {
        $regions = Region::where('category', 'purchase')->get();

        return view('suppliers.create', compact('supplier', 'regions'));
    }
}

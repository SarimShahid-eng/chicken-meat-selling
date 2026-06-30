<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierStoreRequest;
use App\Models\Region;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', $searchTerm)
                        ->orWhere('description', 'LIKE', $searchTerm);
                });
            })
            ->paginate(10);

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

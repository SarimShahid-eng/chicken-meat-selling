<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Region;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $searchTerm = '%'.$request->input('search').'%';

                $q->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', $searchTerm)
                        ->orWhere('description', 'LIKE', $searchTerm);
                });
            })
            ->paginate(10);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $regions = Region::where('category', 'sale')->get();

        return view('customers.create', compact('regions'));
    }

    public function store(CustomerStoreRequest $request)
    {
        $validated = $request->validated();
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;

        Customer::updateOrCreate(
            ['id' => $validated['update_id']],
            $validated);
        $message = filled($validated['update_id']) ? 'updated' : 'created';

        return redirect()
            ->route('customers.index')
            ->with('toast_success', 'Customer has been '.$message.' successfully!');

    }

    public function edit(Customer $customer)
    {
        // dd($customer);
        $regions = Region::where('category', 'purchase')->get();

        return view('customers.create', compact('customer', 'regions'));
    }
}

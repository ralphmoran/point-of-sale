<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::withCount(['users', 'products', 'sales'])->orderBy('name')->paginate(15);
        return Inertia::render('Stores/Index', ['stores' => $stores]);
    }

    public function create()
    {
        return Inertia::render('Stores/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        Store::create($validated);

        return redirect()->route('stores.index')->with('success', 'Store created.');
    }

    public function edit(Store $store)
    {
        return Inertia::render('Stores/Edit', ['store' => $store]);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        $store->update($validated);

        return redirect()->route('stores.index')->with('success', 'Store updated.');
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Store deleted.');
    }
}

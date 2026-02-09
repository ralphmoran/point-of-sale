<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->user()->store_id;

        $sales = Sale::with('user')
            ->where('store_id', $storeId)
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->cashier, fn ($q, $c) => $q->where('user_id', $c))
            ->when($request->payment_method, fn ($q, $m) => $q->where('payment_method', $m))
            ->withCount('items')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $cashiers = User::where('store_id', $storeId)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'cashiers' => $cashiers,
            'filters' => $request->only('date_from', 'date_to', 'cashier', 'payment_method'),
        ]);
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'items.product']);

        return Inertia::render('Sales/Show', ['sale' => $sale]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();

        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');

        $allowed = ['id', 'created_at', 'total', 'payment_method'];
        if (!in_array($sortField, $allowed)) $sortField = 'created_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $sales = Sale::with(['user', 'store'])
            ->when(!$isAdmin, fn ($q) => $q->where('store_id', $user->store_id))
            ->when($isAdmin && $request->store, fn ($q, $s) => $q->where('store_id', $s))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->when($request->cashier, fn ($q, $c) => $q->where('user_id', $c))
            ->when($request->payment_method, fn ($q, $m) => $q->where('payment_method', $m))
            ->withCount('items')
            ->orderBy($sortField, $sortDir)
            ->paginate(20)
            ->withQueryString();

        $cashiers = $isAdmin
            ? User::orderBy('name')->get(['id', 'name'])
            : User::where('store_id', $user->store_id)->orderBy('name')->get(['id', 'name']);

        $stores = $isAdmin ? Store::orderBy('name')->get(['id', 'name']) : collect();

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'cashiers' => $cashiers,
            'stores' => $stores,
            'isAdmin' => $isAdmin,
            'filters' => $request->only('date_from', 'date_to', 'cashier', 'payment_method', 'store', 'sort', 'dir'),
        ]);
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'store', 'items.product']);

        return Inertia::render('Sales/Show', ['sale' => $sale]);
    }
}

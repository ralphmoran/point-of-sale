<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->isAdmin();
        $storeId = $request->store ?: ($isAdmin ? null : $user->store_id);

        $from = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(30);
        $to = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $baseQuery = fn () => Sale::query()
            ->when($storeId, fn ($q) => $q->where('store_id', $storeId))
            ->when(!$storeId && !$isAdmin, fn ($q) => $q->where('store_id', $user->store_id))
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);

        // Sales over time
        $salesOverTime = $baseQuery()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by user
        $salesByUser = $baseQuery()
            ->select('user_id', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->with('user:id,name')
            ->get()
            ->map(fn ($s) => [
                'name' => $s->user->name,
                'revenue' => (float) $s->revenue,
                'count' => $s->count,
            ]);

        // Payment method breakdown
        $paymentBreakdown = $baseQuery()
            ->select('payment_method', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Sales by store (admin only)
        $salesByStore = $isAdmin ? Sale::query()
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->select('store_id', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('store_id')
            ->with('store:id,name')
            ->get()
            ->map(fn ($s) => [
                'name' => $s->store->name,
                'revenue' => (float) $s->revenue,
                'count' => $s->count,
            ]) : collect();

        // Summary stats for period
        $periodTotal = $baseQuery()->sum('total');
        $periodCount = $baseQuery()->count();

        $stores = $isAdmin ? Store::orderBy('name')->get(['id', 'name']) : collect();

        return Inertia::render('Reports/Index', [
            'salesOverTime' => $salesOverTime,
            'salesByUser' => $salesByUser,
            'paymentBreakdown' => $paymentBreakdown,
            'salesByStore' => $salesByStore,
            'periodTotal' => number_format($periodTotal, 2),
            'periodCount' => $periodCount,
            'stores' => $stores,
            'isAdmin' => $isAdmin,
            'filters' => [
                'date_from' => $from->format('Y-m-d'),
                'date_to' => $to->format('Y-m-d'),
                'store' => $storeId,
            ],
        ]);
    }
}

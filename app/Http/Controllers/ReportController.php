<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->user()->store_id;
        $from = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subDays(30);
        $to = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        // Sales over time
        $salesOverTime = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by user
        $salesByUser = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
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
        $paymentBreakdown = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->select('payment_method', DB::raw('SUM(total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('payment_method')
            ->get();

        // Summary stats for period
        $periodTotal = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->sum('total');

        $periodCount = Sale::where('store_id', $storeId)
            ->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->count();

        return Inertia::render('Reports/Index', [
            'salesOverTime' => $salesOverTime,
            'salesByUser' => $salesByUser,
            'paymentBreakdown' => $paymentBreakdown,
            'periodTotal' => number_format($periodTotal, 2),
            'periodCount' => $periodCount,
            'filters' => [
                'date_from' => $from->format('Y-m-d'),
                'date_to' => $to->format('Y-m-d'),
            ],
        ]);
    }
}

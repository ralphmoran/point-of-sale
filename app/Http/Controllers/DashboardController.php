<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->user()->store_id;
        $today = Carbon::today();

        $todaySales = Sale::where('store_id', $storeId)->whereDate('created_at', $today)->sum('total');
        $todayCount = Sale::where('store_id', $storeId)->whereDate('created_at', $today)->count();
        $totalRevenue = Sale::where('store_id', $storeId)->sum('total');
        $totalSales = Sale::where('store_id', $storeId)->count();
        $productCount = Product::where('store_id', $storeId)->count();
        $avgSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Recent sales
        $recentSales = Sale::with('user')
            ->where('store_id', $storeId)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($sale) => [
                'id' => $sale->id,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'cashier' => $sale->user->name,
                'created_at' => $sale->created_at->format('M d, Y H:i'),
            ]);

        // Sales last 7 days for sparkline
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) use ($storeId) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('M d'),
                'total' => (float) Sale::where('store_id', $storeId)->whereDate('created_at', $date)->sum('total'),
            ];
        });

        // Recently active users
        $activeUsers = User::where('store_id', $storeId)
            ->withCount('sales')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'sales_count' => $u->sales_count,
            ]);

        return Inertia::render('Dashboard', [
            'stats' => [
                'todaySales' => number_format($todaySales, 2),
                'todayCount' => $todayCount,
                'totalRevenue' => number_format($totalRevenue, 2),
                'productCount' => $productCount,
                'avgSale' => number_format($avgSale, 2),
            ],
            'recentSales' => $recentSales,
            'last7Days' => $last7Days,
            'activeUsers' => $activeUsers,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->user()->store_id;

        $products = Product::where('store_id', $storeId)
            ->where('stock', '>', 0)
            ->with('category')
            ->withSum('saleItems', 'quantity')
            ->orderByDesc('sale_items_sum_quantity')
            ->orderBy('name')
            ->get();

        $categories = Category::where('store_id', $storeId)->orderBy('name')->get();

        $stores = $request->user()->isAdmin()
            ? Store::orderBy('name')->get(['id', 'name'])
            : collect();

        return Inertia::render('Pos/Terminal', [
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'customer_name' => 'nullable|string|max:255',
            'table_number' => 'nullable|string|max:10',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $total = 0;
                $saleItems = [];

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name}");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    $saleItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];

                    $product->decrement('stock', $item['quantity']);
                }

                $sale = Sale::create([
                    'user_id' => $request->user()->id,
                    'store_id' => $request->user()->store_id,
                    'total' => $total,
                    'payment_method' => $request->payment_method,
                    'order_type' => $request->order_type,
                    'customer_name' => $request->customer_name,
                    'table_number' => $request->table_number,
                ]);

                foreach ($saleItems as $saleItem) {
                    $sale->items()->create($saleItem);
                }

                return $sale;
            });

            return redirect()->route('pos')->with('success', "Sale #{$sale->id} completed — \${$sale->total}");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function switchStore(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $request->user()->update(['store_id' => $request->store_id]);

        return back()->with('success', 'Store switched successfully.');
    }
}

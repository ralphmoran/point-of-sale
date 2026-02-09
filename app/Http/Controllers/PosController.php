<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
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
            ->orderBy('name')
            ->get();

        $categories = Category::where('store_id', $storeId)->orderBy('name')->get();

        return Inertia::render('Pos/Terminal', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card',
        ]);

        return DB::transaction(function () use ($request) {
            $total = 0;
            $saleItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    return back()->withErrors(['items' => "Insufficient stock for {$product->name}"]);
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
            ]);

            foreach ($saleItems as $saleItem) {
                $sale->items()->create($saleItem);
            }

            return redirect()->route('pos')->with('success', "Sale #$sale->id completed — \$$total");
        });
    }
}

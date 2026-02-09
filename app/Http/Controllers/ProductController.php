<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $storeId = $request->user()->store_id;

        $products = Product::where('store_id', $storeId)
            ->with('category')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%"))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::where('store_id', $storeId)->orderBy('name')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only('search', 'category'),
        ]);
    }

    public function create(Request $request)
    {
        $categories = Category::where('store_id', $request->user()->store_id)->orderBy('name')->get();
        return Inertia::render('Products/Create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validated['store_id'] = $request->user()->store_id;
        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Request $request, Product $product)
    {
        $categories = Category::where('store_id', $request->user()->store_id)->orderBy('name')->get();
        return Inertia::render('Products/Edit', ['product' => $product, 'categories' => $categories]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}

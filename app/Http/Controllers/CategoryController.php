<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('store_id', $request->user()->store_id)
            ->withCount('products')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return Inertia::render('Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $validated['store_id'] = $request->user()->store_id;
        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Categories/Edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}

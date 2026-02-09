<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('store')->withCount('sales')->orderBy('name')->paginate(15);
        return Inertia::render('Users/Index', ['users' => $users]);
    }

    public function create()
    {
        $stores = Store::orderBy('name')->get();
        return Inertia::render('Users/Create', ['stores' => $stores]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'pin' => 'required|digits:4',
            'role' => 'required|in:admin,cashier',
            'store_id' => 'required|exists:stores,id',
        ]);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $stores = Store::orderBy('name')->get();
        return Inertia::render('Users/Edit', ['user' => $user, 'stores' => $stores]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'pin' => 'nullable|digits:4',
            'role' => 'required|in:admin,cashier',
            'store_id' => 'required|exists:stores,id',
        ]);

        if (empty($validated['pin'])) {
            unset($validated['pin']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}

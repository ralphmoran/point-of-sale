<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Stores
        $store1 = Store::create(['name' => 'Downtown Store', 'address' => '123 Main St, New York, NY', 'phone' => '(555) 123-4567']);
        $store2 = Store::create(['name' => 'Mall Outlet', 'address' => '456 Commerce Blvd, New York, NY', 'phone' => '(555) 987-6543']);

        // Users — PIN is 1234 for all demo users
        User::create(['name' => 'Admin User', 'email' => 'admin@pos.com', 'pin' => '1234', 'role' => 'admin', 'store_id' => $store1->id]);
        User::create(['name' => 'Jane Smith', 'email' => 'jane@pos.com', 'pin' => '1234', 'role' => 'cashier', 'store_id' => $store1->id]);
        User::create(['name' => 'Bob Wilson', 'email' => 'bob@pos.com', 'pin' => '1234', 'role' => 'cashier', 'store_id' => $store2->id]);
        User::create(['name' => 'Alice Johnson', 'email' => 'alice@pos.com', 'pin' => '1234', 'role' => 'admin', 'store_id' => $store2->id]);

        // Categories for Store 1
        $electronics = Category::create(['name' => 'Electronics', 'store_id' => $store1->id]);
        $clothing = Category::create(['name' => 'Clothing', 'store_id' => $store1->id]);
        $food = Category::create(['name' => 'Food & Beverages', 'store_id' => $store1->id]);
        $accessories = Category::create(['name' => 'Accessories', 'store_id' => $store1->id]);

        // Categories for Store 2
        $electronics2 = Category::create(['name' => 'Electronics', 'store_id' => $store2->id]);
        $home = Category::create(['name' => 'Home & Garden', 'store_id' => $store2->id]);
        $sports = Category::create(['name' => 'Sports', 'store_id' => $store2->id]);

        // Products for Store 1
        $products1 = [
            ['name' => 'Wireless Headphones', 'sku' => 'EL-001', 'price' => 79.99, 'stock' => 50, 'category_id' => $electronics->id, 'store_id' => $store1->id],
            ['name' => 'USB-C Cable', 'sku' => 'EL-002', 'price' => 12.99, 'stock' => 200, 'category_id' => $electronics->id, 'store_id' => $store1->id],
            ['name' => 'Phone Case', 'sku' => 'EL-003', 'price' => 24.99, 'stock' => 100, 'category_id' => $electronics->id, 'store_id' => $store1->id],
            ['name' => 'Bluetooth Speaker', 'sku' => 'EL-004', 'price' => 49.99, 'stock' => 30, 'category_id' => $electronics->id, 'store_id' => $store1->id],
            ['name' => 'T-Shirt (M)', 'sku' => 'CL-001', 'price' => 19.99, 'stock' => 75, 'category_id' => $clothing->id, 'store_id' => $store1->id],
            ['name' => 'Jeans', 'sku' => 'CL-002', 'price' => 49.99, 'stock' => 40, 'category_id' => $clothing->id, 'store_id' => $store1->id],
            ['name' => 'Hoodie', 'sku' => 'CL-003', 'price' => 39.99, 'stock' => 35, 'category_id' => $clothing->id, 'store_id' => $store1->id],
            ['name' => 'Coffee (Large)', 'sku' => 'FB-001', 'price' => 4.99, 'stock' => 500, 'category_id' => $food->id, 'store_id' => $store1->id],
            ['name' => 'Sandwich', 'sku' => 'FB-002', 'price' => 7.99, 'stock' => 50, 'category_id' => $food->id, 'store_id' => $store1->id],
            ['name' => 'Water Bottle', 'sku' => 'FB-003', 'price' => 1.99, 'stock' => 300, 'category_id' => $food->id, 'store_id' => $store1->id],
            ['name' => 'Sunglasses', 'sku' => 'AC-001', 'price' => 29.99, 'stock' => 60, 'category_id' => $accessories->id, 'store_id' => $store1->id],
            ['name' => 'Watch', 'sku' => 'AC-002', 'price' => 149.99, 'stock' => 15, 'category_id' => $accessories->id, 'store_id' => $store1->id],
        ];

        // Products for Store 2
        $products2 = [
            ['name' => 'Tablet Stand', 'sku' => 'EL-101', 'price' => 34.99, 'stock' => 45, 'category_id' => $electronics2->id, 'store_id' => $store2->id],
            ['name' => 'Wireless Mouse', 'sku' => 'EL-102', 'price' => 29.99, 'stock' => 80, 'category_id' => $electronics2->id, 'store_id' => $store2->id],
            ['name' => 'Keyboard', 'sku' => 'EL-103', 'price' => 59.99, 'stock' => 40, 'category_id' => $electronics2->id, 'store_id' => $store2->id],
            ['name' => 'Desk Lamp', 'sku' => 'HG-001', 'price' => 39.99, 'stock' => 25, 'category_id' => $home->id, 'store_id' => $store2->id],
            ['name' => 'Plant Pot', 'sku' => 'HG-002', 'price' => 14.99, 'stock' => 60, 'category_id' => $home->id, 'store_id' => $store2->id],
            ['name' => 'Yoga Mat', 'sku' => 'SP-001', 'price' => 24.99, 'stock' => 35, 'category_id' => $sports->id, 'store_id' => $store2->id],
            ['name' => 'Water Bottle (Sport)', 'sku' => 'SP-002', 'price' => 9.99, 'stock' => 100, 'category_id' => $sports->id, 'store_id' => $store2->id],
            ['name' => 'Resistance Bands', 'sku' => 'SP-003', 'price' => 19.99, 'stock' => 50, 'category_id' => $sports->id, 'store_id' => $store2->id],
        ];

        $allProducts = [];
        foreach (array_merge($products1, $products2) as $p) {
            $allProducts[] = Product::create($p);
        }

        // Generate sales over the last 30 days
        $users = User::all();
        $now = Carbon::now();

        for ($day = 29; $day >= 0; $day--) {
            $date = $now->copy()->subDays($day);
            $salesCount = rand(3, 8);

            for ($s = 0; $s < $salesCount; $s++) {
                $user = $users->random();
                $storeProducts = collect($allProducts)->where('store_id', $user->store_id);

                $saleItems = [];
                $total = 0;
                $itemCount = rand(1, 4);

                $selectedProducts = $storeProducts->random(min($itemCount, $storeProducts->count()));
                foreach ($selectedProducts as $product) {
                    $qty = rand(1, 3);
                    $subtotal = $product->price * $qty;
                    $total += $subtotal;
                    $saleItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                }

                $sale = Sale::create([
                    'user_id' => $user->id,
                    'store_id' => $user->store_id,
                    'total' => $total,
                    'payment_method' => rand(0, 1) ? 'cash' : 'card',
                    'created_at' => $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                    'updated_at' => $date->copy()->addHours(rand(8, 20))->addMinutes(rand(0, 59)),
                ]);

                foreach ($saleItems as $item) {
                    $sale->items()->create($item);
                }
            }
        }
    }
}

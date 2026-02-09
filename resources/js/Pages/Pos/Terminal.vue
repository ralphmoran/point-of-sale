<template>
    <AppLayout title="POS Terminal">
        <Head title="POS Terminal" />

        <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-8rem)]">
            <!-- Products Panel -->
            <div class="flex-1 flex flex-col min-h-0">
                <!-- Search + Category Filter -->
                <div class="flex flex-col sm:flex-row gap-3 mb-4">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input v-model="search" type="text" placeholder="Search products..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <button @click="selectedCategory = null"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="!selectedCategory ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'">
                            All
                        </button>
                        <button v-for="cat in categories" :key="cat.id" @click="selectedCategory = cat.id"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="selectedCategory === cat.id ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'">
                            {{ cat.name }}
                        </button>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        <button v-for="product in filteredProducts" :key="product.id" @click="addToCart(product)"
                            class="bg-white border border-gray-200 rounded-xl p-4 text-left hover:border-primary-300 hover:shadow-sm transition group">
                            <div class="w-full aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                            <p class="text-xs text-gray-400 mb-1">{{ product.category?.name }}</p>
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-bold text-primary-600">${{ product.price }}</p>
                                <span class="text-xs text-gray-400">{{ product.stock }} left</span>
                            </div>
                        </button>
                    </div>
                    <p v-if="filteredProducts.length === 0" class="text-center text-gray-400 py-12">No products found</p>
                </div>
            </div>

            <!-- Cart Panel -->
            <div class="w-full lg:w-96 flex flex-col bg-white border border-gray-200 rounded-xl">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">Cart</h2>
                    <button v-if="cart.length" @click="cart = []" class="text-xs text-red-500 hover:text-red-700">Clear all</button>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="(item, idx) in cart" :key="item.product.id" class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ item.product.name }}</p>
                            <p class="text-xs text-gray-400">${{ item.product.price }} each</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button @click="updateQty(idx, -1)" class="w-7 h-7 rounded-md border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50">-</button>
                            <span class="w-8 text-center text-sm font-medium">{{ item.quantity }}</span>
                            <button @click="updateQty(idx, 1)" class="w-7 h-7 rounded-md border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50">+</button>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 w-16 text-right">${{ (item.product.price * item.quantity).toFixed(2) }}</p>
                        <button @click="cart.splice(idx, 1)" class="text-gray-300 hover:text-red-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <p v-if="!cart.length" class="text-center text-gray-400 py-8 text-sm">Cart is empty</p>
                </div>

                <!-- Totals + Checkout -->
                <div class="p-4 border-t border-gray-200 space-y-3">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Items</span>
                        <span>{{ totalItems }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>${{ totalPrice }}</span>
                    </div>

                    <!-- Payment Method -->
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="paymentMethod = 'cash'"
                            class="py-2 rounded-lg text-sm font-medium border transition-colors"
                            :class="paymentMethod === 'cash' ? 'bg-green-50 border-green-300 text-green-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            Cash
                        </button>
                        <button @click="paymentMethod = 'card'"
                            class="py-2 rounded-lg text-sm font-medium border transition-colors"
                            :class="paymentMethod === 'card' ? 'bg-blue-50 border-blue-300 text-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            Card
                        </button>
                    </div>

                    <button @click="checkout" :disabled="!cart.length || processing"
                        class="w-full bg-primary-600 text-white py-3 rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50">
                        {{ processing ? 'Processing...' : 'Complete Sale' }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ products: Array, categories: Array });

const search = ref('');
const selectedCategory = ref(null);
const cart = ref([]);
const paymentMethod = ref('cash');
const processing = ref(false);

const filteredProducts = computed(() => {
    return props.products.filter(p => {
        const matchSearch = !search.value || p.name.toLowerCase().includes(search.value.toLowerCase()) || p.sku.toLowerCase().includes(search.value.toLowerCase());
        const matchCat = !selectedCategory.value || p.category_id === selectedCategory.value;
        return matchSearch && matchCat;
    });
});

const totalItems = computed(() => cart.value.reduce((sum, i) => sum + i.quantity, 0));
const totalPrice = computed(() => cart.value.reduce((sum, i) => sum + (i.product.price * i.quantity), 0).toFixed(2));

const addToCart = (product) => {
    const existing = cart.value.find(i => i.product.id === product.id);
    if (existing) {
        if (existing.quantity < product.stock) existing.quantity++;
    } else {
        cart.value.push({ product, quantity: 1 });
    }
};

const updateQty = (idx, delta) => {
    const item = cart.value[idx];
    const newQty = item.quantity + delta;
    if (newQty <= 0) cart.value.splice(idx, 1);
    else if (newQty <= item.product.stock) item.quantity = newQty;
};

const checkout = () => {
    processing.value = true;
    router.post('/pos/checkout', {
        items: cart.value.map(i => ({ product_id: i.product.id, quantity: i.quantity })),
        payment_method: paymentMethod.value,
    }, {
        onSuccess: () => { cart.value = []; processing.value = false; },
        onError: () => { processing.value = false; },
    });
};
</script>

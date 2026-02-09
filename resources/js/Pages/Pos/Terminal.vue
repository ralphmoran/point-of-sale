<template>
    <AppLayout title="POS Terminal">
        <Head title="POS Terminal" />

        <!-- Mobile: toggle between products and cart views -->
        <div class="lg:hidden flex gap-2 mb-3 flex-shrink-0">
            <button @click="mobileView = 'products'"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors"
                :class="mobileView === 'products' ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700'">
                Menu
            </button>
            <button @click="mobileView = 'cart'"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors relative"
                :class="mobileView === 'cart' ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700'">
                Cart
                <span v-if="cart.length" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold">
                    {{ totalItems }}
                </span>
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 lg:h-[calc(100vh-8rem)]">
            <!-- Products Panel -->
            <div class="flex-1 flex flex-col min-h-0" :class="mobileView === 'cart' ? 'hidden lg:flex' : 'flex'" style="min-height: 0;">
                <!-- Search + Category Filter (always visible) -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3 sm:mb-4 flex-shrink-0">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input v-model="search" type="text" placeholder="Search products..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                    </div>
                    <div class="flex gap-2 overflow-x-auto pb-2 sm:flex-wrap sm:overflow-visible flex-shrink-0">
                        <button @click="selectedCategory = null"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
                            :class="!selectedCategory ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            All
                        </button>
                        <button v-for="cat in categories" :key="cat.id" @click="selectedCategory = cat.id"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
                            :class="selectedCategory === cat.id ? 'bg-primary-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'">
                            <span v-html="categoryIcon(cat.name)" class="w-4 h-4 flex-shrink-0"></span>
                            {{ cat.name }}
                        </button>
                    </div>
                </div>

                <!-- Product Grid (scrollable) -->
                <div class="flex-1 overflow-y-auto min-h-0">
                    <div class="grid grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-3">
                        <div v-for="product in filteredProducts" :key="product.id"
                            class="bg-white border border-gray-200 rounded-lg sm:rounded-xl p-2 sm:p-4 text-left hover:border-primary-300 hover:shadow-sm transition relative">
                            <div class="w-full aspect-square bg-gray-100 rounded-md sm:rounded-lg mb-1.5 sm:mb-3 flex items-center justify-center cursor-pointer"
                                @click="addToCart(product)">
                                <svg class="w-5 h-5 sm:w-8 sm:h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ product.name }}</p>
                            <p class="text-[10px] sm:text-xs text-gray-400 mb-0.5 sm:mb-1 hidden sm:block">{{ product.category?.name }}</p>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs sm:text-sm font-bold text-primary-600">${{ product.price }}</p>
                                <!-- Quantity controls on card -->
                                <div class="flex items-center gap-1">
                                    <button @click="removeFromCart(product)" v-if="getCartQty(product.id)"
                                        class="w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs hover:bg-gray-300">
                                        -
                                    </button>
                                    <span v-if="getCartQty(product.id)"
                                        class="text-xs sm:text-sm font-semibold text-primary-700 w-4 sm:w-5 text-center">
                                        {{ getCartQty(product.id) }}
                                    </span>
                                    <button @click="addToCart(product)"
                                        class="w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center text-xs"
                                        :class="getCartQty(product.id) ? 'bg-primary-600 text-white hover:bg-primary-700' : 'bg-gray-200 text-gray-600 hover:bg-gray-300'">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="filteredProducts.length === 0" class="text-center text-gray-400 py-12">No products found</p>
                </div>
            </div>

            <!-- Order / Cart Panel -->
            <div class="w-full lg:w-96 flex flex-col bg-white border border-gray-200 rounded-xl overflow-hidden min-h-0"
                :class="mobileView === 'products' ? 'hidden lg:flex' : 'flex'">

                <!-- Order Type Tabs -->
                <div class="flex border-b border-gray-200 flex-shrink-0">
                    <button v-for="t in orderTypes" :key="t.value" @click="orderType = t.value"
                        class="flex-1 py-3 text-xs sm:text-sm font-medium transition-colors relative"
                        :class="orderType === t.value ? 'text-primary-700' : 'text-gray-500 hover:text-gray-700'">
                        {{ t.label }}
                        <span v-if="orderType === t.value" class="absolute bottom-0 left-2 right-2 h-0.5 bg-primary-600 rounded-full"></span>
                    </button>
                </div>

                <!-- Customer / Table fields -->
                <div class="px-4 pt-3 pb-2 border-b border-gray-200 flex-shrink-0">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-[10px] sm:text-xs text-gray-500 mb-1">Customer name</label>
                            <input v-model="customerName" type="text" placeholder="Optional"
                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                        </div>
                        <div v-if="orderType === 'dine_in'" class="w-20">
                            <label class="block text-[10px] sm:text-xs text-gray-500 mb-1">Table #</label>
                            <input v-model="tableNumber" type="text" placeholder="--"
                                class="w-full px-2.5 py-1.5 border border-gray-300 rounded-lg text-sm text-center focus:ring-1 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                        </div>
                    </div>
                </div>

                <!-- Cart Header -->
                <div class="px-4 pt-3 pb-2 flex items-center justify-between flex-shrink-0">
                    <h2 class="text-sm font-semibold text-gray-900">Ordered items</h2>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-primary-600 font-medium">{{ String(totalItems).padStart(2, '0') }} items</span>
                        <button v-if="cart.length" @click="cart = []" class="text-xs text-red-500 hover:text-red-700">Clear</button>
                    </div>
                </div>

                <!-- Cart Items (scrollable) -->
                <div class="flex-1 overflow-y-auto px-4 space-y-2 min-h-0">
                    <div v-for="(item, idx) in cart" :key="item.product.id" class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ item.product.name }}</p>
                            <p class="text-xs text-gray-400">${{ item.product.price }} x{{ item.quantity }}</p>
                            <div class="flex items-center gap-1.5 mt-1">
                                <button @click="updateQty(idx, -1)" class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xs">-</button>
                                <span class="w-5 text-center text-xs font-medium">{{ item.quantity }}</span>
                                <button @click="updateQty(idx, 1)" class="w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center text-gray-500 hover:bg-gray-50 text-xs">+</button>
                                <button @click="cart.splice(idx, 1)" class="ml-1 text-gray-300 hover:text-red-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 flex-shrink-0">${{ (item.product.price * item.quantity).toFixed(2) }}</p>
                    </div>
                    <p v-if="!cart.length" class="text-center text-gray-400 py-8 text-sm">No items added</p>
                </div>

                <!-- Payment Details + Checkout (always visible) -->
                <div class="p-4 border-t border-gray-200 space-y-2.5 flex-shrink-0">
                    <p class="text-sm font-semibold text-gray-900">Payment Details</p>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span>${{ totalPrice }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-gray-900 pt-1 border-t border-gray-100">
                        <span>Total</span>
                        <span>${{ totalPrice }}</span>
                    </div>

                    <p v-if="checkoutError" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5">{{ checkoutError }}</p>

                    <!-- Payment Method -->
                    <div class="grid grid-cols-2 gap-2">
                        <button @click="paymentMethod = 'cash'"
                            class="flex items-center justify-center gap-1.5 py-2 rounded-lg text-sm font-medium border transition-colors"
                            :class="paymentMethod === 'cash' ? 'bg-green-50 border-green-300 text-green-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cash
                        </button>
                        <button @click="paymentMethod = 'card'"
                            class="flex items-center justify-center gap-1.5 py-2 rounded-lg text-sm font-medium border transition-colors"
                            :class="paymentMethod === 'card' ? 'bg-blue-50 border-blue-300 text-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Card
                        </button>
                    </div>

                    <button @click="checkout" :disabled="!cart.length || processing"
                        class="w-full bg-primary-600 text-white py-3 rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
                        <svg v-if="!processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        {{ processing ? 'Processing...' : 'Check Out' }}
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

const props = defineProps({ products: Array, categories: Array, stores: Array });

const search = ref('');
const selectedCategory = ref(null);
const cart = ref([]);
const paymentMethod = ref('cash');
const processing = ref(false);
const checkoutError = ref('');
const mobileView = ref('products');

// Order type
const orderType = ref('dine_in');
const customerName = ref('');
const tableNumber = ref('');
const orderTypes = [
    { label: 'Dine-in', value: 'dine_in' },
    { label: 'Takeaway', value: 'takeaway' },
    { label: 'Delivery', value: 'delivery' },
];

const filteredProducts = computed(() => {
    return props.products.filter(p => {
        const matchSearch = !search.value || p.name.toLowerCase().includes(search.value.toLowerCase()) || p.sku.toLowerCase().includes(search.value.toLowerCase());
        const matchCat = !selectedCategory.value || p.category_id === selectedCategory.value;
        return matchSearch && matchCat;
    });
});

const totalItems = computed(() => cart.value.reduce((sum, i) => sum + i.quantity, 0));
const totalPrice = computed(() => cart.value.reduce((sum, i) => sum + (i.product.price * i.quantity), 0).toFixed(2));

const getCartQty = (productId) => {
    const item = cart.value.find(i => i.product.id === productId);
    return item ? item.quantity : 0;
};

const addToCart = (product) => {
    checkoutError.value = '';
    const existing = cart.value.find(i => i.product.id === product.id);
    if (existing) {
        if (existing.quantity < product.stock) existing.quantity++;
    } else {
        cart.value.push({ product, quantity: 1 });
    }
};

const removeFromCart = (product) => {
    const idx = cart.value.findIndex(i => i.product.id === product.id);
    if (idx === -1) return;
    const item = cart.value[idx];
    if (item.quantity <= 1) cart.value.splice(idx, 1);
    else item.quantity--;
};

const updateQty = (idx, delta) => {
    const item = cart.value[idx];
    const newQty = item.quantity + delta;
    if (newQty <= 0) cart.value.splice(idx, 1);
    else if (newQty <= item.product.stock) item.quantity = newQty;
};

const checkout = () => {
    processing.value = true;
    checkoutError.value = '';
    router.post('/pos/checkout', {
        items: cart.value.map(i => ({ product_id: i.product.id, quantity: i.quantity })),
        payment_method: paymentMethod.value,
        order_type: orderType.value,
        customer_name: customerName.value || null,
        table_number: orderType.value === 'dine_in' ? (tableNumber.value || null) : null,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            cart.value = [];
            customerName.value = '';
            tableNumber.value = '';
            processing.value = false;
            mobileView.value = 'products';
        },
        onError: (errors) => {
            processing.value = false;
            checkoutError.value = Object.values(errors).flat().join(', ');
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

// Category icon mapping
const categoryIcons = {
    electronics: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
    clothing: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V3H8v8M8 3L3 8l2 2 3-3M16 3l5 5-2 2-3-3M6 12v8h12v-8"/></svg>',
    'food': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>',
    accessories: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>',
    'home': '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
    sports: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
};

const defaultIcon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>';

const categoryIcon = (name) => {
    const lower = name.toLowerCase();
    for (const [key, icon] of Object.entries(categoryIcons)) {
        if (lower.includes(key)) return icon;
    }
    return defaultIcon;
};
</script>

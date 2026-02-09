<template>
    <AppLayout title="Sales History">
        <Head title="Sales History" />

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-6">
            <input v-model="filters.date_from" @change="applyFilters" type="date"
                class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
            <input v-model="filters.date_to" @change="applyFilters" type="date"
                class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
            <select v-model="filters.cashier" @change="applyFilters"
                class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <option value="">All Cashiers</option>
                <option v-for="c in cashiers" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <select v-model="filters.payment_method" @change="applyFilters"
                class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <option value="">All Payment Methods</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
            </select>
            <select v-if="isAdmin && stores.length" v-model="filters.store" @change="applyFilters"
                class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <option value="">All Stores</option>
                <option v-for="s in stores" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">
                            <button @click="toggleSort('id')" class="flex items-center gap-1 hover:text-gray-700">
                                Sale # <SortIcon field="id" :current="filters.sort" :dir="filters.dir" />
                            </button>
                        </th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase hidden md:table-cell">
                            <button @click="toggleSort('created_at')" class="flex items-center gap-1 hover:text-gray-700">
                                Date <SortIcon field="created_at" :current="filters.sort" :dir="filters.dir" />
                            </button>
                        </th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Cashier</th>
                        <th v-if="isAdmin" class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase hidden lg:table-cell">Store</th>
                        <th class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Type</th>
                        <th class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">
                            <button @click="toggleSort('total')" class="flex items-center gap-1 ml-auto hover:text-gray-700">
                                Total <SortIcon field="total" :current="filters.sort" :dir="filters.dir" />
                            </button>
                        </th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sale in sales.data" :key="sale.id" class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">#{{ sale.id }}</td>
                        <td class="py-3 px-4 text-gray-500 hidden md:table-cell">{{ formatDate(sale.created_at) }}</td>
                        <td class="py-3 px-4 text-gray-700 hidden sm:table-cell">{{ sale.user?.name }}</td>
                        <td v-if="isAdmin" class="py-3 px-4 text-gray-500 hidden lg:table-cell">{{ sale.store?.name }}</td>
                        <td class="py-3 px-4 text-center text-gray-500">{{ sale.items_count }}</td>
                        <td class="py-3 px-4 text-center hidden sm:table-cell">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="{
                                    'bg-purple-50 text-purple-700': sale.order_type === 'dine_in',
                                    'bg-orange-50 text-orange-700': sale.order_type === 'takeaway',
                                    'bg-teal-50 text-teal-700': sale.order_type === 'delivery',
                                }">
                                {{ formatOrderType(sale.order_type) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="sale.payment_method === 'cash' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'">
                                {{ sale.payment_method }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right font-semibold text-gray-900">${{ sale.total }}</td>
                        <td class="py-3 px-4 text-right">
                            <a :href="`/sales/${sale.id}`" class="text-primary-600 hover:text-primary-800 text-xs font-medium">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-if="!sales.data.length" class="text-center text-gray-400 py-8 text-sm">No sales found</p>
        </div>

        <div v-if="sales.last_page > 1" class="flex justify-center gap-1 mt-4">
            <a v-for="link in sales.links" :key="link.label" :href="link.url"
                class="px-3 py-1.5 rounded text-sm"
                :class="link.active ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                v-html="link.label" />
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, h } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    sales: Object,
    cashiers: Array,
    stores: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
    filters: Object,
});

const filters = reactive({
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    cashier: props.filters?.cashier || '',
    payment_method: props.filters?.payment_method || '',
    store: props.filters?.store || '',
    sort: props.filters?.sort || 'created_at',
    dir: props.filters?.dir || 'desc',
});

const applyFilters = () => {
    const params = {};
    Object.entries(filters).forEach(([k, v]) => { if (v) params[k] = v; });
    router.get('/sales', params, { preserveState: true, replace: true });
};

const toggleSort = (field) => {
    if (filters.sort === field) {
        filters.dir = filters.dir === 'desc' ? 'asc' : 'desc';
    } else {
        filters.sort = field;
        filters.dir = 'desc';
    }
    applyFilters();
};

const formatDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });

const formatOrderType = (type) => {
    const map = { dine_in: 'Dine-in', takeaway: 'Takeaway', delivery: 'Delivery' };
    return map[type] || type;
};

// Sort indicator component
const SortIcon = (props) => {
    if (props.current !== props.field) {
        return h('svg', { class: 'w-3 h-3 text-gray-300', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4' }),
        ]);
    }
    const d = props.dir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7';
    return h('svg', { class: 'w-3 h-3 text-primary-600', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d }),
    ]);
};
SortIcon.props = ['field', 'current', 'dir'];
</script>

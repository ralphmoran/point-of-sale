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
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Sale #</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Cashier</th>
                        <th class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase">Items</th>
                        <th class="text-center py-3 px-4 text-xs font-medium text-gray-500 uppercase">Payment</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sale in sales.data" :key="sale.id" class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">#{{ sale.id }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ formatDate(sale.created_at) }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ sale.user?.name }}</td>
                        <td class="py-3 px-4 text-center text-gray-500">{{ sale.items_count }}</td>
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
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ sales: Object, cashiers: Array, filters: Object });

const filters = reactive({
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    cashier: props.filters?.cashier || '',
    payment_method: props.filters?.payment_method || '',
});

const applyFilters = () => {
    const params = {};
    Object.entries(filters).forEach(([k, v]) => { if (v) params[k] = v; });
    router.get('/sales', params, { preserveState: true, replace: true });
};

const formatDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
</script>

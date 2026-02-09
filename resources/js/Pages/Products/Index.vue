<template>
    <AppLayout title="Products">
        <Head title="Products" />

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
            <div class="flex flex-col sm:flex-row gap-3 flex-1">
                <input v-model="filters.search" @input="applyFilters" type="text" placeholder="Search products..."
                    class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none w-full sm:w-64" />
                <select v-model="filters.category" @change="applyFilters"
                    class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>
            <a href="/products/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition whitespace-nowrap">
                + Add Product
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data" :key="product.id" class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ product.name }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ product.sku }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ product.category?.name }}</td>
                        <td class="py-3 px-4 text-right text-gray-900">${{ product.price }}</td>
                        <td class="py-3 px-4 text-right">
                            <span :class="product.stock < 10 ? 'text-red-600 font-medium' : 'text-gray-500'">{{ product.stock }}</span>
                        </td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a :href="`/products/${product.id}/edit`" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                            <button @click="destroy(product.id)" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p v-if="!products.data.length" class="text-center text-gray-400 py-8 text-sm">No products found</p>
        </div>

        <!-- Pagination -->
        <div v-if="products.last_page > 1" class="flex justify-center gap-1 mt-4">
            <a v-for="link in products.links" :key="link.label" :href="link.url"
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

const props = defineProps({ products: Object, categories: Array, filters: Object });

const filters = reactive({ search: props.filters?.search || '', category: props.filters?.category || '' });

let searchTimeout;
const applyFilters = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get('/products', { search: filters.search || undefined, category: filters.category || undefined }, { preserveState: true, replace: true });
    }, 300);
};

const destroy = (id) => {
    if (confirm('Delete this product?')) router.delete(`/products/${id}`);
};
</script>

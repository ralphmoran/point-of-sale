<template>
    <AppLayout title="Stores">
        <Head title="Stores" />

        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500">{{ stores.total }} stores</p>
            <a href="/stores/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition">
                + Add Store
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Address</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Users</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Products</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Sales</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="store in stores.data" :key="store.id" class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ store.name }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ store.address || '—' }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ store.phone || '—' }}</td>
                        <td class="py-3 px-4 text-right text-gray-500">{{ store.users_count }}</td>
                        <td class="py-3 px-4 text-right text-gray-500">{{ store.products_count }}</td>
                        <td class="py-3 px-4 text-right text-gray-500">{{ store.sales_count }}</td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a :href="`/stores/${store.id}/edit`" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                            <button @click="destroy(store.id)" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ stores: Object });

const destroy = (id) => {
    if (confirm('Delete this store and all associated data?')) router.delete(`/stores/${id}`);
};
</script>

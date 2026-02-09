<template>
    <AppLayout title="Sale Details">
        <Head :title="`Sale #${sale.id}`" />

        <div class="max-w-3xl">
            <a href="/sales" class="text-sm text-primary-600 hover:text-primary-800 mb-4 inline-block">&larr; Back to Sales</a>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Sale #{{ sale.id }}</h2>
                            <p class="text-sm text-gray-500 mt-1">{{ formatDate(sale.created_at) }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium" :class="sale.payment_method === 'cash' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'">
                            {{ sale.payment_method }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Cashier: <span class="font-medium text-gray-700">{{ sale.user?.name }}</span></p>
                </div>

                <!-- Items -->
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="text-left py-3 px-6 text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="text-right py-3 px-6 text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in sale.items" :key="item.id" class="border-b border-gray-100 last:border-0">
                            <td class="py-3 px-6 font-medium text-gray-900">{{ item.product?.name }}</td>
                            <td class="py-3 px-4 text-right text-gray-500">${{ item.unit_price }}</td>
                            <td class="py-3 px-4 text-right text-gray-500">{{ item.quantity }}</td>
                            <td class="py-3 px-6 text-right font-semibold text-gray-900">${{ item.subtotal }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Total -->
                <div class="p-6 bg-gray-50 flex justify-end">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="text-2xl font-bold text-gray-900">${{ sale.total }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ sale: Object });

const formatDate = (d) => new Date(d).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' });
</script>

<template>
    <AppLayout title="Dashboard">
        <Head title="Dashboard" />

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div v-for="card in kpiCards" :key="card.label" class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">{{ card.label }}</span>
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center" :class="card.iconBg" v-html="card.icon"></span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ card.value }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ card.sub }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sales Chart (Last 7 Days) -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Sales — Last 7 Days</h3>
                <LineChart :data="last7Days" />
            </div>

            <!-- Recent Sales -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Recent Sales</h3>
                <div class="space-y-3">
                    <div v-for="sale in recentSales" :key="sale.id" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">#{{ sale.id }}</p>
                            <p class="text-xs text-gray-500">{{ sale.cashier }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">${{ sale.total }}</p>
                            <span class="text-xs px-1.5 py-0.5 rounded-full" :class="sale.payment_method === 'cash' ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'">
                                {{ sale.payment_method }}
                            </span>
                        </div>
                    </div>
                    <p v-if="recentSales.length === 0" class="text-sm text-gray-400 text-center py-4">No sales today</p>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="mt-6 bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Team Members</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="text-left py-2 px-3 text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="text-right py-2 px-3 text-xs font-medium text-gray-500 uppercase">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in activeUsers" :key="user.id" class="border-b border-gray-100 last:border-0">
                            <td class="py-2.5 px-3 font-medium text-gray-900">{{ user.name }}</td>
                            <td class="py-2.5 px-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="user.role === 'admin' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-600'">
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 text-right text-gray-600">{{ user.sales_count }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LineChart from '@/Components/Charts/LineChart.vue';

const props = defineProps({
    stats: Object,
    recentSales: Array,
    last7Days: Array,
    activeUsers: Array,
});

const kpiCards = [
    {
        label: "Today's Sales",
        value: `$${props.stats.todaySales}`,
        sub: `${props.stats.todayCount} transactions`,
        icon: '<svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        iconBg: 'bg-green-50',
    },
    {
        label: 'Total Revenue',
        value: `$${props.stats.totalRevenue}`,
        sub: 'All time',
        icon: '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
        iconBg: 'bg-blue-50',
    },
    {
        label: 'Products',
        value: props.stats.productCount,
        sub: 'In inventory',
        icon: '<svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
        iconBg: 'bg-purple-50',
    },
    {
        label: 'Avg. Sale',
        value: `$${props.stats.avgSale}`,
        sub: 'Per transaction',
        icon: '<svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
        iconBg: 'bg-orange-50',
    },
];
</script>

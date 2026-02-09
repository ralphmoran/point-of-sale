<template>
    <AppLayout title="Reports">
        <Head title="Reports" />

        <!-- Date Range Filter -->
        <div class="flex flex-wrap items-center gap-3 mb-6">
            <input v-model="dateFrom" type="date" class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
            <span class="text-gray-400 text-sm">to</span>
            <input v-model="dateTo" type="date" class="px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
            <button @click="applyFilter" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition">
                Apply
            </button>
        </div>

        <!-- Period KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500 mb-1">Total Revenue (Period)</p>
                <p class="text-2xl font-bold text-gray-900">${{ periodTotal }}</p>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <p class="text-sm text-gray-500 mb-1">Total Transactions (Period)</p>
                <p class="text-2xl font-bold text-gray-900">{{ periodCount }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Sales Over Time -->
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Sales Over Time</h3>
                <LineChart :data="salesOverTime.map(d => ({ date: d.date, total: parseFloat(d.revenue) }))" />
            </div>

            <!-- Sales by User -->
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Sales by Team Member</h3>
                <BarChart :data="salesByUser" />
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Payment Methods</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="pm in paymentBreakdown" :key="pm.payment_method" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full" :class="pm.payment_method === 'cash' ? 'bg-green-500' : 'bg-blue-500'"></span>
                        <span class="text-sm font-medium text-gray-700 capitalize">{{ pm.payment_method }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">${{ parseFloat(pm.revenue).toFixed(2) }}</p>
                        <p class="text-xs text-gray-400">{{ pm.count }} transactions</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import LineChart from '@/Components/Charts/LineChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';

const props = defineProps({
    salesOverTime: Array,
    salesByUser: Array,
    paymentBreakdown: Array,
    periodTotal: String,
    periodCount: Number,
    filters: Object,
});

const dateFrom = ref(props.filters.date_from);
const dateTo = ref(props.filters.date_to);

const applyFilter = () => {
    router.get('/reports', { date_from: dateFrom.value, date_to: dateTo.value }, { preserveState: true, replace: true });
};
</script>

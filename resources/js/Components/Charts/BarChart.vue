<template>
    <div class="h-64">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Tooltip } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip);

const props = defineProps({ data: Array, labelKey: { type: String, default: 'name' }, valueKey: { type: String, default: 'revenue' } });

const chartData = computed(() => ({
    labels: props.data.map(d => d[props.labelKey]),
    datasets: [{
        label: 'Revenue',
        data: props.data.map(d => d[props.valueKey]),
        backgroundColor: ['#2563eb', '#7c3aed', '#059669', '#d97706', '#dc2626', '#0891b2'],
        borderRadius: 6,
    }],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => `$${ctx.parsed.y.toFixed(2)}` } } },
    scales: {
        y: { beginAtZero: true, ticks: { callback: (v) => `$${v}` }, grid: { color: '#f3f4f6' } },
        x: { grid: { display: false } },
    },
};
</script>

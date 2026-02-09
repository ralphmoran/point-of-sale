<template>
    <div class="h-64">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Filler, Tooltip);

const props = defineProps({ data: Array });

const chartData = computed(() => ({
    labels: props.data.map(d => d.date),
    datasets: [{
        label: 'Revenue',
        data: props.data.map(d => d.total ?? d.revenue),
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37, 99, 235, 0.08)',
        fill: true,
        tension: 0.3,
        pointRadius: 4,
        pointBackgroundColor: '#2563eb',
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

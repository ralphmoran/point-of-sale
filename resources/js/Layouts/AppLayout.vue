<template>
    <div class="min-h-screen bg-gray-50 flex">
        <Sidebar :current-route="currentRoute" :user="$page.props.auth.user" />

        <div class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900">{{ title }}</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="hidden sm:inline text-sm text-gray-500">{{ $page.props.auth.user?.store?.name }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-semibold">
                                {{ initials }}
                            </div>
                            <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ $page.props.auth.user?.name }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="$page.props.flash?.success" class="mx-4 sm:mx-6 mt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ $page.props.flash.success }}
                </div>
            </div>
            <div v-if="$page.props.flash?.error" class="mx-4 sm:mx-6 mt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Main Content -->
            <main class="p-4 sm:p-6">
                <slot />
            </main>
        </div>

        <!-- Mobile sidebar overlay -->
        <div v-if="sidebarOpen" class="fixed inset-0 z-30 lg:hidden" @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-gray-900/50"></div>
            <div class="fixed inset-y-0 left-0 w-64">
                <Sidebar :current-route="currentRoute" :user="$page.props.auth.user" :mobile="true" @close="sidebarOpen = false" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';

const props = defineProps({
    title: { type: String, default: 'Dashboard' },
});

const page = usePage();
const sidebarOpen = ref(false);

const currentRoute = computed(() => {
    return window.location.pathname;
});

const initials = computed(() => {
    const name = page.props.auth.user?.name || '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});
</script>

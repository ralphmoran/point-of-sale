<template>
    <div class="min-h-screen bg-gray-50 flex">
        <Sidebar :current-route="currentRoute" :user="$page.props.auth.user" class="hidden lg:flex" />

        <div class="flex-1 transition-[margin] duration-300" :class="collapsed ? 'lg:ml-16' : 'lg:ml-64'">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16">
                    <div class="flex items-center gap-3">
                        <button @click="openMobile" class="lg:hidden p-2 text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-lg font-semibold text-gray-900">{{ title }}</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Store switcher (admin only) -->
                        <div v-if="isAdmin && allStores.length > 1" class="relative" ref="storeSwitcherRef">
                            <button @click="storeSwitcherOpen = !storeSwitcherOpen"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm border border-gray-300 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="hidden sm:inline text-gray-700 font-medium">{{ currentStoreName }}</span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div v-if="storeSwitcherOpen" class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-30">
                                <button v-for="store in allStores" :key="store.id" @click="switchStore(store.id)"
                                    class="w-full text-left px-3 py-2 text-sm transition-colors"
                                    :class="store.id === currentStoreId ? 'bg-primary-50 text-primary-700 font-medium' : 'text-gray-700 hover:bg-gray-50'">
                                    {{ store.name }}
                                </button>
                            </div>
                        </div>
                        <span v-else class="hidden sm:inline text-sm text-gray-500">{{ $page.props.auth.user?.store?.name }}</span>

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
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="mobileOpen" class="fixed inset-0 z-30 lg:hidden" @click="closeMobile">
                <div class="fixed inset-0 bg-gray-900/50"></div>
            </div>
        </Transition>
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <div v-if="mobileOpen" class="fixed inset-y-0 left-0 w-64 z-40 lg:hidden" @click.stop>
                <Sidebar :current-route="currentRoute" :user="$page.props.auth.user" :mobile="true" @close="closeMobile" />
            </div>
        </Transition>

        <!-- Calculator modal -->
        <Calculator />

        <!-- Mobile calculator FAB -->
        <button @click="openCalc" class="lg:hidden fixed bottom-6 right-6 z-20 w-14 h-14 bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-700 transition flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Sidebar from '@/Components/Sidebar.vue';
import Calculator from '@/Components/Calculator.vue';
import { useSidebar } from '@/composables/useSidebar';
import { useCalculator } from '@/composables/useCalculator';

const props = defineProps({
    title: { type: String, default: 'Dashboard' },
});

const page = usePage();
const { collapsed, mobileOpen, openMobile, closeMobile } = useSidebar();
const { open: openCalc } = useCalculator();

const currentRoute = computed(() => window.location.pathname);

const initials = computed(() => {
    const name = page.props.auth.user?.name || '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

// Store switcher
const storeSwitcherOpen = ref(false);
const storeSwitcherRef = ref(null);
const isAdmin = computed(() => page.props.auth.user?.role === 'admin');
const allStores = computed(() => page.props.stores || []);
const currentStoreId = computed(() => page.props.auth.user?.store_id);
const currentStoreName = computed(() => page.props.auth.user?.store?.name);

const switchStore = (storeId) => {
    storeSwitcherOpen.value = false;
    if (storeId === currentStoreId.value) return;
    router.post('/switch-store', { store_id: storeId }, { preserveScroll: true });
};

const closeStoreSwitcher = (e) => {
    if (storeSwitcherRef.value && !storeSwitcherRef.value.contains(e.target)) {
        storeSwitcherOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', closeStoreSwitcher));
onUnmounted(() => document.removeEventListener('click', closeStoreSwitcher));
</script>

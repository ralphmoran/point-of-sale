<template>
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30 flex flex-col" :class="{ 'shadow-xl': mobile }">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900">PoS</span>
            </div>
            <button v-if="mobile" @click="$emit('close')" class="ml-auto p-1 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Main</p>

            <a v-for="item in mainNav" :key="item.href" :href="item.href"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               :class="isActive(item.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
            >
                <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                {{ item.label }}
            </a>

            <p class="px-3 pt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Management</p>

            <a v-for="item in mgmtNav" :key="item.href" :href="item.href"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
               :class="isActive(item.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
            >
                <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                {{ item.label }}
            </a>

            <template v-if="user?.role === 'admin'">
                <p class="px-3 pt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Admin</p>
                <a v-for="item in adminNav" :key="item.href" :href="item.href"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                   :class="isActive(item.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                >
                    <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                    {{ item.label }}
                </a>
            </template>
        </nav>

        <!-- Logout -->
        <div class="p-3 border-t border-gray-200">
            <button @click="logout" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 w-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Log out
            </button>
        </div>
    </aside>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    currentRoute: String,
    user: Object,
    mobile: { type: Boolean, default: false },
});

defineEmits(['close']);

const isActive = (href) => {
    if (href === '/dashboard') return props.currentRoute === '/dashboard';
    return props.currentRoute.startsWith(href);
};

const logout = () => {
    router.post('/logout');
};

const icon = (d) => `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${d}"/></svg>`;

const mainNav = [
    { label: 'Dashboard', href: '/dashboard', icon: icon('M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6') },
    { label: 'POS Terminal', href: '/pos', icon: icon('M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z') },
];

const mgmtNav = [
    { label: 'Products', href: '/products', icon: icon('M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4') },
    { label: 'Categories', href: '/categories', icon: icon('M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z') },
    { label: 'Sales History', href: '/sales', icon: icon('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01') },
    { label: 'Reports', href: '/reports', icon: icon('M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z') },
];

const adminNav = [
    { label: 'Users', href: '/users', icon: icon('M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z') },
    { label: 'Stores', href: '/stores', icon: icon('M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4') },
];
</script>

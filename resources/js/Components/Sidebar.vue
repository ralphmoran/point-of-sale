<template>
    <aside
        class="fixed inset-y-0 left-0 bg-white border-r border-gray-200 z-30 flex flex-col transition-[width] duration-300"
        :class="[mobile ? 'w-64 shadow-xl' : (collapsed ? 'w-16' : 'w-64')]"
    >
        <!-- Logo -->
        <div class="h-16 flex items-center border-b border-gray-200" :class="collapsed && !mobile ? 'justify-center px-2' : 'px-6'">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span v-if="!collapsed || mobile" class="text-lg font-bold text-gray-900">PoS</span>
            </div>
            <button v-if="mobile" @click="$emit('close')" class="ml-auto p-1 text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <!-- Collapse toggle (desktop only) -->
            <button v-if="!mobile" @click="toggleCollapsed" class="ml-auto p-1 text-gray-400 hover:text-gray-600 hidden lg:block">
                <svg class="w-5 h-5 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            <template v-for="(group, gi) in navGroups" :key="gi">
                <!-- Group header -->
                <template v-if="group.show !== false">
                    <hr v-if="collapsed && !mobile && gi > 0" class="my-3 border-gray-200" />
                    <p v-if="!collapsed || mobile" class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2" :class="gi > 0 ? 'pt-4' : ''">
                        {{ group.label }}
                    </p>

                    <template v-for="item in group.items" :key="item.href || item.label">
                        <!-- Item with children (sub-menu) -->
                        <template v-if="item.children">
                            <!-- Expanded sidebar: accordion -->
                            <div v-if="!collapsed || mobile">
                                <button @click="toggleMenu(item.label)"
                                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors w-full"
                                    :class="isChildActive(item) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                                    <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                                    <span class="flex-1 text-left">{{ item.label }}</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="expandedMenus.has(item.label) ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <div v-if="expandedMenus.has(item.label)" class="ml-6 mt-1 space-y-1">
                                    <a v-for="child in item.children" :key="child.href" :href="child.href"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                                        :class="isActive(child.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'">
                                        <span v-html="child.icon" class="w-4 h-4 flex-shrink-0"></span>
                                        {{ child.label }}
                                    </a>
                                </div>
                            </div>
                            <!-- Collapsed sidebar: flyout on hover -->
                            <div v-else class="relative group">
                                <div class="flex items-center justify-center px-3 py-2 rounded-lg transition-colors cursor-pointer"
                                    :class="isChildActive(item) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                                    <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                                </div>
                                <div class="hidden group-hover:block absolute left-full top-0 ml-1 bg-white border border-gray-200 rounded-lg shadow-lg py-1 min-w-[160px] z-50">
                                    <p class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase">{{ item.label }}</p>
                                    <a v-for="child in item.children" :key="child.href" :href="child.href"
                                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors"
                                        :class="isActive(child.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                                        <span v-html="child.icon" class="w-4 h-4 flex-shrink-0"></span>
                                        {{ child.label }}
                                    </a>
                                </div>
                            </div>
                        </template>

                        <!-- Regular nav item -->
                        <template v-else>
                            <div v-if="collapsed && !mobile" class="relative group">
                                <a :href="item.href"
                                    class="flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                                    :class="isActive(item.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                                    <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                                </a>
                                <div class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                                    {{ item.label }}
                                </div>
                            </div>
                            <a v-else :href="item.href"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="isActive(item.href) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                                <span v-html="item.icon" class="w-5 h-5 flex-shrink-0"></span>
                                {{ item.label }}
                            </a>
                        </template>
                    </template>
                </template>
            </template>
        </nav>

        <!-- Footer: Calculator + Logout -->
        <div class="p-2 border-t border-gray-200 space-y-1">
            <!-- Calculator -->
            <div v-if="collapsed && !mobile" class="relative group">
                <button @click="openCalculator" class="flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 w-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </button>
                <div class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                    Calculator
                </div>
            </div>
            <button v-else @click="openCalculator" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 w-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Calculator
            </button>

            <!-- Logout -->
            <div v-if="collapsed && !mobile" class="relative group">
                <button @click="logout" class="flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 w-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
                <div class="pointer-events-none absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">
                    Log out
                </div>
            </div>
            <button v-else @click="logout" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 w-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Log out
            </button>
        </div>
    </aside>
</template>

<script setup>
import { reactive, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useSidebar } from '@/composables/useSidebar';
import { useCalculator } from '@/composables/useCalculator';

const props = defineProps({
    currentRoute: String,
    user: Object,
    mobile: { type: Boolean, default: false },
});

defineEmits(['close']);

const { collapsed, toggleCollapsed } = useSidebar();
const { open: openCalculator } = useCalculator();

const expandedMenus = reactive(new Set());

const toggleMenu = (label) => {
    if (expandedMenus.has(label)) expandedMenus.delete(label);
    else expandedMenus.add(label);
};

const isActive = (href) => {
    if (href === '/dashboard') return props.currentRoute === '/dashboard';
    return props.currentRoute.startsWith(href);
};

const isChildActive = (item) => {
    return item.children?.some(c => isActive(c.href));
};

const logout = () => { router.post('/logout'); };

const icon = (d) => `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${d}"/></svg>`;
const iconSm = (d) => `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${d}"/></svg>`;

const navGroups = computed(() => {
    const groups = [
        {
            label: 'Main',
            items: [
                { label: 'Dashboard', href: '/dashboard', icon: icon('M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6') },
                { label: 'POS Terminal', href: '/pos', icon: icon('M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z') },
            ],
        },
        {
            label: 'Management',
            items: [
                {
                    label: 'Inventory',
                    icon: icon('M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'),
                    children: [
                        { label: 'Products', href: '/products', icon: iconSm('M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4') },
                        { label: 'Categories', href: '/categories', icon: iconSm('M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z') },
                    ],
                },
                { label: 'Sales History', href: '/sales', icon: icon('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01') },
                { label: 'Reports', href: '/reports', icon: icon('M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z') },
            ],
        },
        {
            label: 'Admin',
            show: props.user?.role === 'admin',
            items: [
                { label: 'Users', href: '/users', icon: icon('M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z') },
                { label: 'Stores', href: '/stores', icon: icon('M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4') },
            ],
        },
    ];
    return groups;
});

// Auto-expand parent matching current route on mount
onMounted(() => {
    for (const group of navGroups.value) {
        for (const item of group.items) {
            if (item.children && isChildActive(item)) {
                expandedMenus.add(item.label);
            }
        }
    }
});
</script>

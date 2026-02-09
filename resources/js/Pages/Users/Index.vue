<template>
    <AppLayout title="Users">
        <Head title="Users" />

        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500">{{ users.total }} users</p>
            <a href="/users/create" class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition">
                + Add User
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase hidden md:table-cell">Email</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase hidden sm:table-cell">Store</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Sales</th>
                        <th class="text-right py-3 px-4 text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users.data" :key="user.id" class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ user.name }}</td>
                        <td class="py-3 px-4 text-gray-500 hidden md:table-cell">{{ user.email }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" :class="user.role === 'admin' ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-600'">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-500 hidden sm:table-cell">{{ user.store?.name }}</td>
                        <td class="py-3 px-4 text-right text-gray-500">{{ user.sales_count }}</td>
                        <td class="py-3 px-4 text-right space-x-2">
                            <a :href="`/users/${user.id}/edit`" class="text-primary-600 hover:text-primary-800 text-xs font-medium">Edit</a>
                            <button @click="destroy(user.id)" class="text-red-500 hover:text-red-700 text-xs font-medium">Delete</button>
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

defineProps({ users: Object });

const destroy = (id) => {
    if (confirm('Delete this user?')) router.delete(`/users/${id}`);
};
</script>

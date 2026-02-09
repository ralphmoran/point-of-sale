<template>
    <AppLayout title="Edit User">
        <Head title="Edit User" />
        <div class="max-w-2xl">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input v-model="form.name" type="text" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="form.email" type="email" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIN (leave blank to keep)</label>
                            <input v-model="form.pin" type="password" maxlength="4" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" placeholder="****" />
                            <p v-if="form.errors.pin" class="text-red-500 text-xs mt-1">{{ form.errors.pin }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                            <select v-model="form.role" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="cashier">Cashier</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                            <select v-model="form.store_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option v-for="store in stores" :key="store.id" :value="store.id">{{ store.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" :disabled="form.processing" class="bg-primary-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50">Update User</button>
                        <a href="/users" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ user: Object, stores: Array });

const form = useForm({ name: props.user.name, email: props.user.email, pin: '', role: props.user.role, store_id: props.user.store_id });
const submit = () => form.put(`/users/${props.user.id}`);
</script>

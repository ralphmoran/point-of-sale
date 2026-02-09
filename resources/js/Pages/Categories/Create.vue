<template>
    <AppLayout title="Add Category">
        <Head title="Add Category" />
        <div class="max-w-lg">
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input v-model="form.name" type="text" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                        <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" :disabled="form.processing" class="bg-primary-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50">Create Category</button>
                        <a href="/categories" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({ name: '' });
const submit = () => form.post('/categories');
</script>

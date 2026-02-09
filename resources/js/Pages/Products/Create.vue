<template>
    <AppLayout title="Add Product">
        <Head title="Add Product" />

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
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input v-model="form.sku" type="text" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                            <p v-if="form.errors.sku" class="text-red-500 text-xs mt-1">{{ form.errors.sku }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input v-model="form.price" type="number" step="0.01" min="0" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                            <p v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input v-model="form.stock" type="number" min="0" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                            <p v-if="form.errors.stock" class="text-red-500 text-xs mt-1">{{ form.errors.stock }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select v-model="form.category_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                <option value="">Select...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                            <p v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" :disabled="form.processing" class="bg-primary-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50">
                            Create Product
                        </button>
                        <a href="/products" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ categories: Array });

const form = useForm({ name: '', sku: '', price: '', stock: 0, category_id: '' });

const submit = () => form.post('/products');
</script>

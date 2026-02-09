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
                            <div class="flex gap-2">
                                <select v-model="form.category_id" class="flex-1 px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                                    <option value="">Select...</option>
                                    <option v-for="cat in localCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                                <button type="button" @click="showNewCategory = true"
                                    class="px-2.5 py-2.5 border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition flex-shrink-0"
                                    title="Create new category">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</p>
                        </div>
                    </div>

                    <!-- Inline new category form -->
                    <div v-if="showNewCategory" class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-sm font-medium text-gray-700 mb-2">New Category</p>
                        <div class="flex gap-2">
                            <input v-model="newCategoryName" type="text" placeholder="Category name..."
                                ref="newCategoryInput"
                                @keydown.enter.prevent="createCategory"
                                class="flex-1 px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" />
                            <button type="button" @click="createCategory" :disabled="!newCategoryName.trim() || creatingCategory"
                                class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition disabled:opacity-50">
                                {{ creatingCategory ? 'Creating...' : 'Add' }}
                            </button>
                            <button type="button" @click="showNewCategory = false; newCategoryName = ''"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                        </div>
                        <p v-if="categoryError" class="text-red-500 text-xs mt-1">{{ categoryError }}</p>
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
import { ref, watch, nextTick } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ categories: Array });

const form = useForm({ name: '', sku: '', price: '', stock: 0, category_id: '' });
const localCategories = ref([...props.categories]);

const showNewCategory = ref(false);
const newCategoryName = ref('');
const creatingCategory = ref(false);
const categoryError = ref('');
const newCategoryInput = ref(null);

watch(showNewCategory, (v) => {
    if (v) nextTick(() => newCategoryInput.value?.focus());
});

const createCategory = async () => {
    if (!newCategoryName.value.trim() || creatingCategory.value) return;
    creatingCategory.value = true;
    categoryError.value = '';

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch('/categories', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Inline-Create': '1',
            },
            body: JSON.stringify({ name: newCategoryName.value.trim() }),
        });
        if (!res.ok) {
            const data = await res.json();
            categoryError.value = data.errors?.name?.[0] || 'Failed to create category.';
            return;
        }
        const cat = await res.json();
        localCategories.value.push(cat);
        localCategories.value.sort((a, b) => a.name.localeCompare(b.name));
        form.category_id = cat.id;
        showNewCategory.value = false;
        newCategoryName.value = '';
    } catch {
        categoryError.value = 'Failed to create category.';
    } finally {
        creatingCategory.value = false;
    }
};

const submit = () => form.post('/products');
</script>

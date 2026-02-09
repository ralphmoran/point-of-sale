<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-primary-600 rounded-xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Point of Sale</h1>
                <p class="text-gray-500 mt-1">Enter your credentials to continue</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                <form @submit.prevent="submit">
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition"
                            placeholder="admin@pos.com"
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">PIN</label>
                        <input
                            v-model="form.pin"
                            type="password"
                            maxlength="4"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition tracking-[0.5em] text-center text-lg"
                            placeholder="****"
                        />
                    </div>

                    <div class="flex items-center mb-6">
                        <input v-model="form.remember" type="checkbox" id="remember"
                            class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
                        <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-primary-600 text-white py-2.5 px-4 rounded-lg text-sm font-semibold hover:bg-primary-700 focus:ring-4 focus:ring-primary-100 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'Signing in...' : 'Sign in' }}
                    </button>
                </form>

                <div class="mt-6 p-3 bg-gray-50 rounded-lg text-xs text-gray-500">
                    <p class="font-medium text-gray-700 mb-1">Demo Credentials</p>
                    <p>Admin: admin@pos.com / PIN: 1234</p>
                    <p>Cashier: jane@pos.com / PIN: 1234</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    pin: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: data.remember ? 'on' : '',
    })).post('/login');
};
</script>

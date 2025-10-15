<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100 px-4">
        <div class="max-w-md w-full">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-xl mb-4">
                    <Workflow :size="32" class="text-white" />
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-gray-600 mt-2">Sign in to your workflow automation platform</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form @submit.prevent="handleLogin" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Mail :size="18" class="text-gray-400" />
                            </div>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.email }"
                                placeholder="you@example.com"
                            />
                        </div>
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock :size="18" class="text-gray-400" />
                            </div>
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                class="w-full pl-10 pr-10 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.password }"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            >
                                <component :is="showPassword ? EyeOff : Eye" :size="18" class="text-gray-400" />
                            </button>
                        </div>
                        <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
                    </div>

                    <!-- Remember Me and Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                            />
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="/forgot-password" class="text-sm text-orange-600 hover:text-orange-700">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start">
                        <AlertCircle :size="20" class="flex-shrink-0 mr-2 mt-0.5" />
                        <span class="text-sm">{{ errorMessage }}</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 px-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="!loading" class="flex items-center justify-center">
                            <LogIn :size="20" class="mr-2" />
                            Sign In
                        </span>
                        <span v-else class="flex items-center justify-center">
                            <Loader :size="20" class="animate-spin mr-2" />
                            Signing in...
                        </span>
                    </button>

                    <!-- OAuth Options -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="handleOAuth('google')"
                            class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <Chrome :size="20" class="mr-2" />
                            Google
                        </button>
                        <button
                            type="button"
                            @click="handleOAuth('github')"
                            class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <Github :size="20" class="mr-2" />
                            GitHub
                        </button>
                    </div>
                </form>

                <!-- Sign Up Link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Don't have an account?
                        <a href="/register" class="text-orange-600 hover:text-orange-700 font-medium">
                            Sign up for free
                        </a>
                    </p>
                </div>
            </div>

            <!-- Additional Links -->
            <div class="mt-8 text-center space-x-4">
                <a href="/privacy" class="text-sm text-gray-500 hover:text-gray-700">Privacy Policy</a>
                <span class="text-gray-300">•</span>
                <a href="/terms" class="text-sm text-gray-500 hover:text-gray-700">Terms of Service</a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import { useAuthStore } from '../../stores/auth';
import { 
    Workflow, Mail, Lock, Eye, EyeOff, LogIn, 
    Loader, AlertCircle, Chrome, Github 
} from 'lucide-vue-next';

const authStore = useAuthStore();

const loading = ref(false);
const showPassword = ref(false);
const errorMessage = ref('');

const form = reactive({
    email: '',
    password: '',
    remember: false
});

const errors = reactive({
    email: '',
    password: ''
});

const handleLogin = async () => {
    // Reset errors
    errorMessage.value = '';
    errors.email = '';
    errors.password = '';
    
    // Validate
    if (!form.email) {
        errors.email = 'Email is required';
        return;
    }
    
    if (!form.password) {
        errors.password = 'Password is required';
        return;
    }
    
    loading.value = true;
    
    try {
        const result = await authStore.login(form);
        
        if (result.success) {
            // Redirect to workflows or intended page
            const intended = new URLSearchParams(window.location.search).get('redirect') || '/workflows';
            router.visit(intended);
        } else {
            errorMessage.value = result.error || 'Login failed. Please try again.';
            
            // Handle specific field errors
            if (result.errors) {
                errors.email = result.errors.email?.[0] || '';
                errors.password = result.errors.password?.[0] || '';
            }
        }
    } catch (error) {
        console.error('Login error:', error);
        errorMessage.value = 'An unexpected error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const handleOAuth = (provider) => {
    window.location.href = `/api/v1/auth/oauth/${provider}`;
};
</script>

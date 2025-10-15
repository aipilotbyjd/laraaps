<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100 px-4">
        <div class="max-w-md w-full">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-xl mb-4">
                    <Workflow :size="32" class="text-white" />
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Forgot Password?</h1>
                <p class="text-gray-600 mt-2">No worries, we'll send you reset instructions</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Success Message -->
                    <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-start">
                        <CheckCircle :size="20" class="flex-shrink-0 mr-2 mt-0.5" />
                        <span class="text-sm">{{ successMessage }}</span>
                    </div>

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
                                :disabled="emailSent"
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent disabled:bg-gray-50 disabled:text-gray-500"
                                :class="{ 'border-red-500': errors.email }"
                                placeholder="Enter your registered email"
                            />
                        </div>
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start">
                        <AlertCircle :size="20" class="flex-shrink-0 mr-2 mt-0.5" />
                        <span class="text-sm">{{ errorMessage }}</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        v-if="!emailSent"
                        type="submit"
                        :disabled="loading"
                        class="w-full py-3 px-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="!loading" class="flex items-center justify-center">
                            <Send :size="20" class="mr-2" />
                            Send Reset Link
                        </span>
                        <span v-else class="flex items-center justify-center">
                            <Loader :size="20" class="animate-spin mr-2" />
                            Sending...
                        </span>
                    </button>

                    <!-- Resend Button -->
                    <div v-else class="space-y-4">
                        <button
                            type="button"
                            @click="resendEmail"
                            :disabled="resendCooldown > 0"
                            class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            <span v-if="resendCooldown > 0" class="flex items-center justify-center">
                                Resend in {{ resendCooldown }}s
                            </span>
                            <span v-else class="flex items-center justify-center">
                                <RefreshCw :size="20" class="mr-2" />
                                Resend Email
                            </span>
                        </button>
                        
                        <div class="text-center text-sm text-gray-600">
                            Didn't receive the email? Check your spam folder or try resending.
                        </div>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-8 text-center">
                    <a href="/login" class="text-gray-600 hover:text-gray-900 font-medium inline-flex items-center">
                        <ArrowLeft :size="16" class="mr-1" />
                        Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onUnmounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { 
    Workflow, Mail, Send, Loader, AlertCircle, 
    CheckCircle, ArrowLeft, RefreshCw 
} from 'lucide-vue-next';

const authStore = useAuthStore();

const loading = ref(false);
const emailSent = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const resendCooldown = ref(0);
let cooldownInterval = null;

const form = reactive({
    email: ''
});

const errors = reactive({
    email: ''
});

const handleSubmit = async () => {
    // Reset messages
    errorMessage.value = '';
    successMessage.value = '';
    errors.email = '';
    
    // Validate
    if (!form.email) {
        errors.email = 'Email is required';
        return;
    }
    
    if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Please enter a valid email address';
        return;
    }
    
    loading.value = true;
    
    try {
        const result = await authStore.forgotPassword(form.email);
        
        if (result.success) {
            emailSent.value = true;
            successMessage.value = result.message || 'Password reset link has been sent to your email!';
            startResendCooldown();
        } else {
            errorMessage.value = result.error || 'Failed to send reset email. Please try again.';
        }
    } catch (error) {
        console.error('Forgot password error:', error);
        errorMessage.value = 'An unexpected error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const resendEmail = async () => {
    if (resendCooldown.value > 0) return;
    
    errorMessage.value = '';
    successMessage.value = '';
    
    loading.value = true;
    
    try {
        const result = await authStore.forgotPassword(form.email);
        
        if (result.success) {
            successMessage.value = 'Reset link has been resent to your email!';
            startResendCooldown();
        } else {
            errorMessage.value = result.error || 'Failed to resend email. Please try again.';
        }
    } catch (error) {
        console.error('Resend email error:', error);
        errorMessage.value = 'An unexpected error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const startResendCooldown = () => {
    resendCooldown.value = 60; // 60 seconds cooldown
    
    cooldownInterval = setInterval(() => {
        resendCooldown.value--;
        if (resendCooldown.value <= 0) {
            clearInterval(cooldownInterval);
        }
    }, 1000);
};

onUnmounted(() => {
    if (cooldownInterval) {
        clearInterval(cooldownInterval);
    }
});
</script>

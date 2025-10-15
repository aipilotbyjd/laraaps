<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-50 to-orange-100 px-4 py-8">
        <div class="max-w-md w-full">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 rounded-xl mb-4">
                    <Workflow :size="32" class="text-white" />
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
                <p class="text-gray-600 mt-2">Start building automation workflows today</p>
            </div>

            <!-- Register Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form @submit.prevent="handleRegister" class="space-y-5">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <User :size="18" class="text-gray-400" />
                            </div>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.name }"
                                placeholder="John Doe"
                            />
                        </div>
                        <p v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</p>
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
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.email }"
                                placeholder="you@example.com"
                            />
                        </div>
                        <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
                    </div>

                    <!-- Organization Name Field -->
                    <div>
                        <label for="organization" class="block text-sm font-medium text-gray-700 mb-1">
                            Organization Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Building :size="18" class="text-gray-400" />
                            </div>
                            <input
                                id="organization"
                                v-model="form.organization_name"
                                type="text"
                                required
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.organization_name }"
                                placeholder="My Company"
                            />
                        </div>
                        <p v-if="errors.organization_name" class="text-red-500 text-sm mt-1">{{ errors.organization_name }}</p>
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
                                @input="checkPasswordStrength"
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
                        
                        <!-- Password Strength Indicator -->
                        <div v-if="form.password" class="mt-2">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-600">Password strength:</span>
                                <span :class="passwordStrengthClass">{{ passwordStrength }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div 
                                    class="h-1.5 rounded-full transition-all duration-300"
                                    :class="passwordStrengthBarClass"
                                    :style="{ width: passwordStrengthPercentage + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Lock :size="18" class="text-gray-400" />
                            </div>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                class="w-full pl-10 pr-3 py-2.5 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.password_confirmation }"
                                placeholder="••••••••"
                            />
                        </div>
                        <p v-if="errors.password_confirmation" class="text-red-500 text-sm mt-1">
                            {{ errors.password_confirmation }}
                        </p>
                    </div>

                    <!-- Terms Checkbox -->
                    <div>
                        <label class="flex items-start">
                            <input
                                v-model="form.agree_terms"
                                type="checkbox"
                                required
                                class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500 mt-0.5"
                            />
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the
                                <a href="/terms" class="text-orange-600 hover:text-orange-700">Terms of Service</a>
                                and
                                <a href="/privacy" class="text-orange-600 hover:text-orange-700">Privacy Policy</a>
                            </span>
                        </label>
                        <p v-if="errors.agree_terms" class="text-red-500 text-sm mt-1">{{ errors.agree_terms }}</p>
                    </div>

                    <!-- Error Message -->
                    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start">
                        <AlertCircle :size="20" class="flex-shrink-0 mr-2 mt-0.5" />
                        <span class="text-sm">{{ errorMessage }}</span>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        :disabled="loading || !form.agree_terms"
                        class="w-full py-3 px-4 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="!loading" class="flex items-center justify-center">
                            <UserPlus :size="20" class="mr-2" />
                            Create Account
                        </span>
                        <span v-else class="flex items-center justify-center">
                            <Loader :size="20" class="animate-spin mr-2" />
                            Creating account...
                        </span>
                    </button>

                    <!-- OAuth Options -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or sign up with</span>
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

                <!-- Sign In Link -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600">
                        Already have an account?
                        <a href="/login" class="text-orange-600 hover:text-orange-700 font-medium">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useAuthStore } from '../../stores/auth';
import { 
    Workflow, User, Mail, Lock, Eye, EyeOff, UserPlus,
    Loader, AlertCircle, Chrome, Github, Building
} from 'lucide-vue-next';

const authStore = useAuthStore();

const loading = ref(false);
const showPassword = ref(false);
const errorMessage = ref('');
const passwordStrength = ref('');
const passwordStrengthPercentage = ref(0);

const form = reactive({
    name: '',
    email: '',
    organization_name: '',
    password: '',
    password_confirmation: '',
    agree_terms: false
});

const errors = reactive({
    name: '',
    email: '',
    organization_name: '',
    password: '',
    password_confirmation: '',
    agree_terms: ''
});

const passwordStrengthClass = computed(() => {
    const classes = {
        'Weak': 'text-red-600',
        'Fair': 'text-yellow-600',
        'Good': 'text-blue-600',
        'Strong': 'text-green-600'
    };
    return classes[passwordStrength.value] || 'text-gray-600';
});

const passwordStrengthBarClass = computed(() => {
    const classes = {
        'Weak': 'bg-red-500',
        'Fair': 'bg-yellow-500',
        'Good': 'bg-blue-500',
        'Strong': 'bg-green-500'
    };
    return classes[passwordStrength.value] || 'bg-gray-300';
});

const checkPasswordStrength = () => {
    const password = form.password;
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    const strengths = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Strong'];
    passwordStrength.value = strengths[strength];
    passwordStrengthPercentage.value = (strength / 5) * 100;
};

const validateForm = () => {
    let isValid = true;
    
    // Reset errors
    Object.keys(errors).forEach(key => errors[key] = '');
    
    if (!form.name) {
        errors.name = 'Name is required';
        isValid = false;
    }
    
    if (!form.email) {
        errors.email = 'Email is required';
        isValid = false;
    } else if (!/\S+@\S+\.\S+/.test(form.email)) {
        errors.email = 'Invalid email format';
        isValid = false;
    }
    
    if (!form.organization_name) {
        errors.organization_name = 'Organization name is required';
        isValid = false;
    }
    
    if (!form.password) {
        errors.password = 'Password is required';
        isValid = false;
    } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters';
        isValid = false;
    }
    
    if (!form.password_confirmation) {
        errors.password_confirmation = 'Please confirm your password';
        isValid = false;
    } else if (form.password !== form.password_confirmation) {
        errors.password_confirmation = 'Passwords do not match';
        isValid = false;
    }
    
    if (!form.agree_terms) {
        errors.agree_terms = 'You must agree to the terms';
        isValid = false;
    }
    
    return isValid;
};

const handleRegister = async () => {
    errorMessage.value = '';
    
    if (!validateForm()) {
        return;
    }
    
    loading.value = true;
    
    try {
        const result = await authStore.register(form);
        
        if (result.success) {
            // Redirect to workflows after successful registration
            router.visit('/workflows');
        } else {
            errorMessage.value = result.error || 'Registration failed. Please try again.';
            
            // Handle specific field errors
            if (result.errors) {
                Object.keys(result.errors).forEach(key => {
                    if (errors[key] !== undefined) {
                        errors[key] = result.errors[key][0];
                    }
                });
            }
        }
    } catch (error) {
        console.error('Registration error:', error);
        errorMessage.value = 'An unexpected error occurred. Please try again.';
    } finally {
        loading.value = false;
    }
};

const handleOAuth = (provider) => {
    window.location.href = `/api/v1/auth/oauth/${provider}`;
};
</script>

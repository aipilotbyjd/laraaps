<template>
    <MainLayout current-view="settings">
        <div class="h-full bg-gray-50 overflow-auto">
            <div class="max-w-4xl mx-auto px-4 py-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-8">Account Settings</h1>

                <!-- Tabs -->
                <div class="bg-white rounded-lg shadow">
                    <div class="border-b">
                        <nav class="flex -mb-px">
                            <button
                                v-for="tab in tabs"
                                :key="tab.id"
                                @click="activeTab = tab.id"
                                :class="[
                                    'px-6 py-3 text-sm font-medium border-b-2 transition-colors',
                                    activeTab === tab.id
                                        ? 'text-orange-600 border-orange-500'
                                        : 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300'
                                ]"
                            >
                                <component :is="tab.icon" :size="16" class="inline mr-2" />
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <div class="p-6">
                        <!-- Profile Tab -->
                        <div v-if="activeTab === 'profile'" class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
                            
                            <form @submit.prevent="updateProfile" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Full Name
                                        </label>
                                        <input
                                            v-model="profileForm.name"
                                            type="text"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Email Address
                                        </label>
                                        <input
                                            v-model="profileForm.email"
                                            type="email"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Bio
                                    </label>
                                    <textarea
                                        v-model="profileForm.bio"
                                        rows="3"
                                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        placeholder="Tell us about yourself..."
                                    ></textarea>
                                </div>

                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="profileLoading"
                                        class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg disabled:opacity-50"
                                    >
                                        <Loader v-if="profileLoading" :size="16" class="animate-spin inline mr-2" />
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div v-if="activeTab === 'security'" class="space-y-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h2>
                            
                            <!-- Change Password -->
                            <div class="border rounded-lg p-6">
                                <h3 class="font-medium text-gray-900 mb-4">Change Password</h3>
                                <form @submit.prevent="changePassword" class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Current Password
                                        </label>
                                        <input
                                            v-model="passwordForm.current_password"
                                            type="password"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            New Password
                                        </label>
                                        <input
                                            v-model="passwordForm.new_password"
                                            type="password"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Confirm New Password
                                        </label>
                                        <input
                                            v-model="passwordForm.new_password_confirmation"
                                            type="password"
                                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        />
                                    </div>
                                    
                                    <button
                                        type="submit"
                                        :disabled="passwordLoading"
                                        class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg disabled:opacity-50"
                                    >
                                        Update Password
                                    </button>
                                </form>
                            </div>

                            <!-- Two-Factor Authentication -->
                            <div class="border rounded-lg p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-medium text-gray-900">Two-Factor Authentication</h3>
                                    <span 
                                        :class="[
                                            'px-2 py-1 rounded text-xs font-medium',
                                            twoFactorEnabled
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-100 text-gray-600'
                                        ]"
                                    >
                                        {{ twoFactorEnabled ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-4">
                                    Add an extra layer of security to your account using two-factor authentication.
                                </p>
                                
                                <button
                                    @click="toggle2FA"
                                    :class="[
                                        'px-4 py-2 font-medium rounded-lg',
                                        twoFactorEnabled
                                            ? 'bg-red-500 hover:bg-red-600 text-white'
                                            : 'bg-green-500 hover:bg-green-600 text-white'
                                    ]"
                                >
                                    <Shield :size="16" class="inline mr-2" />
                                    {{ twoFactorEnabled ? 'Disable 2FA' : 'Enable 2FA' }}
                                </button>
                            </div>
                        </div>

                        <!-- Organizations Tab -->
                        <div v-if="activeTab === 'organizations'" class="space-y-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">Organizations</h2>
                                <button
                                    @click="showCreateOrg = true"
                                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg"
                                >
                                    <Plus :size="16" class="inline mr-2" />
                                    New Organization
                                </button>
                            </div>

                            <div class="space-y-3">
                                <div
                                    v-for="org in organizations"
                                    :key="org.id"
                                    class="border rounded-lg p-4 flex items-center justify-between"
                                    :class="{ 'bg-orange-50 border-orange-300': org.id === currentOrganization?.id }"
                                >
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ org.name }}</h4>
                                        <p class="text-sm text-gray-500">{{ org.member_count || 1 }} members</p>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <button
                                            v-if="org.id !== currentOrganization?.id"
                                            @click="switchOrganization(org.id)"
                                            class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-medium text-sm"
                                        >
                                            Switch
                                        </button>
                                        <span v-else class="px-3 py-1 bg-orange-500 text-white rounded font-medium text-sm">
                                            Current
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- API Keys Tab -->
                        <div v-if="activeTab === 'api'" class="space-y-6">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-semibold text-gray-900">API Keys</h2>
                                <button
                                    @click="createApiKey"
                                    class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg"
                                >
                                    <Plus :size="16" class="inline mr-2" />
                                    Generate Key
                                </button>
                            </div>

                            <div v-if="apiKeys.length === 0" class="text-center py-8 text-gray-500">
                                No API keys generated yet
                            </div>
                            
                            <div v-else class="space-y-3">
                                <div
                                    v-for="apiKey in apiKeys"
                                    :key="apiKey.id"
                                    class="border rounded-lg p-4 flex items-center justify-between"
                                >
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ apiKey.name }}</h4>
                                        <p class="text-sm text-gray-500 font-mono">
                                            {{ apiKey.key.substring(0, 20) }}...
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Created {{ formatDate(apiKey.created_at) }}
                                        </p>
                                    </div>
                                    
                                    <button
                                        @click="deleteApiKey(apiKey.id)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded"
                                    >
                                        <Trash2 :size="16" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import MainLayout from '../../components/MainLayout.vue';
import { 
    User, Shield, Building, Key, Plus, Trash2, 
    Loader, CheckCircle, AlertCircle 
} from 'lucide-vue-next';

const authStore = useAuthStore();

const activeTab = ref('profile');
const profileLoading = ref(false);
const passwordLoading = ref(false);
const showCreateOrg = ref(false);

const tabs = [
    { id: 'profile', label: 'Profile', icon: User },
    { id: 'security', label: 'Security', icon: Shield },
    { id: 'organizations', label: 'Organizations', icon: Building },
    { id: 'api', label: 'API Keys', icon: Key }
];

const profileForm = reactive({
    name: authStore.user?.name || '',
    email: authStore.user?.email || '',
    bio: authStore.user?.bio || ''
});

const passwordForm = reactive({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

const organizations = computed(() => authStore.organizations);
const currentOrganization = computed(() => authStore.currentOrganization);
const apiKeys = computed(() => authStore.apiKeys);
const twoFactorEnabled = computed(() => authStore.twoFactorEnabled);

onMounted(async () => {
    await authStore.fetchOrganizations();
    await authStore.fetchApiKeys();
});

const updateProfile = async () => {
    profileLoading.value = true;
    try {
        const result = await authStore.updateProfile(profileForm);
        if (result.success) {
            // Show success message
            console.log('Profile updated successfully');
        } else {
            console.error('Profile update failed:', result.error);
        }
    } finally {
        profileLoading.value = false;
    }
};

const changePassword = async () => {
    passwordLoading.value = true;
    try {
        const result = await authStore.changePassword(passwordForm);
        if (result.success) {
            // Clear form
            passwordForm.current_password = '';
            passwordForm.new_password = '';
            passwordForm.new_password_confirmation = '';
            // Show success message
            console.log('Password changed successfully');
        } else {
            console.error('Password change failed:', result.error);
        }
    } finally {
        passwordLoading.value = false;
    }
};

const toggle2FA = async () => {
    if (twoFactorEnabled.value) {
        // Disable 2FA
        const code = prompt('Enter your 2FA code to disable:');
        if (code) {
            const result = await authStore.disable2FA(code);
            if (!result.success) {
                alert('Failed to disable 2FA: ' + result.error);
            }
        }
    } else {
        // Enable 2FA
        const result = await authStore.enable2FA();
        if (result.success) {
            // Show QR code in modal
            alert('2FA enabled! QR Code: ' + result.qrCode);
        }
    }
};

const switchOrganization = (orgId) => {
    authStore.switchOrganization(orgId);
};

const createApiKey = async () => {
    const name = prompt('Enter a name for the API key:');
    if (name) {
        const result = await authStore.createApiKey(name);
        if (result.success) {
            alert('API Key created: ' + result.apiKey.key);
        }
    }
};

const deleteApiKey = async (id) => {
    if (confirm('Are you sure you want to delete this API key?')) {
        await authStore.deleteApiKey(id);
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString();
};
</script>

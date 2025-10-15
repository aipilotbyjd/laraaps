<template>
    <div class="h-screen flex flex-col bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-gray-200 px-4 py-2 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-sm">n8n</span>
                    </div>
                    <span class="font-semibold text-gray-900">Workflow Automation</span>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-1 ml-8">
                    <button
                        @click="navigateTo('workflows')"
                        :class="[
                            'px-3 py-1.5 rounded text-sm font-medium',
                            currentView === 'workflows' 
                                ? 'bg-orange-50 text-orange-600' 
                                : 'text-gray-600 hover:bg-gray-100'
                        ]"
                    >
                        <Workflow :size="16" class="inline mr-1" />
                        Workflows
                    </button>
                    <button
                        @click="navigateTo('executions')"
                        :class="[
                            'px-3 py-1.5 rounded text-sm font-medium',
                            currentView === 'executions' 
                                ? 'bg-orange-50 text-orange-600' 
                                : 'text-gray-600 hover:bg-gray-100'
                        ]"
                    >
                        <Play :size="16" class="inline mr-1" />
                        Executions
                    </button>
                    <button
                        @click="navigateTo('credentials')"
                        :class="[
                            'px-3 py-1.5 rounded text-sm font-medium',
                            currentView === 'credentials' 
                                ? 'bg-orange-50 text-orange-600' 
                                : 'text-gray-600 hover:bg-gray-100'
                        ]"
                    >
                        <Key :size="16" class="inline mr-1" />
                        Credentials
                    </button>
                    <button
                        @click="navigateTo('templates')"
                        :class="[
                            'px-3 py-1.5 rounded text-sm font-medium',
                            currentView === 'templates' 
                                ? 'bg-orange-50 text-orange-600' 
                                : 'text-gray-600 hover:bg-gray-100'
                        ]"
                    >
                        <FileText :size="16" class="inline mr-1" />
                        Templates
                    </button>
                </div>
            </div>

            <!-- Right side actions -->
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-600 hover:bg-gray-100 rounded">
                    <Bell :size="20" />
                </button>
                <button 
                    @click="navigateTo('settings')"
                    class="p-2 text-gray-600 hover:bg-gray-100 rounded"
                >
                    <Settings :size="20" />
                </button>
                
                <!-- User Menu -->
                <div class="relative ml-2">
                    <button
                        @click="showUserMenu = !showUserMenu"
                        class="flex items-center space-x-2 p-1 hover:bg-gray-100 rounded-lg"
                    >
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-medium">
                            {{ userInitials }}
                        </div>
                        <ChevronDown :size="16" class="text-gray-600" />
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div
                        v-if="showUserMenu"
                        v-click-outside="() => showUserMenu = false"
                        class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border z-50"
                    >
                        <div class="px-4 py-3 border-b">
                            <p class="text-sm font-medium text-gray-900">{{ userName }}</p>
                            <p class="text-xs text-gray-500">{{ userEmail }}</p>
                        </div>
                        
                        <div class="py-1">
                            <button
                                @click="navigateTo('profile')"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <User :size="16" class="inline mr-2" />
                                Profile Settings
                            </button>
                            
                            <button
                                @click="navigateTo('organizations')"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <Building :size="16" class="inline mr-2" />
                                Organizations
                            </button>
                            
                            <button
                                @click="navigateTo('api-keys')"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <Key :size="16" class="inline mr-2" />
                                API Keys
                            </button>
                        </div>
                        
                        <div class="border-t py-1">
                            <button
                                @click="handleLogout"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            >
                                <LogOut :size="16" class="inline mr-2" />
                                Sign Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-hidden">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Workflow, Play, Key, FileText, Bell, Settings, ChevronDown, User, Building, LogOut } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
    currentView: {
        type: String,
        default: 'workflows'
    }
});

const authStore = useAuthStore();
const showUserMenu = ref(false);

const userName = computed(() => authStore.userName || 'User');
const userEmail = computed(() => authStore.userEmail || 'user@example.com');
const userInitials = computed(() => {
    const name = authStore.userName || 'U';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const navigateTo = (view) => {
    const routes = {
        workflows: '/workflows',
        executions: '/executions',
        credentials: '/credentials',
        templates: '/templates',
        profile: '/profile/settings',
        settings: '/profile/settings',
        organizations: '/profile/settings?tab=organizations',
        'api-keys': '/profile/settings?tab=api'
    };
    
    if (routes[view]) {
        showUserMenu.value = false;
        router.visit(routes[view]);
    }
};

const handleLogout = async () => {
    showUserMenu.value = false;
    await authStore.logout();
};

// Click outside directive (simple implementation)
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value();
            }
        };
        document.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
    }
};
</script>

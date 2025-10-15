<template>
    <MainLayout current-view="workflows">
        <div class="h-full flex flex-col">
            <!-- Header with actions -->
            <div class="bg-white border-b px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">Workflows</h1>
                        <p class="text-sm text-gray-600 mt-1">Manage and monitor your automation workflows</p>
                    </div>
                    <button
                        @click="createNewWorkflow"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2"
                    >
                        <Plus :size="20" />
                        <span>Create Workflow</span>
                    </button>
                </div>
            </div>

            <!-- Search and filters -->
            <div class="bg-white border-b px-6 py-3">
                <div class="flex items-center space-x-4">
                    <div class="relative flex-1 max-w-md">
                        <Search :size="20" class="absolute left-3 top-2.5 text-gray-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search workflows..."
                            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        >
                    </div>
                    <select
                        v-model="statusFilter"
                        class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                    >
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <button class="p-2 border rounded-lg hover:bg-gray-50">
                        <Filter :size="20" />
                    </button>
                </div>
            </div>

            <!-- Workflows list -->
            <div class="flex-1 overflow-auto bg-gray-50 p-6">
                <div v-if="loading" class="flex items-center justify-center h-64">
                    <div class="text-gray-500">Loading workflows...</div>
                </div>

                <div v-else-if="filteredWorkflows.length === 0" class="bg-white rounded-lg p-12 text-center">
                    <Workflow :size="48" class="mx-auto text-gray-300 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No workflows found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first workflow</p>
                    <button
                        @click="createNewWorkflow"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-medium"
                    >
                        Create First Workflow
                    </button>
                </div>

                <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="workflow in filteredWorkflows"
                        :key="workflow.id"
                        class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-shadow cursor-pointer"
                        @click="openWorkflow(workflow.id)"
                    >
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-medium text-gray-900">{{ workflow.name }}</h3>
                                <div class="flex items-center space-x-1">
                                    <span
                                        :class="[
                                            'px-2 py-1 rounded text-xs font-medium',
                                            workflow.is_active
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-100 text-gray-600'
                                        ]"
                                    >
                                        {{ workflow.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <button
                                        @click.stop="toggleWorkflowMenu(workflow.id)"
                                        class="p-1 hover:bg-gray-100 rounded"
                                    >
                                        <MoreVertical :size="16" />
                                    </button>
                                </div>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-3">
                                {{ workflow.description || 'No description' }}
                            </p>

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <div class="flex items-center space-x-3">
                                    <span class="flex items-center">
                                        <Clock :size="14" class="mr-1" />
                                        {{ formatDate(workflow.updated_at) }}
                                    </span>
                                    <span class="flex items-center">
                                        <Activity :size="14" class="mr-1" />
                                        {{ workflow.execution_count || 0 }} runs
                                    </span>
                                </div>
                            </div>

                            <!-- Dropdown menu -->
                            <div
                                v-if="openMenuId === workflow.id"
                                class="absolute right-4 mt-2 w-48 bg-white rounded-lg shadow-lg border z-10"
                                @click.stop
                            >
                                <button
                                    @click="editWorkflow(workflow.id)"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm"
                                >
                                    <Edit :size="16" class="inline mr-2" />
                                    Edit
                                </button>
                                <button
                                    @click="duplicateWorkflow(workflow.id)"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm"
                                >
                                    <Copy :size="16" class="inline mr-2" />
                                    Duplicate
                                </button>
                                <button
                                    @click="toggleWorkflowStatus(workflow)"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm"
                                >
                                    <ToggleLeft :size="16" class="inline mr-2" />
                                    {{ workflow.is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <hr class="my-1" />
                                <button
                                    @click="deleteWorkflow(workflow.id)"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-red-600"
                                >
                                    <Trash :size="16" class="inline mr-2" />
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useWorkflowStore } from '../../stores/workflow';
import MainLayout from '../../components/MainLayout.vue';
import {
    Plus, Search, Filter, Workflow, Clock, Activity,
    MoreVertical, Edit, Copy, Trash, ToggleLeft
} from 'lucide-vue-next';

const workflowStore = useWorkflowStore();

const loading = ref(false);
const searchQuery = ref('');
const statusFilter = ref('');
const openMenuId = ref(null);

const filteredWorkflows = computed(() => {
    let workflows = workflowStore.workflows;
    
    if (searchQuery.value) {
        workflows = workflows.filter(w =>
            w.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (w.description && w.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
        );
    }
    
    if (statusFilter.value) {
        workflows = workflows.filter(w =>
            statusFilter.value === 'active' ? w.is_active : !w.is_active
        );
    }
    
    return workflows;
});

onMounted(async () => {
    loading.value = true;
    try {
        await workflowStore.fetchWorkflows();
    } catch (error) {
        console.error('Failed to load workflows:', error);
    } finally {
        loading.value = false;
    }
});

const createNewWorkflow = () => {
    router.visit('/workflows/create');
};

const openWorkflow = (id) => {
    router.visit(`/workflows/${id}/edit`);
};

const editWorkflow = (id) => {
    openMenuId.value = null;
    router.visit(`/workflows/${id}/edit`);
};

const duplicateWorkflow = async (id) => {
    openMenuId.value = null;
    try {
        await workflowStore.duplicateWorkflow(id);
        await workflowStore.fetchWorkflows();
    } catch (error) {
        console.error('Failed to duplicate workflow:', error);
    }
};

const toggleWorkflowStatus = async (workflow) => {
    openMenuId.value = null;
    try {
        if (workflow.is_active) {
            await workflowStore.deactivateWorkflow(workflow.id);
        } else {
            await workflowStore.activateWorkflow(workflow.id);
        }
    } catch (error) {
        console.error('Failed to toggle workflow status:', error);
    }
};

const deleteWorkflow = async (id) => {
    openMenuId.value = null;
    if (confirm('Are you sure you want to delete this workflow?')) {
        try {
            await workflowStore.deleteWorkflow(id);
        } catch (error) {
            console.error('Failed to delete workflow:', error);
        }
    }
};

const toggleWorkflowMenu = (id) => {
    openMenuId.value = openMenuId.value === id ? null : id;
};

const formatDate = (date) => {
    if (!date) return 'Never';
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};
</script>

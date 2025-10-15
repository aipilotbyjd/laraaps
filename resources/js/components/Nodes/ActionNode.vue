<template>
    <div 
        class="action-node bg-white rounded-lg shadow-md border-2 border-blue-400 min-w-[180px]"
        :class="{ 'ring-2 ring-blue-500': selected }"
    >
        <!-- Input handle -->
        <Handle
            type="target"
            :position="Position.Top"
            class="w-3 h-3 bg-blue-500 border-2 border-white"
        />

        <div class="px-3 py-2 bg-blue-50 rounded-t-lg border-b border-blue-200">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-blue-500 rounded flex items-center justify-center">
                    <component :is="getIcon(data.icon)" :size="14" class="text-white" />
                </div>
                <span class="text-sm font-medium text-blue-900">Action</span>
            </div>
        </div>
        
        <div class="px-3 py-2">
            <div class="text-sm font-medium text-gray-900">{{ data.label }}</div>
            <div v-if="data.description" class="text-xs text-gray-500 mt-1">
                {{ data.description }}
            </div>
            
            <!-- Show execution status if available -->
            <div v-if="data.status" class="mt-2">
                <div 
                    class="inline-flex items-center px-2 py-1 rounded text-xs"
                    :class="getStatusClass(data.status)"
                >
                    <component :is="getStatusIcon(data.status)" :size="12" class="mr-1" />
                    {{ data.status }}
                </div>
            </div>
        </div>

        <!-- Output handle -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="w-3 h-3 bg-blue-500 border-2 border-white"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import { 
    Box, Globe, Database, Mail, Code, Filter, 
    MessageSquare, FileText, Cloud, Activity,
    CheckCircle, XCircle, Clock, Loader
} from 'lucide-vue-next';

defineProps({
    data: {
        type: Object,
        required: true
    },
    selected: {
        type: Boolean,
        default: false
    }
});

const getIcon = (iconName) => {
    const icons = {
        'Globe': Globe,
        'Database': Database,
        'Mail': Mail,
        'Code': Code,
        'Filter': Filter,
        'MessageSquare': MessageSquare,
        'FileText': FileText,
        'Cloud': Cloud,
        'Activity': Activity,
        'Box': Box
    };
    return icons[iconName] || Box;
};

const getStatusClass = (status) => {
    const classes = {
        'success': 'bg-green-100 text-green-700',
        'error': 'bg-red-100 text-red-700',
        'running': 'bg-blue-100 text-blue-700',
        'waiting': 'bg-yellow-100 text-yellow-700'
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};

const getStatusIcon = (status) => {
    const icons = {
        'success': CheckCircle,
        'error': XCircle,
        'running': Loader,
        'waiting': Clock
    };
    return icons[status] || Clock;
};
</script>

<style scoped>
.action-node {
    transition: all 0.2s ease;
}

.action-node:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

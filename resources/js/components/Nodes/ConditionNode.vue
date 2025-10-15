<template>
    <div 
        class="condition-node bg-white rounded-lg shadow-md border-2 border-orange-400 min-w-[180px]"
        :class="{ 'ring-2 ring-orange-500': selected }"
    >
        <!-- Input handle -->
        <Handle
            type="target"
            :position="Position.Top"
            class="w-3 h-3 bg-orange-500 border-2 border-white"
        />

        <div class="px-3 py-2 bg-orange-50 rounded-t-lg border-b border-orange-200">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-orange-500 rounded flex items-center justify-center">
                    <GitBranch :size="14" class="text-white" />
                </div>
                <span class="text-sm font-medium text-orange-900">Condition</span>
            </div>
        </div>
        
        <div class="px-3 py-2">
            <div class="text-sm font-medium text-gray-900">{{ data.label }}</div>
            <div v-if="data.description" class="text-xs text-gray-500 mt-1">
                {{ data.description }}
            </div>
            
            <!-- Show condition info if configured -->
            <div v-if="data.config && data.config.field" class="mt-2 text-xs bg-gray-50 px-2 py-1 rounded">
                <code class="text-orange-600">
                    {{ data.config.field }} {{ getOperatorSymbol(data.config.operator) }} {{ data.config.value }}
                </code>
            </div>
        </div>

        <!-- True output handle -->
        <Handle
            id="true"
            type="source"
            :position="Position.Right"
            :style="{ top: '50%' }"
            class="w-3 h-3 bg-green-500 border-2 border-white"
        />
        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-green-600 font-medium">
            True
        </div>

        <!-- False output handle -->
        <Handle
            id="false"
            type="source"
            :position="Position.Bottom"
            class="w-3 h-3 bg-red-500 border-2 border-white"
        />
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-xs text-red-600 font-medium">
            False
        </div>
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import { GitBranch } from 'lucide-vue-next';

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

const getOperatorSymbol = (operator) => {
    const symbols = {
        'equals': '==',
        'not_equals': '!=',
        'contains': '∋',
        'greater': '>',
        'less': '<',
        'greater_equal': '>=',
        'less_equal': '<='
    };
    return symbols[operator] || operator;
};
</script>

<style scoped>
.condition-node {
    transition: all 0.2s ease;
    position: relative;
}

.condition-node:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

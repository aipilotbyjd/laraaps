<template>
    <div 
        class="trigger-node bg-white rounded-lg shadow-md border-2 border-purple-400 min-w-[180px]"
        :class="{ 'ring-2 ring-purple-500': selected }"
    >
        <div class="px-3 py-2 bg-purple-50 rounded-t-lg border-b border-purple-200">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-purple-500 rounded flex items-center justify-center">
                    <Zap :size="14" class="text-white" />
                </div>
                <span class="text-sm font-medium text-purple-900">Trigger</span>
            </div>
        </div>
        
        <div class="px-3 py-2">
            <div class="text-sm font-medium text-gray-900">{{ data.label }}</div>
            <div v-if="data.description" class="text-xs text-gray-500 mt-1">
                {{ data.description }}
            </div>
        </div>

        <!-- Output handle -->
        <Handle
            type="source"
            :position="Position.Bottom"
            class="w-3 h-3 bg-purple-500 border-2 border-white"
        />
    </div>
</template>

<script setup>
import { Handle, Position } from '@vue-flow/core';
import { Zap } from 'lucide-vue-next';

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
</script>

<style scoped>
.trigger-node {
    transition: all 0.2s ease;
}

.trigger-node:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

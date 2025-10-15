<template>
    <div class="w-80 bg-white border-l overflow-y-auto">
        <div class="p-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Node Settings</h3>
            <button @click="emit('close')" class="p-1 hover:bg-gray-100 rounded">
                <X :size="20" />
            </button>
        </div>

        <div class="p-4">
            <!-- Node Info -->
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div
                        :class="[
                            'w-10 h-10 rounded flex items-center justify-center',
                            getNodeColorClass(node.data.category)
                        ]"
                    >
                        <component :is="getNodeIcon(node.data.icon)" :size="20" />
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">{{ node.data.label }}</h4>
                        <p class="text-sm text-gray-500">{{ node.type }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Node Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Node Name
                        </label>
                        <input
                            v-model="nodeData.label"
                            @input="updateNode"
                            type="text"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        />
                    </div>

                    <!-- Node Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea
                            v-model="nodeData.description"
                            @input="updateNode"
                            rows="2"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Add a description..."
                        />
                    </div>
                </div>
            </div>

            <!-- Node Type Specific Settings -->
            <div class="border-t pt-4">
                <h4 class="font-medium text-gray-900 mb-4">Configuration</h4>
                
                <!-- Webhook Node Settings -->
                <div v-if="node.type === 'webhook'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            HTTP Method
                        </label>
                        <select
                            v-model="nodeData.config.method"
                            @change="updateNode"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        >
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                            <option value="PATCH">PATCH</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Path
                        </label>
                        <input
                            v-model="nodeData.config.path"
                            @input="updateNode"
                            type="text"
                            placeholder="/webhook/endpoint"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        />
                    </div>
                </div>

                <!-- HTTP Request Node Settings -->
                <div v-else-if="node.type === 'http'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            URL
                        </label>
                        <input
                            v-model="nodeData.config.url"
                            @input="updateNode"
                            type="text"
                            placeholder="https://api.example.com/endpoint"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Method
                        </label>
                        <select
                            v-model="nodeData.config.method"
                            @change="updateNode"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        >
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                            <option value="PATCH">PATCH</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Headers
                        </label>
                        <textarea
                            v-model="nodeData.config.headers"
                            @input="updateNode"
                            rows="3"
                            placeholder='{"Content-Type": "application/json"}'
                            class="w-full px-3 py-2 border rounded-lg font-mono text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Body
                        </label>
                        <textarea
                            v-model="nodeData.config.body"
                            @input="updateNode"
                            rows="4"
                            placeholder="Request body (JSON)"
                            class="w-full px-3 py-2 border rounded-lg font-mono text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        />
                    </div>
                </div>

                <!-- Code Node Settings -->
                <div v-else-if="node.type === 'code'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Language
                        </label>
                        <select
                            v-model="nodeData.config.language"
                            @change="updateNode"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                        >
                            <option value="javascript">JavaScript</option>
                            <option value="python">Python</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Code
                        </label>
                        <textarea
                            v-model="nodeData.config.code"
                            @input="updateNode"
                            rows="10"
                            placeholder="// Your code here"
                            class="w-full px-3 py-2 border rounded-lg font-mono text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        />
                    </div>
                </div>

                <!-- IF Node Settings -->
                <div v-else-if="node.type === 'if'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Condition
                        </label>
                        <div class="flex space-x-2">
                            <input
                                v-model="nodeData.config.field"
                                @input="updateNode"
                                type="text"
                                placeholder="Field"
                                class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            />
                            <select
                                v-model="nodeData.config.operator"
                                @change="updateNode"
                                class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            >
                                <option value="equals">=</option>
                                <option value="not_equals">!=</option>
                                <option value="contains">Contains</option>
                                <option value="greater">&gt;</option>
                                <option value="less">&lt;</option>
                            </select>
                            <input
                                v-model="nodeData.config.value"
                                @input="updateNode"
                                type="text"
                                placeholder="Value"
                                class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Default/Generic Settings -->
                <div v-else class="space-y-4">
                    <div class="text-sm text-gray-500">
                        No configuration options available for this node type.
                    </div>
                </div>
            </div>

            <!-- Test Node Button -->
            <div class="mt-6 pt-6 border-t">
                <button
                    @click="testNode"
                    class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium"
                >
                    <PlayCircle :size="16" class="inline mr-2" />
                    Test Node
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, reactive } from 'vue';
import { X, PlayCircle } from 'lucide-vue-next';
import { 
    Globe, Clock, Zap, Database, Mail, GitBranch, 
    Code, Filter, MessageSquare, Calendar, FileText, 
    Shield, Cloud, Box, Activity, Hash, Type 
} from 'lucide-vue-next';

const props = defineProps({
    node: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['update', 'close']);

const nodeData = reactive({
    label: props.node.data.label || '',
    description: props.node.data.description || '',
    config: props.node.data.config || {}
});

// Initialize config if not exists
if (!nodeData.config.method && (props.node.type === 'webhook' || props.node.type === 'http')) {
    nodeData.config.method = 'POST';
}
if (!nodeData.config.language && props.node.type === 'code') {
    nodeData.config.language = 'javascript';
}
if (!nodeData.config.operator && props.node.type === 'if') {
    nodeData.config.operator = 'equals';
}

watch(() => props.node, (newNode) => {
    nodeData.label = newNode.data.label || '';
    nodeData.description = newNode.data.description || '';
    nodeData.config = newNode.data.config || {};
}, { deep: true });

const updateNode = () => {
    emit('update', props.node.id, {
        ...props.node.data,
        label: nodeData.label,
        description: nodeData.description,
        config: nodeData.config
    });
};

const testNode = () => {
    console.log('Testing node:', props.node);
    // Implement node testing logic
};

const getNodeColorClass = (category) => {
    const colors = {
        'Triggers': 'bg-purple-100 text-purple-600',
        'Actions': 'bg-blue-100 text-blue-600',
        'Flow Control': 'bg-orange-100 text-orange-600',
        'Data Processing': 'bg-green-100 text-green-600',
        'Integrations': 'bg-pink-100 text-pink-600',
    };
    return colors[category] || 'bg-gray-100 text-gray-600';
};

const getNodeIcon = (iconName) => {
    const icons = {
        'Globe': Globe,
        'Clock': Clock,
        'Zap': Zap,
        'Database': Database,
        'Mail': Mail,
        'GitBranch': GitBranch,
        'Code': Code,
        'Filter': Filter,
        'MessageSquare': MessageSquare,
        'Calendar': Calendar,
        'FileText': FileText,
        'Shield': Shield,
        'Cloud': Cloud,
        'Box': Box,
        'Activity': Activity,
        'Hash': Hash,
        'Type': Type,
    };
    return icons[iconName] || Box;
};
</script>

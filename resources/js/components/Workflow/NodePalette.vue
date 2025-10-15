<template>
    <div class="w-64 bg-white border-r overflow-y-auto">
        <div class="p-4 border-b">
            <div class="relative">
                <Search :size="16" class="absolute left-3 top-2.5 text-gray-400" />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search nodes..."
                    class="w-full pl-9 pr-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                />
            </div>
        </div>

        <div class="p-4">
            <div v-for="category in filteredCategories" :key="category.name" class="mb-6">
                <button
                    @click="toggleCategory(category.name)"
                    class="flex items-center justify-between w-full text-left mb-2"
                >
                    <span class="text-xs font-semibold text-gray-500 uppercase">
                        {{ category.name }}
                    </span>
                    <ChevronDown
                        :size="14"
                        :class="[
                            'text-gray-400 transition-transform',
                            collapsedCategories[category.name] ? '-rotate-90' : ''
                        ]"
                    />
                </button>
                
                <div
                    v-show="!collapsedCategories[category.name]"
                    class="space-y-2"
                >
                    <div
                        v-for="node in getCategoryNodes(category.name)"
                        :key="node.type"
                        @dragstart="handleDragStart($event, node)"
                        draggable="true"
                        class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 cursor-move transition-colors"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                :class="[
                                    'w-8 h-8 rounded flex items-center justify-center',
                                    getNodeColorClass(node.category)
                                ]"
                            >
                                <component :is="getNodeIcon(node.icon)" :size="16" />
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ node.name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ node.description }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regular Nodes -->
            <div v-if="regularNodes.length > 0" class="mb-6">
                <div class="text-xs font-semibold text-gray-500 uppercase mb-2">
                    Common
                </div>
                <div class="space-y-2">
                    <div
                        v-for="node in regularNodes"
                        :key="node.type"
                        @click="() => emit('add-node', node)"
                        class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 cursor-pointer transition-colors"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                                <component :is="getNodeIcon(node.icon)" :size="16" />
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ node.name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ node.description }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { 
    Search, ChevronDown, Zap, Globe, Database, 
    Mail, MessageSquare, Calendar, FileText, 
    GitBranch, Code, Shield, Cloud, Box,
    Activity, Hash, Type, Clock, Filter
} from 'lucide-vue-next';

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    nodes: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['add-node']);

const searchQuery = ref('');
const collapsedCategories = ref({});

// Sample nodes data - in production this would come from the API
const sampleNodes = [
    // Triggers
    { 
        type: 'webhook', 
        category: 'Triggers', 
        name: 'Webhook', 
        description: 'Start workflow on webhook',
        icon: 'Globe',
        nodeType: 'trigger'
    },
    { 
        type: 'schedule', 
        category: 'Triggers', 
        name: 'Schedule', 
        description: 'Run on schedule',
        icon: 'Clock',
        nodeType: 'trigger'
    },
    { 
        type: 'manual', 
        category: 'Triggers', 
        name: 'Manual', 
        description: 'Trigger manually',
        icon: 'Zap',
        nodeType: 'trigger'
    },
    
    // Actions
    { 
        type: 'http', 
        category: 'Actions', 
        name: 'HTTP Request', 
        description: 'Make HTTP requests',
        icon: 'Globe',
        nodeType: 'action'
    },
    { 
        type: 'database', 
        category: 'Actions', 
        name: 'Database', 
        description: 'Query database',
        icon: 'Database',
        nodeType: 'action'
    },
    { 
        type: 'email', 
        category: 'Actions', 
        name: 'Send Email', 
        description: 'Send email messages',
        icon: 'Mail',
        nodeType: 'action'
    },
    
    // Flow Control
    { 
        type: 'if', 
        category: 'Flow Control', 
        name: 'IF', 
        description: 'Conditional branching',
        icon: 'GitBranch',
        nodeType: 'condition'
    },
    { 
        type: 'switch', 
        category: 'Flow Control', 
        name: 'Switch', 
        description: 'Multiple conditions',
        icon: 'GitBranch',
        nodeType: 'condition'
    },
    
    // Data Processing
    { 
        type: 'code', 
        category: 'Data Processing', 
        name: 'Code', 
        description: 'Execute custom code',
        icon: 'Code',
        nodeType: 'action'
    },
    { 
        type: 'filter', 
        category: 'Data Processing', 
        name: 'Filter', 
        description: 'Filter data items',
        icon: 'Filter',
        nodeType: 'action'
    },
];

const allNodes = computed(() => {
    return props.nodes.length > 0 ? props.nodes : sampleNodes;
});

const filteredCategories = computed(() => {
    const categories = new Set();
    
    allNodes.value.forEach(node => {
        if (!searchQuery.value || 
            node.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            node.description.toLowerCase().includes(searchQuery.value.toLowerCase())) {
            categories.add(node.category);
        }
    });
    
    return Array.from(categories).map(name => ({ name }));
});

const regularNodes = computed(() => {
    return allNodes.value.filter(node => {
        const matchesSearch = !searchQuery.value || 
            node.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            node.description.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        return matchesSearch && !node.category;
    });
});

const getCategoryNodes = (category) => {
    return allNodes.value.filter(node => {
        const matchesSearch = !searchQuery.value || 
            node.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            node.description.toLowerCase().includes(searchQuery.value.toLowerCase());
        
        return matchesSearch && node.category === category;
    });
};

const toggleCategory = (category) => {
    collapsedCategories.value[category] = !collapsedCategories.value[category];
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

const handleDragStart = (event, node) => {
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('application/vueflow', JSON.stringify(node));
};
</script>

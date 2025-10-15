<template>
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg transition-all duration-300"
         :style="{ height: panelHeight + 'px' }">
        
        <!-- Resize Handle -->
        <div 
            @mousedown="startResize"
            class="absolute top-0 left-0 right-0 h-1 bg-gray-200 hover:bg-orange-400 cursor-ns-resize"
        />
        
        <!-- Panel Header -->
        <div class="flex items-center justify-between px-4 py-2 border-b">
            <div class="flex items-center space-x-4">
                <h3 class="font-semibold text-gray-900">Execution</h3>
                <div v-if="execution" class="flex items-center space-x-2">
                    <span 
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                        :class="getStatusClass(execution.status)"
                    >
                        <component :is="getStatusIcon(execution.status)" :size="12" class="mr-1" />
                        {{ execution.status }}
                    </span>
                    <span class="text-sm text-gray-500">
                        ID: {{ execution.id }}
                    </span>
                    <span v-if="execution.started_at" class="text-sm text-gray-500">
                        Started: {{ formatTime(execution.started_at) }}
                    </span>
                    <span v-if="execution.completed_at" class="text-sm text-gray-500">
                        Duration: {{ calculateDuration(execution.started_at, execution.completed_at) }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <button
                    v-if="execution && execution.status === 'running'"
                    @click="stopExecution"
                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium"
                >
                    <StopCircle :size="14" class="inline mr-1" />
                    Stop
                </button>
                <button
                    v-if="execution && execution.status === 'error'"
                    @click="retryExecution"
                    class="px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white rounded text-sm font-medium"
                >
                    <RefreshCw :size="14" class="inline mr-1" />
                    Retry
                </button>
                <button @click="emit('close')" class="p-1 hover:bg-gray-100 rounded">
                    <X :size="20" />
                </button>
            </div>
        </div>
        
        <!-- Panel Content -->
        <div class="flex h-full">
            <!-- Tabs -->
            <div class="w-48 border-r bg-gray-50 p-2">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'w-full text-left px-3 py-2 rounded text-sm font-medium mb-1',
                        activeTab === tab.id
                            ? 'bg-white text-orange-600 shadow-sm'
                            : 'text-gray-600 hover:bg-white'
                    ]"
                >
                    <component :is="tab.icon" :size="14" class="inline mr-2" />
                    {{ tab.label }}
                </button>
            </div>
            
            <!-- Tab Content -->
            <div class="flex-1 overflow-auto p-4">
                <!-- Output Tab -->
                <div v-if="activeTab === 'output'">
                    <div v-if="!execution">
                        <div class="text-gray-500 text-center py-8">
                            No execution running. Click "Execute" to start.
                        </div>
                    </div>
                    <div v-else-if="execution.output">
                        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-x-auto">{{ JSON.stringify(execution.output, null, 2) }}</pre>
                    </div>
                    <div v-else>
                        <div class="text-gray-500 text-center py-8">
                            <Loader class="animate-spin mx-auto mb-2" :size="24" />
                            Waiting for output...
                        </div>
                    </div>
                </div>
                
                <!-- Logs Tab -->
                <div v-if="activeTab === 'logs'">
                    <div v-if="executionLogs.length === 0" class="text-gray-500 text-center py-8">
                        No logs available
                    </div>
                    <div v-else class="space-y-2">
                        <div 
                            v-for="(log, index) in executionLogs" 
                            :key="index"
                            class="flex items-start space-x-2 text-sm"
                        >
                            <span class="text-gray-400 font-mono">{{ formatTime(log.timestamp) }}</span>
                            <span 
                                class="px-2 py-0.5 rounded text-xs font-medium"
                                :class="getLogLevelClass(log.level)"
                            >
                                {{ log.level }}
                            </span>
                            <span class="flex-1 font-mono">{{ log.message }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Nodes Tab -->
                <div v-if="activeTab === 'nodes'">
                    <div v-if="nodeExecutions.length === 0" class="text-gray-500 text-center py-8">
                        No node executions yet
                    </div>
                    <div v-else class="space-y-4">
                        <div 
                            v-for="node in nodeExecutions" 
                            :key="node.id"
                            class="bg-gray-50 rounded-lg p-4"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">{{ node.name }}</h4>
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                                    :class="getStatusClass(node.status)"
                                >
                                    {{ node.status }}
                                </span>
                            </div>
                            <div v-if="node.input" class="mb-2">
                                <div class="text-xs text-gray-500 mb-1">Input:</div>
                                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">{{ JSON.stringify(node.input, null, 2) }}</pre>
                            </div>
                            <div v-if="node.output">
                                <div class="text-xs text-gray-500 mb-1">Output:</div>
                                <pre class="bg-white p-2 rounded text-xs overflow-x-auto">{{ JSON.stringify(node.output, null, 2) }}</pre>
                            </div>
                            <div v-if="node.error" class="mt-2">
                                <div class="text-xs text-red-600 mb-1">Error:</div>
                                <pre class="bg-red-50 text-red-700 p-2 rounded text-xs">{{ node.error }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Errors Tab -->
                <div v-if="activeTab === 'errors'">
                    <div v-if="executionErrors.length === 0" class="text-gray-500 text-center py-8">
                        No errors
                    </div>
                    <div v-else class="space-y-4">
                        <div 
                            v-for="(error, index) in executionErrors" 
                            :key="index"
                            class="bg-red-50 border border-red-200 rounded-lg p-4"
                        >
                            <div class="flex items-start space-x-2">
                                <AlertCircle :size="20" class="text-red-500 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-medium text-red-900 mb-1">{{ error.message }}</h4>
                                    <div class="text-sm text-red-700">
                                        Node: {{ error.node_name }} ({{ error.node_id }})
                                    </div>
                                    <div v-if="error.stack" class="mt-2">
                                        <pre class="text-xs text-red-600 overflow-x-auto">{{ error.stack }}</pre>
                                    </div>
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
import { ref, computed, watch } from 'vue';
import { useWorkflowStore } from '../../stores/workflow';
import { 
    X, PlayCircle, StopCircle, RefreshCw, FileText, 
    Terminal, Activity, AlertCircle, CheckCircle, 
    XCircle, Clock, Loader 
} from 'lucide-vue-next';

const props = defineProps({
    execution: Object
});

const emit = defineEmits(['close']);

const workflowStore = useWorkflowStore();

const panelHeight = ref(300);
const isResizing = ref(false);
const activeTab = ref('output');

const tabs = [
    { id: 'output', label: 'Output', icon: FileText },
    { id: 'logs', label: 'Logs', icon: Terminal },
    { id: 'nodes', label: 'Nodes', icon: Activity },
    { id: 'errors', label: 'Errors', icon: AlertCircle }
];

// Mock data - in production these would come from the execution
const executionLogs = ref([]);
const nodeExecutions = ref([]);
const executionErrors = ref([]);

// Watch for execution changes
watch(() => props.execution, (newExecution) => {
    if (newExecution) {
        // Fetch execution details
        fetchExecutionDetails(newExecution.id);
    }
});

const fetchExecutionDetails = async (executionId) => {
    // This would fetch real data from the API
    // For now, using mock data
    executionLogs.value = [
        { timestamp: new Date(), level: 'info', message: 'Workflow execution started' },
        { timestamp: new Date(), level: 'debug', message: 'Processing trigger node' }
    ];
    
    nodeExecutions.value = [
        { 
            id: 'node1', 
            name: 'Webhook Trigger', 
            status: 'success',
            input: { method: 'POST', path: '/webhook' },
            output: { data: { message: 'Hello' } }
        }
    ];
};

const startResize = (e) => {
    isResizing.value = true;
    const startY = e.clientY;
    const startHeight = panelHeight.value;
    
    const handleMouseMove = (e) => {
        const diff = startY - e.clientY;
        panelHeight.value = Math.min(Math.max(startHeight + diff, 200), 600);
    };
    
    const handleMouseUp = () => {
        isResizing.value = false;
        document.removeEventListener('mousemove', handleMouseMove);
        document.removeEventListener('mouseup', handleMouseUp);
    };
    
    document.addEventListener('mousemove', handleMouseMove);
    document.addEventListener('mouseup', handleMouseUp);
};

const stopExecution = async () => {
    if (props.execution) {
        await workflowStore.stopExecution(props.execution.id);
    }
};

const retryExecution = async () => {
    // Implement retry logic
    console.log('Retrying execution');
};

const formatTime = (timestamp) => {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleTimeString();
};

const calculateDuration = (start, end) => {
    if (!start || !end) return '';
    const duration = new Date(end) - new Date(start);
    const seconds = Math.floor(duration / 1000);
    const minutes = Math.floor(seconds / 60);
    if (minutes > 0) {
        return `${minutes}m ${seconds % 60}s`;
    }
    return `${seconds}s`;
};

const getStatusClass = (status) => {
    const classes = {
        'success': 'bg-green-100 text-green-700',
        'error': 'bg-red-100 text-red-700',
        'running': 'bg-blue-100 text-blue-700',
        'waiting': 'bg-yellow-100 text-yellow-700',
        'pending': 'bg-gray-100 text-gray-700'
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};

const getStatusIcon = (status) => {
    const icons = {
        'success': CheckCircle,
        'error': XCircle,
        'running': Loader,
        'waiting': Clock,
        'pending': Clock
    };
    return icons[status] || Clock;
};

const getLogLevelClass = (level) => {
    const classes = {
        'debug': 'bg-gray-100 text-gray-700',
        'info': 'bg-blue-100 text-blue-700',
        'warn': 'bg-yellow-100 text-yellow-700',
        'error': 'bg-red-100 text-red-700'
    };
    return classes[level] || 'bg-gray-100 text-gray-700';
};
</script>

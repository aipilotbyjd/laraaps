<template>
    <div class="h-screen flex flex-col bg-gray-100">
        <!-- Editor Header -->
        <div class="bg-white border-b px-4 py-2 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <button @click="goBack" class="p-2 hover:bg-gray-100 rounded">
                    <ArrowLeft :size="20" />
                </button>
                <input
                    v-model="workflowName"
                    @blur="updateWorkflowName"
                    class="text-lg font-medium bg-transparent border-b border-transparent hover:border-gray-300 focus:border-orange-500 focus:outline-none px-1"
                    placeholder="Workflow name"
                />
                <span
                    :class="[
                        'px-2 py-1 rounded text-xs font-medium',
                        isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'
                    ]"
                >
                    {{ isActive ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="flex items-center space-x-2">
                <button
                    @click="saveWorkflow"
                    :disabled="!isDirty"
                    :class="[
                        'px-4 py-2 rounded font-medium',
                        isDirty
                            ? 'bg-orange-500 hover:bg-orange-600 text-white'
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    ]"
                >
                    <Save :size="16" class="inline mr-1" />
                    Save
                </button>
                <button
                    @click="executeWorkflow"
                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded font-medium"
                >
                    <PlayCircle :size="16" class="inline mr-1" />
                    Execute
                </button>
                <button @click="showSettings" class="p-2 hover:bg-gray-100 rounded">
                    <Settings :size="20" />
                </button>
            </div>
        </div>

        <!-- Main Editor Area -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Left Sidebar - Node Palette -->
            <NodePalette 
                :categories="nodeCategories"
                :nodes="availableNodes"
                @add-node="handleAddNode"
            />

            <!-- Canvas Area -->
            <div class="flex-1 relative">
                <VueFlow
                    v-model:nodes="nodes"
                    v-model:edges="edges"
                    @nodes-change="onNodesChange"
                    @edges-change="onEdgesChange"
                    @connect="onConnect"
                    @node-drag-stop="onNodeDragStop"
                    @node-click="onNodeClick"
                    @pane-click="onPaneClick"
                    :fit-view-on-init="true"
                    :default-edge-options="defaultEdgeOptions"
                    :connection-line-style="connectionLineStyle"
                    class="bg-gray-50"
                >
                    <Background pattern-color="#e5e7eb" />
                    <Controls />
                    <MiniMap pannable zoomable />

                    <template #node-trigger="{ data }">
                        <TriggerNode :data="data" />
                    </template>
                    <template #node-action="{ data }">
                        <ActionNode :data="data" />
                    </template>
                    <template #node-condition="{ data }">
                        <ConditionNode :data="data" />
                    </template>

                    <template #connection-line="{ sourceX, sourceY, targetX, targetY }">
                        <ConnectionLine
                            :source-x="sourceX"
                            :source-y="sourceY"
                            :target-x="targetX"
                            :target-y="targetY"
                        />
                    </template>
                </VueFlow>
            </div>

            <!-- Right Sidebar - Node Settings -->
            <NodeSettings
                v-if="selectedNode"
                :node="selectedNode"
                @update="updateNodeData"
                @close="closeNodeSettings"
            />
        </div>

        <!-- Execution Panel -->
        <ExecutionPanel
            v-if="showExecutionPanel"
            :execution="currentExecution"
            @close="showExecutionPanel = false"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { VueFlow, useVueFlow } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';
import { MiniMap } from '@vue-flow/minimap';
import { useWorkflowStore } from '../../stores/workflow';
import NodePalette from '../../components/Workflow/NodePalette.vue';
import NodeSettings from '../../components/Workflow/NodeSettings.vue';
import ExecutionPanel from '../../components/Workflow/ExecutionPanel.vue';
import TriggerNode from '../../components/Nodes/TriggerNode.vue';
import ActionNode from '../../components/Nodes/ActionNode.vue';
import ConditionNode from '../../components/Nodes/ConditionNode.vue';
import ConnectionLine from '../../components/Workflow/ConnectionLine.vue';
import { 
    ArrowLeft, Save, PlayCircle, Settings 
} from 'lucide-vue-next';

const workflowStore = useWorkflowStore();
const { project, addNodes, addEdges } = useVueFlow();

// Props
const props = defineProps({
    workflowId: String
});

// State
const workflowName = ref('Untitled Workflow');
const isActive = ref(false);
const isDirty = computed(() => workflowStore.isDirty);
const selectedNode = ref(null);
const showExecutionPanel = ref(false);

// Computed
const nodes = computed({
    get: () => workflowStore.nodes,
    set: (value) => workflowStore.nodes = value
});

const edges = computed({
    get: () => workflowStore.edges,
    set: (value) => workflowStore.edges = value
});

const availableNodes = computed(() => workflowStore.availableNodes);
const nodeCategories = computed(() => workflowStore.nodeCategories);
const currentExecution = computed(() => workflowStore.currentExecution);

// Edge configuration
const defaultEdgeOptions = {
    animated: true,
    style: { strokeWidth: 2, stroke: '#94a3b8' },
};

const connectionLineStyle = {
    strokeWidth: 2,
    stroke: '#94a3b8',
};

// Lifecycle
onMounted(async () => {
    // Load workflow if ID is provided
    if (props.workflowId) {
        try {
            const workflow = await workflowStore.fetchWorkflow(props.workflowId);
            workflowName.value = workflow.name;
            isActive.value = workflow.is_active;
        } catch (error) {
            console.error('Failed to load workflow:', error);
        }
    }

    // Load available nodes and categories
    await workflowStore.fetchAvailableNodes();
    await workflowStore.fetchNodeCategories();
});

// Methods
const goBack = () => {
    if (isDirty.value && !confirm('You have unsaved changes. Are you sure you want to leave?')) {
        return;
    }
    router.visit('/workflows');
};

const updateWorkflowName = () => {
    if (workflowStore.currentWorkflow) {
        workflowStore.currentWorkflow.name = workflowName.value;
        workflowStore.isDirty = true;
    }
};

const saveWorkflow = async () => {
    try {
        if (!workflowStore.currentWorkflow) {
            // Create new workflow
            await workflowStore.createWorkflow({
                name: workflowName.value,
                description: '',
                definition: {
                    nodes: nodes.value,
                    edges: edges.value
                }
            });
        } else {
            // Update existing workflow
            workflowStore.currentWorkflow.name = workflowName.value;
            await workflowStore.saveWorkflow();
        }
        
        // Show success message
        console.log('Workflow saved successfully');
    } catch (error) {
        console.error('Failed to save workflow:', error);
    }
};

const executeWorkflow = async () => {
    if (!workflowStore.currentWorkflow) {
        alert('Please save the workflow first');
        return;
    }

    try {
        showExecutionPanel.value = true;
        await workflowStore.testExecuteWorkflow(workflowStore.currentWorkflow.id);
    } catch (error) {
        console.error('Failed to execute workflow:', error);
    }
};

const showSettings = () => {
    // Show workflow settings modal
    console.log('Show settings');
};

const handleAddNode = (nodeType) => {
    const id = `node_${Date.now()}`;
    const newNode = {
        id,
        type: nodeType.type,
        position: project({
            x: window.innerWidth / 2 - 100,
            y: window.innerHeight / 2 - 50
        }),
        data: {
            label: nodeType.name,
            type: nodeType.type,
            icon: nodeType.icon,
            config: {}
        }
    };
    
    addNodes([newNode]);
    workflowStore.addNode(newNode);
};

const onNodesChange = (changes) => {
    // Handle node changes
    changes.forEach(change => {
        if (change.type === 'remove') {
            workflowStore.deleteNode(change.id);
        } else if (change.type === 'position') {
            workflowStore.updateNode(change.id, { position: change.position });
        }
    });
};

const onEdgesChange = (changes) => {
    // Handle edge changes
    changes.forEach(change => {
        if (change.type === 'remove') {
            workflowStore.deleteEdge(change.id);
        }
    });
};

const onConnect = (params) => {
    const id = `edge_${Date.now()}`;
    const edge = {
        id,
        ...params,
        animated: true,
    };
    
    addEdges([edge]);
    workflowStore.addEdge(edge);
};

const onNodeDragStop = (event) => {
    workflowStore.isDirty = true;
};

const onNodeClick = (event) => {
    selectedNode.value = event.node;
    workflowStore.setSelectedNode(event.node);
};

const onPaneClick = () => {
    selectedNode.value = null;
    workflowStore.setSelectedNode(null);
};

const updateNodeData = (nodeId, data) => {
    workflowStore.updateNode(nodeId, { data });
};

const closeNodeSettings = () => {
    selectedNode.value = null;
    workflowStore.setSelectedNode(null);
};
</script>

<style>
@import '@vue-flow/core/dist/style.css';
@import '@vue-flow/core/dist/theme-default.css';
@import '@vue-flow/controls/dist/style.css';
@import '@vue-flow/minimap/dist/style.css';
</style>

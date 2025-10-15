import { defineStore } from 'pinia';
import { workflowAPI, nodeAPI, executionAPI } from '../services/api';

export const useWorkflowStore = defineStore('workflow', {
    state: () => ({
        workflows: [],
        currentWorkflow: null,
        nodes: [],
        edges: [],
        availableNodes: [],
        nodeCategories: [],
        executions: [],
        currentExecution: null,
        isExecuting: false,
        selectedNode: null,
        viewportTransform: { x: 0, y: 0, zoom: 1 },
        isDirty: false,
    }),

    getters: {
        getNodeById: (state) => (id) => {
            return state.nodes.find(node => node.id === id);
        },
        
        getExecutionStatus: (state) => {
            if (!state.currentExecution) return null;
            return state.currentExecution.status;
        },
        
        getNodesByCategory: (state) => (category) => {
            return state.availableNodes.filter(node => node.category === category);
        },
    },

    actions: {
        async fetchWorkflows() {
            try {
                const response = await workflowAPI.list();
                this.workflows = response.data.data || [];
                return this.workflows;
            } catch (error) {
                console.error('Error fetching workflows:', error);
                throw error;
            }
        },

        async fetchWorkflow(id) {
            try {
                const response = await workflowAPI.get(id);
                this.currentWorkflow = response.data;
                
                // Parse workflow data if it exists
                if (this.currentWorkflow.definition) {
                    const definition = typeof this.currentWorkflow.definition === 'string' 
                        ? JSON.parse(this.currentWorkflow.definition) 
                        : this.currentWorkflow.definition;
                    
                    this.nodes = definition.nodes || [];
                    this.edges = definition.edges || [];
                }
                
                return this.currentWorkflow;
            } catch (error) {
                console.error('Error fetching workflow:', error);
                throw error;
            }
        },

        async createWorkflow(data) {
            try {
                const response = await workflowAPI.create(data);
                this.currentWorkflow = response.data;
                return response.data;
            } catch (error) {
                console.error('Error creating workflow:', error);
                throw error;
            }
        },

        async saveWorkflow() {
            if (!this.currentWorkflow) return;
            
            try {
                const workflowData = {
                    ...this.currentWorkflow,
                    definition: {
                        nodes: this.nodes,
                        edges: this.edges,
                    }
                };
                
                const response = await workflowAPI.update(
                    this.currentWorkflow.id, 
                    workflowData
                );
                
                this.currentWorkflow = response.data;
                this.isDirty = false;
                return response.data;
            } catch (error) {
                console.error('Error saving workflow:', error);
                throw error;
            }
        },

        async deleteWorkflow(id) {
            try {
                await workflowAPI.delete(id);
                this.workflows = this.workflows.filter(w => w.id !== id);
            } catch (error) {
                console.error('Error deleting workflow:', error);
                throw error;
            }
        },

        async duplicateWorkflow(id) {
            try {
                const response = await workflowAPI.duplicate(id);
                return response.data;
            } catch (error) {
                console.error('Error duplicating workflow:', error);
                throw error;
            }
        },

        async activateWorkflow(id) {
            try {
                const response = await workflowAPI.activate(id);
                const workflow = this.workflows.find(w => w.id === id);
                if (workflow) {
                    workflow.is_active = true;
                }
                return response.data;
            } catch (error) {
                console.error('Error activating workflow:', error);
                throw error;
            }
        },

        async deactivateWorkflow(id) {
            try {
                const response = await workflowAPI.deactivate(id);
                const workflow = this.workflows.find(w => w.id === id);
                if (workflow) {
                    workflow.is_active = false;
                }
                return response.data;
            } catch (error) {
                console.error('Error deactivating workflow:', error);
                throw error;
            }
        },

        async fetchAvailableNodes() {
            try {
                const response = await nodeAPI.list();
                this.availableNodes = response.data.data || [];
                return this.availableNodes;
            } catch (error) {
                console.error('Error fetching nodes:', error);
                throw error;
            }
        },

        async fetchNodeCategories() {
            try {
                const response = await nodeAPI.getCategories();
                this.nodeCategories = response.data || [];
                return this.nodeCategories;
            } catch (error) {
                console.error('Error fetching node categories:', error);
                throw error;
            }
        },

        async executeWorkflow(workflowId, inputData = {}) {
            try {
                this.isExecuting = true;
                const response = await workflowAPI.execute(workflowId, inputData);
                this.currentExecution = response.data;
                return response.data;
            } catch (error) {
                console.error('Error executing workflow:', error);
                throw error;
            } finally {
                this.isExecuting = false;
            }
        },

        async testExecuteWorkflow(workflowId, inputData = {}) {
            try {
                this.isExecuting = true;
                const response = await workflowAPI.testExecute(workflowId, inputData);
                this.currentExecution = response.data;
                return response.data;
            } catch (error) {
                console.error('Error test executing workflow:', error);
                throw error;
            } finally {
                this.isExecuting = false;
            }
        },

        async fetchExecutions(workflowId = null) {
            try {
                const params = workflowId ? { workflow_id: workflowId } : {};
                const response = await executionAPI.list(params);
                this.executions = response.data.data || [];
                return this.executions;
            } catch (error) {
                console.error('Error fetching executions:', error);
                throw error;
            }
        },

        async fetchExecution(id) {
            try {
                const response = await executionAPI.get(id);
                this.currentExecution = response.data;
                return response.data;
            } catch (error) {
                console.error('Error fetching execution:', error);
                throw error;
            }
        },

        async stopExecution(id) {
            try {
                const response = await executionAPI.stop(id);
                return response.data;
            } catch (error) {
                console.error('Error stopping execution:', error);
                throw error;
            }
        },

        // Vue Flow specific actions
        addNode(node) {
            this.nodes.push(node);
            this.isDirty = true;
        },

        updateNode(id, updates) {
            const nodeIndex = this.nodes.findIndex(n => n.id === id);
            if (nodeIndex !== -1) {
                this.nodes[nodeIndex] = { ...this.nodes[nodeIndex], ...updates };
                this.isDirty = true;
            }
        },

        deleteNode(id) {
            this.nodes = this.nodes.filter(n => n.id !== id);
            // Also remove connected edges
            this.edges = this.edges.filter(e => e.source !== id && e.target !== id);
            this.isDirty = true;
        },

        addEdge(edge) {
            this.edges.push(edge);
            this.isDirty = true;
        },

        updateEdge(id, updates) {
            const edgeIndex = this.edges.findIndex(e => e.id === id);
            if (edgeIndex !== -1) {
                this.edges[edgeIndex] = { ...this.edges[edgeIndex], ...updates };
                this.isDirty = true;
            }
        },

        deleteEdge(id) {
            this.edges = this.edges.filter(e => e.id !== id);
            this.isDirty = true;
        },

        setSelectedNode(node) {
            this.selectedNode = node;
        },

        updateViewport(transform) {
            this.viewportTransform = transform;
        },

        clearWorkflow() {
            this.nodes = [];
            this.edges = [];
            this.currentWorkflow = null;
            this.selectedNode = null;
            this.isDirty = false;
        },
    },
});

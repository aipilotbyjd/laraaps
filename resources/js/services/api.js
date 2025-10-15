import axios from 'axios';

const API_BASE_URL = '/api/v1';

const apiClient = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
});

// Add CSRF token to requests
apiClient.interceptors.request.use((config) => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token.content;
    }
    
    // Add bearer token if exists
    const authToken = localStorage.getItem('auth_token');
    if (authToken) {
        config.headers.Authorization = `Bearer ${authToken}`;
    }
    
    // Add organization context if exists
    const organization = JSON.parse(localStorage.getItem('current_organization') || 'null');
    if (organization?.id) {
        config.headers['X-Organization-ID'] = organization.id;
    }
    
    return config;
});

// Handle responses
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Handle unauthorized
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// Workflow API
export const workflowAPI = {
    list: (params = {}) => apiClient.get('/workflows', { params }),
    get: (id) => apiClient.get(`/workflows/${id}`),
    create: (data) => apiClient.post('/workflows', data),
    update: (id, data) => apiClient.put(`/workflows/${id}`, data),
    delete: (id) => apiClient.delete(`/workflows/${id}`),
    duplicate: (id) => apiClient.post(`/workflows/${id}/duplicate`),
    activate: (id) => apiClient.patch(`/workflows/${id}/activate`),
    deactivate: (id) => apiClient.patch(`/workflows/${id}/deactivate`),
    execute: (id, data = {}) => apiClient.post(`/workflows/${id}/execute`, data),
    testExecute: (id, data = {}) => apiClient.post(`/workflows/${id}/test-execute`, data),
    validateWorkflow: (id) => apiClient.post(`/workflows/${id}/validate`),
    testRun: (id, data = {}) => apiClient.post(`/workflows/${id}/test-run`, data),
};

// Node API
export const nodeAPI = {
    list: () => apiClient.get('/nodes'),
    getCategories: () => apiClient.get('/nodes/categories'),
    getTags: () => apiClient.get('/nodes/tags'),
    getNodeDetails: (type) => apiClient.get(`/nodes/${type}`),
    getNodeSchema: (type) => apiClient.get(`/nodes/${type}/schema`),
    testNode: (type, data) => apiClient.post(`/nodes/${type}/test`, data),
    validateConfig: (type, data) => apiClient.post(`/nodes/${type}/validate-config`, data),
    getDynamicParameters: (type) => apiClient.get(`/nodes/${type}/parameters/dynamic`),
    resolveParameters: (type, data) => apiClient.post(`/nodes/${type}/parameters/resolve`, data),
};

// Execution API
export const executionAPI = {
    list: (params = {}) => apiClient.get('/executions', { params }),
    get: (id) => apiClient.get(`/executions/${id}`),
    delete: (id) => apiClient.delete(`/executions/${id}`),
    stop: (id) => apiClient.post(`/executions/${id}/stop`),
    retry: (id) => apiClient.post(`/executions/${id}/retry`),
    resume: (id) => apiClient.post(`/executions/${id}/resume`),
    getNodes: (id) => apiClient.get(`/executions/${id}/nodes`),
    getLogs: (id) => apiClient.get(`/executions/${id}/logs`),
    getData: (id) => apiClient.get(`/executions/${id}/data`),
    getErrors: (id) => apiClient.get(`/executions/${id}/errors`),
    getStats: () => apiClient.get('/executions/stats'),
};

// Credentials API
export const credentialAPI = {
    list: () => apiClient.get('/credentials'),
    get: (id) => apiClient.get(`/credentials/${id}`),
    create: (data) => apiClient.post('/credentials', data),
    update: (id, data) => apiClient.put(`/credentials/${id}`, data),
    delete: (id) => apiClient.delete(`/credentials/${id}`),
    getTypes: () => apiClient.get('/credentials/types'),
    getTypeSchema: (type) => apiClient.get(`/credentials/types/${type}/schema`),
    test: (id) => apiClient.post(`/credentials/${id}/test`),
};

// Template API
export const templateAPI = {
    list: (params = {}) => apiClient.get('/templates', { params }),
    getFeatured: () => apiClient.get('/templates/featured'),
    getTrending: () => apiClient.get('/templates/trending'),
    getCategories: () => apiClient.get('/templates/categories'),
    search: (query) => apiClient.get('/templates/search', { params: { q: query } }),
    get: (id) => apiClient.get(`/templates/${id}`),
    useTemplate: (id) => apiClient.post(`/templates/${id}/use`),
    cloneTemplate: (id) => apiClient.post(`/templates/${id}/clone`),
};

// Auth API
export const authAPI = {
    login: (data) => apiClient.post('/auth/login', data),
    logout: () => apiClient.post('/auth/logout'),
    register: (data) => apiClient.post('/auth/register', data),
    me: () => apiClient.get('/auth/me'),
    updateProfile: (data) => apiClient.put('/auth/profile', data),
    changePassword: (data) => apiClient.post('/auth/change-password', data),
    forgotPassword: (data) => apiClient.post('/auth/forgot-password', data),
    resetPassword: (data) => apiClient.post('/auth/reset-password', data),
    enable2FA: () => apiClient.post('/auth/mfa/enable'),
    verify2FA: (data) => apiClient.post('/auth/mfa/verify', data),
    disable2FA: (data) => apiClient.post('/auth/mfa/disable', data),
    getApiKeys: () => apiClient.get('/auth/api-keys'),
    createApiKey: (data) => apiClient.post('/auth/api-keys', data),
    deleteApiKey: (id) => apiClient.delete(`/auth/api-keys/${id}`),
};

// Organization API
export const organizationAPI = {
    list: () => apiClient.get('/organizations'),
    get: (id) => apiClient.get(`/organizations/${id}`),
    create: (data) => apiClient.post('/organizations', data),
    update: (id, data) => apiClient.put(`/organizations/${id}`, data),
    delete: (id) => apiClient.delete(`/organizations/${id}`),
    getMembers: (id) => apiClient.get(`/organizations/${id}/members`),
    addMember: (id, data) => apiClient.post(`/organizations/${id}/members`, data),
};

export default apiClient;

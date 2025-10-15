import { defineStore } from 'pinia';
import { authAPI, organizationAPI } from '../services/api';
import { router } from '@inertiajs/vue3';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('auth_token'),
        isAuthenticated: false,
        loading: false,
        organizations: [],
        currentOrganization: JSON.parse(localStorage.getItem('current_organization') || 'null'),
        twoFactorEnabled: false,
        apiKeys: [],
    }),

    getters: {
        isLoggedIn: (state) => !!state.token && !!state.user,
        userName: (state) => state.user?.name || 'Guest',
        userEmail: (state) => state.user?.email || '',
        hasOrganization: (state) => !!state.currentOrganization,
        organizationId: (state) => state.currentOrganization?.id || null,
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            try {
                const response = await authAPI.login(credentials);
                const { user, token, organizations } = response.data;
                
                this.user = user;
                this.token = token;
                this.isAuthenticated = true;
                this.organizations = organizations || [];
                
                // Store token
                localStorage.setItem('auth_token', token);
                
                // Set first organization as current if available
                if (this.organizations.length > 0 && !this.currentOrganization) {
                    this.setCurrentOrganization(this.organizations[0]);
                }
                
                // Set auth header for future requests
                this.setAuthHeader(token);
                
                return { success: true };
            } catch (error) {
                console.error('Login error:', error);
                const message = error.response?.data?.message || 'Login failed';
                return { success: false, error: message };
            } finally {
                this.loading = false;
            }
        },

        async register(userData) {
            this.loading = true;
            try {
                const response = await authAPI.register(userData);
                const { user, token } = response.data;
                
                // Auto login after registration
                this.user = user;
                this.token = token;
                this.isAuthenticated = true;
                
                localStorage.setItem('auth_token', token);
                this.setAuthHeader(token);
                
                // Create default organization
                if (userData.organization_name) {
                    await this.createOrganization({
                        name: userData.organization_name,
                        description: `${userData.name}'s Organization`
                    });
                }
                
                return { success: true };
            } catch (error) {
                console.error('Registration error:', error);
                const message = error.response?.data?.message || 'Registration failed';
                const errors = error.response?.data?.errors || {};
                return { success: false, error: message, errors };
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await authAPI.logout();
            } catch (error) {
                console.error('Logout error:', error);
            } finally {
                // Clear local data regardless of API response
                this.user = null;
                this.token = null;
                this.isAuthenticated = false;
                this.currentOrganization = null;
                this.organizations = [];
                
                localStorage.removeItem('auth_token');
                localStorage.removeItem('current_organization');
                
                // Redirect to login
                router.visit('/login');
            }
        },

        async fetchUser() {
            if (!this.token) return null;
            
            try {
                const response = await authAPI.me();
                this.user = response.data;
                this.isAuthenticated = true;
                
                // Fetch organizations
                await this.fetchOrganizations();
                
                return this.user;
            } catch (error) {
                console.error('Fetch user error:', error);
                if (error.response?.status === 401) {
                    await this.logout();
                }
                return null;
            }
        },

        async updateProfile(profileData) {
            try {
                const response = await authAPI.updateProfile(profileData);
                this.user = response.data;
                return { success: true };
            } catch (error) {
                console.error('Update profile error:', error);
                const message = error.response?.data?.message || 'Update failed';
                return { success: false, error: message };
            }
        },

        async changePassword(passwordData) {
            try {
                await authAPI.changePassword(passwordData);
                return { success: true, message: 'Password changed successfully' };
            } catch (error) {
                console.error('Change password error:', error);
                const message = error.response?.data?.message || 'Password change failed';
                return { success: false, error: message };
            }
        },

        async forgotPassword(email) {
            try {
                const response = await authAPI.forgotPassword({ email });
                return { 
                    success: true, 
                    message: response.data.message || 'Password reset email sent' 
                };
            } catch (error) {
                console.error('Forgot password error:', error);
                const message = error.response?.data?.message || 'Failed to send reset email';
                return { success: false, error: message };
            }
        },

        async resetPassword(data) {
            try {
                const response = await authAPI.resetPassword(data);
                return { 
                    success: true, 
                    message: 'Password reset successfully' 
                };
            } catch (error) {
                console.error('Reset password error:', error);
                const message = error.response?.data?.message || 'Password reset failed';
                return { success: false, error: message };
            }
        },

        async enable2FA() {
            try {
                const response = await authAPI.enable2FA();
                this.twoFactorEnabled = true;
                return { 
                    success: true, 
                    qrCode: response.data.qr_code,
                    secret: response.data.secret 
                };
            } catch (error) {
                console.error('Enable 2FA error:', error);
                return { success: false, error: 'Failed to enable 2FA' };
            }
        },

        async verify2FA(code) {
            try {
                const response = await authAPI.verify2FA({ code });
                return { success: true };
            } catch (error) {
                console.error('Verify 2FA error:', error);
                return { success: false, error: 'Invalid verification code' };
            }
        },

        async disable2FA(code) {
            try {
                await authAPI.disable2FA({ code });
                this.twoFactorEnabled = false;
                return { success: true };
            } catch (error) {
                console.error('Disable 2FA error:', error);
                return { success: false, error: 'Failed to disable 2FA' };
            }
        },

        async fetchOrganizations() {
            try {
                const response = await organizationAPI.list();
                this.organizations = response.data.data || [];
                
                // Set current organization if not set
                if (this.organizations.length > 0 && !this.currentOrganization) {
                    this.setCurrentOrganization(this.organizations[0]);
                }
                
                return this.organizations;
            } catch (error) {
                console.error('Fetch organizations error:', error);
                return [];
            }
        },

        async createOrganization(data) {
            try {
                const response = await organizationAPI.create(data);
                const newOrg = response.data;
                
                this.organizations.push(newOrg);
                this.setCurrentOrganization(newOrg);
                
                return { success: true, organization: newOrg };
            } catch (error) {
                console.error('Create organization error:', error);
                const message = error.response?.data?.message || 'Failed to create organization';
                return { success: false, error: message };
            }
        },

        setCurrentOrganization(organization) {
            this.currentOrganization = organization;
            localStorage.setItem('current_organization', JSON.stringify(organization));
            
            // Add organization context to API headers
            if (organization?.id) {
                this.setOrganizationHeader(organization.id);
            }
        },

        switchOrganization(organizationId) {
            const org = this.organizations.find(o => o.id === organizationId);
            if (org) {
                this.setCurrentOrganization(org);
                // Reload current page to refresh data
                router.reload();
            }
        },

        async fetchApiKeys() {
            try {
                const response = await authAPI.getApiKeys();
                this.apiKeys = response.data || [];
                return this.apiKeys;
            } catch (error) {
                console.error('Fetch API keys error:', error);
                return [];
            }
        },

        async createApiKey(name) {
            try {
                const response = await authAPI.createApiKey({ name });
                const newKey = response.data;
                this.apiKeys.push(newKey);
                return { success: true, apiKey: newKey };
            } catch (error) {
                console.error('Create API key error:', error);
                return { success: false, error: 'Failed to create API key' };
            }
        },

        async deleteApiKey(id) {
            try {
                await authAPI.deleteApiKey(id);
                this.apiKeys = this.apiKeys.filter(key => key.id !== id);
                return { success: true };
            } catch (error) {
                console.error('Delete API key error:', error);
                return { success: false, error: 'Failed to delete API key' };
            }
        },

        setAuthHeader(token) {
            // This would be imported from the API service
            // to set default auth header for all requests
            if (window.axios) {
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            }
        },

        setOrganizationHeader(organizationId) {
            // Set organization context header
            if (window.axios) {
                window.axios.defaults.headers.common['X-Organization-ID'] = organizationId;
            }
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            this.isAuthenticated = false;
            this.currentOrganization = null;
            this.organizations = [];
            
            localStorage.removeItem('auth_token');
            localStorage.removeItem('current_organization');
        }
    },
});

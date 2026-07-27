import { defineStore } from 'pinia';
import apiClient from '../axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        loading: false,
    }),
    actions: {
        async fetchUser() {
            this.loading = true;
            try {
                const response = await apiClient.get('/user');
                this.user = response.data;
                this.isAuthenticated = true;
            } catch (error) {
                this.user = null;
                this.isAuthenticated = false;
            } finally {
                this.loading = false;
            }
        },
        async requestLoginLink(email) {
            await apiClient.post('/auth/request-link', { email });
            // The link will be sent via email (or logged)
        },
        async verifyLogin(token, email) {
            // This is typically handled by a browser redirect,
            // but we can also call it directly if needed.
            const response = await apiClient.get('/auth/verify', {
                params: { token, email },
            });
            this.user = response.data.user;
            this.isAuthenticated = true;
            return response.data;
        },
        async logout() {
            await apiClient.post('/auth/logout');
            this.user = null;
            this.isAuthenticated = false;
        },
    },
    getters: {
        isPremium: (state) => state.user?.is_premium ?? false,
        gemBalance: (state) => state.user?.gem_balance ?? 0,
        userId: (state) => state.user?.id ?? null,
    },
});
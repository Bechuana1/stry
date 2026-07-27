import { defineStore } from 'pinia';
import apiClient from '../axios';

export const useReaderStore = defineStore('reader', {
    state: () => ({
        currentChapter: null,
        loading: false,
        error: null,
        // For infinite scroll: we'll store chapters loaded in the current session
        // Could use a map of chapterId -> data
    }),
    actions: {
        async fetchChapter(slug) {
            this.loading = true;
            this.error = null;
            try {
                const response = await apiClient.get(`/chapters/${slug}`);
                this.currentChapter = response.data;
                return response.data;
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to load chapter';
                throw error;
            } finally {
                this.loading = false;
            }
        },
        async unlockChapter(chapterId) {
            const response = await apiClient.post(`/chapters/${chapterId}/unlock`);
            // After unlock, refresh chapter data
            if (this.currentChapter && this.currentChapter.id === chapterId) {
                // Fetch updated chapter to get content
                await this.fetchChapter(this.currentChapter.slug);
            }
            return response.data;
        },
        clearChapter() {
            this.currentChapter = null;
            this.error = null;
        },
    },
});
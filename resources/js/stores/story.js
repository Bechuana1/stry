import { defineStore } from 'pinia';
import apiClient from '../axios';

export const useStoryStore = defineStore('story', {
    state: () => ({
        stories: [],
        currentStory: null,
        genres: [],
        loading: false,
    }),
    actions: {
        async fetchStories(params = {}) {
            this.loading = true;
            const response = await apiClient.get('/stories', { params });
            this.stories = response.data.data; // assuming pagination
            this.loading = false;
            return response.data;
        },
        async fetchStory(slug) {
            this.loading = true;
            const response = await apiClient.get(`/stories/${slug}`);
            this.currentStory = response.data;
            this.loading = false;
            return response.data;
        },
        async fetchGenres() {
            const response = await apiClient.get('/stories/genres');
            this.genres = response.data;
            return response.data;
        },
    },
});
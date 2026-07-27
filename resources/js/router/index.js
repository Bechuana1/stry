import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    // We'll add routes later (Home, Reader, Dashboard)
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

export default router;
import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import StoryShow from '../views/StoryShow.vue';
import ChapterReader from '../views/ChapterReader.vue';

const routes = [
    { path: '/', name: 'home', component: Home },
    { path: '/login', name: 'login', component: Login },
    { path: '/stories/:slug', name: 'story', component: StoryShow },
    { path: '/read/:chapterSlug', name: 'reader', component: ChapterReader },
];

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes,
});

// Add debug logging
router.beforeEach((to, from, next) => {
    console.log('🔀 Navigating to:', to.path);
    console.log('   Component:', to.component);
    next();
});

export default router;
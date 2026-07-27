import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import '../css/app.css';

// Import the axios client to ensure it's initialized
import './axios';

const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount('#app');
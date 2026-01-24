import './bootstrap';

// ສ້າງ Vue App
import { createApp } from 'vue';
import { createPinia } from 'pinia'
import App from './App.vue';
import router from './router';

// ສ້າງ instance ແລະ mount
const pinia = createPinia()
const app = createApp(App);
app.use(router);
app.use(pinia);
app.mount('#app');
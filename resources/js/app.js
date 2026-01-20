import './bootstrap';

// ສ້າງ Vue App
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

// ສ້າງ instance ແລະ mount
const app = createApp(App);
app.use(router);
app.mount('#app');
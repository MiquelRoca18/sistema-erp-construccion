import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

const app = createApp(App);

app.use(router); // Usamos el router en la aplicación
app.mount('#app');

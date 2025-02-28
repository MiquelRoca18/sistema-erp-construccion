import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './style.css'

const app = createApp(App);

app.use(router); // Usamos el router en la aplicación
app.mount('#app');

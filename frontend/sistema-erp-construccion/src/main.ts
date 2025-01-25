import { createApp } from 'vue';
import App from './App.vue';
import router from './router'; // Asegúrate de importar el archivo de rutas

const app = createApp(App);

app.use(router); // Usamos el router en la aplicación
app.mount('#app');

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './style.css'

const app = createApp(App);

app.use(router); 
app.mount('#app');

// Registrar el service worker para cachear recursos estáticos
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/serviceWorker.js')
      .then(registration => {
      })
      .catch(error => {
      });
  });
}
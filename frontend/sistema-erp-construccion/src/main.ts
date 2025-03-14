import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './style.css'
import './assets/css/responsive.css' // Importamos los nuevos estilos responsive

const app = createApp(App);

app.use(router); 
app.mount('#app');

// Agregar meta viewport ajustado para mejor experiencia móvil
const updateViewport = () => {
  let viewportMeta = document.querySelector('meta[name="viewport"]') as HTMLMetaElement;
  if (!viewportMeta) {
    viewportMeta = document.createElement('meta') as HTMLMetaElement;
    viewportMeta.setAttribute('name', 'viewport');
    document.head.appendChild(viewportMeta);
  }
  
  // En dispositivos móviles, ajustar la escala inicial para mejorar la legibilidad
  if (window.innerWidth < 640) {
    viewportMeta.setAttribute('content', 'width=device-width, initial-scale=0.9, maximum-scale=1.0, user-scalable=no');
  } else {
    viewportMeta.setAttribute('content', 'width=device-width, initial-scale=1.0');
  }
};

// Ejecutar al cargar y al cambiar tamaño de ventana
window.addEventListener('DOMContentLoaded', updateViewport);
window.addEventListener('resize', updateViewport);

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
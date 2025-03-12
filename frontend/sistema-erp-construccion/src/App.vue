<template>
  <div :class="{ dark: isDark }" class="flex flex-col min-h-screen transition-colors duration-300">
    <div class="flex flex-1 w-full">
      
      <!-- Sidebar (Solo visible en pantallas grandes) -->
      <Sidebar v-if="isAuthenticated" :sidebarOpen="sidebarOpen" @toggleSidebar="toggleSidebar" :isDark="isDark" @toggleDarkMode="toggleDarkMode" />

      <!-- Contenedor de contenido -->
      <div class="relative flex flex-col flex-1">
        
        <!-- Botón de hamburguesa (visible en móviles) -->
        <button 
          v-if="isAuthenticated" 
          @click="toggleSidebar" 
          class="lg:hidden fixed top-4 right-4 z-50 flex items-center justify-center w-10 h-10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-full shadow-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300"
        >
          <svg 
            :class="{ 'rotate-90': sidebarOpen }"
            class="w-6 h-6 transition-transform duration-300 ease-in-out"
            fill="none" 
            stroke="currentColor" 
            stroke-width="2" 
            viewBox="0 0 24 24" 
            xmlns="http://www.w3.org/2000/svg"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>

        <!-- Contenido principal con ajustes de escala y viewportdisable -->
        <div class="flex-1 p-4 md:p-6 transition-colors duration-300 bg-gray-100 dark:bg-gray-900">
          <div class="responsive-container">
            <router-view />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, onUnmounted } from 'vue';
import Sidebar from './components/Sidebar.vue';
import { isAuthenticated } from '@/service/authStore';

const sidebarOpen = ref(false);
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

// Variable para controlar el dark mode
const isDark = ref(false);

const toggleDarkMode = () => {
  isDark.value = !isDark.value;
  // Guardar preferencia en localStorage
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

// Inicializar el modo según preferencia guardada o del sistema
onMounted(() => {
  // Revisar si hay una preferencia guardada
  const savedTheme = localStorage.getItem('theme');
  
  if (savedTheme === 'dark' || 
     (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true;
  } else {
    isDark.value = false;
  }

  // Ajustar viewport para dispositivos móviles
  updateViewportMeta();
  window.addEventListener('resize', updateViewportMeta);
});

// Función para ajustar el meta viewport en dispositivos móviles
const updateViewportMeta = () => {
  const viewportMeta = document.querySelector('meta[name="viewport"]');
  if (viewportMeta) {
    if (window.innerWidth < 640) {
      // Para dispositivos móviles, ajustar el viewport para mejor legibilidad
      viewportMeta.setAttribute('content', 'width=device-width, initial-scale=0.85, maximum-scale=1.0, user-scalable=no');
    } else {
      // Para dispositivos más grandes, usar configuración estándar
      viewportMeta.setAttribute('content', 'width=device-width, initial-scale=1.0');
    }
  }
};

// Observar cambios en el modo oscuro
watch(isDark, (newValue) => {
  if (newValue) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
});

// Limpiar event listener al desmontar
onUnmounted(() => {
  window.removeEventListener('resize', updateViewportMeta);
});
</script>

<style>
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  -webkit-text-size-adjust: 100%;
}

#app {
  height: 100%;
}

/* Contenedor responsive que se adapta a diferentes pantallas */
.responsive-container {
  width: 100%;
  margin: 0 auto;
  height: 100%;
}

/* Ajustes específicos para dispositivos móviles */
@media screen and (max-width: 640px) {
  .responsive-container {
    overflow-x: hidden;
  }
  
  /* Reducción de tamaño de fuentes para móviles */
  h1 {
    font-size: 95%;
  }
  h2 {
    font-size: 95%;
  }
  p, div, span, button, a, input, select, textarea {
    font-size: 92%;
  }
}

/* Ajustes para iOS Safari */
@supports (-webkit-touch-callout: none) {
  .min-h-screen {
    /* Usar altura de la ventana gráfica móvil */
    min-height: -webkit-fill-available;
  }
}
</style>
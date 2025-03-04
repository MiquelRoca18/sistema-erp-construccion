<template>
  <div :class="{ dark: isDark }" class="flex flex-col h-screen overflow-hidden transition-colors duration-300">
    <div class="flex flex-1 w-full overflow-hidden">
      
      <!-- Sidebar (Solo visible en pantallas grandes) -->
      <Sidebar v-if="isAuthenticated" :sidebarOpen="sidebarOpen" @toggleSidebar="toggleSidebar" :isDark="isDark" @toggleDarkMode="toggleDarkMode" />

      <!-- Contenedor de contenido -->
      <div class="relative flex flex-col flex-1 overflow-auto">
        
        <!-- Botón de hamburguesa (visible en móviles) -->
        <button 
          v-if="isAuthenticated" 
          @click="toggleSidebar" 
          class="lg:hidden fixed top-4 right-4 z-50 flex items-center justify-center w-12 h-12 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-full shadow-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300"
        >
          <svg 
            :class="{ 'rotate-90': sidebarOpen }"
            class="w-7 h-7 transition-transform duration-300 ease-in-out"
            fill="none" 
            stroke="currentColor" 
            stroke-width="2" 
            viewBox="0 0 24 24" 
            xmlns="http://www.w3.org/2000/svg"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>

        <!-- Contenido principal -->
        <div class="flex-1 p-6 flex items-center justify-center transition-colors duration-300 bg-gray-100 dark:bg-gray-900">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
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
});

// Observar cambios en el modo oscuro
watch(isDark, (newValue) => {
  if (newValue) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
});
</script>

<style>
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

#app {
  height: 100%;
  overflow: hidden;
}
</style>
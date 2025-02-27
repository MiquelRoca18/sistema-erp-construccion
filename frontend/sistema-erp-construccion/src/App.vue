<template>
  <!-- Se aplica la clase 'dark' al contenedor raíz cuando isDark es true -->
  <div :class="{ dark: isDark }" class="min-h-screen flex flex-col transition-colors duration-300">
    <div class="flex w-full">
      
      <!-- Sidebar (Solo visible en pantallas grandes) -->
      <Sidebar v-if="isAuthenticated" :sidebarOpen="sidebarOpen" @toggleSidebar="toggleSidebar" />

      <!-- Contenedor de contenido -->
      <div class="relative flex flex-col w-full overflow-auto">
        
        <!-- Botón de hamburguesa (visible en móviles) -->
        <button 
          v-if="isAuthenticated" 
          @click="toggleSidebar" 
          class="lg:hidden fixed top-4 right-4 flex items-center justify-center w-12 h-12 bg-white/90 text-gray-800 dark:text-gray-100 rounded-full shadow-lg border border-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all duration-300 z-50"
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
        <div class="w-full h-full p-6 flex items-center justify-center transition-colors duration-300 bg-gray-100 dark:bg-gray-900">
          <router-view />
        </div>
      </div>
    </div>

    <!-- Botón fijo para alternar Dark Mode (oculto en móviles) -->
    <button 
      @click="toggleDarkMode" 
      class="hidden sm:flex fixed bottom-4 right-4 items-center justify-center w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-full shadow-lg border border-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600 transition"
    >
      <!-- Ícono de sol cuando está en light mode -->
      <svg 
        v-if="!isDark" 
        xmlns="http://www.w3.org/2000/svg" 
        width="24" 
        height="24" 
        viewBox="0 0 24 24" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="2" 
        stroke-linecap="round" 
        stroke-linejoin="round" 
        class="icon icon-tabler icon-tabler-sun text-yellow-500"
      >
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
        <path d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
      </svg>
      <!-- Ícono de luna cuando está en dark mode -->
      <svg 
        v-else 
        xmlns="http://www.w3.org/2000/svg" 
        width="24" 
        height="24" 
        viewBox="0 0 24 24" 
        fill="none" 
        stroke="currentColor" 
        stroke-width="2" 
        stroke-linecap="round" 
        stroke-linejoin="round" 
        class="icon icon-tabler icon-tabler-moon text-gray-200"
      >
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
      </svg>
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
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
};
</script>

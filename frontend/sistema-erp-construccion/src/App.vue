<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <!-- Contenedor principal -->
    <div class="flex w-full">
      
      <!-- Sidebar (Solo visible en pantallas grandes) -->
      <Sidebar v-if="isAuthenticated" :sidebarOpen="sidebarOpen" @toggleSidebar="toggleSidebar" />

      <!-- Contenedor de contenido -->
      <div class="relative flex flex-col w-full overflow-auto">
        
        <!-- 🚀 Botón de hamburguesa ahora en la derecha -->
        <button 
          v-if="isAuthenticated" 
          @click="toggleSidebar" 
          class="lg:hidden fixed top-4 right-4 flex items-center justify-center w-12 h-12 bg-white/90 text-gray-800 rounded-full shadow-lg border border-gray-300 hover:bg-gray-200 transition-all duration-300 z-50"
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

        <div class="w-full h-full p-6 flex items-center justify-center">
          <router-view />
        </div>
      </div>
    </div>
  </div>
</template>



<script setup>
import { ref } from 'vue';
import Sidebar from './components/Sidebar.vue';
import { isAuthenticated } from '@/service/authStore';

const sidebarOpen = ref(false);
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};
</script>

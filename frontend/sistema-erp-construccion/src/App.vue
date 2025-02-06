<template>
  <div class="min-h-screen bg-gray-100 grid grid-cols-[auto_1fr]">
    <!-- Sidebar con control responsivo -->
    <Sidebar v-if="isAuthenticated" :sidebarOpen="sidebarOpen" @toggleSidebar="toggleSidebar" />

    <!-- Vista principal -->
    <div class="relative flex flex-col w-full overflow-auto">
      <button 
        v-if="isAuthenticated" 
        @click="toggleSidebar" 
        class="md:hidden fixed top-4 left-4 bg-gray-400 text-white p-2 rounded-md z-50 shadow-lg"
      >
        ☰
      </button>

      <div class="p-6">
        <router-view />
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

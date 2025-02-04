<template>
  <div class="flex min-h-screen bg-[#d6d8db]">
    <!-- Sidebar condicional -->
    <Sidebar 
      v-if="isAuthenticated" 
      class="w-64 bg-gray-200 fixed inset-y-0 left-0 p-6"
    />
    <!-- Vista principal -->
    <div :class="['flex-1 p-6 overflow-auto', isAuthenticated ? 'ml-64' : 'ml-0']">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import Sidebar from './components/Sidebar.vue';
import { isAuthenticated } from '@/service/authStore';
</script>
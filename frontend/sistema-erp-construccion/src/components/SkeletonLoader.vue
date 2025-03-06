<template>
    <div class="animate-pulse">
      <!-- Versión para desktop -->
      <div class="hidden sm:block w-full">
        <div class="min-w-full">
          <!-- Encabezado del skeleton -->
          <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4"></div>
          
          <!-- Filas del skeleton -->
          <div v-for="i in rows" :key="`skeleton-row-${i}`" class="mt-4">
            <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
          </div>
        </div>
      </div>
      
      <!-- Versión para mobile -->
      <div class="block sm:hidden w-full">
        <div v-for="i in rows" :key="`skeleton-card-${i}`" class="mb-4">
          <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  defineProps({
    rows: {
      type: Number,
      default: 5
    }
  });
  </script>
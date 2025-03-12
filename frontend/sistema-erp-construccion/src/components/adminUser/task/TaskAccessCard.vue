<template>
  <router-link to="/tasks-admin" class="block">
    <div class="p-4 sm:p-6 bg-gradient-to-r from-orange-100 to-orange-200 dark:from-orange-800 dark:to-orange-900 rounded-lg shadow-lg transform transition hover:scale-105">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-orange-800 dark:text-orange-200">Tareas</h2>
          <p v-if="!isLoading" class="mt-1 sm:mt-2 text-sm sm:text-base text-orange-700 dark:text-orange-300">
            Total: {{ totalTasks }}
          </p>
          <div v-else class="mt-1 sm:mt-2 flex items-center">
            <div class="h-3 w-3 sm:h-4 sm:w-4 border-2 border-orange-700 dark:border-orange-300 border-t-transparent rounded-full animate-spin"></div>
            <span class="ml-2 text-xs sm:text-sm text-orange-700 dark:text-orange-300">Cargando...</span>
          </div>
        </div>
        <div>
          <svg class="w-10 h-10 sm:w-12 sm:h-12 text-orange-800 dark:text-orange-200" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14l4-4h12c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
          </svg>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { getAllCompanyTasks } from '@/service/taskService';

const totalTasks = ref(0);
const isLoading = ref(true);

const fetchTotalTasks = async () => {
  isLoading.value = true;
  try {
    const tasks = await getAllCompanyTasks();
    totalTasks.value = tasks.length;
  } catch (err: any) {
    console.error('Error al obtener tareas:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchTotalTasks();
});
</script>
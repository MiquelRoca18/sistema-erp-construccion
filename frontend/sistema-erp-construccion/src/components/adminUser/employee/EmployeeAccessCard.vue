<template>
  <router-link to="/employees-admin" class="block">
    <div class="p-4 sm:p-6 bg-gradient-to-r from-sky-100 to-sky-200 dark:from-sky-800 dark:to-sky-900 rounded-lg shadow-lg transform transition hover:scale-105">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-sky-800 dark:text-sky-200">Empleados</h2>
          <p v-if="!isLoading" class="mt-1 sm:mt-2 text-sm sm:text-base text-sky-700 dark:text-sky-300">
            Total: {{ totalEmployees }}
          </p>
          <div v-else class="mt-1 sm:mt-2 flex items-center">
            <div class="h-3 w-3 sm:h-4 sm:w-4 border-2 border-sky-700 dark:border-sky-300 border-t-transparent rounded-full animate-spin"></div>
            <span class="ml-2 text-xs sm:text-sm text-sky-700 dark:text-sky-300">Cargando...</span>
          </div>
        </div>
        <div class="flex-shrink-0">
          <svg class="w-8 h-8 sm:w-12 sm:h-12 text-sky-800 dark:text-sky-200" fill="currentColor" viewBox="0 0 24 24">
            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 2.04 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
          </svg>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { getEmployees } from '@/service/employeeService';

const totalEmployees = ref(0);
const isLoading = ref(true);

const fetchTotalEmployees = async () => {
  isLoading.value = true;
  try {
    const employees = await getEmployees();
    totalEmployees.value = employees.length;
  } catch (err: any) {
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchTotalEmployees();
});
</script>
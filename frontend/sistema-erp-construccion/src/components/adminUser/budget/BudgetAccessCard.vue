<template>
  <router-link to="/budgets" class="block">
    <div class="p-4 sm:p-6 bg-gradient-to-r from-yellow-100 to-yellow-200 dark:from-yellow-900/40 dark:to-amber-800/40 rounded-lg shadow-lg dark:shadow-gray-900/30 transform transition hover:scale-105 transition-all duration-300">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-yellow-800 dark:text-yellow-300 transition-colors duration-300">Presupuestos</h2>
          <p v-if="!isLoading" class="mt-1 sm:mt-2 text-sm sm:text-base text-yellow-700 dark:text-yellow-400">
            Total: {{ totalBudgets }}
          </p>
          <div v-else class="mt-1 sm:mt-2 flex items-center">
            <div class="h-3 w-3 sm:h-4 sm:w-4 border-2 border-yellow-700 dark:border-yellow-400 border-t-transparent rounded-full animate-spin"></div>
            <span class="ml-2 text-sm sm:text-base text-yellow-700 dark:text-yellow-400">Cargando...</span>
          </div>
        </div>
        <div>
          <!-- Ícono de documento con signo de dólar -->
          <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 sm:w-12 sm:h-12 text-yellow-800 dark:text-yellow-300 transition-colors duration-300" viewBox="0 0 24 24">
            <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="none" stroke="currentColor" stroke-width="2"/>
            <polyline points="14 2 14 8 20 8" fill="none" stroke="currentColor" stroke-width="2"/>
            <text x="12" y="16" text-anchor="middle" fill="currentColor" font-size="8" font-weight="bold">$</text>
          </svg>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { getBudgets } from '@/service/budgetService';

const totalBudgets = ref(0);
const isLoading = ref(true);

const fetchTotalBudgets = async () => {
  isLoading.value = true;
  try {
    const budgets = await getBudgets();
    totalBudgets.value = budgets.length;
  } catch (err: any) {
    // Error handling
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchTotalBudgets();
});
</script>
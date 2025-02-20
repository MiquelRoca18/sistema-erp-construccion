<template>
  <router-link to="/budgets" class="block">
    <div class="p-6 bg-gradient-to-r from-yellow-100 to-yellow-200 rounded-lg shadow-lg transform transition hover:scale-105">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-yellow-800">Presupuestos</h2>
        </div>
        <div>
          <!-- Ícono de documento con signo de dólar -->
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-yellow-800" viewBox="0 0 24 24">
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
const fetchTotalBudgets = async () => {
  try {
    const budgets = await getBudgets();
    totalBudgets.value = budgets.length;
  } catch (err: any) {
    console.error("Error al obtener presupuestos:", err);
  }
};
onMounted(() => {
  fetchTotalBudgets();
});
</script>

<style scoped>
</style>

<template>
  <div class="p-4 sm:p-6 transition-colors duration-300">
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
      <!-- KPI: Total de Empleados -->
      <div class="flex flex-col items-center bg-white/5 dark:bg-blue-950/10 p-3 rounded-lg">
        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-blue-200 dark:bg-blue-900/60 rounded-full flex items-center justify-center transition-colors duration-300">
          <svg class="w-7 h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 text-blue-800 dark:text-blue-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
          </svg>
        </div>
        <p class="mt-2 sm:mt-3 text-base sm:text-lg md:text-xl font-bold text-blue-800 dark:text-blue-300 transition-colors duration-300">{{ totalEmployees }}</p>
        <p class="text-xs sm:text-sm md:text-base text-gray-500 dark:text-gray-400 transition-colors duration-300">Empleados</p>
      </div>
      
      <!-- KPI: Proyectos Activos -->
      <div class="flex flex-col items-center bg-white/5 dark:bg-green-950/10 p-3 rounded-lg">
        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-green-200 dark:bg-green-900/60 rounded-full flex items-center justify-center transition-colors duration-300">
          <svg class="w-7 h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 text-green-800 dark:text-green-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 8h14v-2H7v2zm0-4h14v-2H7v2zm0-6v2h14V7H7z"/>
          </svg>
        </div>
        <p class="mt-2 sm:mt-3 text-base sm:text-lg md:text-xl font-bold text-green-800 dark:text-green-300 transition-colors duration-300">{{ totalProjects }}</p>
        <p class="text-xs sm:text-sm md:text-base text-gray-500 dark:text-gray-400 transition-colors duration-300">Proyectos</p>
      </div>
      
      <!-- KPI: Tareas -->
      <div class="flex flex-col items-center bg-white/5 dark:bg-orange-950/10 p-3 rounded-lg">
        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-orange-200 dark:bg-orange-900/60 rounded-full flex items-center justify-center transition-colors duration-300">
          <svg class="w-7 h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 text-orange-800 dark:text-orange-300" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14l4-4h12c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
          </svg>
        </div>
        <p class="mt-2 sm:mt-3 text-base sm:text-lg md:text-xl font-bold text-orange-800 dark:text-orange-300 transition-colors duration-300">{{ totalTasks }}</p>
        <p class="text-xs sm:text-sm md:text-base text-gray-500 dark:text-gray-400 transition-colors duration-300">Tareas</p>
      </div>
      
      <!-- KPI: Presupuesto Total -->
      <div class="flex flex-col items-center bg-white/5 dark:bg-amber-950/10 p-3 rounded-lg">
        <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-amber-200 dark:bg-amber-900/60 rounded-full flex items-center justify-center transition-colors duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 sm:w-8 sm:h-8 md:w-10 md:h-10 text-amber-800 dark:text-amber-300" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="none" stroke="currentColor" stroke-width="2"/>
            <polyline points="14 2 14 8 20 8" fill="none" stroke="currentColor" stroke-width="2"/>
            <text x="12" y="16" text-anchor="middle" fill="currentColor" font-size="10" font-weight="bold">$</text>
          </svg>
        </div>
        <p class="mt-2 sm:mt-3 text-base sm:text-lg md:text-xl font-bold text-amber-800 dark:text-amber-300 transition-colors duration-300">
          <span class="text-sm sm:text-base md:text-xl whitespace-nowrap overflow-hidden text-ellipsis max-w-[90px] sm:max-w-full block text-center">
            {{ totalBudgetFormatted }}
          </span>
        </p>
        <p class="text-xs sm:text-sm md:text-base text-gray-500 dark:text-gray-400 transition-colors duration-300">Presupuesto</p>
      </div>
    </div>
    
    <!-- Leyenda de Stats (pequeña en móvil) -->
    <div class="mt-3 sm:mt-4 text-center">
      <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
        Actualizado: {{ lastUpdated }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { getEmployees } from '@/service/employeeService';
import { getProjects } from '@/service/projectService';
import { getAllCompanyTasks } from '@/service/taskService';
import { getBudgets } from '@/service/budgetService';

const totalEmployees = ref(0);
const totalProjects = ref(0);
const totalTasks = ref(0);
const totalBudget = ref(0);
const lastUpdated = ref('');

// Sumar los tres campos para cada presupuesto
const calculateTotalBudget = (budgets: any[]) => {
  return budgets.reduce((sum, item) => {
    return sum + (Number(item.equipos) || 0)
               + (Number(item.mano_obra) || 0)
               + (Number(item.materiales) || 0);
  }, 0);
};

const fetchKpiData = async () => {
  try {
    const employees = await getEmployees();
    totalEmployees.value = employees.length;

    const projects = await getProjects();
    totalProjects.value = projects.length;

    const tasks = await getAllCompanyTasks();
    totalTasks.value = tasks.length;

    const budgets = await getBudgets();
    totalBudget.value = calculateTotalBudget(budgets);
    
    // Actualizar timestamp para mostrar última actualización
    const now = new Date();
    lastUpdated.value = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
  } catch (err: any) {
    console.error("Error al obtener KPI:", err);
  }
};

onMounted(() => {
  fetchKpiData();
});

const totalBudgetFormatted = computed(() => {
  // Para móviles, podríamos necesitar un formato más compacto
  if (window.innerWidth < 640) {
    const formatter = new Intl.NumberFormat('es-ES', { 
      style: 'currency', 
      currency: 'EUR',
      notation: 'compact',
      maximumFractionDigits: 1
    });
    return formatter.format(totalBudget.value);
  }
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(totalBudget.value);
});
</script>

<style scoped>
/* Asegurar que el texto del presupuesto no cause overflow en móviles */
@media (max-width: 370px) {
  .text-ellipsis {
    max-width: 70px !important;
  }
}
</style>
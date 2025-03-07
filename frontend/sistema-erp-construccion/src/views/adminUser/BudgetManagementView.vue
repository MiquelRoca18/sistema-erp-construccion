<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-8">
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado sin botón de crear -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-yellow-800 dark:text-yellow-200 text-center sm:text-left">
          Gestión de Presupuestos
        </h1>
      </div>

      <!-- Filtros -->
      <div class="mb-6 w-full grid grid-cols-1 md:grid-cols-2 gap-4">
        <input
          type="text"
          v-model="searchProject"
          placeholder="Buscar por proyecto..."
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition-colors duration-300"
        />
        <input
          type="number"
          v-model.number="searchTotal"
          placeholder="Filtrar por total (máximo)..."
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition-colors duration-300"
        />
      </div>

      <!-- Loader principal mientras se cargan los presupuestos -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-10">
        <div class="w-12 h-12 border-4 border-yellow-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600 dark:text-gray-300">Cargando presupuestos...</p>
      </div>

      <div v-else>
        <!-- Vista Mobile: Tarjetas -->
        <div class="sm:hidden w-full">
          <div
            v-for="budget in paginatedBudgets"
            :key="budget.presupuestos_id"
            class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow dark:shadow-gray-900/20 mb-4 cursor-pointer transition-colors duration-300 hover:bg-yellow-100 dark:hover:bg-yellow-800/40"
            @click="openViewModal(budget)"
          >
            <div class="flex justify-between items-center">
              <div>
                <p class="text-base font-bold text-yellow-800 dark:text-yellow-200">
                  {{ budget.nombre_proyecto }}
                </p>
                <p class="text-sm text-gray-700 dark:text-gray-300">Total: ${{ budget.total }}</p>
              </div>
              <div>
                <button
                  @click.stop="openEditModal(budget)"
                  class="px-3 py-1 bg-green-500 dark:bg-green-600 text-white rounded hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-sm"
                >
                  Editar
                </button>
              </div>
            </div>
          </div>
          <div v-if="paginatedBudgets.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-6">
            No se encontraron presupuestos.
          </div>
          <!-- Divs vacíos para mantener el espacio de 5 elementos -->
          <div v-for="n in missingRows" :key="'empty-' + n" class="h-20"></div>
        </div>

        <!-- Vista Desktop: Tabla -->
        <div class="hidden sm:block w-full overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="bg-gradient-to-r from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 text-yellow-800 dark:text-yellow-200">
                <th class="px-6 py-3 text-left font-semibold">ID</th>
                <th class="px-6 py-3 text-left font-semibold">Proyecto</th>
                <th class="px-6 py-3 text-left font-semibold hidden [@media(min-width:1200px)]:table-cell">Equipos</th>
                <th class="px-6 py-3 text-left font-semibold hidden [@media(min-width:1200px)]:table-cell">Mano de obra</th>
                <th class="px-6 py-3 text-left font-semibold hidden [@media(min-width:1200px)]:table-cell">Materiales</th>
                <th class="px-6 py-3 text-left font-semibold">Total</th>
                <th class="px-6 py-3 text-left font-semibold">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="budget in paginatedBudgets"
                :key="budget.presupuestos_id"
                class="bg-white dark:bg-gray-700 shadow dark:shadow-gray-900/10 rounded-lg transition-colors duration-300 cursor-pointer hover:bg-yellow-100 dark:hover:bg-yellow-800/40"
                @click="openViewModal(budget)"
              >
                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ budget.presupuestos_id }}</td>
                <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ budget.nombre_proyecto }}</td>
                <td class="px-6 py-4 hidden [@media(min-width:1200px)]:table-cell text-gray-800 dark:text-gray-200">{{ budget.equipos }}</td>
                <td class="px-6 py-4 hidden [@media(min-width:1200px)]:table-cell text-gray-800 dark:text-gray-200">{{ budget.mano_obra }}</td>
                <td class="px-6 py-4 hidden [@media(min-width:1200px)]:table-cell text-gray-800 dark:text-gray-200">{{ budget.materiales }}</td>
                <td class="px-6 py-4 font-bold text-gray-800 dark:text-yellow-200">{{ budget.total }}</td>
                <td class="px-6 py-4">
                  <div class="flex flex-col sm:flex-row gap-1 sm:gap-2" @click.stop>
                    <button
                      @click="openEditModal(budget)"
                      class="px-3 py-1 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-xs sm:text-sm"
                    >
                      Editar
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="paginatedBudgets.length === 0">
                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                  No se encontraron presupuestos.
                </td>
              </tr>
              <!-- Filas vacías para mantener el espacio -->
              <tr v-for="n in missingRows" :key="'empty-' + n" class="h-20 bg-transparent">
                <td colspan="7" class="px-6 py-4"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  
      <!-- Paginación -->
      <div v-if="totalPages > 1 && !loading" class="mt-6 flex items-center justify-center space-x-2">
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-600 dark:bg-yellow-500 text-white hover:bg-yellow-700 dark:hover:bg-yellow-600 disabled:opacity-50 transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <div class="flex space-x-2">
          <button 
            v-for="page in pages" 
            :key="page" 
            @click="goToPage(page)" 
            class="w-10 h-10 rounded-full border border-yellow-600 dark:border-yellow-500 flex items-center justify-center transition-colors duration-300 font-medium"
            :class="page === currentPage 
              ? 'bg-yellow-600 dark:bg-yellow-500 text-white' 
              : 'bg-white dark:bg-gray-800 text-yellow-600 dark:text-yellow-400 hover:bg-yellow-600 dark:hover:bg-yellow-500 hover:text-white'"
          >
            <span>{{ page }}</span>
          </button>
        </div>
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-600 dark:bg-yellow-500 text-white hover:bg-yellow-700 dark:hover:bg-yellow-600 disabled:opacity-50 transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
  
      <!-- Mensaje de error -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 dark:bg-red-900/40 border border-red-300 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        {{ error }}
      </div>
    </div>
  
    <!-- Modales -->
    <EditBudgetModal v-if="showEditModal" :budget="budgetToEdit" @close="closeEditModal" @updated="fetchBudgets" />
  </div>
</template>
  
<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { getBudgets } from '@/service/budgetService';
import EditBudgetModal from '@/components/adminUser/budget/EditBudgetModal.vue';
  
const budgets = ref<any[]>([]);
const loading = ref(true); // Iniciar como true para mostrar el loader inmediatamente
const error = ref('');
const searchProject = ref('');
const searchTotal = ref(''); 
const currentPage = ref(1);
const pageSize = 5;
  
const showEditModal = ref(false);
const budgetToEdit = ref(null);
const showViewModal = ref(false);
const selectedBudget = ref(null);
  
// Tiempo de espera mínimo para mostrar el loader
const minLoadingTime = 500; // ms

const fetchBudgets = async () => {
  const startTime = Date.now();
  loading.value = true;
  error.value = '';
  
  try {
    const data = await getBudgets();
    budgets.value = data;
    
    // Asegurar que el loader se muestre por al menos minLoadingTime ms
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < minLoadingTime) {
      await new Promise(resolve => setTimeout(resolve, minLoadingTime - elapsedTime));
    }
  } catch (err: any) {
    error.value = err.message || 'Error al obtener presupuestos.';
  } finally {
    loading.value = false;
  }
};
  
onMounted(() => {
  fetchBudgets();
});
  
// Reiniciar la página actual al cambiar los filtros
watch([searchProject, searchTotal], () => {
  currentPage.value = 1;
});
  
const filteredBudgets = computed(() => {
  const projectTerm = searchProject.value.trim().toLowerCase();
  const maxTotal = searchTotal.value;

  return budgets.value.filter(budget => {
    const projectMatch = budget.nombre_proyecto.toLowerCase().includes(projectTerm);
    let totalMatch = true;
    if (maxTotal !== null && maxTotal !== undefined && maxTotal !== "") {
      const totalVal = parseFloat(budget.total);
      totalMatch = !isNaN(totalVal) && totalVal <= parseFloat(maxTotal.toString());
    }
    return projectMatch && totalMatch;
  });
});
  
const totalPages = computed(() => Math.ceil(filteredBudgets.value.length / pageSize));
  
const paginatedBudgets = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredBudgets.value.slice(start, start + pageSize);
});
  
const missingRows = computed(() => {
  const missing = pageSize - paginatedBudgets.value.length;
  return missing > 0 ? missing : 0;
});
  
const pages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  if (total <= 5) {
    return Array.from({ length: total }, (_, i) => i + 1);
  } else if (current <= 3) {
    return [1,2,3,4,5];
  } else if (current >= total - 2) {
    return [total - 4, total - 3, total - 2, total - 1, total];
  } else {
    return [current - 2, current - 1, current, current + 1, current + 2];
  }
});
  
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
  
const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};
  
const goToPage = (page: number) => {
  currentPage.value = page;
};
  
const openEditModal = (budget: any) => {
  budgetToEdit.value = budget;
  showEditModal.value = true;
};
  
const closeEditModal = () => {
  showEditModal.value = false;
  budgetToEdit.value = null;
};

const openViewModal = (budget: any) => {
  selectedBudget.value = budget;
  showViewModal.value = true;
};
</script>
  
<style scoped>
table {
  border-collapse: separate;
  border-spacing: 0 0.5rem;
}
thead tr th {
  border: none;
}
tbody tr {
  border-radius: 0.5rem;
}
tbody tr td {
  border: none;
}

/* Animación para el loader */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
.pulse {
  animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
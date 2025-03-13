<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-4 md:p-8">
    <div class="w-full max-w-5xl mx-auto bg-white dark:bg-gray-800 p-4 md:p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-4 md:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-yellow-800 dark:text-yellow-200 text-center sm:text-left mb-3 sm:mb-0">
          Gestión de Presupuestos
        </h1>
      </div>

      <!-- Filtros -->
      <div class="mb-4 md:mb-6 w-full grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
        <input
          type="text"
          v-model="searchProject"
          placeholder="Buscar por proyecto..."
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition-colors duration-300 text-sm"
        />
        <input
          type="number"
          v-model.number="searchTotal"
          placeholder="Filtrar por total (máximo)..."
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-yellow-500 dark:focus:ring-yellow-400 transition-colors duration-300 text-sm"
        />
      </div>

      <!-- Loader principal mientras se cargan los presupuestos -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-8">
        <div class="w-12 h-12 border-4 border-yellow-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600 dark:text-gray-300">Cargando presupuestos...</p>
      </div>

      <!-- Tabla Responsiva -->
      <div v-else>
        <ResponsiveTable
          :items="paginatedBudgets"
          :headers="tableHeaders"
          headerClass="bg-gradient-to-r from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 text-yellow-800 dark:text-yellow-200"
          :has-pagination="true"
          :current-page="currentPage"
          :total-pages="totalPages"
          :mobile-properties="['nombre_proyecto', 'total', 'equipos', 'mano_obra', 'materiales']"
          empty-message="No se encontraron presupuestos."
          :edit-action="true"
          :delete-action="false"
          @edit="openEditModal"
          @item-click="openViewModal"
          @prev-page="prevPage"
          @next-page="nextPage"
          @go-to-page="goToPage"
        >
          <!-- Personalización de celdas para la tabla desktop -->
          <template #cell-total="{ value }">
            <span class="font-bold text-gray-800 dark:text-yellow-200">
              {{ formatCurrency(value) }}
            </span>
          </template>

          <template #cell-equipos="{ value }">
            {{ formatCurrency(value) }}
          </template>

          <template #cell-mano_obra="{ value }">
            {{ formatCurrency(value) }}
          </template>

          <template #cell-materiales="{ value }">
            {{ formatCurrency(value) }}
          </template>
          
          <!-- Personalización de vista móvil -->
          <template #mobile-item="{ item }">
            <div class="flex flex-col">
              <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ (item as Budget).nombre_proyecto }}</h3>
              <p class="text-base font-semibold text-yellow-700 dark:text-yellow-300 mt-1">
                Total: {{ formatCurrency((item as Budget).total) }}
              </p>
              <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-300">
                <p>
                  <span class="font-medium">Equipos:</span> {{ formatCurrency((item as Budget).equipos) }}
                </p>
                <p>
                  <span class="font-medium">Mano de obra:</span> {{ formatCurrency((item as Budget).mano_obra) }}
                </p>
                <p class="col-span-2">
                  <span class="font-medium">Materiales:</span> {{ formatCurrency((item as Budget).materiales) }}
                </p>
              </div>
            </div>
          </template>
        </ResponsiveTable>
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
import ResponsiveTable from '@/components/ResponsiveTable.vue';

// Definir la interfaz para Budget
interface Budget {
  presupuestos_id: number;
  proyectos_id: number;
  nombre_proyecto: string;
  equipos: number;
  mano_obra: number;
  materiales: number;
  total?: number;
}

// Definir la interfaz para las cabeceras de la tabla
interface TableHeader {
  key: string;
  title: string;
  class?: string;
}
  
const budgets = ref<Budget[]>([]);
const loading = ref(true);
const error = ref('');
const searchProject = ref('');
const searchTotal = ref<number | string>(''); 
const currentPage = ref(1);
const pageSize = 5;
  
const showEditModal = ref(false);
const budgetToEdit = ref<Budget | null>(null);
const showViewModal = ref(false);
const selectedBudget = ref<Budget | null>(null);

// Definición de cabeceras para la tabla
const tableHeaders: TableHeader[] = [
  { key: 'presupuestos_id', title: 'ID' },
  { key: 'nombre_proyecto', title: 'Proyecto' },
  { key: 'equipos', title: 'Equipos', class: 'hidden [@media(min-width:1200px)]:table-cell text-right' },
  { key: 'mano_obra', title: 'Mano de obra', class: 'hidden [@media(min-width:1200px)]:table-cell text-right' },
  { key: 'materiales', title: 'Materiales', class: 'hidden [@media(min-width:1200px)]:table-cell text-right' },
  { key: 'total', title: 'Total', class: 'text-right' }
];
  
// Tiempo de espera mínimo para mostrar el loader
const minLoadingTime = 500; 

const fetchBudgets = async () => {
  const startTime = Date.now();
  loading.value = true;
  error.value = '';
  
  try {
    const data = await getBudgets();
    
    // Añadir campo total calculado a cada presupuesto
    budgets.value = data.map((budget: any) => {
      const equipos = Number(budget.equipos) || 0;
      const manoObra = Number(budget.mano_obra) || 0;
      const materiales = Number(budget.materiales) || 0;
      return {
        ...budget,
        total: equipos + manoObra + materiales
      } as Budget;
    });
    
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
      const totalVal = budget.total !== undefined ? budget.total : 0;
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
  
const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};
  
const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};
  
const goToPage = (page: number) => {
  currentPage.value = page;
};
  
const openEditModal = (budget: Budget) => {
  budgetToEdit.value = budget;
  showEditModal.value = true;
};
  
const closeEditModal = () => {
  showEditModal.value = false;
  budgetToEdit.value = null;
};

const openViewModal = (budget: Budget) => {
  selectedBudget.value = budget;
  showViewModal.value = true;
};

// Función para formatear valores monetarios
const formatCurrency = (value: number | string | undefined): string => {
  if (value === null || value === undefined) return '€0,00';
  const numValue = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('es-ES', { 
    style: 'currency', 
    currency: 'EUR',
    minimumFractionDigits: 2
  }).format(numValue);
};
</script>
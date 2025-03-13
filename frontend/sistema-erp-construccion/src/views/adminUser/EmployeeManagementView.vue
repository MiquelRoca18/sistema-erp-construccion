<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-4 md:p-8 transition-colors duration-300">
    <div class="w-full max-w-5xl mx-auto bg-white dark:bg-gray-800 p-4 md:p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-4 md:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white text-center sm:text-left mb-3 sm:mb-0 transition-colors duration-300">
          Gestión de Empleados
        </h1>
        <button
          @click="openModal"
          :disabled="loading || loadingButton"
          class="px-3 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300 disabled:opacity-70 disabled:cursor-not-allowed flex items-center text-sm"
        >
          <svg v-if="loadingButton" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Nuevo Empleado
        </button>
      </div>

      <!-- Filtro -->
      <div class="mb-4 md:mb-6">
        <input
          type="text"
          v-model="searchTerm"
          placeholder="Buscar por nombre..."
          :disabled="loading"
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-500 transition-colors duration-300 disabled:opacity-70 disabled:cursor-not-allowed text-sm"
        />
      </div>

      <!-- Estado de carga: Skeleton loader integrado -->
      <div v-if="loading" class="animate-pulse">
        <div class="space-y-3">
          <!-- Skeleton para encabezado -->
          <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
          
          <!-- Skeleton para filas -->
          <div v-for="i in 5" :key="`skeleton-row-${i}`" class="mt-2">
            <div class="h-14 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
          </div>
        </div>
        <div class="text-center mt-4 text-sm text-gray-500 dark:text-gray-400">
          Cargando empleados...
        </div>
      </div>

      <!-- Tabla responsiva -->
      <div v-else>
        <ResponsiveTable
          :items="paginatedEmployees"
          :headers="tableHeaders"
          headerClass="bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 text-blue-800 dark:text-blue-200"
          :has-pagination="true"
          :current-page="currentPage"
          :total-pages="totalPages"
          :mobile-properties="['nombre', 'rol', 'telefono', 'correo']"
          empty-message="No se encontraron empleados."
          @edit="openEditModal"
          @delete="openDeleteModal"
          @prev-page="prevPage"
          @next-page="nextPage"
          @go-to-page="goToPage"
        >
          <!-- Personalización de elementos para móvil -->
          <template #mobile-item="{ item }">
            <div class="flex flex-col">
              <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ (item as EmployeeType).nombre }}</h3>
              <p class="text-sm text-blue-600 dark:text-blue-400">{{ (item as EmployeeType).rol }}</p>
              <div class="flex flex-col mt-1 text-xs text-gray-600 dark:text-gray-300">
                <p>
                  <span class="font-medium">Tel:</span> {{ (item as EmployeeType).telefono }}
                </p>
                <p>
                  <span class="font-medium">Email:</span> {{ (item as EmployeeType).correo }}
                </p>
              </div>
            </div>
          </template>
        </ResponsiveTable>
      </div>

      <!-- Mensaje de error -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        {{ error }}
        <button 
          @click="fetchEmployees" 
          class="ml-auto px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs flex items-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          Reintentar
        </button>
      </div>

      <!-- Modales -->
      <CreateEmployeeModal v-if="showModal" @close="closeModal" @created="fetchEmployees" />

      <EditEmployeeModal
        v-if="employeeToEdit"
        :employee="employeeToEdit"
        @close="closeEditModal"
        @updated="fetchEmployees"
      />

      <DeleteEmployeeModal
        v-if="employeeToDelete"
        :employee="employeeToDelete"
        @close="closeDeleteModal"
        @deleted="deleteEmployeeConfirmed"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { getEmployees, deleteEmployee } from '@/service/employeeService';
import { debounce } from '@/utils/index'; 
import CreateEmployeeModal from '@/components/adminUser/employee/CreateEmployeeModal.vue';
import EditEmployeeModal from '@/components/adminUser/employee/EditEmployeeModal.vue';
import DeleteEmployeeModal from '@/components/adminUser/employee/DeleteEmployeeModal.vue';
import ResponsiveTable from '@/components/ResponsiveTable.vue';

// Definición de tipo para el empleado
interface EmployeeType {
  empleados_id: number;
  nombre: string;
  rol: string;
  telefono: string;
  correo: string;
  fecha_contratacion?: string;
  [key: string]: any;
}

const employees = ref<EmployeeType[]>([]);
const loading = ref(true);
const error = ref('');
const searchTerm = ref('');
const currentPage = ref(1);
const pageSize = 5;
const showModal = ref(false);
const employeeToEdit = ref<EmployeeType | null>(null);
const employeeToDelete = ref<EmployeeType | null>(null);
const loadingButton = ref(false);

// Definición de cabeceras para la tabla
const tableHeaders = [
  { key: 'empleados_id', title: 'ID' },
  { key: 'nombre', title: 'Nombre' },
  { key: 'rol', title: 'Rol' },
  { key: 'telefono', title: 'Teléfono', class: 'hidden xl:table-cell' },
  { key: 'correo', title: 'Correo', class: 'hidden xl:table-cell' }
];

const fetchEmployees = async () => {
  error.value = '';
  loading.value = true;
  
  try {
    // Implementamos un timeout mínimo para evitar parpadeos en conexiones rápidas
    const startTime = Date.now();
    
    const data = await getEmployees();
    
    // Aseguramos que el loader se muestre por al menos 500ms para mejor UX
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    employees.value = data as EmployeeType[];
  } catch (err: any) {
    console.error('Error fetching employees:', err);
    error.value = err.message || 'Error al obtener empleados. Por favor, inténtelo de nuevo.';
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = debounce(() => {
  currentPage.value = 1;
}, 300);

// Observar cambios en searchTerm con debounce
watch(searchTerm, () => {
  debouncedSearch();
});

onMounted(() => {
  fetchEmployees();
});

const filteredEmployees = computed(() => {
  if (!searchTerm.value) return employees.value;
  const searchValue = searchTerm.value.toLowerCase().trim();
  return employees.value.filter((emp) =>
    emp.nombre.toLowerCase().includes(searchValue)
  );
});

const totalPages = computed(() => Math.ceil(filteredEmployees.value.length / pageSize));

const paginatedEmployees = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredEmployees.value.slice(start, start + pageSize);
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

const openModal = () => {
  loadingButton.value = true;
  setTimeout(() => {
    showModal.value = true;
    loadingButton.value = false;
  }, 200);
};

const closeModal = () => {
  showModal.value = false;
};

const openEditModal = (employee: EmployeeType) => {
  employeeToEdit.value = employee;
};

const closeEditModal = () => {
  employeeToEdit.value = null;
};

const openDeleteModal = (employee: EmployeeType) => {
  employeeToDelete.value = employee;
};

const closeDeleteModal = () => {
  employeeToDelete.value = null;
};

const deleteEmployeeConfirmed = async () => {
  if (!employeeToDelete.value) return;
  try {
    loading.value = true;
    await deleteEmployee(employeeToDelete.value.empleados_id);
    await fetchEmployees();
  } catch (err: any) {
    console.error(err.message);
    error.value = err.message || 'Error al eliminar el empleado.';
    loading.value = false;
  } finally {
    employeeToDelete.value = null;
  }
};
</script>

<style scoped>
/* Animaciones para los loaders */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
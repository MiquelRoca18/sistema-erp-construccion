<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-8 transition-colors duration-300">
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white text-center sm:text-left mb-4 sm:mb-0 transition-colors duration-300">
          Gestión de Empleados
        </h1>
        <button
          @click="openModal"
          :disabled="loading || loadingButton"
          class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300 disabled:opacity-70 disabled:cursor-not-allowed flex items-center"
        >
          <svg v-if="loadingButton" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Nuevo Empleado
        </button>
      </div>

      <!-- Filtro -->
      <div class="mb-6">
        <input
          type="text"
          v-model="searchTerm"
          placeholder="Buscar por nombre..."
          :disabled="loading"
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-500 transition-colors duration-300 disabled:opacity-70 disabled:cursor-not-allowed"
        />
      </div>

      <!-- Estado de carga: Skeleton loader integrado -->
      <div v-if="loading" class="animate-pulse">
        <!-- Versión para desktop -->
        <div class="hidden sm:block w-full">
          <div class="min-w-full">
            <!-- Encabezado del skeleton -->
            <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4"></div>
            
            <!-- Filas del skeleton -->
            <div v-for="i in 5" :key="`skeleton-row-${i}`" class="mt-4">
              <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
          </div>
        </div>
        
        <!-- Versión para mobile -->
        <div class="block sm:hidden w-full">
          <div v-for="i in 5" :key="`skeleton-card-${i}`" class="mb-4">
            <div class="h-20 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
          </div>
        </div>
        
        <div class="text-center mt-4 text-sm text-gray-500 dark:text-gray-400">
          Cargando empleados...
        </div>
      </div>

      <!-- Vista Mobile: Card view -->
      <div v-else class="block sm:hidden">
        <div
          v-for="employee in paginatedEmployees"
          :key="employee.empleados_id"
          class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow dark:shadow-gray-900/20 mb-4 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors duration-300"
        >
          <div class="flex justify-between items-center">
            <div>
              <p class="text-base font-medium text-blue-600 dark:text-blue-400">{{ employee.nombre }}</p>
              <p class="text-sm text-gray-600 dark:text-gray-300">{{ employee.rol }}</p>
            </div>
            <!-- Botones: Editar y Eliminar -->
            <div class="flex space-x-2">
              <button
                @click="openEditModal(employee)"
                class="px-3 py-1 bg-green-500 dark:bg-green-600 text-white rounded hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-sm"
              >
                Editar
              </button>
              <button
                @click="openDeleteModal(employee)"
                class="px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-sm"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
        <div v-if="paginatedEmployees.length === 0 && !loading" class="text-center text-gray-500 dark:text-gray-400 my-8">
          No se encontraron empleados.
        </div>
        <!-- Divs vacíos para mantener espacio de 5 elementos -->
        <div v-for="n in missingRows" :key="'empty-' + n" class="h-16"></div>
      </div>

      <!-- Vista Desktop: Tabla -->
      <div v-if="!loading" class="hidden sm:block overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gradient-to-r from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 text-blue-800 dark:text-blue-200">
              <th class="px-6 py-3 text-left font-semibold">ID</th>
              <th class="px-6 py-3 text-left font-semibold">Nombre</th>
              <th class="px-6 py-3 text-left font-semibold">Rol</th>
              <th class="px-6 py-3 text-left font-semibold hidden xl:table-cell">Teléfono</th>
              <th class="px-6 py-3 text-left font-semibold hidden xl:table-cell">Correo</th>
              <th class="px-6 py-3 text-left font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="employee in paginatedEmployees"
              :key="employee.empleados_id"
              class="bg-white dark:bg-gray-700 shadow dark:shadow-gray-900/10 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors duration-300"
            >
              <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ employee.empleados_id }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ employee.nombre }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ employee.rol }}</td>
              <td class="px-6 py-4 hidden xl:table-cell text-gray-800 dark:text-gray-200">{{ employee.telefono }}</td>
              <td class="px-6 py-4 hidden xl:table-cell text-gray-800 dark:text-gray-200">{{ employee.correo }}</td>
              <td class="px-6 py-4">
                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2">
                  <button
                    @click="openEditModal(employee)"
                    class="px-3 py-1 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-xs sm:text-sm"
                  >
                    Editar
                  </button>
                  <button
                    @click="openDeleteModal(employee)"
                    class="px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-xs sm:text-sm"
                  >
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="paginatedEmployees.length === 0 && !loading">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                No se encontraron empleados.
              </td>
            </tr>
            <!-- Filas vacías para mantener 5 filas siempre -->
            <tr
              v-for="n in missingRows"
              :key="'empty-' + n"
              class="h-16 bg-transparent"
            >
              <td class="px-6 py-4" colspan="6"></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div v-if="totalPages > 1 && !loading" class="mt-6 flex items-center justify-center space-x-2">
        <!-- Botón Anterior -->
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-50 transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>

        <!-- Botones Numerados -->
        <div class="flex space-x-2">
          <button 
            v-for="page in pages" 
            :key="page" 
            @click="goToPage(page)" 
            class="w-10 h-10 rounded-full border border-blue-600 dark:border-blue-500 flex items-center justify-center transition-colors duration-300 font-medium"
            :class="page === currentPage 
              ? 'bg-blue-600 dark:bg-blue-500 text-white' 
              : 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white'"
          >
            <span>{{ page }}</span>
          </button>
        </div>

        <!-- Botón Siguiente -->
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-50 transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- Mensaje de error -->
      <div v-if="error" class="mt-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-red-700 dark:text-red-300">
        <div class="flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ error }}
        </div>
        <button 
          @click="fetchEmployees" 
          class="mt-2 px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm flex items-center"
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

const employees = ref<any[]>([]);
const loading = ref(true);
const error = ref('');
const searchTerm = ref('');
const currentPage = ref(1);
const pageSize = 5;
const showModal = ref(false);
const employeeToEdit = ref(null);
const employeeToDelete = ref(null);
const loadingButton = ref(false);

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
    
    employees.value = data;
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

// Filas vacías para mantener 5 filas siempre
const missingRows = computed(() => {
  const missing = pageSize - paginatedEmployees.value.length;
  return missing > 0 ? missing : 0;
});

// Paginación Premium: limitar a 5 números de página
const pages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  if (total <= 5) {
    return Array.from({ length: total }, (_, i) => i + 1);
  } else if (current <= 3) {
    return [1, 2, 3, 4, 5];
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

const openEditModal = (employee: any) => {
  employeeToEdit.value = employee;
};

const closeEditModal = () => {
  employeeToEdit.value = null;
};

const openDeleteModal = (employee: any) => {
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
</style>
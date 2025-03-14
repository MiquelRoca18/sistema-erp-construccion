<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-4 md:p-8 transition-colors duration-300">
    <div class="w-full max-w-5xl mx-auto bg-white dark:bg-gray-800 p-4 md:p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-4 md:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white text-center sm:text-left mb-3 sm:mb-0 transition-colors duration-300">
          Gestión de Tareas
        </h1>
        <button
          @click="openModal"
          class="px-3 py-2 bg-orange-600 dark:bg-orange-500 text-white rounded-lg hover:bg-orange-700 dark:hover:bg-orange-600 transition-colors duration-300 text-sm"
          :disabled="loading"
        >
          <span v-if="!loading">Nueva Tarea</span>
          <span v-else class="flex items-center space-x-1">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Cargando...</span>
          </span>
        </button>
      </div>

      <!-- Filtro Desplegable -->
      <div class="mb-4 md:mb-6">
        <button 
          @click="toggleFilter" 
          class="px-3 py-2 bg-orange-600 dark:bg-orange-500 text-white rounded-lg hover:bg-orange-700 dark:hover:bg-orange-600 transition-colors duration-300 text-sm"
          :disabled="loading"
        >
          Filtros
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block ml-1 transition-transform" :class="{'rotate-180': showFilter}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <transition name="fade">
          <div v-if="showFilter" class="mt-3 p-3 md:p-4 border dark:border-gray-600 rounded-lg shadow dark:shadow-gray-900/20 bg-gray-50 dark:bg-gray-700 transition-colors duration-300">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <!-- Filtros -->
              <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-1 text-sm">Proyecto</label>
                <select 
                  v-model="filter.project" 
                  class="w-full p-2 border dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 transition-colors duration-300 text-sm"
                  :disabled="loading"
                >
                  <option value="">Todos</option>
                  <option v-for="project in projects" :key="project" :value="project">
                    {{ project }}
                  </option>
                </select>
              </div>
              <!-- Otros filtros (Estado, Empleado, etc.) -->
              <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-1 text-sm">Estado</label>
                <select 
                  v-model="filter.state" 
                  class="w-full p-2 border dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 transition-colors duration-300 text-sm"
                  :disabled="loading"
                >
                  <option value="">Todos</option>
                  <option value="pendiente">Pendiente</option>
                  <option value="en progreso">En Progreso</option>
                  <option value="finalizado">Finalizado</option>
                </select>
              </div>
              <!-- Filtro de empleado -->
              <div>
                <label class="block text-gray-700 dark:text-gray-300 mb-1 text-sm">Empleado</label>
                <select 
                  v-model="filter.employee" 
                  class="w-full p-2 border dark:border-gray-600 rounded-lg bg-white dark:bg-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 transition-colors duration-300 text-sm"
                  :disabled="loading"
                >
                  <option value="">Todos</option>
                  <option v-for="employee in employees" :key="employee" :value="employee">
                    {{ employee }}
                  </option>
                </select>
              </div>
              <!-- Botón para limpiar filtros -->
              <div class="col-span-1 sm:col-span-2 text-right mt-2">
                <button 
                  @click="clearFilters" 
                  class="px-3 py-1.5 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-300 text-sm"
                  :disabled="loading"
                >
                  Limpiar filtros
                </button>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Componente Loader -->
      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="relative">
          <div class="h-16 w-16 rounded-full border-t-4 border-b-4 border-orange-500 animate-spin"></div>
          <div class="absolute top-0 left-0 h-16 w-16 rounded-full border-t-4 border-b-4 border-orange-200 animate-spin" style="animation-duration: 1.5s;"></div>
        </div>
        <p class="ml-4 text-gray-600 dark:text-gray-300">Cargando tareas...</p>
      </div>

      <!-- Tabla Responsiva -->
      <div v-else>
        <ResponsiveTable
          :items="paginatedTasks"
          :headers="tableHeaders"
          headerClass="bg-gradient-to-r from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 text-orange-800 dark:text-orange-200"
          :has-pagination="true"
          :current-page="currentPage"
          :total-pages="totalPages"
          :mobile-properties="['nombre_tarea', 'estado', 'nombre_proyecto', 'empleado_nombre']"
          empty-message="No se encontraron tareas."
          @edit="openEditModal"
          @delete="openDeleteModal"
          @item-click="openViewModal"
          @prev-page="prevPage"
          @next-page="nextPage"
          @go-to-page="goToPage"
        >
          <!-- Personalización de celdas -->
          <template #cell-estado="{ value }">
            <span 
              class="px-2 py-1 rounded-full text-xs font-medium" 
              :class="{
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200': value === 'pendiente',
                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200': value === 'en progreso',
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200': value === 'finalizado'
              }"
            >
              {{ value }}
            </span>
          </template>
          
          <!-- Personalización de acciones -->
          <template #actions="{ item }">
            <button
              @click="openEditModal(item as Task)"
              class="px-2 py-1 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-xs"
            >
              Editar
            </button>
            <button
              @click="openDeleteModal(item as Task)"
              class="px-2 py-1 bg-red-500 dark:bg-red-600 text-white rounded-lg hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-xs"
            >
              Eliminar
            </button>
            <button
              @click="openAssignModal(item as Task)"
              class="px-2 py-1 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300 text-xs"
            >
              Asignar
            </button>
          </template>
          
          <!-- Personalización de elementos para móvil -->
          <template #mobile-item="{ item }">
            <div class="flex flex-col">
              <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ (item as Task).nombre_tarea }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-300">
                Proyecto: {{ (item as Task).nombre_proyecto }}
              </p>
              <div class="flex items-center mt-1">
                <span class="text-xs mr-1">Estado:</span>
                <span 
                  class="px-2 py-0.5 rounded-full text-xs" 
                  :class="{
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200': (item as Task).estado === 'pendiente',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200': (item as Task).estado === 'en progreso',
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200': (item as Task).estado === 'finalizado'
                  }"
                >
                  {{ (item as Task).estado }}
                </span>
              </div>
              <p v-if="(item as Task).empleado_nombre" class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Asignado a: {{ (item as Task).empleado_nombre }}
              </p>
            </div>
          </template>
        </ResponsiveTable>
      </div>

      <!-- Mensaje de error -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        {{ error }}
        <button @click="error = ''" class="ml-auto text-red-500 hover:text-red-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Modales -->
      <CreateTaskModal 
        v-if="showModal" 
        @close="closeModal" 
        @created="fetchTasks" 
      />
      <EditTaskModal
        v-if="taskToEdit"
        :task="taskToEdit"
        @close="closeEditModal"
        @updated="fetchTasks"
      />
      <DeleteTaskModal
        v-if="taskToDelete"
        :task="taskToDelete"
        @close="closeDeleteModal"
        @deleted="deleteTaskConfirmed"
      />
      <TaskViewModal
        v-if="taskToView"
        :task="taskToView"
        @close="closeViewModal"
      />
      <AssignEmployeesModal
        v-if="taskToAssign"
        :task="taskToAssign"
        @close="closeAssignModal"
        @updated="taskUpdated"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { getAllCompanyTasks, deleteTask } from '@/service/taskService';
import CreateTaskModal from '@/components/adminUser/task/CreateTaskModal.vue';
import EditTaskModal from '@/components/adminUser/task/EditTaskModal.vue';
import DeleteTaskModal from '@/components/adminUser/task/DeleteTaskModal.vue';
import TaskViewModal from '@/components/adminUser/task/TaskViewModal.vue';
import AssignEmployeesModal from '@/components/adminUser/employee/AssignEmployeesModal.vue';
import ResponsiveTable from '@/components/ResponsiveTable.vue';
import type { Task } from '@/types/task';

const tasks = ref<Task[]>([]);
const loading = ref(true);
const error = ref('');
const currentPage = ref(1);
const pageSize = 5;
const showModal = ref(false);
const taskToEdit = ref<Task | null>(null);
const taskToDelete = ref<Task | null>(null);
const taskToView = ref<Task | null>(null);
const taskToAssign = ref<Task | null>(null);

const showFilter = ref(false);
const filter = ref({
  project: '',
  state: '',
  employee: ''
});

// Definición de cabeceras para la tabla
const tableHeaders = [
  { key: 'tareas_id', title: 'ID' },
  { key: 'nombre_tarea', title: 'Tarea' },
  { key: 'estado', title: 'Estado', class: 'hidden md:table-cell' },
  { key: 'nombre_proyecto', title: 'Proyecto', class: 'hidden lg:table-cell' },
  { key: 'empleado_nombre', title: 'Empleado(s)' }
];

const toggleFilter = () => {
  showFilter.value = !showFilter.value;
};

const clearFilters = () => {
  filter.value = {
    project: '',
    state: '',
    employee: ''
  };
};

// Cada vez que se modifiquen los filtros se resetea la página actual a 1
watch(filter, () => {
  currentPage.value = 1;
}, { deep: true });

const clearAllCaches = () => {
  console.log("Limpiando todas las cachés...");
  for (let key in localStorage) {
    if (
      key.includes('task') || 
      key.includes('employee') || 
      key.includes('project') || 
      key.includes('company')
    ) {
      console.log("Eliminando caché:", key);
      localStorage.removeItem(key);
    }
  }
};


const fetchTasks = async () => {
  try {
    loading.value = true;
    error.value = '';
    
    // Simulamos un retardo mínimo para evitar parpadeos en cargas muy rápidas
    const startTime = Date.now();
    
    // Limpiar cualquier caché de localStorage relacionada con tareas
    Object.keys(localStorage)
      .filter(key => key.includes('task') || key.includes('employee'))
      .forEach(key => localStorage.removeItem(key));
    
    // También podemos intentar limpiar cualquier caché de sessionStorage
    Object.keys(sessionStorage)
      .filter(key => key.includes('task') || key.includes('employee'))
      .forEach(key => sessionStorage.removeItem(key));
    
    const data = await getAllCompanyTasks();
    
    // Aseguramos que el loader se muestre al menos por 500ms para evitar parpadeos
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    tasks.value = data;
  } catch (err) {
    error.value = err.message || 'Error al obtener tareas';
    console.error('Error al cargar tareas:', err);
  } finally {
    loading.value = false;
  }
};


onMounted(() => {
  fetchTasks();
  
  // Verificar si hay una señal para forzar recarga
  const forceReload = window.sessionStorage.getItem('forceTasksReload');
  if (forceReload === 'true') {
    window.sessionStorage.removeItem('forceTasksReload');
    // Recargar después de un breve retraso para asegurar que el componente está montado
    setTimeout(() => {
      fetchTasks();
    }, 200);
  }
});

// Opciones de filtro
const projects = computed(() => {
  const all = tasks.value.map(task => task.nombre_proyecto);
  return [...new Set(all)];
});

const employees = computed(() => {
  const employeeSet = new Set<string>();
  tasks.value.forEach(task => {
    if (task.empleado_nombre) {
      task.empleado_nombre.split(',').forEach(emp => {
        const name = emp.trim();
        if (name) employeeSet.add(name);
      });
    }
  });
  return [...employeeSet];
});

// Filtrado combinando los filtros seleccionados
const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    let valid = true;
    if (filter.value.project && task.nombre_proyecto !== filter.value.project) {
      valid = false;
    }
    if (filter.value.state && task.estado.toLowerCase() !== filter.value.state.toLowerCase()) {
      valid = false;
    }
    if (filter.value.employee) {
      const taskEmployees = task.empleado_nombre ? task.empleado_nombre.split(',').map(e => e.trim()) : [];
      if (!taskEmployees.includes(filter.value.employee)) {
        valid = false;
      }
    }
    return valid;
  });
});

const totalPages = computed(() => Math.ceil(filteredTasks.value.length / pageSize));

const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredTasks.value.slice(start, start + pageSize);
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
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const openEditModal = (task: Task) => {
  taskToEdit.value = task;
};

const closeEditModal = () => {
  taskToEdit.value = null;
};

const openDeleteModal = (task: Task) => {
  taskToDelete.value = task;
};

const closeDeleteModal = () => {
  taskToDelete.value = null;
};

const deleteTaskConfirmed = async () => {
  if (!taskToDelete.value) return;
  try {
    loading.value = true; // Activar loader durante la eliminación
    await deleteTask(taskToDelete.value.tareas_id);
    await fetchTasks(); // Recargamos las tareas después de eliminar
  } catch (err: any) {
    error.value = `Error al eliminar tarea: ${err.message || 'Error desconocido'}`;
    console.error('Error al eliminar tarea:', err);
  } finally {
    loading.value = false;
    taskToDelete.value = null;
  }
};

const openViewModal = (task: Task) => {
  taskToView.value = task;
};

const closeViewModal = () => {
  taskToView.value = null;
};

const openAssignModal = (task: Task) => {
  taskToAssign.value = task;
};

// Modificar el evento 'updated' para aceptar el flag
const closeAssignModal = () => {
  taskToAssign.value = null;
};

// Separar la función de actualización
const taskUpdated = async (forceReload = false) => {
  if (forceReload) {
    console.log("Forzando recarga de tareas después de actualización");
    // Limpiar todas las cachés relacionadas
    clearAllCaches();
    // Pequeña pausa para asegurar que cualquier operación de backend se complete
    await new Promise(resolve => setTimeout(resolve, 300));
    await fetchTasks();
  }
};

</script>

<style scoped>
/* Transición para el filtro */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
<template>
  <div class="w-full h-full flex flex-col justify-center px-4 md:px-6 min-h-screen transition-colors duration-300">
    <!-- Panel de Filtros -->
    <div class="mx-auto w-full max-w-5xl mb-4">
      <details class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
        <summary class="cursor-pointer px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-t-md select-none flex items-center justify-between transition-colors duration-300">
          <span class="text-lg font-semibold">Filtrar Tareas</span>
          <svg class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </summary>
        <div class="px-4 py-4 md:px-6 md:py-6 dark:bg-gray-800 transition-colors duration-300">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Filtro de Proyecto -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proyecto</label>
              <select v-model="selectedProject" @change="fetchTasks" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
                <option value="">Todos los proyectos</option>
                <option v-for="project in uniqueProjects" :key="project.id" :value="project.id">
                  {{ project.nombre }}
                </option>
              </select>
            </div>
            <!-- Filtro de Estado -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
              <select v-model="selectedStatus" @change="fetchTasks" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
                <option value="">Todos los estados</option>
                <option v-for="status in availableStatuses" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
            </div>
            <!-- Filtro de Tipo de Tarea -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Tarea</label>
              <select v-model="selectedTaskType" @change="fetchTasks" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300">
                <option value="mis">Mis tareas</option>
                <option value="otros">Tareas de otros</option>
              </select>
            </div>
            <!-- Filtro de Fecha de Inicio -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Inicio</label>
              <input v-model="startDate" @change="fetchTasks" type="date" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300" />
            </div>
            <!-- Filtro de Fecha Fin -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Fin</label>
              <input v-model="endDate" @change="fetchTasks" type="date" class="w-full p-2.5 border border-gray-300 dark:border-gray-600 rounded-md text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors duration-300" />
            </div>
          </div>
        </div>
      </details>
    </div>

    <!-- Lista de Tareas -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/30 p-6 mx-auto w-full max-w-5xl flex flex-col min-h-[600px] transition-colors duration-300">
      <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4 text-center">Lista de Tareas</h2>

      <div v-if="loading" class="text-center text-lg text-gray-500 dark:text-gray-400 flex-grow flex items-center justify-center">
        Cargando tareas...
      </div>
      <div v-if="error" class="text-red-500 dark:text-red-400 text-center text-lg flex-grow flex items-center justify-center">
        {{ error }}
      </div>
      <div v-if="filteredTasks.length === 0" class="text-center text-gray-500 dark:text-gray-400 flex-grow flex items-center justify-center">
        No hay tareas disponibles.
      </div>

      <!-- Grid de Tareas -->
      <div class="grid gap-5 flex-grow" :class="(isMobile || isTablet) ? 'grid-cols-1 grid-rows-3' : 'grid-cols-2 grid-rows-3'">
        <div
          v-for="task in paginatedTasks"
          :key="task.tareas_id"
          :class="[
            'relative rounded-lg transition duration-300 cursor-pointer overflow-hidden',
            selectedTaskType === 'otros' 
              ? 'bg-white dark:bg-gray-700 border-l-4 border-blue-500 dark:border-blue-400' 
              : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600'
          ]"
          @click="openTaskModal(task)"
        >
          <!-- Encabezado: Nombre de la tarea y estado -->
          <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-600">
            <div class="flex justify-between items-center">
              <h3 class="font-bold text-lg text-gray-800 dark:text-white truncate">
                {{ task.nombre_tarea }}
              </h3>
              <span
                class="inline-block px-3 py-1 text-xs font-semibold rounded-full"
                :class="{
                  'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300': task.estado === 'en progreso',
                  'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300': task.estado === 'finalizado',
                  'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300': task.estado !== 'en progreso' && task.estado !== 'finalizado'
                }"
              >
                {{ task.estado }}
              </span>
            </div>
          </div>
          <!-- Cuerpo: Información -->
          <div class="px-6 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">
              <strong>Proyecto:</strong> {{ task.nombre_proyecto }}
            </p>
            <!-- Para "mis tareas": se muestran las fechas -->
            <div v-if="selectedTaskType !== 'otros'" class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
              <p><strong>Inicio:</strong> {{ task.fecha_inicio }}</p>
              <p><strong>Fin:</strong> {{ task.fecha_fin }}</p>
            </div>
            <!-- Para "Tareas de otros": se muestra el nombre del empleado asignado -->
            <div v-else class="mt-2">
              <p class="text-sm text-gray-600 dark:text-gray-300">
                <strong>Asignado a:</strong> {{ task.nombre_empleado }}
              </p>
            </div>
          </div>
        </div>

        <!-- Espacios vacíos para completar el grid -->
        <div v-for="n in emptySlots" :key="'empty-'+n" class="p-4 opacity-0"></div>
      </div>

      <!-- Paginación -->
      <div v-if="totalPages > 1" class="mt-6 flex items-center justify-center space-x-2">
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
    </div>

    <!-- Modales -->
    <TaskDetailModal
      v-if="selectedTask && selectedTaskType !== 'otros'"
      :task="selectedTask"
      @close="selectedTask = null"
      @update="handleTaskUpdate"
    />

    <TaskDetailModalOthers
      v-if="selectedTask && selectedTaskType === 'otros'"
      :task="selectedTask"
      @close="selectedTask = null"
      @update="handleTaskUpdate"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { getAllTasks, getTasksByResponsible } from '@/service/taskService';
import TaskDetailModal from '@/components/normalUser/TaskDetailModal.vue';
import TaskDetailModalOthers from '@/components/normalUser/TaskDetailModalOthers.vue';

const router = useRouter();
const route = useRoute();
const employeeId = ref(router.currentRoute.value.params.id);

const tasks = ref([]);
const loading = ref(true);
const error = ref('');

// Filtros
const selectedProject = ref('');
const selectedStatus = ref('');
const selectedTaskType = ref('mis');
const startDate = ref('');
const endDate = ref('');

const selectedTask = ref(null);

// Paginación y funciones relacionadas
const currentPage = ref(1);
const resetPagination = () => { currentPage.value = 1; };

const uniqueProjects = computed(() => {
  const projectMap = new Map();
  tasks.value.forEach(task => {
    if (task.proyectos_id && task.nombre_proyecto) {
      projectMap.set(task.proyectos_id, task.nombre_proyecto);
    }
  });
  return Array.from(projectMap, ([id, nombre]) => ({ id, nombre }));
});

const availableStatuses = computed(() => {
  const statusesSet = new Set();
  tasks.value.forEach(task => {
    if (task.estado) statusesSet.add(task.estado);
  });
  statusesSet.add("en progreso");
  statusesSet.add("finalizado");
  return Array.from(statusesSet);
});

const screenWidth = ref(window.innerWidth);
const isMobile = computed(() => screenWidth.value < 640);
const isTablet = computed(() => screenWidth.value >= 640 && screenWidth.value < 1024);
const isOneColumn = computed(() => isMobile.value || isTablet.value);
const itemsPerPage = computed(() => (isOneColumn.value ? 3 : 6));

// Espacios vacíos para mantener grid
const emptySlots = computed(() => {
  const slots = itemsPerPage.value - paginatedTasks.value.length;
  return slots > 0 ? slots : 0;
});

// Sincronizar filtros con la URL 
watch([selectedProject, selectedStatus, selectedTaskType, startDate, endDate], ([newProject, newStatus, newTaskType, newStartDate, newEndDate]) => {
  router.replace({
    query: {
      ...route.query,
      project: newProject || undefined,
      status: newStatus || undefined,
      taskType: newTaskType || undefined,
      startDate: newStartDate || undefined,
      endDate: newEndDate || undefined,
    },
  });
}, { deep: true });

watch(() => route.query.status, (newStatus) => {
  if (newStatus === 'pendiente') {
    selectedStatus.value = 'pendiente';
    resetPagination();
  }
}, { immediate: true });

// Función para obtener tareas según el filtro de tipo
const fetchTasks = async () => {
  loading.value = true;
  try {
    let response;
    if (selectedTaskType.value === 'otros') {
      response = await getTasksByResponsible(employeeId.value);
    } else {
      response = await getAllTasks(employeeId.value);
    }
    tasks.value = response;
  } catch (err) {
    error.value = err.message || 'Error al obtener las tareas.';
  } finally {
    loading.value = false;
  }
  resetPagination();
};

onMounted(fetchTasks);

// Filtro local
const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    const matchesProject = !selectedProject.value || task.proyectos_id == selectedProject.value;
    const matchesStatus = !selectedStatus.value || task.estado === selectedStatus.value;
    const matchesStartDate = !startDate.value || new Date(task.fecha_inicio) >= new Date(startDate.value);
    const matchesEndDate = !endDate.value || new Date(task.fecha_fin) <= new Date(endDate.value);
    return matchesProject && matchesStatus && matchesStartDate && matchesEndDate;
  });
});

const totalPages = computed(() => Math.ceil(filteredTasks.value.length / itemsPerPage.value));

const paginatedTasks = computed(() =>
  filteredTasks.value.slice(
    (currentPage.value - 1) * itemsPerPage.value,
    currentPage.value * itemsPerPage.value
  )
);

// Paginación: limitar a 5 números de página
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

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

const goToPage = (page) => { currentPage.value = page; };

const openTaskModal = (task) => { selectedTask.value = task; };

const handleTaskUpdate = (updatedTask) => {
  const index = tasks.value.findIndex(t => t.tareas_id === updatedTask.tareas_id);
  if (index !== -1) {
    tasks.value[index] = updatedTask;
  }
  selectedTask.value = null;
};

const updateScreenWidth = () => { screenWidth.value = window.innerWidth; };
window.addEventListener('resize', updateScreenWidth);
</script>

<style>
/* Rota el ícono del <summary> al abrir/cerrar */
details[open] summary svg {
  transform: rotate(180deg);
}
/* Elimina el marcador por defecto del summary en algunos navegadores */
summary::-webkit-details-marker {
  display: none;
}
</style>
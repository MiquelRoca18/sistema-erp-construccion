<template>
  <div class="w-full h-full flex flex-col justify-center px-4 md:px-6 min-h-screen">
    <!-- Panel de Filtros -->
    <div class="mx-auto w-full max-w-5xl mb-4">
      <details class="bg-white border border-gray-300 rounded-lg shadow-lg">
        <summary class="cursor-pointer px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-t-md select-none flex items-center justify-between">
          <span class="text-lg font-semibold">Filtrar Tareas</span>
          <svg class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </summary>
        <div class="px-4 py-4 md:px-6 md:py-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Filtro de Proyecto -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto</label>
              <select v-model="selectedProject" @change="fetchTasks" class="w-full p-2.5 border border-gray-300 rounded-md text-sm">
                <option value="">Todos los proyectos</option>
                <option v-for="project in uniqueProjects" :key="project.id" :value="project.id">
                  {{ project.nombre }}
                </option>
              </select>
            </div>
            <!-- Filtro de Estado -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <select v-model="selectedStatus" @change="fetchTasks" class="w-full p-2.5 border border-gray-300 rounded-md text-sm">
                <option value="">Todos los estados</option>
                <option v-for="status in availableStatuses" :key="status" :value="status">
                  {{ status }}
                </option>
              </select>
            </div>
            <!-- Filtro de Tipo de Tarea -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Tarea</label>
              <select v-model="selectedTaskType" @change="fetchTasks" class="w-full p-2.5 border border-gray-300 rounded-md text-sm">
                <option value="mis">Mis tareas</option>
                <option value="otros">Tareas de otros</option>
              </select>
            </div>
            <!-- Filtro de Fecha de Inicio -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
              <input v-model="startDate" @change="fetchTasks" type="date" class="w-full p-2.5 border border-gray-300 rounded-md text-sm" />
            </div>
            <!-- Filtro de Fecha Fin -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
              <input v-model="endDate" @change="fetchTasks" type="date" class="w-full p-2.5 border border-gray-300 rounded-md text-sm" />
            </div>
          </div>
        </div>
      </details>
    </div>

    <!-- Lista de Tareas -->
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 mx-auto w-full max-w-5xl flex flex-col min-h-[600px]">
      <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">Lista de Tareas</h2>

      <div v-if="loading" class="text-center text-lg text-gray-500 flex-grow flex items-center justify-center">
        Cargando tareas...
      </div>
      <div v-if="error" class="text-red-500 text-center text-lg flex-grow flex items-center justify-center">
        {{ error }}
      </div>
      <div v-if="filteredTasks.length === 0" class="text-center text-gray-500 flex-grow flex items-center justify-center">
        No hay tareas disponibles.
      </div>

      <!-- Grid de Tareas -->
      <div class="grid gap-5 flex-grow" :class="(isMobile || isTablet) ? 'grid-cols-1 grid-rows-3' : 'grid-cols-2 grid-rows-3'">
        <div
          v-for="task in paginatedTasks"
          :key="task.tareas_id"
          :class="[
            'relative rounded-lg transition duration-300 cursor-pointer overflow-hidden',
            selectedTaskType === 'otros' ? 'bg-white border-l-4 border-blue-500' : 'bg-white border border-gray-200'
          ]"
          @click="openTaskModal(task)"
        >
          <!-- Encabezado: Nombre de la tarea y estado -->
          <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex justify-between items-center">
              <h3 class="font-bold text-lg text-gray-800 truncate">
                {{ task.nombre_tarea }}
              </h3>
              <span
                class="inline-block px-3 py-1 text-xs font-semibold rounded-full"
                :class="{
                  'bg-yellow-100 text-yellow-800': task.estado === 'en progreso',
                  'bg-green-100 text-green-800': task.estado === 'finalizado',
                  'bg-blue-100 text-blue-800': task.estado !== 'en progreso' && task.estado !== 'finalizado'
                }"
              >
                {{ task.estado }}
              </span>
            </div>
          </div>
          <!-- Cuerpo: Información -->
          <div class="px-6 py-4">
            <p class="text-sm text-gray-600 mb-2">
              <strong>Proyecto:</strong> {{ task.nombre_proyecto }}
            </p>
            <!-- Para "mis tareas": se muestran las fechas -->
            <div v-if="selectedTaskType !== 'otros'" class="flex justify-between text-xs text-gray-500">
              <p><strong>Inicio:</strong> {{ task.fecha_inicio }}</p>
              <p><strong>Fin:</strong> {{ task.fecha_fin }}</p>
            </div>
            <!-- Para "Tareas de otros": se muestra el nombre del empleado asignado -->
            <div v-else class="mt-2">
              <p class="text-sm text-gray-600">
                <strong>Asignado a:</strong> {{ task.nombre_empleado }}
              </p>
            </div>
          </div>
        </div>

        <!-- Espacios vacíos para completar el grid -->
        <div v-for="n in emptySlots" :key="'empty-'+n" class="p-4 opacity-0"></div>
      </div>

      <!-- Paginación -->
      <div class="flex justify-center mt-4" v-if="totalPages > 1">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 mx-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
          Anterior
        </button>
        <span class="px-4 py-2">Página {{ currentPage }} de {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 mx-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
          Siguiente
        </button>
      </div>
    </div>

    <!-- Modal de Detalle de Tarea -->
    <TaskDetailModal
      v-if="selectedTask && selectedTaskType !== 'otros'"
      :task="selectedTask"
      @close="selectedTask = null"
      @update="handleTaskUpdate"
    />

    <!-- Modal para Tareas de otros -->
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
import TaskDetailModal from '@/components/TaskDetailModal.vue';
import TaskDetailModalOthers from '@/components/TaskDetailModalOthers.vue';

const router = useRouter();
const route = useRoute();
const employeeId = ref(router.currentRoute.value.params.id);

const tasks = ref([]);
const loading = ref(true);
const error = ref('');

// Filtros
const selectedProject = ref('');
const selectedStatus = ref('');
const selectedTaskType = ref('mis'); // Por defecto "mis tareas"
const startDate = ref('');
const endDate = ref('');

// Tarea seleccionada para modal
const selectedTask = ref(null);

// Paginación y funciones relacionadas...
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

// Sincronizar filtros con la URL...
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

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

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

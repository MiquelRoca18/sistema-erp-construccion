<template>
  <div class="flex flex-col justify-center min-h-screen py-10 px-4 md:px-6">
    <!-- Contenedor de filtros -->
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 mx-auto mb-6 w-full max-w-5xl">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Filtrar Tareas</h2>
      <div class="flex flex-wrap gap-4 justify-between">
        <select v-model="selectedProject" class="p-2 border border-gray-300 rounded-md w-full sm:w-auto">
          <option value="">Todos los proyectos</option>
          <option v-for="project in projects" :key="project.id" :value="project.id">
            {{ project.nombre }}
          </option>
        </select>
        <input v-model="startDate" type="date" class="p-2 border border-gray-300 rounded-md w-full sm:w-auto" />
        <input v-model="endDate" type="date" class="p-2 border border-gray-300 rounded-md w-full sm:w-auto" />
        <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full sm:w-auto">
          Filtrar
        </button>
      </div>
    </div>

    <!-- Contenedor de la lista de tareas -->
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-6 mx-auto w-full max-w-5xl">
      <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Lista de Tareas</h2>
      
      <div v-if="loading" class="text-center text-lg text-gray-500">Cargando tareas...</div>
      <div v-if="error" class="text-red-500 text-center text-lg">{{ error }}</div>
      <div v-if="filteredTasks.length === 0" class="text-center text-gray-500">No hay tareas disponibles.</div>

      <!-- Diseño adaptable -->
      <div 
        v-else 
        class="grid gap-4 min-h-[500px]"
        :class="{
          'grid-cols-1': isMobile,
          'lg:grid-cols-2': isTabletOrDesktop
        }"
      >
        <div
          v-for="task in paginatedTasks"
          :key="task.tareas_id"
          class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg shadow-sm transition duration-200 ease-in-out cursor-pointer border border-gray-300"
          @click="viewTaskDetails(task.tareas_id)"
        >
          <h3 class="text-md font-semibold text-gray-800 truncate">{{ task.nombre_tarea }}</h3>
          <p class="text-sm text-gray-500 truncate">Proyecto: {{ task.nombre_proyecto }}</p>
          <p class="text-sm text-gray-500">Estado: {{ task.estado }}</p>
        </div>
      </div>

      <!-- Paginación -->
      <div class="flex justify-center mt-6" v-if="totalPages > 1">
        <button @click="prevPage" :disabled="currentPage === 1" class="px-4 py-2 mx-1 bg-gray-400 text-white rounded-md hover:bg-gray-500 disabled:opacity-50">
          Anterior
        </button>
        <span class="px-4 py-2">Página {{ currentPage }} de {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="px-4 py-2 mx-1 bg-gray-400 text-white rounded-md hover:bg-gray-500 disabled:opacity-50">
          Siguiente
        </button>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { getAllTasks } from '@/service/taskService';

const tasks = ref([]);
const projects = ref([]);
const loading = ref(true);
const error = ref('');
const router = useRouter();
const employeeId = router.currentRoute.value.params.id;

// Filtros
const selectedProject = ref('');
const startDate = ref('');
const endDate = ref('');

// Paginación
const currentPage = ref(1);
const itemsPerPage = ref(10); // Máximo 2 columnas x 5 filas

// Obtener todas las tareas
const fetchAllTasks = async () => {
  try {
    const response = await getAllTasks(employeeId); 
    tasks.value = response;
  } catch (err) {
    error.value = err.message || 'Error al obtener las tareas.';
  } finally {
    loading.value = false;
  }
};

// Filtrar tareas
const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    return (
      (!selectedProject.value || task.proyecto_id === selectedProject.value) &&
      (!startDate.value || new Date(task.fecha_inicio) >= new Date(startDate.value)) &&
      (!endDate.value || new Date(task.fecha_fin) <= new Date(endDate.value))
    );
  });
});

// Paginación
const totalPages = computed(() => Math.ceil(filteredTasks.value.length / itemsPerPage.value));
const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return filteredTasks.value.slice(start, start + itemsPerPage.value);
});

// Detectar tamaño de pantalla para diseño responsive
const isMobile = computed(() => window.innerWidth < 640);
const isTabletOrDesktop = computed(() => window.innerWidth >= 640);

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

const applyFilters = () => {
  currentPage.value = 1; // Reiniciar paginación al aplicar filtros
};

const viewTaskDetails = (taskId) => {
  router.push(`/task-details/${taskId}`);
};

onMounted(() => {
  fetchAllTasks();
  window.addEventListener("resize", () => {
    isMobile.value = window.innerWidth < 640;
    isTabletOrDesktop.value = window.innerWidth >= 640;
  });
});
</script>

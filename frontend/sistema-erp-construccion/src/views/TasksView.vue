<template>
  <div class="flex flex-col justify-center px-4 md:px-6 bg-gray-100">
    <!-- Contenedor de filtros (Ajustado para móviles) -->
    <div class="bg-white border border-gray-300 rounded-lg shadow-lg p-4 md:p-6 mx-auto mb-3 md:mb-4 w-full max-w-5xl">
      <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-3 md:mb-4 text-center md:text-left">Filtrar Tareas</h2>
      <div class="flex flex-wrap gap-3 md:gap-4 justify-between">
        <select v-model="selectedProject" class="p-2.5 border border-gray-300 rounded-md w-full sm:w-auto text-sm">
          <option value="">Todos los proyectos</option>
          <option v-for="project in projects" :key="project.id" :value="project.id">
            {{ project.nombre }}
          </option>
        </select>
        <input v-model="startDate" type="date" class="p-2.5 border border-gray-300 rounded-md w-full sm:w-auto text-sm" />
        <input v-model="endDate" type="date" class="p-2.5 border border-gray-300 rounded-md w-full sm:w-auto text-sm" />
        <button @click="applyFilters" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full sm:w-auto text-sm md:text-base">
          Filtrar
        </button>
      </div>
    </div>

    <!-- Contenedor de la lista de tareas -->
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

      <!-- Diseño adaptable con espacios rellenados -->
      <div 
        v-else 
        class="grid gap-5 flex-grow"
        :class="{
          'grid-cols-1 grid-rows-3': isMobile,  // 1 columna x 3 filas
          'grid-cols-1 md:grid-rows-4': isTablet, // 1 columna x 4 filas
          'grid-cols-2 md:grid-rows-3': isLaptop, // ✅ 2 columnas x 3 filas
          'grid-cols-2 md:grid-rows-3': isDesktop // 3 columnas x 4 filas
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

        <!-- Espacios vacíos rellenados para mantener el tamaño -->
        <div v-for="n in emptySlots" :key="'empty-'+n" class="p-4 opacity-0"></div>
      </div>

      <!-- Paginación siempre en el fondo -->
      <div class="flex justify-center mt-4" v-if="totalPages > 1">
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

// Detectar tamaño de pantalla para diseño responsive
const screenWidth = ref(window.innerWidth);
const columns = computed(() => (isMobile.value ? 1 : isTablet.value ? 1 : 2)); // Columnas dinámicas
const rows = computed(() => (isMobile.value ? 3 : isTablet.value ? 4 : 3)); // Filas dinámicas

const isMobile = computed(() => screenWidth.value < 640); // 1 columna x 3 filas
const isTablet = computed(() => screenWidth.value >= 640 && screenWidth.value < 1024); // 1 columna x 4 filas
const isLaptop = computed(() => screenWidth.value >= 1024 && screenWidth.value < 1440); // ✅ 2 columnas x 3 filas
const isDesktop = computed(() => screenWidth.value >= 1440); // 3 columnas x 4 filas

// Paginación adaptada al número de tareas visibles
const currentPage = ref(1);
const itemsPerPage = computed(() => columns.value * rows.value); // Total de celdas necesarias

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

// Cálculo preciso de espacios vacíos
const emptySlots = computed(() => {
  const totalSlots = columns.value * rows.value;
  return totalSlots - paginatedTasks.value.length;
});

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };

const applyFilters = () => {
  currentPage.value = 1;
};

const viewTaskDetails = (taskId) => {
  router.push(`/task-details/${taskId}`);
};

onMounted(() => {
  fetchAllTasks();
  window.addEventListener("resize", () => {
    screenWidth.value = window.innerWidth;
  });
});
</script>


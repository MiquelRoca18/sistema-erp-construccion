<template>
    <div class="flex flex-col justify-center min-h-screen bg-[#d6d8db] py-10 px-6">
      <!-- Gráfica -->
      <div class="bg-gray-200 rounded-lg shadow-lg p-6 mx-auto mb-6 w-full max-w-4xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Estado de las Tareas</h2>
        <BarChart v-if="chartData" :chart-data="chartData" />
      </div>
  
      <!-- Lista de tareas pendientes -->
      <div class="bg-gray-200 border border-gray-300 rounded-lg shadow-lg max-h-105 p-6 mx-auto w-full max-w-4xl">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tareas Pendientes</h2>
        <!-- Estado de carga o error -->
        <div v-if="loading" class="text-center text-lg text-gray-500">Cargando tareas...</div>
        <div v-if="error" class="text-red-500 text-center text-lg">{{ error }}</div>
        <!-- Si no hay tareas -->
        <div v-if="pendingTasksToShow.length === 0" class="text-center text-gray-500">No hay tareas pendientes.</div>
        <!-- Lista de tareas -->
        <div v-else>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[calc(50vh-12rem)] overflow-y-auto">
            <div
              v-for="task in pendingTasksToShow"
              :key="task.tareas_id"
              class="flex flex-col bg-gray-50 hover:bg-gray-100 p-4 rounded-lg shadow-sm mb-4 transition duration-200 ease-in-out"
            >
              <div>
                <h3 class="text-md font-semibold text-gray-800 truncate">{{ task.nombre_tarea }}</h3>
                <p class="text-sm text-gray-500 truncate">Proyecto: {{ task.nombre_proyecto }}</p>
              </div>
              <div class="mt-2 text-sm text-blue-600 cursor-pointer hover:text-blue-800 transition duration-200" @click="viewTaskDetails(task.tareas_id)">
                Ver detalles
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue';
  import { useRouter } from 'vue-router';
  import { getPendingTasks, getAllTasks } from '@/service/taskService';
  import BarChart from '@/components/BarChart.vue'; // Componente de gráfica
  
  const pendingTasks = ref([]);
  const allTasks = ref([]);
  const loading = ref(true);
  const error = ref('');
  const router = useRouter();
  const employeeId = router.currentRoute.value.params.id;
  
  // Función para obtener todas las tareas del empleado
  const fetchAllTasks = async () => {
    try {
      const response = await getAllTasks(employeeId);
  
      // Verifica si la respuesta es un array
      if (Array.isArray(response)) {
        allTasks.value = response;
      } else if (response && Array.isArray(response.data)) {
        allTasks.value = response.data;
      } else {
        allTasks.value = [];
        console.error('La respuesta de la API no es un array:', response);
      }
    } catch (err) {
      console.error('Error al obtener todas las tareas:', err.message);
      allTasks.value = [];
    }
  };
  
  // Función para obtener las tareas pendientes
  const fetchPendingTasks = async () => {
    try {
      const response = await getPendingTasks(employeeId);
      pendingTasks.value = response;
    } catch (err) {
      error.value = err.message || 'Error al obtener las tareas.';
    } finally {
      loading.value = false;
    }
  };
  
  // Llamamos a ambas funciones cuando se monta el componente
  onMounted(() => {
    fetchPendingTasks();
    fetchAllTasks();
  });
  
  // Computed para mostrar las tareas pendientes
  const pendingTasksToShow = computed(() => {
    return pendingTasks.value;
  });
  
  const totalTasks = computed(() => allTasks.value.length);

    const chartData = computed(() => {
    if (!Array.isArray(allTasks.value) || allTasks.value.length === 0) return null;

    const pending = allTasks.value.filter(task => task.estado === 'pendiente').length;
    const inProgress = allTasks.value.filter(task => task.estado === 'en progreso').length;
    const completed = allTasks.value.filter(task => task.estado === 'finalizado').length;

    return {
        labels: ['Pendiente', 'En Progreso', 'Finalizado'],
        datasets: [
        {
            label: 'Estado de Tareas',
            data: [pending, inProgress, completed],
            backgroundColor: ['#FF6384', '#36A2EB', '#4BC0C0'],
        },
        ],
        total: totalTasks.value,
    };
    });

  
  // Función para ver detalles de una tarea
  const viewTaskDetails = (taskId) => {
    router.push(`/task-details/${taskId}`);
  };
  </script>
<template>
  <div class="max-w-3xl mx-auto p-6 rounded-lg">
    <h2 class="text-xl font-semibold mb-4 text-gray-800">Tareas Pendientes</h2>

    <div v-if="loading" class="text-center text-gray-500">Cargando tareas...</div>
    <div v-if="error" class="text-red-500 text-center">{{ error }}</div>
    <div v-if="tasksToShow.length === 0" class="text-center text-gray-600">No hay tareas pendientes.</div>

    <div v-else class="relative overflow-hidden max-h-[250px]">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="task in tasksToShow"
          :key="task.tareas_id"
          class="p-4 rounded-md shadow-sm border-l-4 border-blue-500 transition-all duration-200"
        >
          <h3 class="font-semibold text-md text-gray-900">{{ task.nombre_tarea }}</h3>
          <p class="text-xs text-gray-600">Proyecto: {{ task.nombre_proyecto }}</p>
        </div>
      </div>
      <!-- Efecto de desvanecimiento con fondo blanco para mejor integración -->
      <div class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-b from-transparent to-white md:h-24 pointer-events-none"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { getPendingTasks } from '@/service/taskService';

const tasks = ref([]);
const loading = ref(true);
const error = ref('');
const router = useRouter();

const fetchTasks = async () => {
  try {
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user) {
      router.push('/');
      return;
    }
    const response = await getPendingTasks(user);
    tasks.value = response;
  } catch (err) {
    error.value = 'Error al obtener las tareas pendientes.';
  } finally {
    loading.value = false;
  }
};

const tasksToShow = computed(() => {
  return tasks.value.slice(0, 4);
});

onMounted(() => {
  fetchTasks();
});
</script>

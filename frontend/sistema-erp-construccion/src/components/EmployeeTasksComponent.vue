<template>
  <div class="max-w-3xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">Tareas Pendientes</h2>

    <div v-if="loading" class="text-center">Cargando tareas...</div>
    <div v-if="error" class="text-red-500 text-center">{{ error }}</div>
    <div v-if="tasksToShow.length === 0" class="text-center">No hay tareas pendientes.</div>

    <div v-else class="relative overflow-hidden max-h-[250px]">
      <div class="grid grid-cols-2 gap-4 bg-gray-200 pb-6">
        <div
          v-for="task in tasksToShow"
          :key="task.tareas_id"
          class="p-3 bg-white rounded-md shadow-sm border-l-4 border-blue-500"
        >
          <h3 class="font-semibold text-md text-gray-800">{{ task.nombre_tarea }}</h3>
          <p class="text-xs text-gray-500">Proyecto: {{ task.nombre_proyecto }}</p>
        </div>
      </div>
      <!-- Efecto de desvanecimiento más visible -->
      <div class="absolute bottom-0 left-0 w-full h-24 bg-gradient-to-b from-gray-200/50 to-gray-200 pointer-events-none"></div>
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

// Función para obtener las tareas pendientes
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

// Computed property para limitar las tareas visibles (más de 4 pero con difuminado)
const tasksToShow = computed(() => {
  return tasks.value.slice(0, 4); // Aumentamos la cantidad de tareas visibles a 6 o más
});

// Llamamos a la función cuando el componente se monta
onMounted(() => {
  fetchTasks();
});
</script>

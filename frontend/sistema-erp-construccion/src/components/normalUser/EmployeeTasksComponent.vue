<template>
  <div class="max-w-3xl mx-auto p-6 rounded-lg bg-white">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Tareas Pendientes</h2>

    <div v-if="loading" class="text-center text-gray-500">
      Cargando tareas...
    </div>
    <div v-if="error" class="text-center text-red-500">
      {{ error }}
    </div>
    <div v-if="tasksToShow.length === 0" class="text-center text-gray-600">
      No hay tareas pendientes.
    </div>

    <div v-else class="relative overflow-hidden" style="max-height: 250px;">
      <!-- Se agrega pb-16 para reservar espacio para el degradado -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 pb-16">
        <div
          v-for="task in tasksToShow"
          :key="task.tareas_id"
          class="p-6 rounded-lg shadow-md border border-gray-200 transition-all duration-200 hover:scale-105"
        >
          <h3 class="font-bold text-lg text-gray-900 mb-2">
            {{ task.nombre_tarea }}
          </h3>
          <p class="text-sm text-gray-600">
            Proyecto: {{ task.nombre_proyecto }}
          </p>
        </div>
      </div>
      <!-- El degradado se coloca en el área reservada, sin sobreponerse al contenido -->
      <div class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-b from-transparent to-white pointer-events-none transform"></div>

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
    const user = JSON.parse(localStorage.getItem("user"));
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

const tasksToShow = computed(() => tasks.value.slice(0, 4));

onMounted(() => {
  fetchTasks();
});
</script>

<style scoped>
/* Puedes ajustar estos estilos si fuera necesario */
</style>

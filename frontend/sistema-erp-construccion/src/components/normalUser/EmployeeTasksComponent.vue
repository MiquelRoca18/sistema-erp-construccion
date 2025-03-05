<template>
  <div class="max-w-3xl mx-auto p-6 rounded-lg bg-white dark:bg-gray-800">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-200">Tareas Pendientes</h2>

    <div v-if="!props.employeeId" class="text-center text-gray-500 dark:text-gray-400">
      No se ha seleccionado un empleado
    </div>
    
    <div v-else-if="loading" class="text-center text-gray-500 dark:text-gray-400">
      Cargando tareas...
    </div>
    
    <div v-else-if="error" class="text-center text-red-500 dark:text-red-400">
      {{ error }}
    </div>
    
    <div v-else-if="tasksToShow.length === 0" class="text-center text-gray-600 dark:text-gray-400">
      No hay tareas pendientes.
    </div>

    <div v-else class="relative overflow-hidden" style="max-height: 250px;">
      <!-- Resto del código de renderizado de tareas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 pb-16">
        <div
          v-for="task in tasksToShow"
          :key="task.tareas_id"
          class="p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200 hover:scale-105 bg-white dark:bg-gray-700"
        >
          <h3 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-2">
            {{ task.nombre_tarea }}
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Proyecto: {{ task.nombre_proyecto }}
          </p>
        </div>
      </div>
      <!-- El degradado se coloca en el área reservada, sin sobreponerse al contenido -->
      <div class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-b from-transparent to-white dark:to-gray-800 pointer-events-none transform"></div>
    </div>
  </div>
</template>
<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRouter } from 'vue-router';
import { getPendingTasks } from '@/service/taskService';

const props = defineProps({
  employeeId: {
    type: Number,
    default: null
  }
});

const tasks = ref([]);
const loading = ref(true);
const error = ref('');
const router = useRouter();

const fetchTasks = async () => {
  // Si no hay employeeId, no intentes cargar tareas
  if (!props.employeeId) {
    loading.value = false;
    return;
  }

  try {
    loading.value = true;
    const response = await getPendingTasks(props.employeeId);
    tasks.value = response;
  } catch (err) {
    error.value = 'Error al obtener las tareas pendientes.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const tasksToShow = computed(() => tasks.value.slice(0, 4));

// Observa cambios en employeeId
watch(() => props.employeeId, (newId) => {
  if (newId) {
    fetchTasks();
  }
});

onMounted(() => {
  // Solo fetchTasks si hay un employeeId
  if (props.employeeId) {
    fetchTasks();
  }
});
</script>

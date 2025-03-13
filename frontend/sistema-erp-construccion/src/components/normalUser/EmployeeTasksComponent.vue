<template>
  <div class="max-w-3xl mx-auto p-3 sm:p-6 rounded-lg bg-white dark:bg-gray-800">
    <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-800 dark:text-gray-200">Tareas Pendientes</h2>

    <div v-if="!props.employeeId" class="text-center text-gray-500 dark:text-gray-400 text-sm sm:text-base">
      No se ha seleccionado un empleado
    </div>
    
    <div v-else-if="loading" class="text-center text-gray-500 dark:text-gray-400 text-sm sm:text-base">
      <div class="flex justify-center mb-2">
        <div class="w-8 h-8 border-t-2 border-b-2 border-blue-500 rounded-full animate-spin"></div>
      </div>
      Cargando tareas...
    </div>
    
    <div v-else-if="error" class="text-center text-red-500 dark:text-red-400 text-sm sm:text-base">
      {{ error }}
    </div>
    
    <div v-else-if="tasksToShow.length === 0" class="text-center text-gray-600 dark:text-gray-400 text-sm sm:text-base">
      No hay tareas pendientes.
    </div>

    <div v-else class="relative overflow-hidden" style="max-height: 200px; sm:max-height: 250px;">
      <!-- Contenido de tareas -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-2 sm:gap-4 p-2 pb-16">
        <div
          v-for="task in tasksToShow"
          :key="task.tareas_id"
          class="p-3 sm:p-6 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200 hover:scale-102 bg-white dark:bg-gray-700"
        >
          <h3 class="font-bold text-sm sm:text-lg text-gray-900 dark:text-gray-100 mb-1 sm:mb-2 line-clamp-2">
            {{ task.nombre_tarea }}
          </h3>
          <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
            Proyecto: {{ task.nombre_proyecto }}
          </p>
        </div>
      </div>
      <!-- Degradado -->
      <div class="absolute bottom-0 left-0 w-full h-12 sm:h-16 bg-gradient-to-b from-transparent to-white dark:to-gray-800 pointer-events-none transform"></div>
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
  // Si no hay employeeId, no intentar cargar tareas
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

<style scoped>
/* Para asegurar que el texto se corte adecuadamente */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Animación para el hover de las tarjetas */
@media (min-width: 640px) {
  .hover\:scale-102:hover {
    transform: scale(1.02);
  }
}

/* Animación para el spinner de carga */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
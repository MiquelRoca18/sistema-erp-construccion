<template>
  <div class="fixed inset-0 flex items-center justify-center z-50 px-3 py-4 sm:px-4">
    <!-- Fondo semi-transparente -->
    <div class="fixed inset-0 bg-black opacity-50 dark:opacity-70" @click="close"></div>

    <!-- Contenedor del modal -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl z-50 p-3 sm:p-4 md:p-6 w-full max-w-md sm:max-w-lg md:max-w-2xl mx-auto transform transition-all duration-300">
      <!-- Loader para carga inicial -->
      <div v-if="isLoading" class="py-8 sm:py-10 flex flex-col items-center justify-center">
        <div class="w-12 h-12 sm:w-16 sm:h-16 mb-3 sm:mb-4">
          <svg class="animate-spin h-full w-full text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400">Cargando detalles...</p>
      </div>

      <!-- Contenido del modal -->
      <div v-else>
        <!-- Header del modal -->
        <div class="mb-3 sm:mb-4 border-b dark:border-gray-700 pb-3 sm:pb-4 flex justify-between items-center">
          <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Detalles de la Tarea</h2>
          <button @click="close" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Mensaje de error si existe -->
        <div v-if="errorMessage" class="mb-3 sm:mb-4 p-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded text-sm">
          {{ errorMessage }}
        </div>
    
        <!-- Contenido principal -->
        <div class="space-y-4 sm:space-y-6">
          <!-- Tarea y Estado -->
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex-1 mb-3 md:mb-0">
              <h3 class="text-xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ task.nombre_tarea }}</h3>
            </div>
            <div class="w-full md:w-1/3">
              <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Estado</label>
              <select
                v-model="updatedStatus"
                class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                      bg-white dark:bg-gray-700 text-sm sm:text-base text-gray-900 dark:text-gray-100"
                :disabled="isSaving"
              >
                <option value="pendiente" class="bg-white dark:bg-gray-700">Pendiente</option>
                <option value="en progreso" class="bg-white dark:bg-gray-700">En progreso</option>
                <option value="finalizado" class="bg-white dark:bg-gray-700">Finalizado</option>
              </select>
            </div>
          </div>
    
          <!-- Descripción -->
          <div v-if="task.descripcion">
            <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Descripción</label>
            <div class="mt-1 p-2 border border-gray-200 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 text-sm sm:text-base max-h-24 sm:max-h-32 overflow-y-auto">
              {{ task.descripcion }}
            </div>
          </div>
    
          <!-- Proyecto -->
          <div>
            <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">
              Proyecto: <span class="font-normal text-gray-600 dark:text-gray-400">{{ task.nombre_proyecto }}</span>
            </p>
          </div>
    
          <!-- Fechas -->
          <div class="flex flex-col sm:flex-row sm:justify-between">
            <div>
              <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">
                Inicio: <span class="font-normal text-gray-600 dark:text-gray-400">{{ task.fecha_inicio }}</span>
              </p>
            </div>
            <div>
              <p class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200">
                Fin: <span class="font-normal text-gray-600 dark:text-gray-400">{{ task.fecha_fin }}</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Footer con acciones -->
        <div class="mt-4 sm:mt-6 flex justify-end">
          <button 
            @click="close" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 mr-2 text-xs sm:text-sm"
            :disabled="isSaving"
          >
            Cancelar
          </button>
          <button 
            @click="updateTask" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 flex items-center justify-center min-w-[80px] sm:min-w-[120px] text-xs sm:text-sm"
            :disabled="isSaving"
          >
            <template v-if="isSaving">
              <svg class="animate-spin -ml-1 mr-1 sm:mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Guardando...</span>
            </template>
            <template v-else>
              Guardar Cambios
            </template>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { updateTask as updateTaskService } from '@/service/taskService';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['close', 'update']);

const updatedStatus = ref(props.task.estado);
const isLoading = ref(true);
const isSaving = ref(false);
const errorMessage = ref('');

watch(() => props.task, (newTask) => {
  updatedStatus.value = newTask.estado;
});

// Simular carga inicial
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 300);
});

const close = () => {
  if (isSaving.value) return;
  emit('close');
};

const updateTask = async () => {
  try {
    isSaving.value = true;
    errorMessage.value = '';
    
    const data = { estado: updatedStatus.value };
    await updateTaskService(props.task.tareas_id, data);
    
    emit('update', { ...props.task, estado: updatedStatus.value });
    close();
  } catch (error) {
    errorMessage.value = error.message || 'Error al actualizar la tarea. Intente nuevamente.';
  } finally {
    isSaving.value = false;
  }
};
</script>

<style scoped>
/* Animación para el spinner */
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

/* Ajuste para dispositivos móviles pequeños */
@media (max-width: 360px) {
  .text-xl {
    font-size: 1rem;
  }
  .text-xs {
    font-size: 0.65rem;
  }
}
</style>
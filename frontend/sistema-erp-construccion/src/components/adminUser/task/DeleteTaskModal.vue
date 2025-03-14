<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-3 sm:p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-lg sm:text-2xl font-bold">Eliminar Tarea</h2>
          <button @click="closeModal" class="text-white text-2xl sm:text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300" :disabled="loading">&times;</button>
        </div>
      </div>
      <!-- Contenido -->
      <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Loader -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-3 sm:py-4">
          <div class="relative mb-2 sm:mb-3">
            <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full border-t-4 border-b-4 border-red-500 animate-spin"></div>
            <div class="absolute top-0 left-0 h-10 w-10 sm:h-12 sm:w-12 rounded-full border-t-4 border-b-4 border-red-200 animate-spin" style="animation-duration: 1.5s;"></div>
          </div>
          <p class="text-gray-600 dark:text-gray-300 text-sm">Eliminando tarea...</p>
        </div>
        
        <!-- Mensaje de confirmación -->
        <p v-else class="text-gray-700 dark:text-gray-200 flex items-start sm:items-center text-sm sm:text-base">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 sm:mt-0 mr-2 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          <span>¿Estás seguro de que deseas eliminar la tarea
          <span class="font-bold mx-1">{{ task.nombre_tarea }}</span>?</span>
        </p>
        
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-2 sm:p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-300 text-xs sm:text-sm">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-xs sm:text-sm flex-1">{{ errorMessage }}</span>
            <button @click="errorMessage = ''" class="ml-1 flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-red-500 hover:text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-2 pt-2">
          <button
            type="button"
            @click="closeModal"
            class="px-2 sm:px-4 py-1.5 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-xs sm:text-sm"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="confirmDeletion"
            class="px-2 sm:px-4 py-1.5 sm:py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-xs sm:text-sm dark:bg-red-500 dark:hover:bg-red-600 min-w-[70px] sm:min-w-[90px] flex items-center justify-center"
            :disabled="loading"
          >
            <span v-if="loading">
              <svg class="animate-spin -ml-1 mr-1 h-3 w-3 sm:h-4 sm:w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-xs sm:text-sm">Eliminando...</span>
            </span>
            <span v-else class="text-xs sm:text-sm">Eliminar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { deleteTask } from '@/service/taskService';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'deleted', 'showSuccess', 'showError']);
const errorMessage = ref('');
const loading = ref(false);

const closeModal = () => {
  if (loading.value) return;
  emit('close');
};

const confirmDeletion = async () => {
  if (loading.value) return;
  
  errorMessage.value = '';
  try {
    loading.value = true;
    
    // Simulamos un tiempo mínimo de carga para mejorar UX
    const startTime = Date.now();
    
    await deleteTask(props.task.tareas_id);
    
    // Aseguramos que el loader se muestre al menos por 500ms
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    emit('deleted');
    emit('showSuccess', `La tarea ${props.task.nombre_tarea} ha sido eliminada exitosamente`);
    emit('close'); 
  } catch (error: any) {
    // Error handling
    errorMessage.value = error.message || 'Error al eliminar la tarea';
    loading.value = false;
  }
};
</script>
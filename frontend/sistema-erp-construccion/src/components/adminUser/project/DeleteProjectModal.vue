<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 dark:from-green-700 dark:to-green-600 p-3 sm:p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-xl sm:text-2xl font-bold">Confirmar Borrado</h2>
          <button 
            @click="closeModal" 
            class="text-white text-2xl sm:text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300"
            :disabled="loading"
          >&times;</button>
        </div>
      </div>
      <!-- Contenido -->
      <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <div class="flex items-start sm:items-center mb-3 sm:mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-10 sm:w-10 text-red-500 mr-2 sm:mr-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          <p class="text-sm sm:text-base text-gray-700 dark:text-gray-200">
            ¿Estás seguro de que deseas eliminar el proyecto 
            <span class="font-semibold">{{ project.nombre_proyecto }}</span>?
          </p>
        </div>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-lg p-2 sm:p-3 text-yellow-700 dark:text-yellow-300 text-xs sm:text-sm">
          <p>Esta acción no se puede deshacer y eliminará permanentemente el proyecto del sistema.</p>
        </div>
        
        <!-- Mostrar error, si existe -->
        <div v-if="errorMessage" class="p-2 sm:p-3 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 flex items-center text-xs sm:text-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1.5 sm:mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>
        
        <!-- Botones -->
        <div class="flex justify-end space-x-3 sm:space-x-4">
          <button 
            @click="closeModal" 
            class="px-3 sm:px-4 py-1.5 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-xs sm:text-sm
                   disabled:opacity-70 disabled:cursor-not-allowed"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button 
            @click="confirmDelete" 
            class="px-3 sm:px-4 py-1.5 sm:py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-xs sm:text-sm dark:bg-red-500 dark:hover:bg-red-600
                   disabled:opacity-70 disabled:cursor-not-allowed
                   flex items-center justify-center min-w-[70px] sm:min-w-[100px]"
            :disabled="loading"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-1 sm:mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Eliminando...</span>
            <span v-else>Eliminar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref } from 'vue';
import { deleteProject } from '@/service/projectService';

const props = defineProps({
  project: { type: Object, required: true }
});
const emit = defineEmits(['close', 'deleted', 'showSuccess', 'showError']);

const loading = ref(false);
const errorMessage = ref('');

const closeModal = () => {
  emit('close');
};

const confirmDelete = async () => {
  if (loading.value) return;
  
  loading.value = true;
  errorMessage.value = '';
  
  try {
    const startTime = Date.now();
    
    await deleteProject(props.project.proyectos_id);
    
    // Garantizar tiempo mínimo de carga para feedback visual
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    emit('deleted', true); 
    emit('showSuccess', `El proyecto ${props.project.nombre_proyecto} ha sido eliminado exitosamente`);
    closeModal();
  } catch (error: any) {
    errorMessage.value = error.message || 'Error al eliminar proyecto. Inténtalo de nuevo.';
    emit('showError', error.message || 'Error al eliminar proyecto. Inténtalo de nuevo.');
    emit('deleted', false); 
  } finally {
    loading.value = false;
  }
};
</script>
<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-red-500 to-red-400 dark:from-red-700 dark:to-red-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Confirmar Borrado</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Contenido -->
      <div class="p-6 space-y-6">
        <p class="text-gray-700 dark:text-gray-200">
          ¿Estás seguro de que deseas eliminar al empleado
          <span class="font-semibold">{{ employee.nombre }}</span>?
        </p>
        <!-- Mostrar error, si existe -->
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
          <button
            @click="closeModal"
            :disabled="loading"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm disabled:opacity-70 disabled:cursor-not-allowed"
          >
            Cancelar
          </button>
          <button
            @click="confirmDelete"
            :disabled="loading"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm dark:bg-red-500 dark:hover:bg-red-600 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Eliminando...' : 'Eliminar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { deleteEmployee } from '@/service/employeeService';

const props = defineProps({
  employee: { type: Object, required: true },
});
const emit = defineEmits(['close', 'deleted', 'showSuccess', 'showError']);

const loading = ref(false);
const errorMessage = ref('');

const closeModal = () => {
  if (loading.value) return;
  emit('close');
};

const confirmDelete = async () => {
  loading.value = true;
  errorMessage.value = '';
  
  // Cerramos el modal inmediatamente después de presionar "Eliminar"
  closeModal();
  
  try {
    // Agregamos un pequeño retraso mínimo para mejor UX
    const startTime = Date.now();
    
    await deleteEmployee(props.employee.empleados_id);
    
    // Aseguramos que el loader se muestre por al menos 300ms para mejor UX
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 300) {
      await new Promise(resolve => setTimeout(resolve, 300 - elapsedTime));
    }
    
    emit('deleted');
    emit('showSuccess', `El empleado ${props.employee.nombre} ha sido eliminado exitosamente`);
  } catch (error: any) {
    console.error(error.message);
    emit('showError', error.message || 'Error al eliminar empleado');
  } finally {
    loading.value = false;
  }
};
</script>
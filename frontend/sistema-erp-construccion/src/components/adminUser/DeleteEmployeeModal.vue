<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado azul -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-400 dark:from-blue-700 dark:to-blue-600 p-4 rounded-t-2xl">
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
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm dark:bg-red-500 dark:hover:bg-red-600"
            :disabled="loading"
          >
            <span v-if="loading">Eliminando...</span>
            <span v-else>Eliminar</span>
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
const emit = defineEmits(['close', 'deleted']);

const loading = ref(false);
const errorMessage = ref('');

const closeModal = () => {
  emit('close');
};

const confirmDelete = async () => {
  loading.value = true;
  errorMessage.value = '';
  try {
    await deleteEmployee(props.employee.empleados_id);
    emit('deleted');
    closeModal();
  } catch (error: any) {
    console.error(error.message);
    errorMessage.value = error.message || 'Error al eliminar empleado';
  } finally {
    loading.value = false;
  }
};
</script>
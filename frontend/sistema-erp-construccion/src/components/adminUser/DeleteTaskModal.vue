<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Eliminar Tarea</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Contenido -->
      <div class="p-6 space-y-6">
        <p class="text-gray-700 dark:text-gray-200">
          ¿Estás seguro de que deseas eliminar la tarea
          <span class="font-bold">{{ task.nombre_tarea }}</span>?
        </p>
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="confirmDeletion"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm dark:bg-red-500 dark:hover:bg-red-600"
          >
            Eliminar
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

const emit = defineEmits(['close', 'deleted']);
const errorMessage = ref('');

const closeModal = () => {
  emit('close');
};

const confirmDeletion = async () => {
  errorMessage.value = '';
  try {
    console.log(props.task.tareas_id);
    await deleteTask(props.task.tareas_id);
    emit('deleted');
  } catch (error: any) {
    console.error('Error al eliminar la tarea:', error.message);
    errorMessage.value = error.message || 'Error al eliminar la tarea';
  }
};
</script>

<style scoped>
/* Puedes agregar estilos adicionales si lo consideras necesario */
</style>
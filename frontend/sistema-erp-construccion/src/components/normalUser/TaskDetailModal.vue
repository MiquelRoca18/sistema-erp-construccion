<!-- components/TaskDetailModal.vue -->
<template>
  <div class="fixed inset-0 flex items-center justify-center z-50 px-4">
    <!-- Fondo semi-transparente (clic para cerrar) -->
    <div class="fixed inset-0 bg-black opacity-50" @click="close"></div>

    <!-- Contenedor del modal -->
    <div class="bg-white rounded-xl shadow-2xl z-50 p-6 w-full max-w-2xl mx-auto transform transition-all duration-300">
      <!-- Header del modal -->
      <div class="mb-4 border-b pb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Detalles de la Tarea</h2>
        <button @click="close" class="text-gray-500 hover:text-gray-700 focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Contenido principal -->
      <div class="space-y-6">
        <!-- 1. Tarea y Estado -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <h3 class="text-3xl font-extrabold text-gray-900">{{ task.nombre_tarea }}</h3>
          <div class="mt-4 md:mt-0 w-full md:w-1/3">
            <label class="block text-sm font-medium text-gray-600">Estado</label>
            <select v-model="updatedStatus" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
              <option value="pendiente">Pendiente</option>
              <option value="en progreso">En progreso</option>
              <option value="finalizado">Finalizado</option>
            </select>
          </div>
        </div>

        <!-- 2. Descripción -->
        <div v-if="task.descripcion">
          <label class="block text-sm font-medium text-gray-600">Descripción</label>
          <div class="mt-1 p-2 border border-gray-200 rounded-md text-gray-700 text-base max-h-32 overflow-y-auto">
            {{ task.descripcion }}
          </div>
        </div>

        <!-- 3. Proyecto -->
        <div>
          <p class="text-sm font-semibold text-gray-800">Proyecto: <span class="font-normal text-gray-600">{{ task.nombre_proyecto }}</span></p>
        </div>

        <!-- 4. Fechas -->
        <div class="flex flex-col sm:flex-row sm:justify-between">
          <div>
            <p class="text-sm font-semibold text-gray-800">Inicio: <span class="font-normal text-gray-600">{{ task.fecha_inicio }}</span></p>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">Fin: <span class="font-normal text-gray-600">{{ task.fecha_fin }}</span></p>
          </div>
        </div>
      </div>

      <!-- Footer con acciones -->
      <div class="mt-6 flex justify-end">
        <button @click="close" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 mr-2">
          Cancelar
        </button>
        <button @click="updateTask" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
          Guardar Cambios
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { updateTask as updateTaskService } from '@/service/taskService';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['close', 'update']);

const updatedStatus = ref(props.task.estado);

watch(() => props.task, (newTask) => {
  updatedStatus.value = newTask.estado;
});

const close = () => {
  emit('close');
};

const updateTask = async () => {
  try {
    const data = { estado: updatedStatus.value };
    await updateTaskService(props.task.tareas_id, data);
    emit('update', { ...props.task, estado: updatedStatus.value });
    close();
  } catch (error) {
    console.error('Error al actualizar la tarea:', error);
  }
};
</script>

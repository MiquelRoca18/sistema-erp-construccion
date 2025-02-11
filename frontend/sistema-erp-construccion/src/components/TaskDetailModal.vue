<!-- components/TaskDetailModal.vue -->
<template>
    <div class="fixed inset-0 flex items-center justify-center z-50 px-4">
      <!-- Fondo semi-transparente (clic para cerrar) -->
      <div class="fixed inset-0 bg-black opacity-50" @click="close"></div>
  
      <!-- Contenedor del modal -->
      <div
        class="bg-white rounded-xl shadow-2xl z-50 p-6 w-full max-w-2xl mx-auto transform transition-all duration-300"
      >
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4">
          <h2 class="text-2xl font-bold text-gray-800">Detalles de la Tarea</h2>
          <button @click="close" class="text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
  
        <!-- Contenido del modal -->
        <div class="mt-6 grid gap-6 md:grid-cols-2">
          <!-- Sección de información -->
          <div>
            <div class="mb-4">
              <p class="text-xs uppercase text-gray-600">Tarea</p>
              <p class="text-lg font-semibold text-gray-800">{{ task.nombre_tarea }}</p>
            </div>
            <div class="mb-4">
              <p class="text-xs uppercase text-gray-600">Proyecto</p>
              <p class="text-lg font-semibold text-gray-800">{{ task.nombre_proyecto }}</p>
            </div>
            <div class="mb-4">
              <p class="text-xs uppercase text-gray-600">Fecha de Inicio</p>
              <p class="text-lg font-semibold text-gray-800">{{ task.fecha_inicio }}</p>
            </div>
            <div class="mb-4">
              <p class="text-xs uppercase text-gray-600">Fecha de Fin</p>
              <p class="text-lg font-semibold text-gray-800">{{ task.fecha_fin }}</p>
            </div>
          </div>
  
          <!-- Sección de actualización -->
          <div>
            <label class="block text-xs uppercase text-gray-600 mb-1">Estado</label>
            <select
              v-model="updatedStatus"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
              <option value="pendiente">Pendiente</option>
              <option value="en progreso">En progreso</option>
              <option value="finalizado">Finalizado</option>
            </select>
  
            <button
              @click="updateTask"
              class="mt-6 w-full py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
            >
              Guardar Cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  
  const props = defineProps({
    task: {
      type: Object,
      required: true,
    },
  });
  const emit = defineEmits(['close', 'update']);
  
  // Variable para almacenar el estado actualizado, iniciada con el estado actual de la tarea.
  const updatedStatus = ref(props.task.estado);
  
  // Si la tarea cambia (por ejemplo, al reabrir el modal con otra tarea), sincronizamos el valor.
  watch(
    () => props.task,
    (newTask) => {
      updatedStatus.value = newTask.estado;
    }
  );
  
  // Función para cerrar el modal
  const close = () => {
    emit('close');
  };
  
  // Función para actualizar la tarea
  const updateTask = () => {
    // Aquí puedes integrar la llamada a un servicio para actualizar la tarea en el backend.
    // En este ejemplo, emitimos el evento "update" con la tarea actualizada.
    emit('update', { ...props.task, estado: updatedStatus.value });
    close();
  };
  </script>
  
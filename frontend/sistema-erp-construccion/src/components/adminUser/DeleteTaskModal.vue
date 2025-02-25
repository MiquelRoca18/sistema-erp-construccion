<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-sm p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Eliminar Tarea</h2>
        <p class="mb-6 text-gray-700">
          ¿Estás seguro de que deseas eliminar la tarea
          <span class="font-bold">{{ task.nombre_tarea }}</span>?
        </p>
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="confirmDeletion"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
          >
            Eliminar
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  const props = defineProps({
    task: {
      type: Object,
      required: true
    }
  });
  
  const emit = defineEmits(['close', 'deleted']);
  
  const confirmDeletion = () => {
    emit('deleted');
  };
  
  const close = () => {
    emit('close');
  };
  </script>
  
  <style scoped>
  /* Puedes agregar estilos adicionales si lo consideras necesario */
  </style>
  
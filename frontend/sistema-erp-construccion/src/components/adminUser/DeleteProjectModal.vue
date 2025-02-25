<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 sm:w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Confirmar Borrado</h2>
        <p class="mb-4">
          ¿Estás seguro de que deseas eliminar el proyecto 
          <span class="font-semibold">{{ project.nombre_proyecto }}</span>
          (ID: {{ project.proyectos_id }})?
        </p>
        <!-- Mostrar error, si existe -->
        <div v-if="errorMessage" class="mb-4 p-2 bg-red-100 text-red-700 border border-red-300 rounded">
          {{ errorMessage }}
        </div>
        <div class="flex justify-end space-x-2">
          <button 
            @click="closeModal" 
            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button 
            @click="confirmDelete" 
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
            :disabled="loading"
          >
            <span v-if="loading">Eliminando...</span>
            <span v-else>Eliminar</span>
          </button>
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
      await deleteProject(props.project.proyectos_id);
      emit('deleted');
      closeModal();
    } catch (error: any) {
      console.error(error.message);
      errorMessage.value = error.message || 'Error al eliminar proyecto';
    } finally {
      loading.value = false;
    }
  };
  </script>
  
  <style scoped>
  /* Puedes ajustar estilos adicionales si es necesario */
  </style>
  
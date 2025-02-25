<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 sm:w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Editar Proyecto</h2>
        <form @submit.prevent="handleSubmit">
          <div class="mb-4">
            <label class="block text-gray-700">Nombre</label>
            <input 
              v-model="form.nombre" 
              type="text" 
              required 
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Estado</label>
            <input 
              v-model="form.estado" 
              type="text" 
              required 
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Fecha de Inicio</label>
            <input 
              v-model="form.fecha_inicio" 
              type="date" 
              required 
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Fecha de Finalización</label>
            <input 
              v-model="form.fecha_fin" 
              type="date" 
              required 
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
            />
          </div>
          <!-- Mostrar error -->
          <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
            {{ errorMessage }}
          </div>
          <div class="flex justify-end space-x-2">
            <button 
              type="button" 
              @click="closeModal" 
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
            >
              Cancelar
            </button>
            <button 
              type="submit" 
              class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
            >
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted, watch } from 'vue';
  import { updateProject } from '@/service/projectService';
  
  const props = defineProps({
    project: {
      type: Object,
      required: true,
    },
  });
  const emit = defineEmits(['close', 'updated']);
  
  const form = ref({
    nombre: '',
    estado: '',
    fecha_inicio: '',
    fecha_fin: '',
  });
  const errorMessage = ref('');
  
  const initializeForm = () => {
    form.value = {
      nombre: props.project.nombre,
      estado: props.project.estado,
      fecha_inicio: props.project.fecha_inicio,
      fecha_fin: props.project.fecha_fin,
    };
  };
  
  onMounted(() => {
    initializeForm();
  });
  watch(() => props.project, () => {
    initializeForm();
  });
  
  const closeModal = () => {
    emit('close');
  };
  
  const handleSubmit = async () => {
    errorMessage.value = '';
    try {
      await updateProject(props.project.proyectos_id, form.value);
      emit('updated');
      closeModal();
    } catch (error: any) {
      console.error(error.message);
      errorMessage.value = error.message || 'Error al actualizar proyecto';
    }
  };
  </script>
  
  <style scoped>
  /* Puedes ajustar estilos adicionales si es necesario */
  </style>
  
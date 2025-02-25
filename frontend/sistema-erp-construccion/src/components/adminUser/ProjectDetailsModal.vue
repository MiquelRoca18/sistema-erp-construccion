<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50">
      <div class="bg-white rounded-xl shadow-2xl p-8 w-11/12 sm:w-full max-w-2xl">
        <!-- Encabezado -->
        <div class="flex justify-between items-center border-b pb-3 mb-6">
          <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            {{ project.nombre_proyecto }}
          </h2>
          <button @click="closeModal" class="text-gray-500 hover:text-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <!-- Contenido del Proyecto -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-sm text-gray-500 uppercase">Estado</p>
            <p class="text-xl font-semibold text-gray-800">{{ project.estado }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500 uppercase">Fecha de Inicio</p>
            <p class="text-xl font-semibold text-gray-800 whitespace-nowrap">{{ project.fecha_inicio }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500 uppercase">Fecha de Finalización</p>
            <p class="text-xl font-semibold text-gray-800 whitespace-nowrap">{{ project.fecha_fin }}</p>
          </div>
          <div class="md:col-span-2">
            <p class="text-sm text-gray-500 uppercase">Descripción</p>
            <p class="mt-2 text-base text-gray-700 leading-relaxed">
              {{ project.descripcion }}
            </p>
          </div>
        </div>
        
        <!-- Pie del Modal -->
        <div class="mt-8 flex justify-end">
          <button @click="closeModal" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-full hover:bg-gray-300 transition">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { defineProps, defineEmits } from 'vue';
  
  const props = defineProps({
    project: {
      type: Object,
      required: true,
    },
  });
  
  const emit = defineEmits(['close']);
  
  const closeModal = () => {
    emit('close');
  };
  </script>
  
  <style scoped>
  /* Puedes agregar ajustes adicionales si lo deseas */
  </style>
  
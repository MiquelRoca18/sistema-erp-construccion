<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8 w-11/12 sm:w-full max-w-2xl">
      <!-- Encabezado -->
      <div class="flex justify-between items-center border-b dark:border-gray-700 pb-3 mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 tracking-tight">
          {{ project.nombre_proyecto }}
        </h2>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <!-- Contenido del Proyecto -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Responsable</p>
          <p class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ project.responsable_nombre }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Estado</p>
          <p class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ project.estado }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Fecha de Inicio</p>
          <p class="text-xl font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ project.fecha_inicio }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Fecha de Finalización</p>
          <p class="text-xl font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ project.fecha_fin }}</p>
        </div>
        <div class="md:col-span-2">
          <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Descripción</p>
          <p class="mt-2 text-base text-gray-700 dark:text-gray-300 leading-relaxed">
            {{ project.descripcion }}
          </p>
        </div>
      </div>
      
      <!-- Pie del Modal -->
      <div class="mt-8 flex justify-end">
        <button @click="closeModal" class="px-6 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition">
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
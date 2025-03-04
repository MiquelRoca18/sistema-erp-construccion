<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 dark:from-green-700 dark:to-green-600 p-4">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Detalles del Proyecto</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300 cursor-pointer">&times;</button>
        </div>
      </div>
      <!-- Contenido -->
      <div class="p-6 space-y-6">
        <!-- Nombre y Estado -->
        <div>
          <h3 class="text-gray-800 dark:text-gray-100 text-xl font-semibold mb-1">{{ project.nombre_proyecto }}</h3>
          <p class="text-gray-600 dark:text-gray-400">
            <span class="font-medium">Estado:</span> {{ project.estado }}
          </p>
        </div>
        <!-- Responsable -->
        <div>
          <p class="text-gray-600 dark:text-gray-400">
            <span class="font-medium">Responsable:</span> {{ project.responsable_nombre }}
          </p>
        </div>
        <!-- Fechas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-if="project.fecha_inicio">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
              <span class="font-medium">Inicio:</span>
            </p>
            <p class="text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(project.fecha_inicio) }}</p>
          </div>
          <div v-if="project.fecha_fin">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
              <span class="font-medium">Fin:</span>
            </p>
            <p class="text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(project.fecha_fin) }}</p>
          </div>
        </div>
        <!-- Descripción -->
        <div v-if="project.descripcion">
          <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Descripción:</p>
          <p class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ project.descripcion }}</p>
        </div>
      </div>
      <!-- Pie de modal -->
      <div class="bg-gray-100 dark:bg-gray-700 p-4 text-right">
        <button @click="closeModal" class="px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded hover:bg-green-600 dark:hover:bg-green-700 transition">
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

// Función formatDate para formatear las fechas
const formatDate = (date: string): string => {
  if (!date) return 'No definida';
  return new Date(date).toLocaleDateString('es-ES');
};

// Función de cierre para emitir el evento close
const closeModal = () => {
  emit('close');
};
</script>
<template>
  <!-- TaskViewModal.vue -->
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-4">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Detalles de la Tarea</h2>
          <button @click="close" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300 cursor-pointer">&times;</button>
        </div>
      </div>
      <!-- Contenido -->
      <div class="p-6 space-y-6">
        <!-- Nombre y Estado -->
        <div>
          <h3 class="text-gray-800 dark:text-gray-100 text-xl font-semibold mb-1">{{ task.nombre_tarea }}</h3>
          <p class="text-gray-600 dark:text-gray-400">
            <span class="font-medium">Estado:</span> {{ task.estado }}
          </p>
        </div>
        <!-- Proyecto -->
        <div>
          <p class="text-gray-600 dark:text-gray-400">
            <span class="font-medium">Proyecto:</span> {{ task.nombre_proyecto }}
          </p>
        </div>
        <!-- Fechas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-if="task.fecha_inicio">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
              <span class="font-medium">Inicio:</span>
            </p>
            <p class="text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(task.fecha_inicio) }}</p>
          </div>
          <div v-if="task.fecha_fin">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
              <span class="font-medium">Fin:</span>
            </p>
            <p class="text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(task.fecha_fin) }}</p>
          </div>
        </div>
        <!-- Descripción -->
        <div v-if="task.descripcion">
          <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Descripción:</p>
          <p class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ task.descripcion }}</p>
        </div>
        <!-- Responsables -->
        <div>
          <p class="text-gray-600 dark:text-gray-400 text-sm">
            <span class="font-medium">Responsable(s):</span>
          </p>
          <p class="text-gray-800 dark:text-gray-200 font-medium">{{ task.empleado_nombre }}</p>
        </div>
      </div>
      <!-- Pie de modal -->
      <div class="bg-gray-100 dark:bg-gray-700 p-4 text-right">
        <button @click="close" class="px-4 py-2 bg-orange-500 dark:bg-orange-600 text-white rounded hover:bg-orange-600 dark:hover:bg-orange-700 transition">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

const formatDate = (dateStr: string) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString();
};
</script>
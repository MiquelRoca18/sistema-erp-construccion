<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado  -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 dark:from-green-700 dark:to-green-600 p-4">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Detalles del Proyecto</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300 cursor-pointer">&times;</button>
        </div>
      </div>
      
      <!-- Contenido -->
      <div class="p-6 space-y-6">
        <!-- Estado de carga -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-8">
          <div class="w-12 h-12 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
          <p class="text-gray-600 dark:text-gray-300">Cargando detalles del proyecto...</p>
        </div>
        
        <!-- Contenido del proyecto -->
        <div v-else>
          <!-- Nombre y Estado -->
          <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg mb-4">
            <h3 class="text-gray-800 dark:text-gray-100 text-xl font-semibold mb-1">{{ project.nombre_proyecto }}</h3>
            <div class="flex items-center">
              <span class="inline-block w-3 h-3 rounded-full mr-2" 
                :class="{
                  'bg-yellow-400': project.estado === 'pendiente',
                  'bg-blue-400': project.estado === 'en progreso',
                  'bg-green-400': project.estado === 'finalizado'
                }">
              </span>
              <p class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Estado:</span> {{ project.estado }}
              </p>
            </div>
          </div>
          
          <!-- Responsable -->
          <div class="mb-4">
            <p class="text-gray-600 dark:text-gray-400">
              <span class="font-medium">Responsable:</span> {{ project.responsable_nombre || 'No asignado' }}
            </p>
          </div>
          
          <!-- Fechas -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div v-if="project.fecha_inicio" class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                <span class="font-medium">Fecha de inicio:</span>
              </p>
              <p class="text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(project.fecha_inicio) }}</p>
            </div>
            <div v-if="project.fecha_fin" class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
              <p class="text-gray-600 dark:text-gray-400 text-sm">
                <span class="font-medium">Fecha de finalización:</span>
              </p>
              <p class="text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(project.fecha_fin) }}</p>
            </div>
          </div>
          
          <!-- Descripción -->
          <div v-if="project.descripcion" class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg">
            <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Descripción:</p>
            <p class="text-gray-800 dark:text-gray-200 leading-relaxed">{{ project.descripcion }}</p>
          </div>
        </div>
      </div>
      
      <!-- Pie de modal -->
      <div class="bg-gray-100 dark:bg-gray-700 p-4 text-right">
        <button 
          @click="closeModal" 
          class="px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded hover:bg-green-600 dark:hover:bg-green-700 transition"
        >
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, onMounted } from 'vue';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(['close']);
const loading = ref(true);

// Simulamos una carga breve para proporcionar feedback visual
onMounted(async () => {
  // Simular un pequeño retraso para mostrar el loader
  await new Promise(resolve => setTimeout(resolve, 400));
  loading.value = false;
});

// Función formatDate para formatear las fechas
const formatDate = (date: string): string => {
  if (!date) return 'No definida';
  try {
    return new Date(date).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  } catch (error) {
    console.error('Error formatting date:', error);
    return date; 
  }
};

// Función de cierre para emitir el evento close
const closeModal = () => {
  emit('close');
};
</script>
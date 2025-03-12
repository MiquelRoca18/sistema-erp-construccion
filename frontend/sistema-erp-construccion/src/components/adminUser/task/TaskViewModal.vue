<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300 max-h-[85vh] overflow-y-auto">
      <!-- Encabezado - sticky para que se mantenga visible en scroll -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-3 sm:p-4 sticky top-0 z-10">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-lg sm:text-2xl font-bold">Detalles de la Tarea</h2>
          <button @click="close" class="text-white text-2xl sm:text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300 cursor-pointer">&times;</button>
        </div>
      </div>
      
      <!-- Loader -->
      <div v-if="loading" class="p-6 sm:p-8 flex flex-col items-center justify-center">
        <div class="relative mb-4">
          <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full border-t-4 border-b-4 border-orange-500 animate-spin"></div>
          <div class="absolute top-0 left-0 h-10 w-10 sm:h-12 sm:w-12 rounded-full border-t-4 border-b-4 border-orange-200 animate-spin" style="animation-duration: 1.5s;"></div>
        </div>
        <p class="text-gray-600 dark:text-gray-300 text-sm">Cargando detalles...</p>
      </div>
      
      <!-- Contenido -->
      <div v-else class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Nombre y Estado -->
        <div>
          <h3 class="text-gray-800 dark:text-gray-100 text-lg sm:text-xl font-semibold mb-1">{{ task.nombre_tarea }}</h3>
          <div class="flex items-center">
            <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400 mr-2">Estado:</span>
            <span 
              class="px-2 py-0.5 rounded-full text-xs font-medium" 
              :class="{
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200': task.estado === 'pendiente',
                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200': task.estado === 'en progreso',
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200': task.estado === 'finalizado'
              }"
            >
              {{ task.estado }}
            </span>
          </div>
        </div>
        <!-- Proyecto -->
        <div>
          <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium">Proyecto:</span> {{ task.nombre_proyecto }}
          </p>
        </div>
        <!-- Fechas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4">
          <div v-if="task.fecha_inicio">
            <p class="text-xs text-gray-600 dark:text-gray-400">
              <span class="font-medium">Inicio:</span>
            </p>
            <p class="text-xs sm:text-sm text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(task.fecha_inicio) }}</p>
          </div>
          <div v-if="task.fecha_fin">
            <p class="text-xs text-gray-600 dark:text-gray-400">
              <span class="font-medium">Fin:</span>
            </p>
            <p class="text-xs sm:text-sm text-gray-800 dark:text-gray-200 font-medium">{{ formatDate(task.fecha_fin) }}</p>
          </div>
        </div>
        <!-- Descripción -->
        <div v-if="task.descripcion">
          <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 font-medium mb-1">Descripción:</p>
          <div class="bg-gray-50 dark:bg-gray-700 p-2 sm:p-3 rounded-lg text-xs sm:text-sm text-gray-800 dark:text-gray-200 leading-relaxed">
            {{ task.descripcion }}
          </div>
        </div>
        <!-- Responsables -->
        <div>
          <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium">Responsable(s):</span>
          </p>
          <div class="mt-1 flex flex-wrap gap-1">
            <span 
              v-for="(empleado, index) in empleados" 
              :key="index"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-200"
            >
              {{ empleado }}
            </span>
            <span v-if="empleados.length === 0" class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm italic">
              Sin asignaciones
            </span>
          </div>
        </div>
      </div>
      <!-- Pie de modal -->
      <div class="bg-gray-100 dark:bg-gray-700 p-3 sm:p-4 text-right">
        <button 
          @click="close" 
          class="px-3 py-1.5 sm:px-4 sm:py-2 bg-orange-500 dark:bg-orange-600 text-white rounded hover:bg-orange-600 dark:hover:bg-orange-700 transition text-xs sm:text-sm"
          :disabled="loading"
        >
          Cerrar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, computed, onMounted } from 'vue';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close']);
const loading = ref(true);

const empleados = computed(() => {
  if (!props.task.empleado_nombre) return [];
  return props.task.empleado_nombre.split(',').map(e => e.trim()).filter(e => e);
});

const close = () => {
  emit('close');
};

const formatDate = (dateStr: string) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString();
};

onMounted(() => {
  // Simular tiempo de carga para mejor UX
  setTimeout(() => {
    loading.value = false;
  }, 300);
});
</script>
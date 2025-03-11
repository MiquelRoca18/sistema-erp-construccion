<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 px-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 dark:from-yellow-700 dark:to-yellow-600 p-3 sm:p-4 rounded-t-2xl">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-xl sm:text-2xl font-bold">Detalles de Presupuesto</h2>
            <button @click="closeModal" class="text-white text-2xl sm:text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
          </div>
        </div>
        
        <!-- Estado de carga -->
        <div v-if="loading" class="p-6 flex flex-col items-center justify-center">
          <div class="w-10 h-10 sm:w-12 sm:h-12 border-4 border-yellow-500 border-t-transparent rounded-full animate-spin mb-3 sm:mb-4"></div>
          <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base">Cargando detalles...</p>
        </div>
        
        <!-- Contenido -->
        <div v-else class="p-4 sm:p-6">
          <div class="space-y-4">
            <!-- Proyecto -->
            <div>
              <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Proyecto</h3>
              <p class="text-sm sm:text-base text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-2 rounded">
                {{ budget.nombre_proyecto || 'No disponible' }}
              </p>
            </div>
            
            <!-- Costos (con animación) -->
            <div>
              <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Desglose de Costos</h3>
              
              <!-- Equipos -->
              <div class="mb-3">
                <div class="flex justify-between text-sm sm:text-base">
                  <span class="text-gray-600 dark:text-gray-300">Equipos:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-100">{{ formatCurrency(budget.equipos) }}</span>
                </div>
                <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full mt-1 overflow-hidden">
                  <div class="absolute top-0 left-0 h-full bg-blue-500 dark:bg-blue-600 rounded-full transition-all duration-1000 ease-out" :style="{ width: `${getPercentage(budget.equipos)}%` }"></div>
                </div>
              </div>
              
              <!-- Mano de obra -->
              <div class="mb-3">
                <div class="flex justify-between text-sm sm:text-base">
                  <span class="text-gray-600 dark:text-gray-300">Mano de obra:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-100">{{ formatCurrency(budget.mano_obra) }}</span>
                </div>
                <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full mt-1 overflow-hidden">
                  <div class="absolute top-0 left-0 h-full bg-green-500 dark:bg-green-600 rounded-full transition-all duration-1000 ease-out" :style="{ width: `${getPercentage(budget.mano_obra)}%` }"></div>
                </div>
              </div>
              
              <!-- Materiales -->
              <div class="mb-3">
                <div class="flex justify-between text-sm sm:text-base">
                  <span class="text-gray-600 dark:text-gray-300">Materiales:</span>
                  <span class="font-medium text-gray-800 dark:text-gray-100">{{ formatCurrency(budget.materiales) }}</span>
                </div>
                <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full mt-1 overflow-hidden">
                  <div class="absolute top-0 left-0 h-full bg-purple-500 dark:bg-purple-600 rounded-full transition-all duration-1000 ease-out" :style="{ width: `${getPercentage(budget.materiales)}%` }"></div>
                </div>
              </div>
            </div>
            
            <!-- Total -->
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/30 p-3 rounded-lg">
              <div class="flex justify-between items-center">
                <span class="text-yellow-800 dark:text-yellow-300 font-semibold text-base sm:text-lg">Total:</span>
                <span class="text-yellow-800 dark:text-yellow-300 font-bold text-lg sm:text-xl">{{ formatCurrency(totalBudget) }}</span>
              </div>
            </div>
            
            <!-- Proyecto ID -->
            <div class="mt-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
              ID del Proyecto: {{ budget.proyectos_id || 'No disponible' }}
            </div>
          </div>
        </div>
        
        <!-- Pie de modal -->
        <div class="bg-gray-100 dark:bg-gray-700 p-3 sm:p-4 flex justify-end">
          <button 
            @click="closeModal" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-yellow-600 dark:bg-yellow-500 text-white rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition text-xs sm:text-sm"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted } from 'vue';
  
  const props = defineProps({
    budget: {
      type: Object,
      required: true
    }
  });
  
  const emit = defineEmits(['close']);
  const loading = ref(true);
  
  // Calcular el total del presupuesto
  const totalBudget = computed(() => {
    const equipos = parseFloat(props.budget.equipos) || 0;
    const manoObra = parseFloat(props.budget.mano_obra) || 0;
    const materiales = parseFloat(props.budget.materiales) || 0;
    return equipos + manoObra + materiales;
  });
  
  // Simular una breve carga para mejor experiencia
  onMounted(() => {
    setTimeout(() => {
      loading.value = false;
    }, 500);
  });
  
  // Formatear moneda
  const formatCurrency = (value: number | string) => {
    const val = parseFloat(value as string) || 0;
    return new Intl.NumberFormat('es-ES', { 
      style: 'currency', 
      currency: 'EUR',
      minimumFractionDigits: 2
    }).format(val);
  };
  
  // Calcular el porcentaje para las barras
  const getPercentage = (value: number | string) => {
    const val = parseFloat(value as string) || 0;
    if (totalBudget.value === 0) return 0;
    return (val / totalBudget.value) * 100;
  };
  
  const closeModal = () => {
    emit('close');
  };
  </script>
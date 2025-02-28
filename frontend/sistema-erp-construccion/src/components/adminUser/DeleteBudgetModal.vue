<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
        <!-- Encabezado con degradado amarillo -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 p-4 rounded-t-2xl">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-2xl font-bold">Confirmar Borrado</h2>
            <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200">&times;</button>
          </div>
        </div>
        <!-- Contenido -->
        <div class="p-6 space-y-6">
          <p class="text-gray-700 dark:text-gray-300">
            ¿Estás seguro de que deseas eliminar el presupuesto <span class="font-semibold">{{ budget.presupuestos_id }}</span>?
          </p>
          <div v-if="errorMessage" class="p-2 bg-red-100 border border-red-300 rounded-md text-red-700 text-xs">
            {{ errorMessage }}
          </div>
          <div class="flex justify-end space-x-4">
            <button @click="closeModal" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 rounded hover:bg-gray-400 transition text-sm">
              Cancelar
            </button>
            <button @click="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm">
              Eliminar
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref } from 'vue';
  import { deleteBudget } from '@/service/budgetService';
  
  const props = defineProps({
    budget: {
      type: Object,
      required: true
    }
  });
  const emit = defineEmits(['close', 'deleted']);
  
  const errorMessage = ref('');
  
  const closeModal = () => {
    emit('close');
  };
  
  const confirmDelete = async () => {
    errorMessage.value = '';
    try {
      await deleteBudget(props.budget.presupuestos_id);
      emit('deleted');
      emit('close');
    } catch (error: any) {
      errorMessage.value = error.message || 'Error al eliminar presupuesto';
    }
  };
  </script>
  
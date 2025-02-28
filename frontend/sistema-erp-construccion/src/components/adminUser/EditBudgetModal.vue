<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
        <!-- Encabezado con degradado amarillo -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 p-4 rounded-t-2xl">
          <div class="flex justify-between items-center">
            <h2 class="text-white text-2xl font-bold">Editar Presupuesto</h2>
            <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200">&times;</button>
          </div>
        </div>
        <!-- Formulario -->
        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
          <!-- Campo no editable: ID -->
          <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300">Presupuesto ID</label>
            <input type="number" :value="budget.presupuestos_id" disabled class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600" />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300">Proyecto ID</label>
            <input v-model="form.proyectos_id" type="number" required class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600" />
          </div>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
              <label class="block text-gray-700 dark:text-gray-300">Equipos</label>
              <input v-model="form.equipos" type="number" step="0.01" required class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600" />
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300">Mano de obra</label>
              <input v-model="form.mano_obra" type="number" step="0.01" required class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600" />
            </div>
            <div>
              <label class="block text-gray-700 dark:text-gray-300">Materiales</label>
              <input v-model="form.materiales" type="number" step="0.01" required class="w-full p-2 border rounded dark:bg-gray-700 dark:border-gray-600" />
            </div>
          </div>
          <div v-if="errorMessage" class="p-2 bg-red-100 border border-red-300 rounded-md text-red-700 text-xs">
            {{ errorMessage }}
          </div>
          <div class="flex justify-end space-x-4">
            <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 rounded hover:bg-gray-400 transition text-sm">
              Cancelar
            </button>
            <button type="submit" class="px-4 py-2 bg-yellow-600 dark:bg-yellow-500 text-white rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition text-sm">
              Guardar Cambios
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { updateBudget } from '@/service/budgetService';
  
  const props = defineProps({
    budget: {
      type: Object,
      required: true
    }
  });
  const emit = defineEmits(['close', 'updated']);
  
  const form = ref({
    proyectos_id: '',
    equipos: '',
    mano_obra: '',
    materiales: ''
  });
  const errorMessage = ref('');
  
  onMounted(() => {
    form.value = {
      proyectos_id: props.budget.proyectos_id,
      equipos: props.budget.equipos,
      mano_obra: props.budget.mano_obra,
      materiales: props.budget.materiales
    };
  });
  
  const closeModal = () => {
    emit('close');
  };
  
  const handleSubmit = async () => {
    errorMessage.value = '';
    try {
      await updateBudget(props.budget.presupuestos_id, form.value);
      emit('updated');
      emit('close');
    } catch (error: any) {
      errorMessage.value = error.message || 'Error al actualizar presupuesto';
    }
  };
  </script>
  
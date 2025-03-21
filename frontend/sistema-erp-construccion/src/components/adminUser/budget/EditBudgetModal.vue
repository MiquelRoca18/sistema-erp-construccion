<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 dark:from-yellow-700 dark:to-yellow-600 p-3 sm:p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-xl sm:text-2xl font-bold">Editar Presupuesto</h2>
          <button @click="closeModal" class="text-white text-2xl sm:text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Campo no editable: ID -->
        <div class="mb-3 sm:mb-4">
          <label class="block text-sm sm:text-base text-gray-700 dark:text-gray-200">Presupuesto ID</label>
          <input 
            type="number" 
            :value="budget.presupuestos_id" 
            disabled 
            class="w-full p-2 sm:p-2.5 border rounded text-sm sm:text-base
                   bg-gray-100 dark:bg-gray-700 
                   text-gray-600 dark:text-gray-300 
                   border-gray-300 dark:border-gray-600" 
          />
        </div>
        <div class="mb-3 sm:mb-4">
          <label class="block text-sm sm:text-base text-gray-700 dark:text-gray-200">Proyecto ID</label>
          <input 
            v-model="form.proyectos_id" 
            type="number" 
            required 
            class="w-full p-2 sm:p-2.5 border rounded text-sm sm:text-base
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600" 
          />
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label class="block text-sm sm:text-base text-gray-700 dark:text-gray-200">Equipos</label>
            <input 
              v-model="form.equipos" 
              type="number" 
              step="0.01" 
              required 
              class="w-full p-2 sm:p-2.5 border rounded text-sm sm:text-base
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600" 
            />
          </div>
          <div>
            <label class="block text-sm sm:text-base text-gray-700 dark:text-gray-200">Mano de obra</label>
            <input 
              v-model="form.mano_obra" 
              type="number" 
              step="0.01" 
              required 
              class="w-full p-2 sm:p-2.5 border rounded text-sm sm:text-base
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600" 
            />
          </div>
          <div>
            <label class="block text-sm sm:text-base text-gray-700 dark:text-gray-200">Materiales</label>
            <input 
              v-model="form.materiales" 
              type="number" 
              step="0.01" 
              required 
              class="w-full p-2 sm:p-2.5 border rounded text-sm sm:text-base
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600" 
            />
          </div>
        </div>
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs sm:text-sm">
          {{ errorMessage }}
        </div>
        <div class="flex justify-end space-x-3 sm:space-x-4">
          <button 
            type="button" 
            @click="closeModal" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-xs sm:text-sm"
            :disabled="isSubmitting"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-yellow-600 dark:bg-yellow-500 text-white rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition text-xs sm:text-sm flex items-center"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="w-3 h-3 sm:w-4 sm:h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-1 sm:mr-2"></span>
            {{ isSubmitting ? 'Guardando...' : 'Guardar Cambios' }}
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

const emit = defineEmits(['close', 'updated', 'showSuccess', 'showError']);

const form = ref({
  proyectos_id: '',
  equipos: '',
  mano_obra: '',
  materiales: ''
});

const errorMessage = ref('');
const isSubmitting = ref(false);

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
  isSubmitting.value = true;
  
  try {
    // Agregar un pequeño retraso mínimo para mostrar el loader
    const updatePromise = updateBudget(props.budget.presupuestos_id, form.value);
    const minDelay = new Promise(resolve => setTimeout(resolve, 500));
    
    // Esperar a que ambas promesas se resuelvan
    await Promise.all([updatePromise, minDelay]);
    
    emit('updated');
    emit('showSuccess', `Presupuesto del proyecto con ID ${form.value.proyectos_id} actualizado exitosamente`);
    emit('close');
  } catch (error: any) {
    errorMessage.value = error.message || 'Error al actualizar presupuesto';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado amarillo -->
      <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 dark:from-yellow-700 dark:to-yellow-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Editar Presupuesto</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Campo no editable: ID -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Presupuesto ID</label>
          <input 
            type="number" 
            :value="budget.presupuestos_id" 
            disabled 
            class="w-full p-2 border rounded 
                   bg-gray-100 dark:bg-gray-700 
                   text-gray-600 dark:text-gray-300 
                   border-gray-300 dark:border-gray-600" 
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Proyecto ID</label>
          <input 
            v-model="form.proyectos_id" 
            type="number" 
            required 
            class="w-full p-2 border rounded 
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600" 
          />
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label class="block text-gray-700 dark:text-gray-200">Equipos</label>
            <input 
              v-model="form.equipos" 
              type="number" 
              step="0.01" 
              required 
              class="w-full p-2 border rounded 
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600" 
            />
          </div>
          <div>
            <label class="block text-gray-700 dark:text-gray-200">Mano de obra</label>
            <input 
              v-model="form.mano_obra" 
              type="number" 
              step="0.01" 
              required 
              class="w-full p-2 border rounded 
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600" 
            />
          </div>
          <div>
            <label class="block text-gray-700 dark:text-gray-200">Materiales</label>
            <input 
              v-model="form.materiales" 
              type="number" 
              step="0.01" 
              required 
              class="w-full p-2 border rounded 
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600" 
            />
          </div>
        </div>
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs">
          {{ errorMessage }}
        </div>
        <div class="flex justify-end space-x-4">
          <button 
            type="button" 
            @click="closeModal" 
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm"
            :disabled="isSubmitting"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="px-4 py-2 bg-yellow-600 dark:bg-yellow-500 text-white rounded hover:bg-yellow-700 dark:hover:bg-yellow-600 transition text-sm flex items-center"
            :disabled="isSubmitting"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
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

const emit = defineEmits(['close', 'updated']);

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
  // No permitir cerrar el modal durante el envío
  if (isSubmitting.value) return;
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
    emit('close');
  } catch (error: any) {
    errorMessage.value = error.message || 'Error al actualizar presupuesto';
  } finally {
    isSubmitting.value = false;
  }
};
</script>
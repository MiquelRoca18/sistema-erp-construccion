<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-400 dark:from-blue-700 dark:to-blue-600 p-3 sm:p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-lg sm:text-2xl font-bold">Editar Empleado</h2>
          <button @click="closeModal" class="text-white text-2xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-4 sm:p-6 space-y-4 sm:space-y-6 max-h-[70vh] overflow-y-auto">
        <div class="mb-3 sm:mb-4">
          <label class="block text-gray-700 dark:text-gray-200 text-sm">Nombre</label>
          <input
            v-model="form.nombre"
            type="text"
            required
            :disabled="loading"
            class="w-full border rounded px-2 py-1.5 sm:px-3 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600
                   disabled:opacity-70 disabled:cursor-not-allowed text-sm"
          />
        </div>
        <div class="mb-3 sm:mb-4">
          <label class="block text-gray-700 dark:text-gray-200 text-sm">Rol</label>
          <input
            v-model="form.rol"
            type="text"
            required
            :disabled="loading"
            class="w-full border rounded px-2 py-1.5 sm:px-3 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600
                   disabled:opacity-70 disabled:cursor-not-allowed text-sm"
          />
        </div>
        <div class="mb-3 sm:mb-4">
          <label class="block text-gray-700 dark:text-gray-200 text-sm">Fecha de Contratación</label>
          <input
            v-model="form.fecha_contratacion"
            type="date"
            required
            :disabled="loading"
            class="w-full border rounded px-2 py-1.5 sm:px-3 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600
                   disabled:opacity-70 disabled:cursor-not-allowed text-sm"
          />
        </div>
        <div class="mb-3 sm:mb-4">
          <label class="block text-gray-700 dark:text-gray-200 text-sm">Teléfono</label>
          <input
            v-model="form.telefono"
            type="text"
            required
            :disabled="loading"
            class="w-full border rounded px-2 py-1.5 sm:px-3 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600
                   disabled:opacity-70 disabled:cursor-not-allowed text-sm"
          />
        </div>
        <div class="mb-3 sm:mb-4">
          <label class="block text-gray-700 dark:text-gray-200 text-sm">Correo</label>
          <input
            v-model="form.correo"
            type="email"
            required
            :disabled="loading"
            class="w-full border rounded px-2 py-1.5 sm:px-3 sm:py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600
                   disabled:opacity-70 disabled:cursor-not-allowed text-sm"
          />
        </div>
        <!-- Mostrar error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-2 sm:space-x-4">
          <button
            type="button"
            @click="closeModal"
            :disabled="loading"
            class="px-2 py-1 sm:px-4 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-xs sm:text-sm disabled:opacity-70 disabled:cursor-not-allowed"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-2 py-1 sm:px-4 sm:py-2 bg-blue-600 dark:bg-blue-500 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition text-xs sm:text-sm flex items-center justify-center disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-1 h-3 w-3 sm:h-4 sm:w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { updateEmployee } from '@/service/employeeService';

const props = defineProps({
  employee: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['close', 'updated', 'showSuccess', 'showError']);

const form = ref({
  nombre: '',
  rol: '',
  fecha_contratacion: '',
  telefono: '',
  correo: '',
});
const loading = ref(false);
const errorMessage = ref('');

const initializeForm = () => {
  form.value = {
    nombre: props.employee.nombre,
    rol: props.employee.rol,
    fecha_contratacion: props.employee.fecha_contratacion,
    telefono: props.employee.telefono,
    correo: props.employee.correo,
  };
};

onMounted(() => {
  initializeForm();
});

// Si el empleado cambia, reinicializamos el formulario
watch(() => props.employee, () => {
  initializeForm();
});

// Modificamos esta función para permitir cerrar siempre el modal
const closeModal = () => {
  emit('close');
};

const handleSubmit = async () => {
  errorMessage.value = '';
  loading.value = true;
  
  try {
    // Añadimos un tiempo mínimo para mostrar el loader
    const startTime = Date.now();
    
    await updateEmployee(props.employee.empleados_id, form.value);
    
    // Aseguramos que el loader se muestre por al menos 400ms para mejor UX
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 400) {
      await new Promise(resolve => setTimeout(resolve, 400 - elapsedTime));
    }
    
    emit('updated');
    emit('showSuccess', `Empleado ${form.value.nombre} actualizado exitosamente`);
    emit('close'); 
  } catch (error: any) {
    // Error handling
    emit('showError', error.message || 'Error al actualizar empleado');
  } finally {
    loading.value = false;
  }
};
</script>
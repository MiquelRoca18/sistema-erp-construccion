<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado azul -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-400 dark:from-blue-700 dark:to-blue-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Editar Empleado</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Nombre</label>
          <input
            v-model="form.nombre"
            type="text"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Rol</label>
          <input
            v-model="form.rol"
            type="text"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Fecha de Contratación</label>
          <input
            v-model="form.fecha_contratacion"
            type="date"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Teléfono</label>
          <input
            v-model="form.telefono"
            type="text"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Correo</label>
          <input
            v-model="form.correo"
            type="email"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          />
        </div>
        <!-- Mostrar error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition text-sm"
          >
            Guardar Cambios
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
const emit = defineEmits(['close', 'updated']);

const form = ref({
  nombre: '',
  rol: '',
  fecha_contratacion: '',
  telefono: '',
  correo: '',
});
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

const closeModal = () => {
  emit('close');
};

const handleSubmit = async () => {
  errorMessage.value = '';
  try {
    await updateEmployee(props.employee.empleados_id, form.value);
    emit('updated');
    closeModal();
  } catch (error: any) {
    console.error(error.message);
    errorMessage.value = error.message || 'Error al actualizar empleado';
  }
};
</script>

<style scoped>
/* Puedes agregar estilos adicionales si es necesario */
</style>
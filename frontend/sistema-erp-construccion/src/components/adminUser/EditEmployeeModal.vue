<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado azul -->
      <div class="bg-gradient-to-r from-blue-500 to-blue-400 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Editar Empleado</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <div class="mb-4">
          <label class="block text-gray-700">Nombre</label>
          <input
            v-model="form.nombre"
            type="text"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Rol</label>
          <input
            v-model="form.rol"
            type="text"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Fecha de Contratación</label>
          <input
            v-model="form.fecha_contratacion"
            type="date"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Teléfono</label>
          <input
            v-model="form.telefono"
            type="text"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Correo</label>
          <input
            v-model="form.correo"
            type="email"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
        <!-- Mostrar error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 border border-red-300 rounded-md text-red-700 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="closeModal"
            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition text-sm"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm"
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

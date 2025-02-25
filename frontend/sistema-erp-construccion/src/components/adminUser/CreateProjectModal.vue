<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 sm:w-full max-w-md">
      <h2 class="text-2xl font-bold mb-4">Crear Nuevo Proyecto</h2>
      <form @submit.prevent="handleSubmit">
        <!-- Nombre del Proyecto -->
        <div class="mb-4">
          <label class="block text-gray-700">Nombre del Proyecto</label>
          <input 
            v-model="form.nombre_proyecto" 
            type="text" 
            required 
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
          />
        </div>
        <!-- Descripción -->
        <div class="mb-4">
          <label class="block text-gray-700">Descripción</label>
          <textarea 
            v-model="form.descripcion" 
            rows="4"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          ></textarea>
        </div>
        <!-- Responsable (Dropdown) -->
        <div class="mb-4">
          <label class="block text-gray-700">Responsable</label>
          <select 
            v-model="form.responsable_id" 
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          >
            <option value="">Seleccione un responsable</option>
            <option 
              v-for="employee in employees" 
              :key="employee.empleados_id" 
              :value="employee.empleados_id"
            >
              {{ employee.nombre }}
            </option>
          </select>
        </div>
        <!-- Mostrar error de forma elegante -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
          {{ errorMessage }}
        </div>
        <div class="flex justify-end space-x-2">
          <button 
            type="button" 
            @click="closeModal" 
            class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
          >
            Crear
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { createProject } from '@/service/projectService';
import { getEmployees } from '@/service/employeeService';

const form = ref({
  nombre_proyecto: '',
  descripcion: '',
  responsable_id: '',
});

const employees = ref<any[]>([]);
const errorMessage = ref('');

const emit = defineEmits(['close', 'created']);

const closeModal = () => {
  emit('close');
};

const handleSubmit = async () => {
  errorMessage.value = '';
  try {
    await createProject(form.value);
    emit('created');
    closeModal();
  } catch (error: any) {
    console.error(error.message);
    errorMessage.value = error.message || 'Error al crear proyecto';
  }
};

const fetchEmployees = async () => {
  try {
    const data = await getEmployees();
    employees.value = data;
  } catch (error: any) {
    console.error(error.message);
  }
};

onMounted(() => {
  fetchEmployees();
});
</script>

<style scoped>
/* Puedes ajustar estilos adicionales si es necesario */
</style>

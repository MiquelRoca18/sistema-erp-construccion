<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado verde -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 dark:from-green-700 dark:to-green-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Crear Nuevo Proyecto</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Nombre del Proyecto -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Nombre del Proyecto</label>
          <input 
            v-model="form.nombre_proyecto" 
            type="text" 
            required 
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400 
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600" 
          />
        </div>
        <!-- Descripción -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Descripción</label>
          <textarea 
            v-model="form.descripcion" 
            rows="4"
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          ></textarea>
        </div>
        <!-- Responsable (Dropdown) -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Responsable</label>
          <select 
            v-model="form.responsable_id" 
            required
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          >
            <option value="" class="bg-white dark:bg-gray-700">Seleccione un responsable</option>
            <option 
              v-for="employee in employees" 
              :key="employee.empleados_id" 
              :value="employee.empleados_id"
              class="bg-white dark:bg-gray-700"
            >
              {{ employee.nombre }}
            </option>
          </select>
        </div>
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-700 rounded-lg">
          {{ errorMessage }}
        </div>
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-2">
          <button 
            type="button" 
            @click="closeModal" 
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm dark:bg-green-500 dark:hover:bg-green-600"
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
const loading = ref(false);
const fecha_inicio = ref('');
const fecha_fin = ref('');

const emit = defineEmits(['close', 'created']);

const closeModal = () => {
  emit('close');
};

import { computed } from 'vue';

const isFormValid = computed(() => {
  return form.value.nombre_proyecto && form.value.descripcion && form.value.responsable_id;
});

const handleSubmit = async () => {
  if (isFormValid.value) {
    loading.value = true;
    
    try {
      // Crear objeto proyecto con todas las propiedades requeridas
      const proyecto = {
        nombre_proyecto: form.value.nombre_proyecto,
        descripcion: form.value.descripcion,
        responsable_id: form.value.responsable_id,  
        fecha_inicio: fecha_inicio.value || new Date().toISOString().split('T')[0], // Fecha actual si no está definida
        fecha_fin: fecha_fin.value || null // null si no está definida aún
      };
      
      await createProject(proyecto);
      close();
      emit('created');
    } catch (error: any) {
      errorMessage.value = error.message || 'Error al crear el proyecto';
    } finally {
      loading.value = false;
    }
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
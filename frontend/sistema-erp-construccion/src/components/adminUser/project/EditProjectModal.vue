<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 dark:from-green-700 dark:to-green-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Editar Proyecto</h2>
          <button 
            @click="closeModal" 
            class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300"
            :disabled="loading || employeesLoading"
          >&times;</button>
        </div>
      </div>
      
      <!-- Loader para carga inicial -->
      <div v-if="employeesLoading" class="p-6 flex flex-col items-center justify-center">
        <div class="w-12 h-12 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600 dark:text-gray-300">Cargando datos...</p>
      </div>
      
      <!-- Formulario -->
      <form v-else @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Responsable -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Responsable</label>
          <select 
            v-model="form.responsable_id" 
            required 
            :disabled="loading"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                  bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-100 
                  border-gray-300 dark:border-gray-600
                  disabled:opacity-70 disabled:cursor-not-allowed"
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
        <!-- Nombre del Proyecto -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Nombre del Proyecto</label>
          <input 
            v-model="form.nombre_proyecto" 
            type="text" 
            required 
            :disabled="loading"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                  bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-100 
                  border-gray-300 dark:border-gray-600
                  disabled:opacity-70 disabled:cursor-not-allowed" 
          />
        </div>
        <!-- Estado -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Estado</label>
          <select 
            v-model="form.estado" 
            required 
            :disabled="loading"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                  bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-100 
                  border-gray-300 dark:border-gray-600
                  disabled:opacity-70 disabled:cursor-not-allowed"
          >
            <option value="pendiente" class="bg-white dark:bg-gray-700">Pendiente</option>
            <option value="en progreso" class="bg-white dark:bg-gray-700">En progreso</option>
            <option value="finalizado" class="bg-white dark:bg-gray-700">Finalizado</option>
          </select>
        </div>
        <!-- Fecha de Inicio -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Fecha de Inicio</label>
          <input 
            v-model="form.fecha_inicio" 
            type="date" 
            required 
            :disabled="loading"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                  bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-100 
                  border-gray-300 dark:border-gray-600
                  disabled:opacity-70 disabled:cursor-not-allowed" 
          />
        </div>
        <!-- Fecha de Finalización -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Fecha de Finalización</label>
          <input 
            v-model="form.fecha_fin" 
            type="date"
            :disabled="loading"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                  bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-100 
                  border-gray-300 dark:border-gray-600
                  disabled:opacity-70 disabled:cursor-not-allowed" 
          />
        </div>
        <!-- Descripción -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Descripción</label>
          <textarea 
            v-model="form.descripcion" 
            rows="4"
            required
            :disabled="loading"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400
                  bg-white dark:bg-gray-700 
                  text-gray-900 dark:text-gray-100 
                  border-gray-300 dark:border-gray-600
                  disabled:opacity-70 disabled:cursor-not-allowed"
          ></textarea>
        </div>
        <!-- Error -->
        <div v-if="errorMessage" class="p-3 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>
        <!-- Botones -->
        <div class="flex justify-end space-x-4">
          <button 
            type="button" 
            @click="closeModal" 
            :disabled="loading"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-sm
                  disabled:opacity-70 disabled:cursor-not-allowed"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            :disabled="loading"
            class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded hover:bg-green-700 dark:hover:bg-green-600 transition text-sm
                  disabled:opacity-70 disabled:cursor-not-allowed
                  flex items-center justify-center min-w-[120px]"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Guardando...</span>
            <span v-else>Guardar Cambios</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { updateProject } from '@/service/projectService';
import { getEmployees } from '@/service/employeeService';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['close', 'updated']);

const form = ref({
  responsable_id: '',
  nombre_proyecto: '',
  estado: '',
  fecha_inicio: '',
  fecha_fin: '',
  descripcion: '',
});
const errorMessage = ref('');
const employees = ref<any[]>([]);
const loading = ref(false);
const employeesLoading = ref(true);

const initializeForm = () => {
  form.value = {
    responsable_id: props.project.responsable_id,
    nombre_proyecto: props.project.nombre_proyecto, 
    estado: props.project.estado,
    fecha_inicio: props.project.fecha_inicio,
    fecha_fin: props.project.fecha_fin,
    descripcion: props.project.descripcion,
  };
};

onMounted(async () => {
  initializeForm();
  await fetchEmployees();
});

watch(() => props.project, () => {
  initializeForm();
});

const fetchEmployees = async () => {
  employeesLoading.value = true;
  errorMessage.value = '';
  
  try {
    const startTime = Date.now();
    const data = await getEmployees();
    
    // Garantizar tiempo mínimo de carga para feedback visual
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 300) {
      await new Promise(resolve => setTimeout(resolve, 300 - elapsedTime));
    }
    
    employees.value = data;
  } catch (error: any) {
    errorMessage.value = error.message || 'Error al cargar empleados. Inténtalo de nuevo.';
    console.error('Error fetching employees:', error);
  } finally {
    employeesLoading.value = false;
  }
};

const closeModal = () => {
  if (loading.value) return;
  emit('close');
};

const handleSubmit = async () => {
  if (loading.value) return;
  
  loading.value = true;
  errorMessage.value = '';
  
  try {
    const startTime = Date.now();
    
    await updateProject(props.project.proyectos_id, form.value);
    
    // Garantizar tiempo mínimo de carga para feedback visual
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    emit('updated');
    closeModal();
  } catch (error: any) {
    console.error('Error updating project:', error);
    errorMessage.value = error.message || 'Error al actualizar proyecto. Inténtalo de nuevo.';
  } finally {
    loading.value = false;
  }
};
</script>
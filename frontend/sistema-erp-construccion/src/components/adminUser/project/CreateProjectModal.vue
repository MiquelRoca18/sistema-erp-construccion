<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 dark:from-green-700 dark:to-green-600 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Crear Nuevo Proyecto</h2>
          <button 
            @click="closeModal" 
            class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300"
            :disabled="loading"
          >&times;</button>
        </div>
      </div>
      
      <!-- Loader para carga de empleados -->
      <div v-if="employeesLoading" class="p-6 flex flex-col items-center justify-center">
        <div class="w-12 h-12 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600 dark:text-gray-300">Cargando datos...</p>
      </div>
      
      <!-- Formulario -->
      <form v-else @submit.prevent="handleSubmit" class="p-6 space-y-6">
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
        <!-- Responsable (Dropdown) -->
        <div class="mb-4">
          <label class="block text-gray-700 dark:text-gray-200">Responsable</label>
          <div class="relative">
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
        </div>
        
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 border border-red-300 dark:border-red-700 rounded-lg flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span>{{ errorMessage }}</span>
        </div>
        
        <!-- Botones de acción -->
        <div class="flex justify-end space-x-2">
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
            :disabled="loading || !isFormValid"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm dark:bg-green-500 dark:hover:bg-green-600
                  disabled:opacity-70 disabled:cursor-not-allowed 
                  flex items-center justify-center min-w-[80px]"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="loading">Creando...</span>
            <span v-else>Crear</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
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
const employeesLoading = ref(true);
const fecha_inicio = ref('');
const fecha_fin = ref('');

const emit = defineEmits(['close', 'created']);

const closeModal = () => {
  if (loading.value) return;
  emit('close');
};

const isFormValid = computed(() => {
  return form.value.nombre_proyecto && form.value.descripcion && form.value.responsable_id;
});

const handleSubmit = async () => {
  if (isFormValid.value && !loading.value) {
    loading.value = true;
    errorMessage.value = '';
    
    try {
      // Mostrar loader por al menos 500ms para feedback visual
      const startTime = Date.now();
      
      // Crear objeto proyecto con todas las propiedades requeridas
      const proyecto = {
        nombre_proyecto: form.value.nombre_proyecto,
        descripcion: form.value.descripcion,
        responsable_id: form.value.responsable_id,  
        fecha_inicio: fecha_inicio.value || new Date().toISOString().split('T')[0], // Fecha actual si no está definida
        fecha_fin: fecha_fin.value || null // null si no está definida aún
      };
      
      await createProject(proyecto);
      
      // Garantizar tiempo mínimo de carga para feedback visual
      const elapsedTime = Date.now() - startTime;
      if (elapsedTime < 500) {
        await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
      }
      
      emit('created');
      closeModal();
    } catch (error: any) {
      errorMessage.value = error.message || 'Error al crear el proyecto. Inténtalo de nuevo.';
      console.error('Error creating project:', error);
    } finally {
      loading.value = false;
    }
  }
};

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

onMounted(() => {
  fetchEmployees();
});
</script>
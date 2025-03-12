<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all duration-300 max-h-[85vh] overflow-y-auto">
      <!-- Encabezado - sticky para que se mantenga visible al hacer scroll -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-3 sm:p-4 rounded-t-xl flex justify-between items-center sticky top-0 z-10">
        <h2 class="text-white text-lg sm:text-xl font-semibold">Editar Tarea</h2>
        <button @click="close" class="text-white text-2xl hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
      </div>
      
      <!-- Loader de inicialización -->
      <div v-if="initialLoading" class="p-6 sm:p-8 flex flex-col items-center justify-center">
        <div class="relative mb-4">
          <div class="h-10 w-10 sm:h-12 sm:w-12 rounded-full border-t-4 border-b-4 border-orange-500 animate-spin"></div>
          <div class="absolute top-0 left-0 h-10 w-10 sm:h-12 sm:w-12 rounded-full border-t-4 border-b-4 border-orange-200 animate-spin" style="animation-duration: 1.5s;"></div>
        </div>
        <p class="text-gray-600 dark:text-gray-300 text-sm">Cargando datos...</p>
      </div>
      
      <!-- Formulario -->
      <form v-else @submit.prevent="handleSubmit" class="space-y-3 sm:space-y-4 p-4">
        <!-- Nombre de la Tarea -->
        <div>
          <label class="block text-xs sm:text-sm text-gray-700 dark:text-gray-200">Nombre de la Tarea</label>
          <input
            type="text"
            v-model="form.nombre_tarea"
            required
            placeholder="Ingresa el nombre de la tarea"
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-xs sm:text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
            :disabled="loading || projectsLoading"
          />
        </div>
        <!-- Descripción -->
        <div>
          <label class="block text-xs sm:text-sm text-gray-700 dark:text-gray-200">Descripción</label>
          <textarea
            v-model="form.descripcion"
            required
            placeholder="Ingresa una descripción"
            rows="3"
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-xs sm:text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
            :disabled="loading || projectsLoading"
          ></textarea>
        </div>
        <!-- Estado -->
        <div>
          <label class="block text-xs sm:text-sm text-gray-700 dark:text-gray-200">Estado</label>
          <select
            v-model="form.estado"
            required
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-xs sm:text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
            :disabled="loading || projectsLoading"
          >
            <option value="pendiente" class="bg-white dark:bg-gray-700">Pendiente</option>
            <option value="en progreso" class="bg-white dark:bg-gray-700">En Progreso</option>
            <option value="finalizado" class="bg-white dark:bg-gray-700">Finalizado</option>
          </select>
        </div>
        <!-- Proyecto -->
        <div>
          <label class="block text-xs sm:text-sm text-gray-700 dark:text-gray-200">Proyecto</label>
          
          <!-- Loader de proyectos -->
          <div v-if="projectsLoading" class="flex items-center space-x-2 px-3 py-2 border rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <div class="h-4 w-4 rounded-full border-2 border-t-orange-500 border-r-orange-500 border-b-gray-200 border-l-gray-200 animate-spin"></div>
            <span class="text-gray-600 dark:text-gray-300 text-xs sm:text-sm">Cargando proyectos...</span>
          </div>
          
          <select
            v-else
            v-model="form.proyectos_id"
            required
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-xs sm:text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
            :disabled="loading"
          >
            <option value="" disabled class="bg-white dark:bg-gray-700">Selecciona un proyecto</option>
            <option 
              v-for="project in projects" 
              :key="project.proyectos_id" 
              :value="project.proyectos_id"
              class="bg-white dark:bg-gray-700"
            >
              {{ project.nombre_proyecto }}
            </option>
          </select>
        </div>
        <!-- Fechas -->
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs sm:text-sm text-gray-700 dark:text-gray-200">Inicio</label>
            <input
              type="date"
              v-model="form.fecha_inicio"
              required
              class="w-full px-2 sm:px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-xs sm:text-sm
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600"
              :disabled="loading || projectsLoading"
            />
          </div>
          <div>
            <label class="block text-xs sm:text-sm text-gray-700 dark:text-gray-200">Fin</label>
            <input
              type="date"
              v-model="form.fecha_fin"
              class="w-full px-2 sm:px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-xs sm:text-sm
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600"
              :disabled="loading || projectsLoading"
            />
          </div>
        </div>
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-2 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-md text-red-600 dark:text-red-300 text-xs flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <span class="text-xs">{{ errorMessage }}</span>
        </div>
        <!-- Botones -->
        <div class="flex justify-end space-x-2 pt-2">
          <button
            type="button"
            @click="close"
            class="px-2 sm:px-3 py-1.5 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-xs"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-2 sm:px-3 py-1.5 sm:py-2 bg-orange-600 dark:bg-orange-500 text-white rounded hover:bg-orange-700 dark:hover:bg-orange-600 transition text-xs flex items-center justify-center min-w-[70px] sm:min-w-[80px]"
            :disabled="loading || projectsLoading || initialLoading"
          >
            <span v-if="loading">
              <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-xs">Guardando...</span>
            </span>
            <span v-else class="text-xs">Guardar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { updateTask } from '@/service/taskService';
import { getProjects } from '@/service/projectService';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'updated', 'showSuccess', 'showError']);

const form = ref({
  nombre_tarea: '',
  descripcion: '',
  estado: '',
  proyectos_id: '',
  fecha_inicio: '',
  fecha_fin: ''
});

const errorMessage = ref('');
const loading = ref(false);
const initialLoading = ref(true);
const projectsLoading = ref(true);
const projects = ref<any[]>([]);

watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      initialLoading.value = true;
      // Simular un tiempo mínimo de carga para mejorar UX
      setTimeout(() => {
        form.value = {
          nombre_tarea: newTask.nombre_tarea || '',
          descripcion: newTask.descripcion || '',
          estado: newTask.estado || '',
          proyectos_id: newTask.proyectos_id || '',
          fecha_inicio: newTask.fecha_inicio || '',
          fecha_fin: newTask.fecha_fin || ''
        };
        initialLoading.value = false;
      }, 300);
    }
  },
  { immediate: true }
);

const handleSubmit = async () => {
  if (loading.value) return;
  
  try {
    loading.value = true;
    errorMessage.value = '';
    
    const payload = {
      ...form.value,
      proyectos_id: Number(form.value.proyectos_id)
    };
    
    await updateTask(props.task.tareas_id, payload);
    emit('updated');
    emit('showSuccess', `Tarea ${form.value.nombre_tarea} actualizada exitosamente`);
    close();
  } catch (error: any) {
    console.error('Error al actualizar la tarea:', error.message);
    errorMessage.value = error.message || 'Error al actualizar la tarea';
  } finally {
    loading.value = false;
  }
};

const close = () => {
  emit('close');
};

onMounted(async () => {
  try {
    projectsLoading.value = true;
    errorMessage.value = '';
    
    // Simular un tiempo mínimo de carga para mejorar UX
    const startTime = Date.now();
    const data = await getProjects();
    
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 300) {
      await new Promise(resolve => setTimeout(resolve, 300 - elapsedTime));
    }
    
    projects.value = data;
  } catch (error: any) {
    console.error('Error al cargar proyectos:', error.message);
    errorMessage.value = `Error al cargar proyectos: ${error.message || 'Error desconocido'}`;
  } finally {
    projectsLoading.value = false;
  }
});
</script>
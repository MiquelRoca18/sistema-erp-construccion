<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-4">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Crear Nueva Tarea</h2>
          <button @click="close" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Nombre de la Tarea -->
        <div>
          <label for="nombre_tarea" class="block text-gray-700 dark:text-gray-200 mb-2">Nombre de la Tarea</label>
          <input
            type="text"
            id="nombre_tarea"
            v-model="form.nombre_tarea"
            required
            placeholder="Ingresa el nombre de la tarea"
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
            :disabled="loading || projectsLoading"
          />
        </div>
        <!-- Descripción -->
        <div>
          <label for="descripcion" class="block text-gray-700 dark:text-gray-200 mb-2">Descripción</label>
          <textarea
            id="descripcion"
            v-model="form.descripcion"
            required
            placeholder="Ingresa una descripción de la tarea"
            rows="4"
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
            :disabled="loading || projectsLoading"
          ></textarea>
        </div>
        <!-- Selección de Proyecto -->
        <div>
          <label for="proyecto" class="block text-gray-700 dark:text-gray-200 mb-2">Proyecto</label>
          
          <!-- Loader para proyectos -->
          <div v-if="projectsLoading" class="flex items-center space-x-2 p-3 border rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
            <div class="h-4 w-4 rounded-full border-2 border-t-orange-500 border-r-orange-500 border-b-gray-200 border-l-gray-200 animate-spin"></div>
            <span class="text-gray-600 dark:text-gray-300">Cargando proyectos...</span>
          </div>
          
          <select
            v-else
            id="proyecto"
            v-model="form.proyectos_id"
            required
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400
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
        
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-300 text-sm">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ errorMessage }}
          </div>
        </div>
        
        <!-- Acciones -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition dark:bg-orange-500 dark:hover:bg-orange-600 flex items-center justify-center min-w-[90px]"
            :disabled="loading || projectsLoading"
          >
            <span v-if="loading">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Creando...
            </span>
            <span v-else>Crear</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { createTask } from '@/service/taskService';
import { getProjects } from '@/service/projectService';

const emit = defineEmits(['close', 'created', 'showSuccess', 'showError']);

const form = ref({
  nombre_tarea: '',
  descripcion: '',
  proyectos_id: '' 
});

const projects = ref([]);
const loading = ref(false);
const projectsLoading = ref(true);
const errorMessage = ref('');

const closeModal = () => {
  emit('close');
};

const handleSubmit = async () => {
  if (loading.value) return;
  
  try {
    loading.value = true;
    errorMessage.value = '';
    await createTask(form.value);
    emit('created');
    emit('showSuccess', `Tarea ${form.value.nombre_tarea} creada exitosamente`);
    closeModal();
  } catch (error: any) {
    console.error('Error al crear la tarea:', error.message);
    errorMessage.value = error.message || 'Error al crear la tarea. Inténtelo de nuevo.';
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
    
    // Añadir un pequeño retraso para evitar parpadeos en cargas rápidas
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
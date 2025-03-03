<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado -->
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
          ></textarea>
        </div>
        <!-- Selección de Proyecto -->
        <div>
          <label for="proyecto" class="block text-gray-700 dark:text-gray-200 mb-2">Proyecto</label>
          <select
            id="proyecto"
            v-model="form.proyectos_id"
            required
            class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
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
        <!-- Acciones -->
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition dark:bg-orange-500 dark:hover:bg-orange-600"
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
import { createTask } from '@/service/taskService';
import { getProjects } from '@/service/projectService';

const emit = defineEmits(['close', 'created']);

const form = ref({
  nombre_tarea: '',
  descripcion: '',
  proyectos_id: '' // Se mantiene como cadena, se convertirá en número en el submit
});

const projects = ref([]);

const closeModal = () => {
  emit('close');
};

const handleSubmit = async () => {
  try {
    await createTask(form.value);
    emit('created');
    closeModal();
  } catch (error: any) {
    console.error('Error al crear la tarea:', error.message);
  }
};

const close = () => {
  emit('close');
};

onMounted(async () => {
  try {
    projects.value = await getProjects();
  } catch (error: any) {
    console.error('Error al cargar proyectos:', error.message);
  }
});
</script>

<style scoped>
/* Puedes agregar estilos o animaciones adicionales si lo consideras necesario */
</style>
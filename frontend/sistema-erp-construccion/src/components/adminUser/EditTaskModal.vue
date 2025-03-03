<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-[95%] max-w-md overflow-hidden transform transition-all duration-300 max-h-[80vh] overflow-y-auto">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-700 dark:to-orange-600 p-4 rounded-t-xl flex justify-between items-center">
        <h2 class="text-white text-xl font-semibold">Editar Tarea</h2>
        <button @click="close" class="text-white text-2xl hover:text-gray-200 dark:hover:text-gray-300">&times;</button>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="space-y-4 p-4">
        <!-- Nombre de la Tarea -->
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-200">Nombre de la Tarea</label>
          <input
            type="text"
            v-model="form.nombre_tarea"
            required
            placeholder="Ingresa el nombre de la tarea"
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          />
        </div>
        <!-- Descripción -->
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-200">Descripción</label>
          <textarea
            v-model="form.descripcion"
            required
            placeholder="Ingresa una descripción"
            rows="3"
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          ></textarea>
        </div>
        <!-- Estado -->
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-200">Estado</label>
          <select
            v-model="form.estado"
            required
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-sm
                   bg-white dark:bg-gray-700 
                   text-gray-900 dark:text-gray-100 
                   border-gray-300 dark:border-gray-600"
          >
            <option value="pendiente" class="bg-white dark:bg-gray-700">Pendiente</option>
            <option value="en progreso" class="bg-white dark:bg-gray-700">En Progreso</option>
            <option value="finalizado" class="bg-white dark:bg-gray-700">Finalizado</option>
          </select>
        </div>
        <!-- Proyecto -->
        <div>
          <label class="block text-sm text-gray-700 dark:text-gray-200">Proyecto</label>
          <select
            v-model="form.proyectos_id"
            required
            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-sm
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
        <!-- Fechas -->
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-200">Inicio</label>
            <input
              type="date"
              v-model="form.fecha_inicio"
              required
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-sm
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-700 dark:text-gray-200">Fin</label>
            <input
              type="date"
              v-model="form.fecha_fin"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-orange-400 text-sm
                     bg-white dark:bg-gray-700 
                     text-gray-900 dark:text-gray-100 
                     border-gray-300 dark:border-gray-600"
            />
          </div>
        </div>
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 dark:bg-red-900 border border-red-300 dark:border-red-700 rounded-md text-red-700 dark:text-red-200 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones -->
        <div class="flex justify-end space-x-2">
          <button
            type="button"
            @click="close"
            class="px-3 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition text-xs"
          >
            Cancelar
          </button>
          <button
            type="submit"
            class="px-3 py-2 bg-orange-600 dark:bg-orange-500 text-white rounded hover:bg-orange-700 dark:hover:bg-orange-600 transition text-xs"
          >
            Guardar
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

const emit = defineEmits(['close', 'updated']);

const form = ref({
  nombre_tarea: '',
  descripcion: '',
  estado: '',
  proyectos_id: '',
  fecha_inicio: '',
  fecha_fin: ''
});

const errorMessage = ref('');

watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      form.value = {
        nombre_tarea: newTask.nombre_tarea || '',
        descripcion: newTask.descripcion || '',
        estado: newTask.estado || '',
        proyectos_id: newTask.proyectos_id || '',
        fecha_inicio: newTask.fecha_inicio || '',
        fecha_fin: newTask.fecha_fin || ''
      };
    }
  },
  { immediate: true }
);

const projects = ref<any[]>([]);

const handleSubmit = async () => {
  try {
    errorMessage.value = '';
    const payload = {
      ...form.value,
      proyectos_id: Number(form.value.proyectos_id)
    };
    await updateTask(props.task.tareas_id, payload);
    emit('updated');
    close();
  } catch (error: any) {
    console.error('Error al actualizar la tarea:', error.message);
    errorMessage.value = error.message || 'Error al actualizar la tarea';
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
/* Se mantiene un estilo compacto y legible sin responsive adicional */
</style>
<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado verde -->
      <div class="bg-gradient-to-r from-green-500 to-green-400 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Editar Proyecto</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Responsable -->
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
        <!-- Estado -->
        <div class="mb-4">
          <label class="block text-gray-700">Estado</label>
          <select 
            v-model="form.estado" 
            required 
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400"
          >
            <option value="pendiente">Pendiente</option>
            <option value="en progreso">En progreso</option>
            <option value="finalizado">Finalizado</option>
          </select>
        </div>
        <!-- Fecha de Inicio -->
        <div class="mb-4">
          <label class="block text-gray-700">Fecha de Inicio</label>
          <input 
            v-model="form.fecha_inicio" 
            type="date" 
            required 
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-400" 
          />
        </div>
        <!-- Fecha de Finalización -->
        <div class="mb-4">
          <label class="block text-gray-700">Fecha de Finalización</label>
          <input 
            v-model="form.fecha_fin" 
            type="date"
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
        <!-- Error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 border border-red-300 rounded-md text-red-700 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones -->
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
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm"
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
  try {
    const data = await getEmployees();
    employees.value = data;
  } catch (error: any) {
    console.error(error.message);
  }
});
watch(() => props.project, () => {
  initializeForm();
});

const closeModal = () => {
  emit('close');
};

const handleSubmit = async () => {
  errorMessage.value = '';
  try {
    await updateProject(props.project.proyectos_id, form.value);
    emit('updated');
    closeModal();
  } catch (error: any) {
    console.error(error.message);
    errorMessage.value = error.message || 'Error al actualizar proyecto';
  }
};
</script>

<style scoped>
/* Puedes ajustar estilos adicionales si es necesario */
</style>

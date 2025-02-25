<template>
  <div class="fixed inset-0 flex items-center justify-center bg-black/50 bg-opacity-50 z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 sm:w-full max-w-md">
      <h2 class="text-2xl font-bold mb-4">Editar Proyecto</h2>
      <form @submit.prevent="handleSubmit">
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
        <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
          {{ errorMessage }}
        </div>
        <!-- Botones -->
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
    console.log(form.value);
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

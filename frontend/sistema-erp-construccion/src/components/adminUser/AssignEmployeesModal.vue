<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white rounded-lg shadow-lg p-6 w-11/12 sm:w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Asignar Empleados a la Tarea</h2>
        <form @submit.prevent="handleSubmit">
          <!-- Selección de Empleados -->
          <div class="mb-4">
            <label class="block text-gray-700 mb-2">Seleccione Empleados</label>
            <select 
              v-model="form.empleados_ids" 
              multiple 
              required
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
            >
              <option 
                v-for="employee in employees" 
                :key="employee.empleados_id" 
                :value="employee.empleados_id"
              >
                {{ employee.nombre }}
              </option>
            </select>
            <p class="text-sm text-gray-600 mt-1">
              Mantén presionada Ctrl (Cmd en Mac) para seleccionar múltiples.
            </p>
          </div>
          <!-- Mensaje de Error -->
          <div v-if="errorMessage" class="mb-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
            {{ errorMessage }}
          </div>
          <!-- Botones de Acción -->
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
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            >
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { getEmployees } from '@/service/employeeService';
  // Si tienes la función de asignar empleados, impórtala aquí, por ejemplo:
  // import { assignEmployeesToTask } from '@/service/taskService';
  
  const props = defineProps({
    task: {
      type: Object,
      required: true
    }
  });
  
  const emit = defineEmits(['close', 'updated']);
  
  const form = ref({
    // Inicializa con los empleados asignados, si existen, o con un arreglo vacío.
    empleados_ids: props.task.empleados_ids || []
  });
  
  const employees = ref<any[]>([]);
  const errorMessage = ref('');
  
  const fetchEmployees = async () => {
    try {
      employees.value = await getEmployees();
    } catch (error: any) {
      console.error('Error al cargar empleados:', error.message);
      errorMessage.value = error.message || 'Error al cargar empleados';
    }
  };
  
  onMounted(() => {
    fetchEmployees();
  });
  
  const handleSubmit = async () => {
    errorMessage.value = '';
    try {
      // Aquí llamarías a la función del servicio para asignar empleados.
      // Por ejemplo:
      // await assignEmployeesToTask(props.task.tareas_id, form.value.empleados_ids);
      console.log('Asignando empleados', form.value.empleados_ids, 'a la tarea', props.task.tareas_id);
      emit('updated');
      closeModal();
    } catch (error: any) {
      console.error('Error al asignar empleados:', error.message);
      errorMessage.value = error.message || 'Error al asignar empleados';
    }
  };
  
  const closeModal = () => {
    emit('close');
  };
  </script>
  
  <style scoped>
  /* Puedes agregar estilos adicionales aquí si lo consideras necesario */
  </style>
  
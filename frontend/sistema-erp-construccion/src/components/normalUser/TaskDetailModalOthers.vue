<template>
  <div class="fixed inset-0 flex items-center justify-center z-50 px-4">
    <!-- Fondo semi-transparente -->
    <div class="fixed inset-0 bg-black opacity-50 dark:opacity-70" @click="close"></div>

    <!-- Contenedor del modal -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl z-50 p-6 w-full max-w-2xl mx-auto transform transition-all duration-300">
      <!-- Header del modal -->
      <div class="mb-4 border-b dark:border-gray-700 pb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Detalles de la Tarea (Otros)</h2>
        <button @click="close" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      
      <!-- Mensaje de error si existe -->
      <div v-if="errorMessage" class="mb-4 p-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
        {{ errorMessage }}
      </div>
  
      <!-- Contenido principal -->
      <div class="space-y-6">
        <!-- Tarea y Estado -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tarea</label>
            <input
              type="text"
              v-model="updatedNombreTarea"
              class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
          </div>
          <div class="mt-4 md:mt-0 w-full md:w-1/3">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Estado</label>
            <select
              v-model="updatedStatus"
              class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            >
              <option value="pendiente" class="bg-white dark:bg-gray-700">Pendiente</option>
              <option value="en progreso" class="bg-white dark:bg-gray-700">En progreso</option>
              <option value="finalizado" class="bg-white dark:bg-gray-700">Finalizado</option>
            </select>
          </div>
        </div>
  
        <!-- Descripción -->
        <div v-if="task.descripcion !== undefined">
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Descripción</label>
          <textarea
            v-model="updatedDescripcion"
            class="mt-1 block w-full p-2 border border-gray-200 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 text-base max-h-32 overflow-y-auto focus:ring-blue-500 focus:border-blue-500
                   bg-white dark:bg-gray-700"
            rows="4"
          ></textarea>
        </div>
  
        <!-- Proyecto -->
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Proyecto</label>
          <p class="mt-1 text-lg font-semibold text-gray-800 dark:text-gray-200">{{ task.nombre_proyecto }}</p>
        </div>
  
        <!-- Fechas -->
        <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Fecha de Inicio</label>
            <input
              type="date"
              v-model="updatedFechaInicio"
              class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Fecha de Fin</label>
            <input
              type="date"
              v-model="updatedFechaFin"
              class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                     bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            />
          </div>
        </div>
  
        <!-- Empleado asignado (editable con select) -->
        <div>
          <label class="block text-sm font-medium text-gray-600 dark:text-gray-300">Asignado a</label>
          <select
            v-model="selectedEmployeeId"
            class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                   bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          >
            <option disabled value="" class="bg-white dark:bg-gray-700">Seleccione un empleado</option>
            <option 
              v-for="emp in employees" 
              :key="emp.empleados_id" 
              :value="emp.empleados_id"
              class="bg-white dark:bg-gray-700"
            >
              {{ emp.nombre }}
            </option>
          </select>
        </div>
      </div>
  
      <!-- Footer -->
      <div class="mt-6 flex justify-end">
        <button 
          @click="close" 
          class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 mr-2"
        >
          Cancelar
        </button>
        <button 
          @click="updateTask" 
          class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600"
        >
          Guardar Cambios
        </button>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref, watch, onMounted } from 'vue';
import { updateTask as updateTaskService } from '@/service/taskService';
import { updateTaskAssignment } from '@/service/taskService';
import { getEmployees } from '@/service/employeeService';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['close', 'update']);

const updatedNombreTarea = ref(props.task.nombre_tarea);
const updatedStatus = ref(props.task.estado);
const updatedDescripcion = ref(props.task.descripcion);
const updatedFechaInicio = ref(props.task.fecha_inicio);
const updatedFechaFin = ref(props.task.fecha_fin);

// Inicializamos el select con el empleado asignado (empleados_id)
// Si no está definido, intentamos obtenerlo usando nombre_empleado
const selectedEmployeeId = ref(props.task.empleados_id);
const employees = ref([]);
const errorMessage = ref('');

onMounted(async () => {
  try {
    employees.value = await getEmployees();
    // Si no existe empleados_id pero existe nombre_empleado, buscamos en la lista
    if (!props.task.empleados_id && props.task.nombre_empleado) {
      const emp = employees.value.find(e => e.nombre === props.task.nombre_empleado);
      if (emp) {
        selectedEmployeeId.value = emp.empleados_id;
      }
    }
  } catch (error) {
    console.error("Error al obtener empleados:", error);
  }
});

watch(() => props.task, (newTask) => {
  updatedNombreTarea.value = newTask.nombre_tarea;
  updatedStatus.value = newTask.estado;
  updatedDescripcion.value = newTask.descripcion;
  updatedFechaInicio.value = newTask.fecha_inicio;
  updatedFechaFin.value = newTask.fecha_fin;
  // Actualizar el select con el empleado asignado
  if (newTask.empleados_id) {
    selectedEmployeeId.value = newTask.empleados_id;
  } else if (newTask.nombre_empleado) {
    const emp = employees.value.find(e => e.nombre === newTask.nombre_empleado);
    if (emp) {
      selectedEmployeeId.value = emp.empleados_id;
    }
  }
  errorMessage.value = '';
});

const close = () => {
  emit('close');
};

const updateTask = async () => {
  try {
    errorMessage.value = '';
    const taskData = {
      nombre_tarea: updatedNombreTarea.value,
      estado: updatedStatus.value,
      descripcion: updatedDescripcion.value,
      fecha_inicio: updatedFechaInicio.value,
      fecha_fin: updatedFechaFin.value
    };
    await updateTaskService(props.task.tareas_id, taskData);
    
    // Si la asignación ha cambiado, enviar los IDs correspondientes
    if (selectedEmployeeId.value !== props.task.empleados_id) {
      await updateTaskAssignment(props.task.tareas_id, props.task.empleados_id, selectedEmployeeId.value);
    }
    
    const updatedTask = { ...props.task, ...taskData, empleados_id: selectedEmployeeId.value };
    const emp = employees.value.find(e => e.empleados_id === selectedEmployeeId.value);
    if (emp) {
      updatedTask.nombre_empleado = emp.nombre;
    }
    emit('update', updatedTask);
    close();
  } catch (error) {
    console.error('Error al actualizar la tarea:', error);
    errorMessage.value = error.message || 'Error al actualizar la tarea';
  }
};
</script>
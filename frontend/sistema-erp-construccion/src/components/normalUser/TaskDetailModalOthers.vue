<template>
  <div class="fixed inset-0 flex items-center justify-center z-50 px-3 py-4 sm:px-4">
    <!-- Fondo semi-transparente -->
    <div class="fixed inset-0 bg-black opacity-50 dark:opacity-70" @click="close"></div>

    <!-- Contenedor del modal -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl z-50 p-3 sm:p-4 md:p-6 w-full max-w-md sm:max-w-lg md:max-w-2xl mx-auto transform transition-all duration-300">
      <!-- Loader para carga inicial de datos -->
      <div v-if="isLoading" class="py-8 sm:py-10 flex flex-col items-center justify-center">
        <div class="w-12 h-12 sm:w-16 sm:h-16 mb-3 sm:mb-4">
          <svg class="animate-spin h-full w-full text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
        <p class="text-base sm:text-lg text-gray-500 dark:text-gray-400">Cargando detalles...</p>
      </div>

      <!-- Contenido del modal -->
      <div v-else>
        <!-- Header del modal -->
        <div class="mb-3 sm:mb-4 border-b dark:border-gray-700 pb-3 sm:pb-4 flex justify-between items-center">
          <h2 class="text-lg sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Detalles de la Tarea (Otros)</h2>
          <button @click="close" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Mensaje de error si existe -->
        <div v-if="errorMessage" class="mb-3 sm:mb-4 p-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded text-xs sm:text-sm">
          {{ errorMessage }}
        </div>
    
        <!-- Contenido principal -->
        <div class="space-y-3 sm:space-y-6">
          <!-- Tarea y Estado -->
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
            <div class="flex-1">
              <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Tarea</label>
              <input
                type="text"
                v-model="updatedNombreTarea"
                class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                      bg-white dark:bg-gray-700 text-xs sm:text-base text-gray-900 dark:text-gray-100"
                :disabled="isSaving"
              />
            </div>
            <div class="w-full md:w-1/3">
              <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Estado</label>
              <select
                v-model="updatedStatus"
                class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                      bg-white dark:bg-gray-700 text-xs sm:text-base text-gray-900 dark:text-gray-100"
                :disabled="isSaving"
              >
                <option value="pendiente" class="bg-white dark:bg-gray-700">Pendiente</option>
                <option value="en progreso" class="bg-white dark:bg-gray-700">En progreso</option>
                <option value="finalizado" class="bg-white dark:bg-gray-700">Finalizado</option>
              </select>
            </div>
          </div>
    
          <!-- Descripción -->
          <div v-if="task.descripcion !== undefined">
            <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Descripción</label>
            <textarea
              v-model="updatedDescripcion"
              class="mt-1 block w-full p-2 border border-gray-200 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-200 text-xs sm:text-base max-h-24 sm:max-h-32 overflow-y-auto focus:ring-blue-500 focus:border-blue-500
                    bg-white dark:bg-gray-700"
              rows="3"
              :disabled="isSaving"
            ></textarea>
          </div>
    
          <!-- Proyecto -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Proyecto</label>
            <p class="mt-1 text-sm sm:text-lg font-semibold text-gray-800 dark:text-gray-200">{{ task.nombre_proyecto }}</p>
          </div>
    
          <!-- Fechas -->
          <div class="flex flex-col sm:flex-row sm:justify-between gap-3 sm:gap-4">
            <div class="flex-1">
              <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Fecha de Inicio</label>
              <input
                type="date"
                v-model="updatedFechaInicio"
                class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                      bg-white dark:bg-gray-700 text-xs sm:text-base text-gray-900 dark:text-gray-100"
                :disabled="isSaving"
              />
            </div>
            <div class="flex-1">
              <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Fecha de Fin</label>
              <input
                type="date"
                v-model="updatedFechaFin"
                class="mt-1 block w-full p-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500
                      bg-white dark:bg-gray-700 text-xs sm:text-base text-gray-900 dark:text-gray-100"
                :disabled="isSaving"
              />
            </div>
          </div>
    
          <!-- Empleado asignado (Modificado para mostrar solo empleados asignados) -->
          <div>
            <label class="block text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Empleados asignados</label>
            
            <!-- Loader mientras se cargan los empleados -->
            <div v-if="loadingEmployees" class="h-8 sm:h-10 flex items-center pl-2">
              <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Cargando empleados...</span>
            </div>
            
            <!-- Vista de solo los empleados asignados a la tarea -->
            <div v-else>
              <div class="mt-1 flex flex-wrap gap-1">
                <span 
                  v-for="employeeName in assignedEmployees" 
                  :key="employeeName"
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-orange-300 text-orange-800 dark:bg-orange-500 dark:text-orange-100 font-medium"
                >
                  {{ employeeName }}
                </span>
                <span v-if="assignedEmployees.length === 0" class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm italic">
                  No hay empleados asignados a esta tarea
                </span>
              </div>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Esta tarea está asignada a los empleados mostrados arriba
              </p>
            </div>
          </div>
        </div>
    
        <!-- Footer -->
        <div class="mt-4 sm:mt-6 flex justify-end">
          <button 
            @click="close" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 mr-2 text-xs sm:text-sm"
            :disabled="isSaving"
          >
            Cancelar
          </button>
          <button 
            @click="updateTask" 
            class="px-3 py-1.5 sm:px-4 sm:py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 flex items-center justify-center min-w-[80px] sm:min-w-[120px] text-xs sm:text-sm"
            :disabled="isSaving"
          >
            <template v-if="isSaving">
              <svg class="animate-spin -ml-1 mr-1 sm:mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>Guardando...</span>
            </template>
            <template v-else>
              Guardar Cambios
            </template>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import { updateTask as updateTaskService } from '@/service/taskService';
import { getEmployees } from '@/service/employeeService';

const props = defineProps({
  task: {
    type: Object,
    required: true,
  },
});
const emit = defineEmits(['close', 'update']);

// Estado de los formularios
const updatedNombreTarea = ref(props.task.nombre_tarea);
const updatedStatus = ref(props.task.estado);
const updatedDescripcion = ref(props.task.descripcion);
const updatedFechaInicio = ref(props.task.fecha_inicio);
const updatedFechaFin = ref(props.task.fecha_fin);

// Estados de carga
const isLoading = ref(true);
const loadingEmployees = ref(true);
const isSaving = ref(false);
const errorMessage = ref('');

// Arreglo para guardar los nombres de los empleados asignados
const assignedEmployees = computed(() => {
  if (props.task.nombre_empleado) {
    return props.task.nombre_empleado.split(',').map(name => name.trim()).filter(name => name !== '');
  }
  return [];
});

onMounted(async () => {
  try {
    // Simular carga inicial del modal
    setTimeout(() => {
      isLoading.value = false;
    }, 300);
    
    // Cargar los empleados (podría ser necesario para otras funcionalidades)
    loadingEmployees.value = true;
    await getEmployees();
  } catch (error) {
    console.error("Error al obtener empleados:", error);
    errorMessage.value = "Error al cargar la lista de empleados.";
  } finally {
    loadingEmployees.value = false;
  }
});

watch(() => props.task, (newTask) => {
  updatedNombreTarea.value = newTask.nombre_tarea;
  updatedStatus.value = newTask.estado;
  updatedDescripcion.value = newTask.descripcion;
  updatedFechaInicio.value = newTask.fecha_inicio;
  updatedFechaFin.value = newTask.fecha_fin;
  errorMessage.value = '';
});

const close = () => {
  if (isSaving.value) return;
  emit('close');
};

const updateTask = async () => {
  if (isSaving.value) return;
  
  try {
    isSaving.value = true;
    errorMessage.value = '';
    
    const taskData = {
      nombre_tarea: updatedNombreTarea.value,
      estado: updatedStatus.value,
      descripcion: updatedDescripcion.value,
      fecha_inicio: updatedFechaInicio.value,
      fecha_fin: updatedFechaFin.value
    };
    
    await updateTaskService(props.task.tareas_id, taskData);
    
    const updatedTask = { 
      ...props.task, 
      ...taskData, 
      // Mantener el nombre_empleado original ya que no estamos modificando las asignaciones
      nombre_empleado: props.task.nombre_empleado 
    };
    
    emit('update', updatedTask);
    close();
  } catch (error) {
    console.error('Error al actualizar la tarea:', error);
    errorMessage.value = error.message || 'Error al actualizar la tarea';
  } finally {
    isSaving.value = false;
  }
};
</script>

<style scoped>
/* Animación para el spinner */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
.animate-spin {
  animation: spin 1s linear infinite;
}

/* Ajuste para dispositivos móviles pequeños */
@media (max-width: 360px) {
  input, select, textarea {
    font-size: 0.75rem;
  }
  .text-xs {
    font-size: 0.65rem;
  }
}
</style>
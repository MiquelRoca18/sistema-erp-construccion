<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 dark:bg-black/70 transition-colors duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl dark:shadow-gray-900/50 max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 dark:from-orange-600 dark:to-orange-500 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Asignar Empleados a la Tarea</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200 dark:hover:text-gray-300" :disabled="loading">&times;</button>
        </div>
      </div>
      
      <!-- Loader principal -->
      <div v-if="initialLoading" class="p-8 flex flex-col items-center justify-center">
        <div class="relative mb-4">
          <div class="h-12 w-12 rounded-full border-t-4 border-b-4 border-blue-500 animate-spin"></div>
          <div class="absolute top-0 left-0 h-12 w-12 rounded-full border-t-4 border-b-4 border-blue-200 animate-spin" style="animation-duration: 1.5s;"></div>
        </div>
        <p class="text-gray-600 dark:text-gray-300">Cargando empleados...</p>
      </div>
      
      <!-- Formulario -->
      <form v-else @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Listado de asignaciones -->
        <div>
          <label class="block text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">Empleados asignados</label>
          <div v-for="(id, index) in assignments" :key="index" class="flex items-center space-x-2 mb-3">
            <select
              v-model="assignments[index]"
              required
              class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-sm transition-colors duration-300"
              :disabled="loading"
            >
              <option value="" disabled>Seleccione un empleado</option>
              <option v-for="employee in employees" :key="employee.empleados_id" :value="employee.empleados_id">
                {{ employee.nombre }}
              </option>
            </select>
            <button 
              type="button" 
              @click="removeAssignment(index)" 
              class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-300 text-2xl transition-colors duration-300"
              :disabled="loading"
            >
              &times;
            </button>
          </div>
          <button 
            type="button" 
            @click="addAssignment" 
            class="mt-2 px-4 py-2 bg-green-500 dark:bg-green-600 text-white rounded-lg hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-sm flex items-center"
            :disabled="loading"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Añadir empleado
          </button>
        </div>
        
        <!-- Loader de guardado -->
        <div v-if="loading" class="flex justify-center items-center py-2">
          <div class="flex items-center space-x-2 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
            <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-blue-700 dark:text-blue-300">Guardando asignaciones...</span>
          </div>
        </div>
        
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-600 dark:text-red-300 text-sm">
          <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            {{ errorMessage }}
            <button @click="errorMessage = ''" class="ml-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 hover:text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        
        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-4">
          <button 
            type="button" 
            @click="closeModal" 
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors duration-300 text-sm"
            :disabled="loading"
          >
            Cancelar
          </button>
          <button 
            type="submit" 
            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300 text-sm min-w-[90px] flex items-center justify-center"
            :disabled="loading"
          >
            <span v-if="loading">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Guardando...
            </span>
            <span v-else>Guardar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { getEmployees } from '@/service/employeeService';
import { 
  addEmployeeToTask, 
  removeEmployeeFromTask, 
  updateTaskAssignment 
} from '@/service/taskService';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'updated']);

const errorMessage = ref('');
const employees = ref<any[]>([]);
const assignments = ref<number[]>([]);
const initialAssignments = ref<number[]>([]);
const loading = ref(false);
const initialLoading = ref(true);

// Observa cambios en la tarea para derivar las asignaciones a partir de "empleado_nombre"
watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
      initialLoading.value = true;
      if (newTask.empleado_nombre) {
        const names = newTask.empleado_nombre.split(',').map(n => n.trim());
        if (employees.value.length > 0) {
          const derived = names.map(name => {
            const emp = employees.value.find(e => e.nombre === name);
            return emp ? emp.empleados_id : 0;
          }).filter(id => id !== 0);
          assignments.value = derived;
          // Guardar las asignaciones originales solo una vez
          if(initialAssignments.value.length === 0) {
            initialAssignments.value = [...derived];
          }
        } else {
          assignments.value = [];
        }
      } else {
        assignments.value = [];
      }
    }
  },
  { immediate: true }
);

// Cargar empleados y derivar asignaciones si aún no se han establecido
const fetchEmployees = async () => {
  try {
    initialLoading.value = true;
    errorMessage.value = '';
    
    // Simulamos un tiempo mínimo de carga para mejorar UX
    const startTime = Date.now();
    const data = await getEmployees();
    
    // Aseguramos que el loader se muestre al menos por 500ms
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    employees.value = data;
    
    if (props.task.empleado_nombre && assignments.value.length === 0) {
      const names = props.task.empleado_nombre.split(',').map(n => n.trim());
      const derived = names.map(name => {
        const emp = employees.value.find(e => e.nombre === name);
        return emp ? emp.empleados_id : 0;
      }).filter(id => id !== 0);
      assignments.value = derived;
      if(initialAssignments.value.length === 0) {
        initialAssignments.value = [...derived];
      }
    }
  } catch (error: any) {
    console.error('Error al cargar empleados:', error.message);
    errorMessage.value = error.message || 'Error al cargar empleados';
  } finally {
    initialLoading.value = false;
  }
};

onMounted(() => {
  fetchEmployees();
});

const addAssignment = () => {
  assignments.value.push(0);
};

const removeAssignment = (index: number) => {
  assignments.value.splice(index, 1);
};

const handleSubmit = async () => {
  if (loading.value) return;
  
  errorMessage.value = '';

  // Verifica que todos los selectores tengan un empleado válido
  if (assignments.value.some(id => !id || id === 0)) {
    errorMessage.value = 'Seleccione un empleado válido para cada asignación.';
    return;
  }

  // Verificación de duplicados: no se puede asignar el mismo empleado más de una vez
  if (new Set(assignments.value).size !== assignments.value.length) {
    errorMessage.value = 'No se puede asignar el mismo empleado más de una vez.';
    return;
  }
  
  try {
    loading.value = true;
    const finalAssignments = assignments.value;
    
    // Si no hubo cambios, se cierra el modal
    if (JSON.stringify(finalAssignments.slice().sort()) === JSON.stringify(initialAssignments.value.slice().sort())) {
      emit('updated');
      closeModal();
      return;
    }
    
    // Simulamos un tiempo mínimo de carga para mejorar UX
    const startTime = Date.now();
    
    // Si el número de asignaciones es el mismo, y solo hay un cambio, se utiliza updateTaskAssignment
    if (initialAssignments.value.length === finalAssignments.length) {
      const differences = initialAssignments.value.filter((id, i) => id !== finalAssignments[i]);
      if(differences.length === 1) {
        const indexChanged = initialAssignments.value.findIndex((id, i) => id !== finalAssignments[i]);
        const oldEmployeeId = initialAssignments.value[indexChanged];
        const newEmployeeId = finalAssignments[indexChanged];
        await updateTaskAssignment(props.task.tareas_id, oldEmployeeId, newEmployeeId);
      } else {
        // En caso de múltiples diferencias, se procesa removiendo y agregando
        const removals = initialAssignments.value.filter(id => !finalAssignments.includes(id));
        for (const id of removals) {
          await removeEmployeeFromTask(id, props.task.tareas_id);
        }
        const additions = finalAssignments.filter(id => !initialAssignments.value.includes(id));
        for (const id of additions) {
          await addEmployeeToTask(id, props.task.tareas_id);
        }
      }
    } else {
      // Si la cantidad cambió, se identifican las asignaciones a remover y las a agregar
      const removals = initialAssignments.value.filter(id => !finalAssignments.includes(id));
      for (const id of removals) {
        await removeEmployeeFromTask(id, props.task.tareas_id);
      }
      const additions = finalAssignments.filter(id => !initialAssignments.value.includes(id));
      for (const id of additions) {
        await addEmployeeToTask(id, props.task.tareas_id);
      }
    }
    
    // Aseguramos que el loader se muestre al menos por 800ms para operaciones complejas
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 800) {
      await new Promise(resolve => setTimeout(resolve, 800 - elapsedTime));
    }
    
    emit('updated');
    closeModal();
  } catch (error: any) {
    console.error('Error al asignar empleados:', error.message);
    errorMessage.value = error.message || 'Error al asignar empleados';
    loading.value = false;
  }
};

const closeModal = () => {
  if (loading.value) return;
  emit('close');
};
</script>
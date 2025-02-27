<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all duration-300">
      <!-- Encabezado con degradado -->
      <div class="bg-gradient-to-r from-orange-500 to-orange-400 p-4 rounded-t-2xl">
        <div class="flex justify-between items-center">
          <h2 class="text-white text-2xl font-bold">Asignar Empleados a la Tarea</h2>
          <button @click="closeModal" class="text-white text-3xl leading-none hover:text-gray-200">&times;</button>
        </div>
      </div>
      <!-- Formulario -->
      <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
        <!-- Listado de asignaciones -->
        <div>
          <label class="block text-gray-700 mb-2">Empleados asignados</label>
          <div v-for="(id, index) in assignments" :key="index" class="flex items-center space-x-2 mb-3">
            <select
              v-model="assignments[index]"
              required
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm"
            >
              <option value="" disabled>Seleccione un empleado</option>
              <option v-for="employee in employees" :key="employee.empleados_id" :value="employee.empleados_id">
                {{ employee.nombre }}
              </option>
            </select>
            <button type="button" @click="removeAssignment(index)" class="text-red-500 hover:text-red-600 text-2xl">&times;</button>
          </div>
          <button type="button" @click="addAssignment" class="mt-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
            Añadir empleado
          </button>
        </div>
        <!-- Mensaje de error -->
        <div v-if="errorMessage" class="p-2 bg-red-100 border border-red-300 rounded-md text-red-700 text-xs">
          {{ errorMessage }}
        </div>
        <!-- Botones de Acción -->
        <div class="flex justify-end space-x-4">
          <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition text-sm">
            Cancelar
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm">
            Guardar
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

// Observa cambios en la tarea para derivar las asignaciones a partir de "empleado_nombre"
watch(
  () => props.task,
  (newTask) => {
    if (newTask) {
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
    employees.value = await getEmployees();
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
    const finalAssignments = assignments.value;
    
    // Si no hubo cambios, se cierra el modal
    if (JSON.stringify(finalAssignments.slice().sort()) === JSON.stringify(initialAssignments.value.slice().sort())) {
      emit('updated');
      closeModal();
      return;
    }
    
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
/* Puedes agregar estilos adicionales si lo consideras necesario */
</style>

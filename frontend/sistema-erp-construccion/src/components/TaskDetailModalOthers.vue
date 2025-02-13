<!-- components/TaskDetailModalOtros.vue -->
<template>
    <div class="fixed inset-0 flex items-center justify-center z-50 px-4">
      <!-- Fondo semi-transparente (clic para cerrar) -->
      <div class="fixed inset-0 bg-black opacity-50" @click="close"></div>
  
      <!-- Contenedor del modal -->
      <div class="bg-white rounded-xl shadow-2xl z-50 p-6 w-full max-w-2xl mx-auto transform transition-all duration-300">
        <!-- Header del modal -->
        <div class="mb-4 border-b pb-4 flex justify-between items-center">
          <h2 class="text-2xl font-bold text-gray-800">Detalles de la Tarea (Otros)</h2>
          <button @click="close" class="text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
  
        <!-- Contenido principal -->
        <div class="space-y-6">
          <!-- 1. Tarea y Estado -->
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-600">Tarea</label>
              <input
                type="text"
                v-model="updatedNombreTarea"
                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div class="mt-4 md:mt-0 w-full md:w-1/3">
              <label class="block text-sm font-medium text-gray-600">Estado</label>
              <select
                v-model="updatedStatus"
                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="pendiente">Pendiente</option>
                <option value="en progreso">En progreso</option>
                <option value="finalizado">Finalizado</option>
              </select>
            </div>
          </div>
  
          <!-- 2. Descripción -->
          <div v-if="task.descripcion !== undefined">
            <label class="block text-sm font-medium text-gray-600">Descripción</label>
            <textarea
              v-model="updatedDescripcion"
              class="mt-1 block w-full p-2 border border-gray-200 rounded-md text-gray-700 text-base max-h-32 overflow-y-auto focus:ring-blue-500 focus:border-blue-500"
              rows="4"
            ></textarea>
          </div>
  
          <!-- 3. Proyecto (no editable) -->
          <div>
            <label class="block text-sm font-medium text-gray-600">Proyecto</label>
            <p class="mt-1 text-lg font-semibold text-gray-800">{{ task.nombre_proyecto }}</p>
          </div>
  
          <!-- 4. Fechas -->
          <div class="flex flex-col sm:flex-row sm:justify-between gap-4">
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-600">Fecha de Inicio</label>
              <input
                type="date"
                v-model="updatedFechaInicio"
                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-600">Fecha de Fin</label>
              <input
                type="date"
                v-model="updatedFechaFin"
                class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>
  
          <!-- 5. Empleado asignado (editable) -->
          <div>
            <label class="block text-sm font-medium text-gray-600">Asignado a</label>
            <input
              type="text"
              v-model="updatedNombreEmpleado"
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>
  
        <!-- Footer con acciones -->
        <div class="mt-6 flex justify-end">
          <button @click="close" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 mr-2">
            Cancelar
          </button>
          <button @click="updateTask" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  import { updateTask as updateTaskService } from '@/service/taskService';
  import { updateTaskAssignment } from '@/service/taskService';
  
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
  const updatedNombreEmpleado = ref(props.task.nombre_empleado);
  
  watch(() => props.task, (newTask) => {
    updatedNombreTarea.value = newTask.nombre_tarea;
    updatedStatus.value = newTask.estado;
    updatedDescripcion.value = newTask.descripcion;
    updatedFechaInicio.value = newTask.fecha_inicio;
    updatedFechaFin.value = newTask.fecha_fin;
    updatedNombreEmpleado.value = newTask.nombre_empleado;
  });
  
  const close = () => {
    emit('close');
  };
  
  const updateTask = async () => {
    try {
      // Actualizar la tabla tareas (sin el campo de empleado)
      const taskData = {
        nombre_tarea: updatedNombreTarea.value,
        estado: updatedStatus.value,
        descripcion: updatedDescripcion.value,
        fecha_inicio: updatedFechaInicio.value,
        fecha_fin: updatedFechaFin.value
      };
      await updateTaskService(props.task.tareas_id, taskData);
      
      // Actualizar la asignación si ha cambiado
      if (updatedNombreEmpleado.value !== props.task.nombre_empleado) {
        await updateTaskAssignment(props.task.tareas_id, updatedNombreEmpleado.value);
      }
      
      const updatedTask = { ...props.task, ...taskData, nombre_empleado: updatedNombreEmpleado.value };
      emit('update', updatedTask);
      close();
    } catch (error) {
      console.error('Error al actualizar la tarea:', error);
    }
  };
  </script>
  
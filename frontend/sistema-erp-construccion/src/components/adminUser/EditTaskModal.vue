<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-md p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Editar Tarea</h2>
        <form @submit.prevent="handleSubmit">
          <div class="mb-4">
            <label for="nombre_tarea" class="block text-gray-700 mb-2">Nombre de Tarea</label>
            <input
              type="text"
              id="nombre_tarea"
              v-model="form.nombre_tarea"
              required
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>
          <div class="mb-4">
            <label for="estado" class="block text-gray-700 mb-2">Estado</label>
            <input
              type="text"
              id="estado"
              v-model="form.estado"
              required
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>
          <div class="mb-4">
            <label for="nombre_proyecto" class="block text-gray-700 mb-2">Proyecto</label>
            <input
              type="text"
              id="nombre_proyecto"
              v-model="form.nombre_proyecto"
              required
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>
          <div class="mb-4">
            <label for="empleado_nombre" class="block text-gray-700 mb-2">Responsable(s)</label>
            <input
              type="text"
              id="empleado_nombre"
              v-model="form.empleado_nombre"
              required
              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400"
            />
          </div>
          <div class="flex justify-end space-x-4">
            <button
              type="button"
              @click="close"
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition"
            >
              Cancelar
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 transition"
            >
              Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, watch } from 'vue';
  import { updateTask } from '@/service/taskService';
  
  const props = defineProps({
    task: {
      type: Object,
      required: true
    }
  });
  
  const emit = defineEmits(['close', 'updated']);
  
  const form = ref({
    nombre_tarea: '',
    estado: '',
    nombre_proyecto: '',
    empleado_nombre: ''
  });
  
  // Prellenar el formulario cuando se reciba la tarea
  watch(
    () => props.task,
    (newTask) => {
      if (newTask) {
        form.value = {
          nombre_tarea: newTask.nombre_tarea || '',
          estado: newTask.estado || '',
          nombre_proyecto: newTask.nombre_proyecto || '',
          empleado_nombre: newTask.empleado_nombre || ''
        };
      }
    },
    { immediate: true }
  );
  
  const handleSubmit = async () => {
    try {
      await updateTask(props.task.tareas_id, form.value);
      emit('updated');
    } catch (error: any) {
      console.error('Error al actualizar la tarea:', error.message);
    }
  };
  
  const close = () => {
    emit('close');
  };
  </script>
  
  <style scoped>
  /* Puedes agregar estilos adicionales si lo consideras necesario */
  </style>
  
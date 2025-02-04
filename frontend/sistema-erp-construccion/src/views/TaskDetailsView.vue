<template>
    <div class="max-w-3xl mx-auto p-6">
      <h2 class="text-xl font-semibold mb-4">Detalles de la Tarea</h2>
  
      <div v-if="loading" class="text-center">Cargando detalles de la tarea...</div>
      <div v-if="error" class="text-red-500 text-center">{{ error }}</div>
      <div v-if="!taskDetails" class="text-center">No se encontraron detalles para esta tarea.</div>
  
      <div v-else>
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h3 class="text-2xl font-semibold text-gray-800">{{ taskDetails.nombre_tarea }}</h3>
          <p class="text-sm text-gray-500">Proyecto: {{ taskDetails.nombre_proyecto }}</p>
          <p class="text-sm text-gray-500">Descripción: {{ taskDetails.descripcion }}</p>
          <p class="text-sm text-gray-500">Fecha de vencimiento: {{ taskDetails.fecha_vencimiento }}</p>
          <p class="text-sm text-gray-500">Estado: {{ taskDetails.estado }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute } from 'vue-router';
  import { getPendingTasks } from '@/service/taskService';  // Utilizamos getPendingTasks
  import { useRouter } from 'vue-router';
  
  const taskDetails = ref(null);
  const loading = ref(true);
  const error = ref('');
  const route = useRoute();
  const router = useRouter();
  
  const taskId = route.params.taskId;  // Obtener el ID de la tarea desde la ruta
  
  // Función para obtener los detalles de la tarea
  const fetchTaskDetails = async () => {
    try {
      const user = JSON.parse(localStorage.getItem('user'));
      if (!user) {
        router.push('/');
        return;
      }
  
      console.log('Task ID desde la ruta:', taskId); // Agregar log para verificar taskId
      const response = await getPendingTasks(user);  
      console.log('Respuesta de getPendingTasks:', response);  // Verifica qué datos se obtienen de la API
  
      // Asegúrate de que taskId sea del mismo tipo que tareas_id
      const filteredTask = response.find(task => String(task.tareas_id) === String(taskId)); // Convertimos ambos a string para evitar problemas de tipo
  
      console.log('Tarea filtrada:', filteredTask);  // Verifica la tarea filtrada
  
      taskDetails.value = filteredTask;
    } catch (err) {
      error.value = 'Error al obtener los detalles de la tarea.';
    } finally {
      loading.value = false;
    }
  };
  
  // Llamamos a la función cuando el componente se monta
  onMounted(() => {
    fetchTaskDetails();
  });
  </script>
  
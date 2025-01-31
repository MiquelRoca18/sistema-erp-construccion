<template>
    <div class="max-w-3xl mx-auto p-6">
      <h2 class="text-xl font-semibold mb-4">Tareas Pendientes</h2>
      
      <div v-if="loading" class="text-center">Cargando tareas...</div>
      <div v-if="error" class="text-red-500 text-center">{{ error }}</div>
      
      <div v-if="tasks.length === 0" class="text-center">No hay tareas pendientes.</div>
      
      <div v-else>
        <ul>
          <li
            v-for="task in tasks"
            :key="task.tareas_id"
            class="mb-4 p-4 bg-gray-100 rounded-md shadow-sm border-l-4 border-blue-500"
          >
            <h3 class="font-bold text-lg text-gray-800">{{ task.nombre_tarea }}</h3>
            <p class="text-sm text-gray-500">Estado: {{ task.estado }}</p>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  import { getPendingTasks } from '@/service/taskService'; // Llamada al servicio de tareas
  
  const tasks = ref([]);
  const loading = ref(true);
  const error = ref('');
  const router = useRouter();
  
  // Función para obtener las tareas pendientes
  const fetchTasks = async () => {
    try {
      const user = JSON.parse(localStorage.getItem('user'));
      if (!user) {
        router.push('/');
        return;
      }
      console.log(user)
      const response = await getPendingTasks(user);
      tasks.value = response;
      console.log(tasks.value)
    } catch (err) {
      error.value = 'Error al obtener las tareas pendientes.';
    } finally {
      loading.value = false;
    }
  };
  
  // Llamamos a la función cuando el componente se monta
  onMounted(() => {
    fetchTasks();
  });
  </script>
  
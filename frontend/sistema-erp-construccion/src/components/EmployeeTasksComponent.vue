<template>
    <div class="max-w-3xl mx-auto p-6">
      <h2 class="text-xl font-semibold mb-4">Tareas Pendientes</h2>
      
      <div v-if="loading" class="text-center">Cargando tareas...</div>
      <div v-if="error" class="text-red-500 text-center">{{ error }}</div>
      
      <div v-if="tasksToShow.length === 0" class="text-center">No hay tareas pendientes.</div>
      
      <div v-else>
        <div class="grid grid-cols-2 gap-4">
          <div
            v-for="task in tasksToShow"
            :key="task.tareas_id"
            class="p-3 bg-gray-100 rounded-md shadow-sm border-l-2 border-blue-500"
          >
            <h3 class="font-semibold text-md text-gray-800">{{ task.nombre_tarea }}</h3>
            <p class="text-xs text-gray-500">Proyecto: {{ task.nombre_proyecto }}</p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted, computed } from 'vue';
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
      console.log(user);
      const response = await getPendingTasks(user);
      tasks.value = response;
      console.log(tasks.value);
    } catch (err) {
      error.value = 'Error al obtener las tareas pendientes.';
    } finally {
      loading.value = false;
    }
  };
  
  // Computed property para limitar las tareas a 4
  const tasksToShow = computed(() => {
    return tasks.value.slice(0, 4); // Limita las tareas a las primeras 4
  });
  
  // Llamamos a la función cuando el componente se monta
  onMounted(() => {
    fetchTasks();
  });
  </script>
  
<template>
    <div class="w-64 h-full p-6">
      <!-- Foto y nombre del empleado -->
      <div class=" ">
        <router-link :to="`/employee/${employeeId}`" class="w-full flex items-center mb-8 hover:bg-gray-300 py-2 px-4 rounded">
            <img :src="employeePhoto" alt="Perfil" class="w-12 h-12 rounded-full mr-4">
            <span class="font-semibold">{{ employeeName }}</span>
        </router-link>
    </div>
      
      <!-- Navegación -->
      <ul>
        <li>
          <router-link to="/dashboard" class="block py-2 px-4 rounded hover:bg-gray-300">Dashboard</router-link>
        </li>
        <li>
          <router-link :to="`/employee/${employeeId}`" class="block py-2 px-4 rounded hover:bg-gray-300">Perfil</router-link>
        </li>
        <li>
          <router-link :to="`/tasks/${employeeId}`" class="block py-2 px-4 rounded hover:bg-gray-300">Tareas</router-link>
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { getEmployeeData } from '@/service/authService';
  
  // Variables para el nombre del empleado y foto
  const employeeId = ref(null);
  const employeeName = ref("Empleado Desconocido");
  const employeePhoto = ref("/src/assets/images/employeePhoto.webp");
  
  onMounted(async () => {
    const user = JSON.parse(localStorage.getItem("user"));
    if (user) {
      try {
        const token = localStorage.getItem('token');
        const employeeData = await getEmployeeData(user, token);
        employeeId.value = user;
        employeeName.value = employeeData.nombre || "Empleado Desconocido";
        employeePhoto.value = employeeData.photo || "/src/assets/images/employeePhoto.webp";
      } catch (error) {
        console.error("Error al cargar los datos del empleado:", error);
      }
    }
  });
  </script>
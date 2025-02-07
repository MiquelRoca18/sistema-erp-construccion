<template>
  <div>
    <!-- Sidebar -->
    <div 
      class="fixed top-0 left-0 h-full w-64 bg-gray-100 text-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out z-40 border-r border-gray-300 
             md:relative md:translate-x-0 md:flex md:flex-col"
      :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    >
      <!-- Sección del usuario -->
      <div class="p-6 flex flex-col items-center border-b border-gray-300 ">
        <router-link :to="`/employee/${employeeId}`" class="flex flex-col items-center gap-3 hover:bg-gray-200 transition py-3 px-4 rounded-lg">
          <img :src="employeePhoto" alt="Perfil" class="w-16 h-16 rounded-full border-4 border-gray-400 shadow-md">
          <span class="text-lg font-semibold text-gray-900">{{ employeeName }}</span>
        </router-link>
      </div>

      <!-- Navegación -->
      <ul class="p-4 flex flex-col gap-4">
        <li>
          <router-link to="/dashboard" class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition">
            📊 Dashboard
          </router-link>
        </li>
        <li>
          <router-link :to="`/tasks/${employeeId}`" class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition">
            📝 Tareas Pendientes
          </router-link>
        </li>
      </ul>
    </div>

    <!-- Fondo oscuro para cerrar el menú en móviles -->
    <div 
      v-if="sidebarOpen" 
      class="fixed inset-0 bg-black/50 md:hidden z-30"
      @click="$emit('toggleSidebar')"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getEmployeeData } from '@/service/authService';

const props = defineProps(['sidebarOpen']);
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

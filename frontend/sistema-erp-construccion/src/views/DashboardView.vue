<template>
  <div class="flex justify-center min-h-screen p-6">
    <div class="flex items-center space-x-8 w-full max-w-7xl min-h-screen p-6">
      <!-- Sección de Perfil del Empleado -->
      <div class="flex flex-col items-center flex-none w-80 h-80 bg-gray-200 border border-gray-300 p-6 rounded-lg shadow-md cursor-pointer">
        <router-link :to="`/employee/${employeeId}`" class="w-full">
          <EmployeeProfileComponent
            :employeePhoto="employeePhoto"
            :employeeName="employeeName"
          />
        </router-link>
        <button
          @click="logout"
          class="mt-6 px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200"
        >
          Logout
        </button>
      </div>
      <router-link :to="`/tasks/${employeeId}`" class="flex-grow bg-gray-200 border border-gray-300 p-6 rounded-lg shadow-md cursor-pointer">
        <EmployeeTasksComponent />
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getEmployeeData } from '@/service/authService';
import { logout as authLogout } from '@/service/authStore'; // Importar la función logout del store
import EmployeeProfileComponent from "@/components/EmployeeProfileComponent.vue";
import EmployeeTasksComponent from "@/components/EmployeeTasksComponent.vue";

const router = useRouter();
const employeeId = ref(null);
const employeeName = ref("Empleado Desconocido");
const employeePhoto = ref("/src/assets/images/employeePhoto.webp");

onMounted(async () => {
  const user = JSON.parse(localStorage.getItem("user"));
  if (user) {
    const employeeData = await getEmployeeData(user, localStorage.getItem('token'));
    employeeId.value = user;
    employeeName.value = employeeData.nombre || "Empleado Desconocido";
    employeePhoto.value = employeeData.photo || "/src/assets/images/employeePhoto.webp";
  } else {
    router.push('/');
  }
});

const logout = () => {
  // Usar el store para manejar el logout
  authLogout();

  // Restablecer las variables locales
  employeeId.value = null;
  employeeName.value = "Empleado Desconocido";
  employeePhoto.value = "/src/assets/images/employeePhoto.webp";

  // Redirigir al login
  router.push('/');
};
</script>
<template>
  <div class="flex justify-center min-h-screen p-6">
    <div class="flex items-center space-x-8 w-full max-w-7xl min-h-screen p-6">
      <div class="flex flex-col items-center flex-none w-80 h-80 bg-white p-6 rounded-lg shadow-md cursor-pointer">
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

      <!-- Componente de tareas (más grande, rectangular) -->
      <div class="flex-grow bg-white p-6 rounded-lg shadow-md cursor-pointer">
        <EmployeeTasksComponent />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getEmployeeData } from '@/service/authService';
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
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  router.push('/'); 
};
</script>

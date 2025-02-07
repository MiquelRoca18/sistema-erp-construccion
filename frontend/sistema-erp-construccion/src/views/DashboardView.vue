<template>
  <div class="flex items-center justify-center min-h-screen p-4 bg-gray-100">
    <div class="flex flex-col items-center md:flex-row gap-6 w-full max-w-5xl">
      
      <div class="flex flex-col bg-white border border-gray-300 p-4 rounded-lg shadow-md h-[275px] md:w-1/3">
        <div class="flex flex-grow items-center justify-center w-full h-full">
          <router-link :to="`/employee/${employeeId}`">
            <EmployeeProfileComponent
              :employeePhoto="employeePhoto"
              :employeeName="employeeName"
            />
          </router-link>
        </div>
        <button
          @click="logout"
          class="mt-auto px-5 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 w-full"
        >
          Logout
        </button>
      </div>

      <div class="flex flex-col bg-white border border-gray-300 p-4 rounded-lg shadow-md h-[350px] md:w-2/3">
        <router-link :to="`/tasks/${employeeId}`" class="h-full">
          <EmployeeTasksComponent />
        </router-link>
      </div>
    
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getEmployeeData } from '@/service/authService';
import { logout as authLogout } from '@/service/authStore';
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
  authLogout();
  employeeId.value = null;
  employeeName.value = "Empleado Desconocido";
  employeePhoto.value = "/src/assets/images/employeePhoto.webp";
  router.push('/');
};
</script>

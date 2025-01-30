<template>
  <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-md max-w-sm mx-auto cursor-pointer">
    <router-link :to="`/employee/${employeeId}`">
      <EmployeeProfileComponent
        :employeePhoto="employeePhoto"
        :employeeName="employeeName"
      />
    </router-link>
    <button
      @click="logout"
      class="mt-6 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 items-center"
    >
      Logout
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getEmployeeData } from '@/service/authService';
import EmployeeProfileComponent from "@/components/EmployeeProfileComponent.vue";

const router = useRouter();
const employeeId = ref(null);
const employeeName = ref("Empleado Desconocido");
const employeePhoto = ref("/src/assets/images/employeePhoto.webp");

onMounted(async () => {
  const user = JSON.parse(localStorage.getItem("user"));
  if (user) {
    const employeeData = await getEmployeeData(user);
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

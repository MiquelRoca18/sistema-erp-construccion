<template>
  <div>
    <router-link :to="`/employee/${employeeId}`">
      <EmployeeProfileComponent
        :employeePhoto="employeePhoto"
        :employeeName="employeeName"
      />
    </router-link>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getEmployeeData } from '@/service/authService';
import EmployeeProfileComponent from "@/components/EmployeeProfileComponent.vue";

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
  }
});
</script>

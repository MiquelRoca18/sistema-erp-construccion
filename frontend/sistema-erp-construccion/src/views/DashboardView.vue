<template>
  <div>
    <!-- Componente de Perfil del Empleado -->
    <EmployeeProfileComponent
      :employeePhoto="employeePhoto"
      :employeeName="employeeName"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getEmployeeData } from '@/service/authService';
import EmployeeProfileComponent from "@/components/EmployeeProfileComponent.vue";

const employeeName = ref("Cargando...");
const employeePhoto = ref("/path/to/photo.jpg"); // Puedes cambiarlo si tienes fotos en el backend

onMounted(async () => {
  const token = localStorage.getItem("token");
  const user = JSON.parse(localStorage.getItem("user")); // Guarda el usuario en login
  if (user && token) {
    try {
      const employeeData = await getEmployeeData(user.empleados_id, token);
      employeeName.value = employeeData.username || "Empleado Desconocido"; // Ajusta según tu backend
    } catch (error) {
      console.error("Error obteniendo datos del empleado:", error);
    }
  }
});
</script>

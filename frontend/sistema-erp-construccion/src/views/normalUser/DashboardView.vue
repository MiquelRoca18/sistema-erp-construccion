<template>
  <div class="min-h-screen flex flex-col justify-center items-center p-6 gap-6 w-full transition-colors duration-300">
    
    <!-- Fila superior: Perfil + Gráfica -->
    <div class="flex flex-col md:flex-row w-full max-w-3xl gap-6">
      
      <!-- Columna Perfil -->
      <div class="flex flex-col bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full md:w-2/5 p-4 transition-colors duration-300">
        <div class="flex-grow flex flex-col items-center justify-center">
          <router-link :to="`/employee/${employeeId}`">
            <EmployeeProfileComponent
              :employeePhoto="employeePhoto"
              :employeeName="employeeName"
            />
          </router-link>
        </div>
        <button
          @click="logout"
          class="mt-4 px-5 py-2 bg-red-500 dark:bg-red-600 text-white rounded-md hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-200 w-full"
        >
          Logout
        </button>
      </div>
      
      <!-- Columna Gráfica (solo se renderiza si employeeId existe) -->
      <div 
        v-if="employeeId"
        class="flex flex-col bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full md:w-3/5 p-4 transition-colors duration-300"
      >
        <router-link :to="{ path: `/tasks/${employeeId}` }" class="block">
          <EmployeeTasksGraph :employeeId="employeeId" />
        </router-link>
      </div>
    </div>
    
    <!-- Fila inferior: Tareas Pendientes -->
    <div class="bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full max-w-3xl p-4 transition-colors duration-300">
      <router-link :to="pendingTasksLink" class="block">
        <EmployeeTasksComponent />
      </router-link>
    </div>
    
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { getEmployeeData } from '@/service/authService';
import { logout as authLogout, userRole } from '@/service/authStore';
import EmployeeProfileComponent from "@/components/normalUser/EmployeeProfileComponent.vue";
import EmployeeTasksComponent from "@/components/normalUser/EmployeeTasksComponent.vue";
import EmployeeTasksGraph from "@/components/normalUser/EmployeeTasksGraph.vue";

const router = useRouter();
const employeeId = ref(null);
const employeeName = ref("Empleado Desconocido");
const employeePhoto = ref("/src/assets/images/employeePhoto.webp");

onMounted(async () => {
  const user = JSON.parse(localStorage.getItem("user"));
  if (user) {
    const employeeData = await getEmployeeData(user, localStorage.getItem('token'));
    // Asignamos los datos del empleado obtenidos del backend
    employeeId.value = employeeData.empleados_id; 
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

const pendingTasksLink = computed(() => {
  return {
    path: `/tasks/${employeeId.value}`,
    query: { status: 'pendiente' }
  };
});
</script>
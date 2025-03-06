<template>
  <div class="min-h-screen flex flex-col justify-center items-center p-6 gap-6 w-full transition-colors duration-300">
    <!-- Fila superior: Perfil + Gráfica -->
    <div class="flex flex-col md:flex-row w-full max-w-3xl gap-6">
      <!-- Columna Perfil - Oculta en dispositivos pequeños -->
      <div class="hidden md:flex md:flex-col bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full md:w-2/5 p-4 transition-colors duration-300">
        <div v-if="isLoading" class="flex-grow flex flex-col items-center justify-center">
          <div class="animate-pulse flex flex-col items-center w-full">
            <div class="rounded-full bg-gray-300 dark:bg-gray-600 h-24 w-24 mb-4"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
          </div>
        </div>
        <div v-else class="flex-grow flex flex-col items-center justify-center">
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
          :disabled="isLoading"
        >
          Logout
        </button>
      </div>
      <!-- Columna Gráfica (solo se renderiza si employeeId existe) -->
      <div 
        class="flex flex-col bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full md:w-3/5 p-4 transition-colors duration-300"
      >
        <div v-if="isLoading" class="h-48 w-full flex items-center justify-center">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        <router-link v-else-if="employeeId" :to="{ path: `/tasks/${employeeId}` }" class="block">
          <EmployeeTasksGraph :employeeId="employeeId" />
        </router-link>
        <div v-else class="h-48 w-full flex items-center justify-center text-gray-500 dark:text-gray-400">
          No hay datos disponibles
        </div>
      </div>
    </div>
    <!-- Fila inferior: Tareas Pendientes -->
    <div class="bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full max-w-3xl p-4 transition-colors duration-300">
      <div v-if="isLoading" class="h-48 w-full">
        <div class="animate-pulse space-y-4">
          <div class="h-6 bg-gray-300 dark:bg-gray-600 rounded w-1/4 mb-4"></div>
          <div class="space-y-2">
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-4/6"></div>
          </div>
        </div>
      </div>
      <router-link v-else :to="pendingTasksLink" class="block">
        <EmployeeTasksComponent :employeeId="employeeId" />
      </router-link>
    </div>
    <!-- Botón de logout móvil - Visible solo en dispositivos pequeños -->
    <div class="md:hidden w-full max-w-3xl">
      <button
        @click="logout"
        class="w-full px-5 py-3 bg-red-500 dark:bg-red-600 text-white rounded-md hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-200"
        :disabled="isLoading"
      >
        Cerrar Sesión
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { getEmployeeData } from '@/service/authService';
import { logout as authLogout } from '@/service/authStore';
import EmployeeProfileComponent from "@/components/normalUser/EmployeeProfileComponent.vue";
import EmployeeTasksComponent from "@/components/normalUser/EmployeeTasksComponent.vue";
import EmployeeTasksGraph from "@/components/normalUser/EmployeeTasksGraph.vue";
import defaultEmployeePhoto from '@/assets/images/employeePhoto.webp'

const router = useRouter();
const employeeId = ref(null);
const employeeName = ref("Empleado Desconocido");
const employeePhoto = ref(defaultEmployeePhoto);
const isLoading = ref(true);

onMounted(async () => {
  try {
    isLoading.value = true;
    const user = JSON.parse(localStorage.getItem("user"));
    
    if (user) {
      // Mostrar un mensaje en la consola de que se está cargando
      console.log("Cargando datos del empleado...");
      
      // Obtener los datos del empleado
      const employeeData = await getEmployeeData(user.empleados_id, localStorage.getItem('token'));
      
      console.log("Tipo de datos:", typeof employeeData);

      // Asignación segura de valores
      if (employeeData && typeof employeeData === 'object') {
        employeeId.value = employeeData.empleados_id ?? null;
        employeeName.value = employeeData.nombre || "Empleado Desconocido";
        employeePhoto.value = employeeData.photo || defaultEmployeePhoto;
      } else {
        console.error("Datos de empleado inválidos");
        router.push('/');
      }
    } else {
      router.push('/');
    }
  } catch (error) {
    console.error("Error al obtener datos del empleado:", error);
    router.push('/');
  } finally {
    // Independientemente del resultado, desactivar el loader después de un pequeño retraso para evitar parpadeos
    setTimeout(() => {
      isLoading.value = false;
    }, 300);
  }
});

const logout = () => {
  authLogout();
  employeeId.value = null;
  employeeName.value = "Empleado Desconocido";
  employeePhoto.value = defaultEmployeePhoto;
  router.push('/');
};

const pendingTasksLink = computed(() => {
  return {
    path: `/tasks/${employeeId.value}`,
    query: { status: 'pendiente' }
  };
});
</script>
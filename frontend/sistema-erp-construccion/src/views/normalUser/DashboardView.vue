<template>
  <div class="min-h-screen flex flex-col justify-center items-center p-6 gap-6 w-full transition-colors duration-300">
    <!-- Fila superior: Perfil + Gráfica -->
    <div class="flex flex-col md:flex-row w-full max-w-3xl gap-6">
      <!-- Columna Perfil - Oculta en dispositivos pequeños -->
      <div class="hidden md:flex md:flex-col bg-white/90 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg dark:shadow-gray-900/30 w-full md:w-2/5 p-4 transition-colors duration-300">
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
        <EmployeeTasksComponent :employeeId="employeeId" />
      </router-link>
    </div>
    <!-- Botón de logout móvil - Visible solo en dispositivos pequeños -->
    <div class="md:hidden w-full max-w-3xl">
      <button
        @click="logout"
        class="w-full px-5 py-3 bg-red-500 dark:bg-red-600 text-white rounded-md hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-200"
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

onMounted(async () => {
  try {
    const user = JSON.parse(localStorage.getItem("user"));
    if (user) {
      // Agregamos más logs de depuración
      console.log("Usuario obtenido de localStorage:", user);
      console.log("ID de empleado a buscar:", user.empleados_id);

      const employeeData = await getEmployeeData(user.empleados_id, localStorage.getItem('token'));
      
      // Logs de depuración detallados
      console.log("Datos del empleado recibidos:", employeeData);
      console.log("Tipo de datos:", typeof employeeData);
      console.log("Propiedades de employeeData:", Object.keys(employeeData));

      // Asignación segura de valores
      if (employeeData && typeof employeeData === 'object') {
        employeeId.value = employeeData.empleados_id ?? null;
        employeeName.value = employeeData.nombre || "Empleado Desconocido";
        employeePhoto.value = employeeData.photo || defaultEmployeePhoto;

        console.log("Valores asignados:");
        console.log("ID de empleado:", employeeId.value);
        console.log("Nombre de empleado:", employeeName.value);
        console.log("Foto de empleado:", employeePhoto.value);
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
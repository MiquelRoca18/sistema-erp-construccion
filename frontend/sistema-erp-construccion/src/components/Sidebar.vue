<!-- Sidebar.vue -->
<template>
  <div>
    <!-- Sidebar (aparece desde la derecha en móviles y está fijo en pantallas grandes) -->
    <div 
      class="fixed top-0 right-0 h-full w-64 bg-gray-100 text-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out z-40 border-l border-r border-gray-300 
             lg:relative lg:translate-x-0 lg:flex lg:flex-col"
      :class="{ 'translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    >
      <!-- Sección del usuario -->
      <div class="p-6 flex flex-col items-center border-b border-gray-300">
        <router-link 
          :to="`/employee/${employeeId}`" 
          class="flex flex-col items-center gap-3 hover:bg-gray-200 transition py-3 px-4 rounded-lg"
          @click="closeSidebar"
        >
          <img :src="employeePhoto" alt="Perfil" class="w-16 h-16 rounded-full border-4 border-gray-400 shadow-md">
          <span class="text-lg font-semibold text-gray-900">{{ employeeName }}</span>
        </router-link>
      </div>

      <!-- Navegación -->
      <div class="p-4">
        <!-- Si el usuario es admin, mostramos dos secciones desplegables -->
        <template v-if="isAdmin">
          <!-- Menú Personal -->
          <div>
            <div class="flex items-center justify-between cursor-pointer" @click="togglePersonal">
              <h3 class="text-lg font-bold mb-2">Menú Personal</h3>
              <svg :class="{ 'rotate-180': personalOpen }" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <ul v-show="personalOpen" class="flex flex-col gap-4">
              <li>
                <router-link 
                  to="/dashboard" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📊 Dashboard Personal
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}`, query: { status: 'pendiente' } }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📝 Tareas Pendientes
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}` }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📋 Tareas Generales
                </router-link>
              </li>
            </ul>
          </div>
          <!-- Menú Admin -->
          <div class="mt-6">
            <div class="flex items-center justify-between cursor-pointer" @click="toggleAdmin">
              <h3 class="text-lg font-bold mb-2">Menú Admin</h3>
              <svg :class="{ 'rotate-180': adminOpen }" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <ul v-show="adminOpen" class="flex flex-col gap-4">
              <li>
                <router-link 
                  to="/dashboard-admin" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  🖥 Dashboard Admin
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/admin-tasks" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📝 Tareas
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/employees" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  👥 Empleados
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/projects" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📁 Proyectos
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/budgets" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  💰 Presupuestos
                </router-link>
              </li>
            </ul>
          </div>
        </template>
        <!-- Para usuarios no admin, mostramos solo el menú desplegable (Menú Personal) -->
        <template v-else>
          <div>
            <div class="flex items-center justify-between cursor-pointer" @click="togglePersonal">
              <h3 class="text-lg font-bold mb-2">Menú</h3>
              <svg :class="{ 'rotate-180': personalOpen }" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <ul v-show="personalOpen" class="flex flex-col gap-4">
              <li>
                <router-link 
                  to="/dashboard" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📊 Dashboard
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}`, query: { status: 'pendiente' } }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📝 Tareas Pendientes
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}` }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 transition"
                  @click="closeSidebar"
                >
                  📋 Tareas Generales
                </router-link>
              </li>
            </ul>
          </div>
        </template>
      </div>
    </div>

    <!-- Fondo oscuro para cerrar el menú en móviles -->
    <div 
      v-if="sidebarOpen" 
      class="fixed inset-0 bg-black/50 lg:hidden z-30"
      @click="$emit('toggleSidebar')"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { getEmployeeData } from '@/service/authService';
import { userRole } from '@/service/authStore';

const props = defineProps(['sidebarOpen']);
const emit = defineEmits(['toggleSidebar']);

const employeeId = ref(null);
const employeeName = ref("Empleado Desconocido");
const employeePhoto = ref("/src/assets/images/employeePhoto.webp");

// Inicializamos los menús como plegados (false)
const personalOpen = ref(false);
const adminOpen = ref(false);

const togglePersonal = () => {
  personalOpen.value = !personalOpen.value;
};

const toggleAdmin = () => {
  adminOpen.value = !adminOpen.value;
};

onMounted(async () => {
  const user = JSON.parse(localStorage.getItem("user"));
  if (user) {
    try {
      const token = localStorage.getItem('token');
      const employeeData = await getEmployeeData(user, token);
      employeeId.value = employeeData.empleados_id;
      employeeName.value = employeeData.nombre || "Empleado Desconocido";
      employeePhoto.value = employeeData.photo || "/src/assets/images/employeePhoto.webp";
    } catch (error) {
      console.error("Error al cargar los datos del empleado:", error);
    }
  }
});

// Función para cerrar el sidebar en dispositivos pequeños
const closeSidebar = () => {
  if (window.innerWidth < 1024) {
    emit('toggleSidebar');
  }
};

// Computed que verifica si el usuario es admin, usando el valor almacenado en el store
const isAdmin = computed(() => {
  return userRole.value === 'admin';
});
</script>


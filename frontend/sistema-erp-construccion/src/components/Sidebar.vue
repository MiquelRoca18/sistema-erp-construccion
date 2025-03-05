<template>
  <div>
    <div 
      class="fixed top-0 right-0 h-full w-64 bg-gray-100 text-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out z-40 border-l border-r border-gray-300 
             dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700
             lg:relative lg:translate-x-0 lg:flex lg:flex-col"
      :class="{ 'translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    >
      <!-- Sección del usuario -->
      <div class="p-6 flex flex-col items-center border-b border-gray-300 dark:border-gray-700">
        <router-link 
          :to="`/employee/${employeeId}`" 
          class="flex flex-col items-center gap-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition py-3 px-4 rounded-lg"
          @click="closeSidebar"
        >
          <img :src="employeePhoto" alt="Perfil" class="w-16 h-16 rounded-full border-4 border-gray-400 dark:border-blue-500 shadow-md">
          <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ employeeName }}</span>
        </router-link>
      </div>

      <!-- Navegación -->
      <div class="p-4 dark:bg-gray-800">
        <!-- Si el usuario es admin, mostramos dos secciones desplegables -->
        <template v-if="isAdmin">
          <!-- Menú Personal -->
          <div>
            <div class="flex items-center justify-between cursor-pointer" @click="togglePersonal">
              <h3 class="text-lg font-bold mb-2 dark:text-blue-400">Menú Personal</h3>
              <svg :class="{ 'rotate-180': personalOpen }" class="w-5 h-5 transition-transform duration-200 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <ul v-show="personalOpen" class="flex flex-col gap-4">
              <li>
                <router-link 
                  to="/dashboard" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📊 <span class="dark:text-gray-300">Dashboard Personal</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}`, query: { status: 'pendiente' } }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📝 <span class="dark:text-gray-300">Tareas Pendientes</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}` }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📋 <span class="dark:text-gray-300">Tareas Generales</span>
                </router-link>
              </li>
            </ul>
          </div>
          <!-- Menú Admin -->
          <div class="mt-6">
            <div class="flex items-center justify-between cursor-pointer" @click="toggleAdmin">
              <h3 class="text-lg font-bold mb-2 dark:text-blue-400">Menú Admin</h3>
              <svg :class="{ 'rotate-180': adminOpen }" class="w-5 h-5 transition-transform duration-200 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <ul v-show="adminOpen" class="flex flex-col gap-4">
              <li>
                <router-link 
                  to="/dashboard-admin" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  🖥 <span class="dark:text-gray-300">Dashboard Admin</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/employees-admin" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  👥 <span class="dark:text-gray-300">Empleados</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/projects" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📁 <span class="dark:text-gray-300">Proyectos</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/tasks-admin" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📝 <span class="dark:text-gray-300">Tareas Generales</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  to="/budgets" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  💰 <span class="dark:text-gray-300">Presupuestos</span>
                </router-link>
              </li>
            </ul>
          </div>
        </template>
        <!-- Para usuarios no admin, mostramos solo el menú desplegable (Menú Personal) -->
        <template v-else>
          <div>
            <div class="flex items-center justify-between cursor-pointer" @click="togglePersonal">
              <h3 class="text-lg font-bold mb-2 dark:text-blue-400">Menú</h3>
              <svg :class="{ 'rotate-180': personalOpen }" class="w-5 h-5 transition-transform duration-200 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
              </svg>
            </div>
            <ul v-show="personalOpen" class="flex flex-col gap-4">
              <li>
                <router-link 
                  to="/dashboard" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📊 <span class="dark:text-gray-300">Dashboard</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}`, query: { status: 'pendiente' } }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📝 <span class="dark:text-gray-300">Tareas Pendientes</span>
                </router-link>
              </li>
              <li>
                <router-link 
                  :to="{ path: `/tasks/${employeeId}` }" 
                  class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition"
                  @click="closeSidebar"
                >
                  📋 <span class="dark:text-gray-300">Tareas Generales</span>
                </router-link>
              </li>
            </ul>
          </div>
        </template>
      </div>

      <!-- Footer con toggle de dark mode -->
      <div class="mt-auto p-4 border-t border-gray-300 dark:border-gray-700 flex justify-center">
        <button 
          @click="$emit('toggleDarkMode')" 
          class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
          aria-label="Cambiar modo de visualización"
        >
          <!-- Sol para modo oscuro -->
          <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <!-- Luna para modo claro -->
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Fondo oscuro para cerrar el menú en móviles -->
    <div 
      v-if="sidebarOpen" 
      class="fixed inset-0 bg-black/50 dark:bg-black/70 lg:hidden z-30"
      @click="$emit('toggleSidebar')"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { getEmployeeData } from '@/service/authService';
import { userRole } from '@/service/authStore';
import employeePhoto from '@/assets/images/employeePhoto.webp'

const props = defineProps({
  sidebarOpen: Boolean,
  isDark: Boolean
});

const emit = defineEmits(['toggleSidebar', 'toggleDarkMode']);

const employeeId = ref(null);
const employeeName = ref("Empleado Desconocido");
const employeePhoto = ref(employeePhoto);

// Inicializamos los menús como abiertos por defecto para mejor experiencia de usuario
const personalOpen = ref(true);
const adminOpen = ref(true);

const togglePersonal = () => {
  personalOpen.value = !personalOpen.value;
};

const toggleAdmin = () => {
  adminOpen.value = !adminOpen.value;
};

onMounted(async () => {
  // Cargar datos del empleado
  const user = JSON.parse(localStorage.getItem("user"));
  if (user) {
    try {
      const token = localStorage.getItem('token');
      const employeeData = await getEmployeeData(user, token);
      employeeId.value = employeeData.empleados_id;
      employeeName.value = employeeData.nombre || "Empleado Desconocido";
      employeePhoto.value = employeeData.photo || employeePhoto;
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
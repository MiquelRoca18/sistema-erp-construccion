<template>
  <div>
    <div 
      class="fixed top-0 right-0 h-full w-64 bg-gray-100 text-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out z-40 border-l border-r border-gray-300 
             dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700
             lg:relative lg:translate-x-0 lg:flex lg:flex-col overflow-hidden"
      :class="{ 'translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
    >
      <!-- Contenedor con scroll -->
      <div class="flex flex-col h-full overflow-y-auto">
        <!-- Sección del usuario con loader -->
        <div class="p-6 flex flex-col items-center border-b border-gray-300 dark:border-gray-700 flex-shrink-0">
          <!-- Loader para la sección del usuario -->
          <div v-if="isLoading" class="flex flex-col items-center gap-3 py-3 px-4">
            <div class="w-16 h-16 rounded-full border-4 border-gray-400 dark:border-blue-500 shadow-md flex items-center justify-center bg-gray-200 dark:bg-gray-700">
              <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
            <div class="h-6 w-32 bg-gray-300 dark:bg-gray-600 rounded animate-pulse"></div>
          </div>
          
          <!-- Contenido del usuario cuando esté cargado -->
          <router-link 
            v-else
            :to="`/employee/${employeeId}`" 
            class="flex flex-col items-center gap-3 hover:bg-gray-200 dark:hover:bg-gray-700 transition py-3 px-4 rounded-lg"
            @click="closeSidebar"
          >
            <img :src="employeePhoto" alt="Perfil" class="w-16 h-16 rounded-full border-4 border-gray-400 dark:border-blue-500 shadow-md">
            <span v-if="employeeName && employeeName !== 'Empleado Desconocido'" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ employeeName }}</span>
            <div v-else class="h-6 w-32 bg-gray-300 dark:bg-gray-600 rounded animate-pulse"></div>
          </router-link>
        </div>

        <!-- Navegación con capacidad de scroll -->
        <div class="p-4 dark:bg-gray-800 flex-grow overflow-y-auto">
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
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📊</span>
                      <span class="text-gray-800 dark:text-gray-200">Dashboard Personal</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    :to="{ path: `/tasks/${employeeId}`, query: { status: 'pendiente' } }" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📝</span>
                      <span class="text-gray-800 dark:text-gray-200">Tareas Pendientes</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    :to="{ path: `/tasks/${employeeId}` }" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📋</span>
                      <span class="text-gray-800 dark:text-gray-200">Tareas Generales</span>
                    </span>
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
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">🖥</span>
                      <span class="text-gray-800 dark:text-gray-200">Dashboard Admin</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    to="/employees-admin" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">👥</span>
                      <span class="text-gray-800 dark:text-gray-200">Empleados</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    to="/projects" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📁</span>
                      <span class="text-gray-800 dark:text-gray-200">Proyectos</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    to="/tasks-admin" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📝</span>
                      <span class="text-gray-800 dark:text-gray-200">Tareas Generales</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    to="/budgets" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">💰</span>
                      <span class="text-gray-800 dark:text-gray-200">Presupuestos</span>
                    </span>
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
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📊</span>
                      <span class="text-gray-800 dark:text-gray-200">Dashboard</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    :to="{ path: `/tasks/${employeeId}`, query: { status: 'pendiente' } }" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📝</span>
                      <span class="text-gray-800 dark:text-gray-200">Tareas Pendientes</span>
                    </span>
                  </router-link>
                </li>
                <li>
                  <router-link 
                    :to="{ path: `/tasks/${employeeId}` }" 
                    class="block py-3 px-4 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 dark:hover:text-white transition text-base"
                    @click="closeSidebar"
                  >
                    <span class="flex items-center">
                      <span class="text-xl mr-2">📋</span>
                      <span class="text-gray-800 dark:text-gray-200">Tareas Generales</span>
                    </span>
                  </router-link>
                </li>
              </ul>
            </div>
          </template>
        </div>

        <!-- Footer con toggle de dark mode -->
        <div class="p-4 border-t border-gray-300 dark:border-gray-700 flex justify-center flex-shrink-0">
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
import { ref, onMounted, computed, watch } from 'vue';
import { getEmployeeData } from '@/service/authService';
import { userRole } from '@/service/authStore';
import defaultEmployeePhoto from '@/assets/images/employeePhoto.webp'

const props = defineProps({
  sidebarOpen: Boolean,
  isDark: Boolean
});

const emit = defineEmits(['toggleSidebar', 'toggleDarkMode']);

const employeeId = ref(null);
const employeeName = ref("");
const employeePhoto = ref(defaultEmployeePhoto);
const isLoading = ref(true);
const dataLoadError = ref(false);

// Inicializamos los menús como abiertos por defecto para mejor experiencia de usuario
const personalOpen = ref(true);
const adminOpen = ref(true);

const togglePersonal = () => {
  personalOpen.value = !personalOpen.value;
};

const toggleAdmin = () => {
  adminOpen.value = !adminOpen.value;
};

// Función para cargar los datos del empleado
const loadEmployeeData = async () => {
  isLoading.value = true;
  dataLoadError.value = false;
  employeeName.value = "";
  
  try {
    const user = JSON.parse(localStorage.getItem("user"));
    if (!user || !user.empleados_id) {
      throw new Error("No se encontró la información del usuario");
    }
    
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error("No se encontró el token de autenticación");
    }
    
    const employeeData = await getEmployeeData(user.empleados_id, token);
    
    if (employeeData && employeeData.nombre) {
      employeeId.value = employeeData.empleados_id;
      employeeName.value = employeeData.nombre;
      employeePhoto.value = employeeData.photo || defaultEmployeePhoto;
    } else {
      throw new Error("No se pudo obtener la información del empleado");
    }
  } catch (error) {
    // Error handling
    dataLoadError.value = true;
    // Si ocurre un error, intentamos otra vez después de un tiempo
    setTimeout(loadEmployeeData, 3000);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadEmployeeData();
});

// Verificar si el nombre está cargado y si no, mostrar el loader
const showNameLoader = computed(() => {
  return !employeeName.value || employeeName.value === "Empleado Desconocido";
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
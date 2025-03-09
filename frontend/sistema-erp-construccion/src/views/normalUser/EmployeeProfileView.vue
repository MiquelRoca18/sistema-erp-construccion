<template>
  <div class="h-full flex flex-col justify-center w-full min-h-screen max-w-2xl mx-auto md:p-6 transition-colors duration-300">
    <div class="p-4 bg-white dark:bg-gray-800 shadow-md dark:shadow-gray-900/30 rounded-lg transition-colors duration-300">
      <h1 class="text-xl md:text-2xl font-bold text-center mb-3 md:mb-4 text-gray-800 dark:text-white">Perfil del Empleado</h1>

      <!-- Mostrar loader mientras se cargan los datos del empleado -->
      <div v-if="loading" class="flex flex-col items-center py-8">
        <div class="w-12 h-12 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-300">Cargando información del empleado...</p>
      </div>

      <!-- Contenido del perfil cuando los datos están cargados -->
      <div v-else class="animate-fade-in">
        <div class="flex flex-col items-center">
          <img class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-gray-300 dark:border-blue-600" :src="employeePhoto" alt="Foto del Empleado" />
          <h2 class="text-lg md:text-xl font-semibold mt-3 md:mt-4 text-gray-900 dark:text-gray-100">{{ employee.nombre }}</h2>
          <p class="text-gray-700 dark:text-gray-300 text-sm md:text-base">{{ employee.rol }}</p>
        </div>

        <div class="mt-5 md:mt-6">
          <h3 class="text-md md:text-lg font-semibold mb-2 text-gray-800 dark:text-gray-200">Detalles</h3>
          <form @submit.prevent="updateEmployeeData" class="border border-gray-300 dark:border-gray-700 rounded-lg p-3 md:p-4 bg-gray-50 dark:bg-gray-700 transition-colors duration-300">
            <div class="mb-2">
              <label class="block text-gray-800 dark:text-gray-200 text-sm md:text-base">Correo:</label>
              <input v-model="employee.correo" type="email" class="w-full border border-gray-300 dark:border-gray-600 p-2 md:p-2.5 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors" required />
            </div>
            <div class="mb-2">
              <label class="block text-gray-800 dark:text-gray-200 text-sm md:text-base">Teléfono:</label>
              <input v-model="employee.telefono" type="text" class="w-full border border-gray-300 dark:border-gray-600 p-2 md:p-2.5 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors" required />
              <p v-if="errors.telefono" class="text-red-500 dark:text-red-400 text-xs md:text-sm mt-1">{{ errors.telefono }}</p>
            </div>
            <button 
              type="submit" 
              class="mt-4 px-3 md:px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 text-sm md:text-base transition-colors"
              :disabled="updatingData"
            >
              <span v-if="updatingData" class="flex items-center justify-center">
                <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
                Guardando...
              </span>
              <span v-else>Guardar Cambios</span>
            </button>
          </form>
        </div>

        <!-- Notificación de éxito -->
        <div v-if="passwordSuccess" class="mt-4 p-3 md:p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-md shadow-md flex items-center text-sm md:text-base transition-colors">
          <span class="flex-1">✅ Operación realizada con éxito</span>
          <button @click="passwordSuccess = false" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white text-lg">&times;</button>
        </div>

        <!-- Botones mejorados -->
        <div class="mt-5 md:mt-6 flex flex-col space-y-3 md:space-y-4">
          <button 
            @click="showChangePasswordModal = true" 
            class="px-3 md:px-4 py-2 bg-yellow-500 dark:bg-yellow-600 text-white rounded-md hover:bg-yellow-600 dark:hover:bg-yellow-700 w-full text-sm md:text-base transition-colors"
          >
            Cambiar Contraseña
          </button>

          <button 
            @click="goBack" 
            class="px-3 md:px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 w-full text-sm md:text-base transition-colors"
          >
            Volver al Dashboard
          </button>
        </div>

        <!-- Modal para cambiar la contraseña -->
        <div v-if="showChangePasswordModal" class="fixed inset-0 flex items-center justify-center bg-black/50 dark:bg-black/70 z-50">
          <div class="bg-white dark:bg-gray-800 p-5 md:p-6 rounded-lg shadow-md dark:shadow-gray-900/50 w-80 md:w-96 transition-colors duration-300">
            <h2 class="text-md md:text-lg font-bold mb-3 md:mb-4 text-gray-800 dark:text-white">Cambiar Contraseña</h2>
            <form @submit.prevent="changePassword">
              <div class="mb-3 md:mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm md:text-base">Contraseña actual:</label>
                <input v-model="currentPassword" type="password" class="w-full border border-gray-300 dark:border-gray-600 p-2 md:p-2.5 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors" required />
              </div>
              <div class="mb-3 md:mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm md:text-base">Nueva contraseña:</label>
                <input v-model="newPassword" type="password" class="w-full border border-gray-300 dark:border-gray-600 p-2 md:p-2.5 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors" required />
              </div>
              <div class="mb-3 md:mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm md:text-base">Confirmar nueva contraseña:</label>
                <input v-model="confirmPassword" type="password" class="w-full border border-gray-300 dark:border-gray-600 p-2 md:p-2.5 rounded-md bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-100 text-sm md:text-base focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors" required />
                <p v-if="passwordError" class="text-red-500 dark:text-red-400 text-xs md:text-sm mt-1">{{ passwordError }}</p>
              </div>
              <div class="flex justify-end">
                <button @click="showChangePasswordModal = false" class="px-3 md:px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 mr-2 text-sm md:text-base transition-colors">
                  Cancelar
                </button>
                <button 
                  type="submit" 
                  class="px-3 md:px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 text-sm md:text-base transition-colors"
                  :disabled="changingPassword"
                >
                  <span v-if="changingPassword" class="flex items-center justify-center">
                    <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></span>
                    Cambiando...
                  </span>
                  <span v-else>Cambiar</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>



<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getEmployeeData, updateEmployee, changePassword as apiChangePassword } from '@/service/authService';
import defaultEmployeePhoto from '@/assets/images/employeePhoto.webp'

const route = useRoute();
const router = useRouter();
const employee = ref({});
const employeePhoto = ref(defaultEmployeePhoto);
const employeeId = route.params.id;

// Estado de carga
const loading = ref(true);
const updatingData = ref(false);
const changingPassword = ref(false);

// Estado para el modal de cambio de contraseña
const showChangePasswordModal = ref(false);
const passwordSuccess = ref(false);
const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const passwordError = ref('');

// Errores reactivos
const errors = reactive({
  telefono: null,
});

onMounted(async () => {
  loading.value = true;
  try {
    const data = await getEmployeeData(employeeId);
    employee.value = data;
    
    // Modificación para manejar la foto
    if (data.photo) {
      // Si hay una foto en los datos, usarla
      employeePhoto.value = data.photo;
    } else {
      // Si no hay foto, usar la foto por defecto
      employeePhoto.value = defaultEmployeePhoto;
    }
  } catch (error) {
    console.error("Error al cargar el perfil del empleado:", error);
    // Puedes agregar una notificación de error aquí
  } finally {
    // Simulamos una breve demora para mostrar el loader incluso en entornos de desarrollo rápidos
    setTimeout(() => {
      loading.value = false;
    }, 500);
  }
});

// Actualizar datos del empleado
const updateEmployeeData = async () => {
  errors.telefono = null;
  updatingData.value = true;
  
  try {
    await updateEmployee(employeeId, employee.value);
    passwordSuccess.value = true;
    
    // Ocultamos el mensaje después de 3 segundos
    setTimeout(() => {
      passwordSuccess.value = false;
    }, 3000);
  } catch (error) {
    console.error("Error al actualizar:", error);
    if (error.message.includes("teléfono")) {
      errors.telefono = error.message;
    }
  } finally {
    updatingData.value = false;
  }
};

// Cambiar contraseña
const changePassword = async () => {
  passwordError.value = '';
  changingPassword.value = true;

  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = "Las contraseñas no coinciden";
    changingPassword.value = false;
    return;
  }

  try {
    await apiChangePassword({
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
      confirmPassword: confirmPassword.value
    });

    passwordSuccess.value = true;
    showChangePasswordModal.value = false;
    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
    
    // Ocultamos el mensaje después de 3 segundos
    setTimeout(() => {
      passwordSuccess.value = false;
    }, 3000);
  } catch (error) {
    passwordError.value = error.message || "Error al cambiar la contraseña";
  } finally {
    changingPassword.value = false;
  }
};

// Volver al dashboard
const goBack = () => {
  router.push('/dashboard');
};
</script>

<style scoped>
/* Animación para el fade-in cuando se cargan los datos */
.animate-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
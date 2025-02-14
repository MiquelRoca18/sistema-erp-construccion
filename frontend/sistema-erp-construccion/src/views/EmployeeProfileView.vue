<template>
  <div class="h-full flex flex-col justify-center w-full min-h-screen max-w-2xl mx-auto md:p-6">
    <div class="p-4 bg-white  shadow-md rounded-lg">
      <h1 class="text-xl md:text-2xl font-bold text-center mb-3 md:mb-4 text-gray-800">Perfil del Empleado</h1>

      <div class="flex flex-col items-center">
        <img class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-gray-300" :src="employeePhoto" alt="Foto del Empleado" />
        <h2 class="text-lg md:text-xl font-semibold mt-3 md:mt-4 text-gray-900">{{ employee.nombre }}</h2>
        <p class="text-gray-700 text-sm md:text-base">{{ employee.rol }}</p>
      </div>

      <div class="mt-5 md:mt-6">
        <h3 class="text-md md:text-lg font-semibold mb-2 text-gray-800">Detalles</h3>
        <form @submit.prevent="updateEmployeeData" class="border border-gray-300 rounded-lg p-3 md:p-4 bg-gray-50">
          <div class="mb-2">
            <label class="block text-gray-800 text-sm md:text-base">Correo:</label>
            <input v-model="employee.correo" type="email" class="w-full border p-2 md:p-2.5 rounded-md bg-white text-sm md:text-base" required />
          </div>
          <div class="mb-2">
            <label class="block text-gray-800 text-sm md:text-base">Teléfono:</label>
            <input v-model="employee.telefono" type="text" class="w-full border p-2 md:p-2.5 rounded-md bg-white text-sm md:text-base" required />
            <p v-if="errors.telefono" class="text-red-500 text-xs md:text-sm mt-1">{{ errors.telefono }}</p>
          </div>
          <button type="submit" class="mt-4 px-3 md:px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm md:text-base">
            Guardar Cambios
          </button>
        </form>
      </div>

      <!-- Notificación de éxito -->
      <div v-if="passwordSuccess" class="mt-4 p-3 md:p-4 bg-green-100 text-green-700 rounded-md shadow-md flex items-center text-sm md:text-base">
        <span class="flex-1">✅ Contraseña cambiada con éxito</span>
        <button @click="passwordSuccess = false" class="text-gray-500 hover:text-gray-800 text-lg">&times;</button>
      </div>

      <!-- Botones mejorados -->
      <div class="mt-5 md:mt-6 flex flex-col space-y-3 md:space-y-4">
        <button 
          @click="showChangePasswordModal = true" 
          class="px-3 md:px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 w-full text-sm md:text-base"
        >
          Cambiar Contraseña
        </button>

        <button 
          @click="goBack" 
          class="px-3 md:px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full text-sm md:text-base"
        >
          Volver al Dashboard
        </button>
      </div>

      <!-- Modal para cambiar la contraseña -->
      <div v-if="showChangePasswordModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-5 md:p-6 rounded-lg shadow-md w-80 md:w-96">
          <h2 class="text-md md:text-lg font-bold mb-3 md:mb-4">Cambiar Contraseña</h2>
          <form @submit.prevent="changePassword">
            <div class="mb-3 md:mb-4">
              <label class="block text-gray-700 text-sm md:text-base">Contraseña actual:</label>
              <input v-model="currentPassword" type="password" class="w-full border p-2 md:p-2.5 rounded-md bg-white text-sm md:text-base" required />
            </div>
            <div class="mb-3 md:mb-4">
              <label class="block text-gray-700 text-sm md:text-base">Nueva contraseña:</label>
              <input v-model="newPassword" type="password" class="w-full border p-2 md:p-2.5 rounded-md bg-white text-sm md:text-base" required />
            </div>
            <div class="mb-3 md:mb-4">
              <label class="block text-gray-700 text-sm md:text-base">Confirmar nueva contraseña:</label>
              <input v-model="confirmPassword" type="password" class="w-full border p-2 md:p-2.5 rounded-md bg-white text-sm md:text-base" required />
              <p v-if="passwordError" class="text-red-500 text-xs md:text-sm mt-1">{{ passwordError }}</p>
            </div>
            <div class="flex justify-end">
              <button @click="showChangePasswordModal = false" class="px-3 md:px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2 text-sm md:text-base">
                Cancelar
              </button>
              <button type="submit" class="px-3 md:px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm md:text-base">
                Cambiar
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</template>



<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getEmployeeData, updateEmployee, changePassword as apiChangePassword } from '@/service/authService';

const route = useRoute();
const router = useRouter();
const employee = ref({});
const employeePhoto = ref('/src/assets/images/employeePhoto.webp');
const employeeId = route.params.id;

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
  try {
    const data = await getEmployeeData(employeeId);
    employee.value = data;
    employeePhoto.value = data.photo || '/src/assets/images/employeePhoto.webp';
  } catch (error) {
    console.error("Error al cargar el perfil del empleado:", error);
  }
});

// Actualizar datos del empleado
const updateEmployeeData = async () => {
  errors.telefono = null;
  try {
    await updateEmployee(employeeId, employee.value);
    passwordSuccess.value = true; // Mostrar notificación de éxito
  } catch (error) {
    console.error("Error al actualizar:", error);
    if (error.message.includes("teléfono")) {
      errors.telefono = error.message;
    }
  }
};

// Cambiar contraseña
const changePassword = async () => {
  passwordError.value = '';

  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = "Las contraseñas no coinciden";
    return;
  }

  try {
    await apiChangePassword({
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
      confirmPassword: confirmPassword.value
    });

    passwordSuccess.value = true; // Mostrar mensaje de éxito
    showChangePasswordModal.value = false;
    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
  } catch (error) {
    passwordError.value = error.message || "Error al cambiar la contraseña";
  }
};

// Volver al dashboard
const goBack = () => {
  router.push('/dashboard');
};
</script>

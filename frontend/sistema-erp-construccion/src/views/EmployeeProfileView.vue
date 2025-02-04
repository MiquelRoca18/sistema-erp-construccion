<template>
  <div class="max-w-2xl mx-auto p-6 bg-gray-200 shadow-lg rounded-lg">
    <h1 class="text-2xl font-bold text-center mb-4 text-gray-800">Perfil del Empleado</h1>
    
    <div class="flex flex-col items-center">
      <img class="w-32 h-32 rounded-full border-4 border-gray-400" :src="employeePhoto" alt="Foto del Empleado" />
      <h2 class="text-xl font-semibold mt-4 text-gray-900">{{ employee.nombre }}</h2>
      <p class="text-gray-700">{{ employee.rol }}</p>
    </div>

    <div class="mt-6">
      <h3 class="text-lg font-semibold mb-2 text-gray-800">Detalles</h3>
      <!-- Formulario editable -->
      <form @submit.prevent="updateEmployeeData" class="border border-gray-300 rounded-lg p-4 bg-gray-50">
        <div class="mb-2">
          <label class="block text-gray-800">Correo:</label>
          <input v-model="employee.correo" type="email" class="w-full border p-2 rounded-md bg-white" required />
        </div>
        <div class="mb-2">
          <label class="block text-gray-800">Teléfono:</label>
          <input v-model="employee.telefono" type="text" class="w-full border p-2 rounded-md bg-white" required />
          <!-- Mostrar el mensaje de error para el teléfono si existe -->
          <p v-if="errors.telefono" class="text-red-500 text-sm mt-1">{{ errors.telefono }}</p>
        </div>
        <button type="submit" class="mt-4 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
          Guardar Cambios
        </button>
      </form>
    </div>

    <!-- Botón para cambiar la contraseña -->
    <button 
      @click="showChangePasswordModal = true" 
      class="mt-6 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600"
    >
      Cambiar Contraseña
    </button>

    <!-- Modal para cambiar la contraseña -->
    <div v-if="showChangePasswordModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Cambiar Contraseña</h2>
        <form @submit.prevent="changePassword">
          <div class="mb-4">
            <label class="block text-gray-700">Contraseña actual:</label>
            <input 
              v-model="currentPassword" 
              type="password" 
              class="w-full border p-2 rounded-md bg-white" 
              required 
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Nueva contraseña:</label>
            <input 
              v-model="newPassword" 
              type="password" 
              class="w-full border p-2 rounded-md bg-white" 
              required 
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700">Confirmar nueva contraseña:</label>
            <input 
              v-model="confirmPassword" 
              type="password" 
              class="w-full border p-2 rounded-md bg-white" 
              required 
            />
            <p v-if="passwordError" class="text-red-500 text-sm mt-1">{{ passwordError }}</p>
          </div>
          <div class="flex justify-end">
            <button 
              @click="showChangePasswordModal = false" 
              class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md mr-2"
            >
              Cancelar
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Cambiar
            </button>
          </div>
        </form>
      </div>
    </div>

    <button @click="goBack" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
      Volver al Dashboard
    </button>
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
const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const passwordError = ref('');

// Errores reactivos
const errors = reactive({
  fecha_contratacion: null,
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
  errors.fecha_contratacion = null;
  errors.telefono = null;

  try {
    await updateEmployee(employeeId, employee.value);
    alert("Perfil actualizado con éxito");
  } catch (error) {
    console.error("Error al actualizar:", error);

    if (error.message.includes("fecha")) {
      errors.fecha_contratacion = error.message;
    }
    if (error.message.includes("teléfono")) {
      errors.telefono = error.message;
    }
  }
};

// Cambiar contraseña
const changePassword = async () => {
  passwordError.value = ''; // Limpiar errores previos

  if (newPassword.value !== confirmPassword.value) {
    passwordError.value = "Las contraseñas no coinciden";
    return;
  }

  try {
    await apiChangePassword(employeeId, {
      currentPassword: currentPassword.value,
      newPassword: newPassword.value,
    });
    alert("Contraseña cambiada con éxito");
    showChangePasswordModal.value = false; // Cerrar el modal
    currentPassword.value = '';
    newPassword.value = '';
    confirmPassword.value = '';
  } catch (error) {
    console.error("Error al cambiar la contraseña:", error);
    passwordError.value = error.message || "Error al cambiar la contraseña";
  }
};

// Volver al dashboard
const goBack = () => {
  router.push('/dashboard');
};
</script>

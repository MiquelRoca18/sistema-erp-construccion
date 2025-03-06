<template>
  <div class="w-full min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4 transition-colors duration-300">
    <div class="w-full max-w-lg p-8 md:p-10 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-2xl transition-all duration-300">
      <h1 class="text-3xl font-bold text-center mb-6 text-gray-800 dark:text-gray-100">
        Iniciar sesión
      </h1>
      <form @submit.prevent="handleSubmit">
        <!-- Campo de nombre de usuario -->
        <div class="mb-5">
          <label for="username" class="block text-lg font-medium text-gray-700 dark:text-gray-300">
            Nombre de usuario
          </label>
          <input
            type="text"
            id="username"
            v-model="username"
            placeholder="Introduce tu nombre de usuario"
            required
            class="mt-2 p-3 w-full border border-gray-400 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-gray-50 dark:bg-gray-700 text-lg text-gray-900 dark:text-gray-100 transition-colors"
            :disabled="isLoading"
          />
        </div>
        <!-- Campo de contraseña -->
        <div class="mb-6">
          <label for="password_hash" class="block text-lg font-medium text-gray-700 dark:text-gray-300">
            Contraseña
          </label>
          <input
            type="password"
            id="password_hash"
            v-model="password_hash"
            placeholder="Introduce tu contraseña"
            required
            class="mt-2 p-3 w-full border border-gray-400 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-gray-50 dark:bg-gray-700 text-lg text-gray-900 dark:text-gray-100 transition-colors"
            :disabled="isLoading"
          />
        </div>
        <!-- Botón de envío con loader -->
        <button
          type="submit"
          class="w-full py-3 bg-blue-600 dark:bg-blue-500 text-white text-lg font-semibold rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all flex items-center justify-center"
          :disabled="isLoading"
        >
          <span v-if="isLoading" class="inline-block mr-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ isLoading ? 'Iniciando sesión...' : 'Ingresar' }}
        </button>
      </form>
      <!-- Mensaje de error (con color adaptado al dark mode) -->
      <p v-if="errorMessage" class="text-red-500 dark:text-red-400 text-lg mt-4 text-center">
        {{ errorMessage }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login as authLogin } from '@/service/authStore';
import { login as apiLogin } from '@/service/authService';

const username = ref('');
const password_hash = ref('');
const errorMessage = ref('');
const isLoading = ref(false);
const router = useRouter();

const handleSubmit = async () => {
  // Limpiar error previo
  errorMessage.value = '';
  
  // Activar el loader
  isLoading.value = true;
  
  try {
    const response = await apiLogin({ username: username.value, password_hash: password_hash.value });
    authLogin({ token: response.token, empleados_id: response.empleados_id, rol: response.rol });
    
    // Pequeño retraso para mostrar el loader (opcional - puedes eliminar esto)
    await new Promise(resolve => setTimeout(resolve, 300));
    
    router.push('/dashboard');
  } catch (error: any) {
    errorMessage.value = error.message || 'Ocurrió un error. Por favor, inténtalo de nuevo.';
  } finally {
    // Desactivar el loader en caso de error
    // (en caso de éxito, el usuario será redirigido)
    isLoading.value = false;
  }
};
</script>
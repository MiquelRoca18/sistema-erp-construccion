<template>
  <div class="min-h-screen flex items-center justify-center bg-[#d6d8db] px-4">
    <div class="w-full max-w-lg p-10 bg-gray-200 border border-gray-300 rounded-lg shadow-lg">
      <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">Iniciar sesión</h1>
      <form @submit.prevent="handleSubmit">
        <div class="mb-5">
          <label for="username" class="block text-lg font-medium text-gray-700">Nombre de usuario</label>
          <input
            type="text"
            id="username"
            v-model="username"
            class="mt-2 p-3 w-full border border-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-lg"
            placeholder="Introduce tu nombre de usuario"
            required
          />
        </div>
        <div class="mb-6">
          <label for="password_hash" class="block text-lg font-medium text-gray-700">Contraseña</label>
          <input
            type="password"
            id="password_hash"
            v-model="password_hash"
            class="mt-2 p-3 w-full border border-gray-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-lg"
            placeholder="Introduce tu contraseña"
            required
          />
        </div>
        <button
          type="submit"
          class="w-full py-3 bg-blue-600 text-white text-lg font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
        >
          Ingresar
        </button>
      </form>
      <p v-if="errorMessage" class="text-red-500 text-lg mt-4 text-center">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login as authLogin } from '@/service/authStore'; // Importar la función login del store
import { login as apiLogin } from '@/service/authService'; // Importar la función login del servicio API

const username = ref('');
const password_hash = ref('');
const errorMessage = ref('');
const router = useRouter();

const handleSubmit = async () => {
  try {
    const response = await apiLogin({ username: username.value, password_hash: password_hash.value });

    // Usar el store para actualizar el estado de autenticación
    authLogin({ token: response.token, empleados_id: response.empleados_id });

    // Redirigir al dashboard
    router.push('/dashboard');
  } catch (error: any) {
    errorMessage.value = error.message || 'Ocurrió un error. Por favor, inténtalo de nuevo.';
  }
};
</script>
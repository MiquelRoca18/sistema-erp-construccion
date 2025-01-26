<template>
  <div class="max-w-sm mx-auto p-8 bg-white border border-gray-300 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-6">Iniciar sesión</h1>
    <form @submit.prevent="handleSubmit">
      <div class="mb-4">
        <label for="username" class="block text-sm font-medium text-gray-700">Nombre de usuario</label>
        <input
          type="text"
          id="username"
          v-model="username"
          class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Introduce tu nombre de usuario"
          required
        />
      </div>
      <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
        <input
          type="password"
          id="password"
          v-model="password"
          class="mt-1 p-2 w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Introduce tu contraseña"
          required
        />
      </div>
      <button
        type="submit"
        class="w-full py-2 bg-blue-500 text-white font-semibold rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        Ingresar
      </button>
    </form>
    <p v-if="errorMessage" class="text-red-500 text-sm mt-4">{{ errorMessage }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login } from '@/service/authService';

const username = ref('');
const password = ref('');
const errorMessage = ref('');
const router = useRouter();

const handleSubmit = async () => {
  try {
    const response = await login({ username: username.value, password: password.value });
    localStorage.setItem('token', response.token); // Guardar el token en el almacenamiento local
    router.push('/dashboard'); // Redirigir al dashboard
  } catch (error: any) {
    errorMessage.value = error.message || 'Ocurrió un error. Por favor, inténtalo de nuevo.';
  }
};
</script>

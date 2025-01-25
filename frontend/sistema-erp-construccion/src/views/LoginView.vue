<template>
  <div class="max-w-sm mx-auto p-8 bg-white border border-gray-300 rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold text-center mb-6">Iniciar sesión</h1>
    <form @submit.prevent="handleSubmit">
      <div class="mb-4">
        <label for="username" class="block text-sm font-medium text-gray-700">Nombre de usuario</label>
        <input
          type="username"
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
  </div>
</template>

<script setup>
import { ref } from 'vue';

const username = ref('');
const password = ref('');

const handleSubmit = () => {
  alert('Iniciando sesión con:', username.value, password.value);
  // Aquí iría la lógica para enviar el login al backend.
};
</script>

<style scoped>
/* No es necesario agregar CSS ya que estamos utilizando Tailwind */
</style>

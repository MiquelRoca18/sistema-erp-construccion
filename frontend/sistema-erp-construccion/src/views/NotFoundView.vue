<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 transition-colors duration-500">
      <div class="max-w-md w-full text-center bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
        <h1 class="text-6xl font-extrabold text-blue-600 dark:text-blue-400 mb-4">404</h1>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-2">Página no encontrada</h2>
        <p class="text-gray-600 dark:text-gray-300 mb-6">
          Lo sentimos, la página que buscas no existe. Es posible que se haya movido o eliminado.
        </p>
        <button
          @click="goBack"
          class="w-full px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-md transition duration-300"
        >
          Volver al inicio
        </button>
      </div>
    </div>
  </template>
  
  <script setup>
  import { useRouter } from 'vue-router'
  import { isAuthenticated } from '@/service/authStore'
  
  const router = useRouter()
  
  function goBack() {
    if (isAuthenticated.value) {
      router.push({ name: 'dashboard' })
    } else {
      router.push({ name: 'login' })
    }
  }
  </script>
  
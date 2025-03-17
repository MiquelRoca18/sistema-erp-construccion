<template>
  <router-link to="/projects" class="block">
    <div class="p-4 sm:p-6 bg-gradient-to-r from-green-100 to-green-200 dark:from-green-800 dark:to-green-900 rounded-lg shadow-md sm:shadow-lg transform transition hover:scale-102 sm:hover:scale-105">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl sm:text-2xl font-bold text-green-800 dark:text-green-200">Proyectos</h2>
          <p v-if="!isLoading" class="mt-1 sm:mt-2 text-sm sm:text-base text-green-700 dark:text-green-400">
            Total: {{ totalProjects }}
          </p>
          <div v-else class="mt-1 sm:mt-2 flex items-center">
            <div class="h-3 w-3 sm:h-4 sm:w-4 border-2 border-green-700 dark:border-green-400 border-t-transparent rounded-full animate-spin"></div>
            <span class="ml-1.5 sm:ml-2 text-sm sm:text-base text-green-700 dark:text-green-400">Cargando...</span>
          </div>
        </div>
        <div>
          <svg class="w-10 h-10 sm:w-12 sm:h-12 text-green-800 dark:text-green-200" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 8h14v-2H7v2zm0-4h14v-2H7v2zm0-6v2h14V7H7z"/>
          </svg>
        </div>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { getProjects } from '@/service/projectService';

const totalProjects = ref(0);
const isLoading = ref(true);

const fetchTotalProjects = async () => {
  isLoading.value = true;
  try {
    const projects = await getProjects();
    totalProjects.value = projects.length;
  } catch (err: any) {
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchTotalProjects();
});
</script>

<style scoped>
/* Agrega una transición más suave para hover en móviles */
@media (max-width: 640px) {
  .hover\:scale-102:hover {
    transform: scale(1.02);
  }
}
</style>
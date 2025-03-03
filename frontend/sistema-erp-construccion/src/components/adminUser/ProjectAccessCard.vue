<template>
  <router-link to="/projects" class="block">
    <div class="p-6 bg-gradient-to-r from-green-100 to-green-200 dark:from-green-800 dark:to-green-900 rounded-lg shadow-lg transform transition hover:scale-105">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-green-800 dark:text-green-200">Proyectos</h2>
        </div>
        <div>
          <svg class="w-12 h-12 text-green-800 dark:text-green-200" fill="currentColor" viewBox="0 0 24 24">
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
const fetchTotalProjects = async () => {
  try {
    const projects = await getProjects();
    totalProjects.value = projects.length;
  } catch (err: any) {
    console.error('Error al obtener proyectos:', err);
  }
};
onMounted(() => {
  fetchTotalProjects();
});
</script>
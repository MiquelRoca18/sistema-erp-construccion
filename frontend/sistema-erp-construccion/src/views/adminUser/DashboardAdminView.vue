<template>
    <div class="min-h-screen bg-gray-100 p-6 flex items-center justify-center">
      <div class="w-full max-w-7xl">
        <h1 class="text-4xl font-bold text-center mb-10">Dashboard Admin</h1>
        
        <!-- Grid de Access Cards (2 columnas para simetría) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10">
          <EmployeeAccessCard />
          <ProjectAccessCard />
          <TaskAccessCard />
          <BudgetAccessCard />
        </div>
        
        <!-- Sección KPI centrada -->
        <div class="bg-white rounded-lg shadow p-8">
          <AdminKpiComponent />
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import EmployeeAccessCard from '@/components/adminUser/EmployeeAccessCard.vue';
  import ProjectAccessCard from '@/components/adminUser/ProjectAccessCard.vue';
  import TaskAccessCard from '@/components/adminUser/TaskAccessCard.vue';
  import BudgetAccessCard from '@/components/adminUser/BudgetAccessCard.vue';
  import AdminKpiComponent from '@/components/adminUser/AdminKpiComponent.vue';
  </script>
  
  <style scoped>
  /* Ajusta estilos adicionales si fuera necesario */
  </style>
  
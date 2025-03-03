<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 p-6 flex items-center justify-center transition-colors duration-300">
    <div class="w-full max-w-7xl">
      <h1 class="text-4xl font-bold text-center mb-10 text-gray-800 dark:text-white transition-colors duration-300">Dashboard Admin</h1>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-10">
        <EmployeeAccessCard />
        <ProjectAccessCard />
        <TaskAccessCard />
        <BudgetAccessCard />
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-900/30 p-8 transition-colors duration-300">
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
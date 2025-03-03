<template>
  <div class="flex flex-col items-center">
    <!-- Foto del empleado -->
    <img
      class="w-32 h-32 rounded-full border-4 border-gray-300 dark:border-gray-600"
      :src="employeePhoto"
      alt="Employee Photo"
    />
    <!-- Nombre del empleado dinámico -->
    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4">{{ employeeName }}</h2>
  </div>
</template>

<script setup>
defineProps({
  employeeName: String,
  employeePhoto: String
});
</script>
<template>
  <div class="flex flex-col items-center bg-white p-6 rounded-lg shadow-md max-w-sm mx-auto cursor-pointer">
    <!-- Foto del empleado -->
    <img
      class="w-32 h-32 rounded-full border-4 border-gray-300"
      :src="employeePhoto"
      alt="Employee Photo"
    />
    <!-- Nombre del empleado dinámico -->
    <h2 class="text-xl font-bold text-gray-800 mt-4">{{ employeeName }}</h2>
    <!-- Botón de Logout -->
    <button
      @click="logout"
      class="mt-6 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
    >
      Logout
    </button>
  </div>
</template>

<script setup>
defineProps({
  employeeName: String,
  employeePhoto: String
});

const logout = () => {
  localStorage.removeItem("token");
  localStorage.removeItem("user");
  window.location.href = "/"; 
};
</script>

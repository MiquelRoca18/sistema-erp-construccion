<template>
  <div id="app">
    <EmployeeProfileComponent />
  </div>
</template>

<script setup lang="ts">
import EmployeeProfileComponent from './components/EmployeeProfileComponent.vue';
</script>

<style scoped>
@import "tailwindcss";

#app {
  font-family: Arial, sans-serif;
  display: flex;
  flex-direction: column;
  height: 100vh;
}

.layout {
  display: flex;
  flex: 1;
  overflow: hidden;
}
</style>

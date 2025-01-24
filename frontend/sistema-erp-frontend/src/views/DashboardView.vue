<template>
  <div>
    <!-- Employee Profile Component -->
    <EmployeeProfile
      employeePhoto="/path/to/photo.jpg"
      employeeName="Juan Pérez"
    />
  </div>
</template>

<script lang="ts">
import EmployeeProfileComponent from "../components/EmployeeProfileComponent.vue";

export default {
  components: {
    EmployeeProfileComponent,
  },
};
</script>

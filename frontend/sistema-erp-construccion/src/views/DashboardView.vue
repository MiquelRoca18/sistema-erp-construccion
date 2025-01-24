<template>
  <div>
    <!-- Employee Profile Component -->
    <EmployeeProfile
      employeePhoto="/path/to/photo.jpg"
      employeeName="Juan Pérez"
    />
  </div>
</template>

<script>
import EmployeeProfile from "../components/EmployeeProfile.vue";

export default {
  components: {
    EmployeeProfile,
  },
};
</script>

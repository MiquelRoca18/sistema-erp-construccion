<template>
  <div>
    <!-- Employee Profile Component -->
    <EmployeeProfileComponent
      employeePhoto="/path/to/photo.jpg"
      employeeName="Juan Pérez"
    />
  </div>
</template>

<script>
import EmployeeProfileComponent from "@/components/EmployeeProfileComponent.vue";

export default {
  components: {
    EmployeeProfileComponent,
  },
};
</script>

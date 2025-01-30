<template>
    <div class="max-w-2xl mx-auto p-6 bg-white shadow-lg rounded-lg">
      <h1 class="text-2xl font-bold text-center mb-4">Perfil del Empleado</h1>
      
      <div class="flex flex-col items-center">
        <img class="w-32 h-32 rounded-full border-4 border-gray-300"
          :src="employeePhoto"
          alt="Foto del Empleado"
        />
        <h2 class="text-xl font-semibold mt-4">{{ employee.nombre }}</h2>
        <p class="text-gray-600">{{ employee.rol }}</p>
      </div>
  
      <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Detalles</h3>
  
        <!-- 📌 Formulario Editable -->
        <form @submit.prevent="updateEmployeeData" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
          <div class="mb-2">
            <label class="block text-gray-700">Nombre:</label>
            <input v-model="employee.nombre" type="text" class="w-full border p-2 rounded-md" required />
          </div>
  
          <div class="mb-2">
            <label class="block text-gray-700">Rol:</label>
            <input v-model="employee.rol" type="text" class="w-full border p-2 rounded-md" required />
          </div>
  
          <div class="mb-2">
            <label class="block text-gray-700">Correo:</label>
            <input v-model="employee.correo" type="email" class="w-full border p-2 rounded-md" required />
          </div>
  
          <div class="mb-2">
            <label class="block text-gray-700">Teléfono:</label>
            <input v-model="employee.telefono" type="text" class="w-full border p-2 rounded-md" required />
            <!-- Mostrar el mensaje de error para el teléfono si existe -->
            <p v-if="errors.telefono" class="text-red-500 text-sm mt-1">{{ errors.telefono }}</p>
          </div>
  
          <div class="mb-2">
            <label class="block text-gray-700">Fecha de Contratación:</label>
            <input v-model="employee.fecha_contratacion" type="date" class="w-full border p-2 rounded-md" required />
            <!-- Mostrar el mensaje de error para la fecha de contratación si existe -->
            <p v-if="errors.fecha_contratacion" class="text-red-500 text-sm mt-1">{{ errors.fecha_contratacion }}</p>
          </div>
  
          <button type="submit" class="mt-4 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
            Guardar Cambios
          </button>
        </form>
      </div>
  
      <button @click="goBack" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
        Volver al Dashboard
      </button>
    </div>
</template>
  
<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getEmployeeData, updateEmployee } from '@/service/authService';
  
const route = useRoute();
const router = useRouter();
const employee = ref({});
const employeePhoto = ref('/src/assets/images/employeePhoto.webp');
const employeeId = route.params.id;
  
// Usamos `reactive` para que Vue reaccione a los cambios en el objeto de errores
const errors = reactive({
  fecha_contratacion: null,
  telefono: null,  
});
  
onMounted(async () => {
  try {
    const data = await getEmployeeData(employeeId);
    employee.value = data;
    employeePhoto.value = data.photo || '/src/assets/images/employeePhoto.webp';
  } catch (error) {
    console.error("Error al cargar el perfil del empleado:", error);
  }
});
  
const updateEmployeeData = async () => {
  // Limpiar errores previos antes de intentar actualizar
  errors.fecha_contratacion = null;
  errors.telefono = null;  // Limpiar el error del teléfono

  try {
    // Intentar actualizar los datos del empleado
    await updateEmployee(employeeId, employee.value);
    alert("Perfil actualizado con éxito");
  } catch (error) {
    console.error("Error al actualizar:", error);
    
    // Si el error está relacionado con la fecha
    if (error.message.includes("fecha")) {
      errors.fecha_contratacion = error.message;
    }

    // Si el error está relacionado con el teléfono
    if (error.message.includes("teléfono")) {
      errors.telefono = error.message;
    }

  }
};

const goBack = () => {
  router.push('/dashboard');
};
</script>

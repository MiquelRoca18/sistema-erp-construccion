<template>
    <div class="min-h-screen bg-gray-50 p-8">
      <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <!-- Encabezado y acción -->
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-3xl font-bold text-gray-800">Gestión de Empleados</h1>
          <button
            @click="openModal"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
          >
            Nuevo Empleado
          </button>
        </div>
  
        <!-- Filtro -->
        <div class="mb-6">
          <input
            type="text"
            v-model="searchTerm"
            placeholder="Buscar por nombre..."
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
          />
        </div>
  
        <!-- Tabla de empleados -->
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="bg-gradient-to-r from-blue-100 to-blue-200 text-gray-700">
                <th class="px-6 py-3 text-left font-semibold">ID</th>
                <th class="px-6 py-3 text-left font-semibold">Nombre</th>
                <th class="px-6 py-3 text-left font-semibold">Rol</th>
                <th class="px-6 py-3 text-left font-semibold">Teléfono</th>
                <th class="px-6 py-3 text-left font-semibold">Correo</th>
                <th class="px-6 py-3 text-left font-semibold">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="employee in paginatedEmployees"
                :key="employee.empleados_id"
                class="bg-white shadow rounded-lg hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4">{{ employee.empleados_id }}</td>
                <td class="px-6 py-4">{{ employee.nombre }}</td>
                <td class="px-6 py-4">{{ employee.rol }}</td>
                <td class="px-6 py-4">{{ employee.telefono }}</td>
                <td class="px-6 py-4">{{ employee.correo }}</td>
                <td class="px-6 py-4">
                  <div class="flex space-x-2">
                    <button
                      @click="editEmployee(employee)"
                      class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition"
                    >
                      Editar
                    </button>
                    <button
                      @click="deleteEmployee(employee)"
                      class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
                    >
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Si no hay empleados en la página, mostrar un mensaje -->
              <tr v-if="paginatedEmployees.length === 0">
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                  No se encontraron empleados.
                </td>
              </tr>
              <!-- Agregar filas vacías para mantener 5 filas siempre -->
              <tr
                v-for="n in missingRows"
                :key="'empty-' + n"
                class="h-16 bg-transparent"
              >
                <td class="px-6 py-4" colspan="6"></td>
              </tr>
            </tbody>
          </table>
        </div>
  
        <!-- Paginación -->
        <div v-if="totalPages > 1" class="mt-6 flex justify-center space-x-4">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 transition"
          >
            Anterior
          </button>
          <span class="px-4 py-2 text-gray-700">
            Página {{ currentPage }} de {{ totalPages }}
          </span>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 transition"
          >
            Siguiente
          </button>
        </div>
  
        <div v-if="loading" class="text-center mt-4 text-gray-500">
          Cargando empleados...
        </div>
        <div v-if="error" class="text-center mt-4 text-red-500">
          {{ error }}
        </div>
  
        <!-- Modal para crear empleado -->
        <CreateEmployeeModal v-if="showModal" @close="closeModal" @created="fetchEmployees" />
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, computed, onMounted, watch } from 'vue';
  import { getEmployees } from '@/service/employeeService';
  import CreateEmployeeModal from '@/components/adminUser/CreateEmployeeModal.vue';
  
  const employees = ref<any[]>([]);
  const loading = ref(true);
  const error = ref('');
  const searchTerm = ref('');
  const currentPage = ref(1);
  const pageSize = 5;
  const showModal = ref(false);
  
  const fetchEmployees = async () => {
    try {
      loading.value = true;
      const data = await getEmployees();
      employees.value = data;
    } catch (err: any) {
      error.value = err.message || 'Error al obtener empleados.';
    } finally {
      loading.value = false;
    }
  };
  
  onMounted(() => {
    fetchEmployees();
  });
  
  // Reiniciar la página actual al cambiar el término de búsqueda
  watch(searchTerm, () => {
    currentPage.value = 1;
  });
  
  const filteredEmployees = computed(() => {
    if (!searchTerm.value) return employees.value;
    return employees.value.filter((emp) =>
      emp.nombre.toLowerCase().includes(searchTerm.value.toLowerCase())
    );
  });
  
  const totalPages = computed(() => {
    return Math.ceil(filteredEmployees.value.length / pageSize);
  });
  
  const paginatedEmployees = computed(() => {
    const start = (currentPage.value - 1) * pageSize;
    return filteredEmployees.value.slice(start, start + pageSize);
  });
  
  // Computar cuántas filas vacías faltan para llegar a 5
  const missingRows = computed(() => {
    const missing = pageSize - paginatedEmployees.value.length;
    return missing > 0 ? missing : 0;
  });
  
  const prevPage = () => {
    if (currentPage.value > 1) {
      currentPage.value--;
    }
  };
  
  const nextPage = () => {
    if (currentPage.value < totalPages.value) {
      currentPage.value++;
    }
  };
  
  const openModal = () => {
    showModal.value = true;
  };
  
  const closeModal = () => {
    showModal.value = false;
  };
  
  const createEmployee = () => {
    openModal();
  };
  
  const editEmployee = (employee: any) => {
    console.log("Editar empleado", employee);
  };
  
  const deleteEmployee = (employee: any) => {
    console.log("Eliminar empleado", employee);
  };
  </script>
  
  <style scoped>
  table {
    border-collapse: separate;
    border-spacing: 0 0.5rem;
  }
  thead tr th {
    border: none;
  }
  tbody tr {
    background: white;
    border-radius: 0.5rem;
  }
  tbody tr td {
    border: none;
  }
  </style>
  
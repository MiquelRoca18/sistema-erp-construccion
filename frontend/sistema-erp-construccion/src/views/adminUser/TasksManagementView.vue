<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-8">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow-lg">
      <!-- Encabezado y acción -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 text-center sm:text-left mb-4 sm:mb-0">
          Gestión de Tareas
        </h1>
        <button
          @click="openModal"
          class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
        >
          Nueva Tarea
        </button>
      </div>

      <!-- Filtro Desplegable (se mantiene el mismo código de filtros) -->
      <div class="mb-6">
        <button @click="toggleFilter" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
          Filtros
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 inline-block ml-2 transition-transform" :class="{'rotate-180': showFilter}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <transition name="fade">
          <div v-if="showFilter" class="mt-4 p-4 border rounded-lg shadow bg-gray-50">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Filtrar por Proyecto -->
              <div>
                <label class="block text-gray-700 mb-1">Proyecto</label>
                <select v-model="filter.project" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                  <option value="">Todos</option>
                  <option v-for="project in projects" :key="project" :value="project">
                    {{ project }}
                  </option>
                </select>
              </div>
              <!-- Filtrar por Fecha Inicio -->
              <div>
                <label class="block text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" v-model="filter.startDate" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
              </div>
              <!-- Filtrar por Fecha Fin -->
              <div>
                <label class="block text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" v-model="filter.endDate" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
              </div>
              <!-- Filtrar por Estado -->
              <div>
                <label class="block text-gray-700 mb-1">Estado</label>
                <select v-model="filter.state" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                  <option value="">Todos</option>
                  <option value="pendiente">Pendiente</option>
                  <option value="en progreso">En Progreso</option>
                  <option value="finalizado">Finalizado</option>
                </select>
              </div>
              <!-- Filtrar por Empleado -->
              <div>
                <label class="block text-gray-700 mb-1">Empleado</label>
                <select v-model="filter.employee" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400">
                  <option value="">Todos</option>
                  <option v-for="employee in employees" :key="employee" :value="employee">
                    {{ employee }}
                  </option>
                </select>
              </div>
            </div>
            <div class="mt-4 text-right">
              <button @click="clearFilters" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                Limpiar filtros
              </button>
            </div>
          </div>
        </transition>
      </div>

      <!-- Vista Mobile: Tarjetas -->
      <div class="block sm:hidden">
        <div
          v-for="task in paginatedTasks"
          :key="task.tareas_id"
          class="bg-white p-4 rounded-lg shadow mb-4 cursor-pointer hover:bg-gray-100 transition"
          @click="openViewModal(task)"
        >
          <div class="flex justify-between items-center">
            <div>
              <p class="text-lg font-bold">ID: {{ task.tareas_id }}</p>
              <p class="text-base">{{ task.nombre_tarea }}</p>
            </div>
            <div class="flex space-x-2">
              <button
                @click.stop="openEditModal(task)"
                class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition text-sm"
              >
                Editar
              </button>
              <button
                @click.stop="openDeleteModal(task)"
                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm"
              >
                Eliminar
              </button>
              <button
                @click.stop="openAssignModal(task)"
                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-sm"
              >
                Asignar
              </button>
            </div>
          </div>
        </div>
        <div v-if="paginatedTasks.length === 0 && !loading" class="text-center text-gray-500">
          No se encontraron tareas.
        </div>
        <!-- Espaciado para mantener 5 elementos -->
        <div v-for="n in missingRows" :key="'empty-' + n" class="h-16"></div>
      </div>

      <!-- Vista Desktop: Tabla -->
      <div class="hidden sm:block overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gradient-to-r from-orange-100 to-orange-200 text-orange-800">
              <th class="px-6 py-3 text-left font-semibold">ID</th>
              <th class="px-6 py-3 text-left font-semibold">Tarea</th>
              <th class="px-6 py-3 text-left font-semibold hidden xl:table-cell">Estado</th>
              <th class="px-6 py-3 text-left font-semibold hidden xl:table-cell">Proyecto</th>
              <th class="px-6 py-3 text-left font-semibold">Empleado(s)</th>
              <th class="px-6 py-3 text-left font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="task in paginatedTasks"
              :key="task.tareas_id"
              class="bg-white shadow rounded-lg transition-colors cursor-pointer hover:bg-gray-50"
              @click="openViewModal(task)"
            >
              <td class="px-6 py-4">{{ task.tareas_id }}</td>
              <td class="px-6 py-4">{{ task.nombre_tarea }}</td>
              <td class="px-6 py-4 hidden xl:table-cell">{{ task.estado }}</td>
              <td class="px-6 py-4 hidden xl:table-cell">{{ task.nombre_proyecto }}</td>
              <td class="px-6 py-4">{{ task.empleado_nombre }}</td>
              <td class="px-6 py-4">
                <div class="flex flex-col sm:flex-row gap-1 sm:gap-2" @click.stop>
                  <button
                    @click="openEditModal(task)"
                    class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-xs sm:text-sm"
                  >
                    Editar
                  </button>
                  <button
                    @click="openDeleteModal(task)"
                    class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs sm:text-sm"
                  >
                    Eliminar
                  </button>
                  <button
                    @click="openAssignModal(task)"
                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-xs sm:text-sm"
                  >
                    Asignar
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="paginatedTasks.length === 0 && !loading">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                No se encontraron tareas.
              </td>
            </tr>
            <!-- Filas vacías para mantener siempre 5 filas -->
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
      <div v-if="totalPages > 1" class="mt-6 flex items-center justify-center space-x-2">
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-600 text-white hover:bg-orange-700 disabled:opacity-50 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <div class="flex space-x-2">
          <button 
            v-for="page in pages" 
            :key="page" 
            @click="goToPage(page)" 
            class="w-10 h-10 rounded-full border border-orange-600 text-orange-600 flex items-center justify-center transition hover:bg-orange-600 hover:text-white"
            :class="{'bg-orange-600 text-white': page === currentPage}"
          >
            {{ page }}
          </button>
        </div>
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-orange-600 text-white hover:bg-orange-700 disabled:opacity-50 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <div v-if="loading" class="text-center mt-4 text-gray-500">
        Cargando tareas...
      </div>
      <div v-if="error" class="text-center mt-4 text-red-500">
        {{ error }}
      </div>

      <!-- Modales para crear, editar, eliminar, ver y asignar tarea -->
      <CreateTaskModal v-if="showModal" @close="closeModal" @created="fetchTasks" />
      <EditTaskModal
        v-if="taskToEdit"
        :task="taskToEdit"
        @close="closeEditModal"
        @updated="fetchTasks"
      />
      <DeleteTaskModal
        v-if="taskToDelete"
        :task="taskToDelete"
        @close="closeDeleteModal"
        @deleted="deleteTaskConfirmed"
      />
      <TaskViewModal
        v-if="taskToView"
        :task="taskToView"
        @close="closeViewModal"
      />
      <AssignEmployeesModal
        v-if="taskToAssign"
        :task="taskToAssign"
        @close="closeAssignModal"
        @updated="fetchTasks"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { getAllCompanyTasks, deleteTask } from '@/service/taskService';
import CreateTaskModal from '@/components/adminUser/CreateTaskModal.vue';
import EditTaskModal from '@/components/adminUser/EditTaskModal.vue';
import DeleteTaskModal from '@/components/adminUser/DeleteTaskModal.vue';
import TaskViewModal from '@/components/adminUser/TaskViewModal.vue';
import AssignEmployeesModal from '@/components/adminUser/AssignEmployeesModal.vue';

const tasks = ref<any[]>([]);
const loading = ref(true);
const error = ref('');
const currentPage = ref(1);
const pageSize = 5;
const showModal = ref(false);
const taskToEdit = ref(null);
const taskToDelete = ref(null);
const taskToView = ref(null);
const taskToAssign = ref(null);

const showFilter = ref(false);
const filter = ref({
  project: '',
  startDate: '',
  endDate: '',
  state: '',
  employee: ''
});

// Cada vez que se modifiquen los filtros se resetea la página actual a 1
watch(filter, () => {
  currentPage.value = 1;
}, { deep: true });

const toggleFilter = () => {
  showFilter.value = !showFilter.value;
};

const clearFilters = () => {
  filter.value = {
    project: '',
    startDate: '',
    endDate: '',
    state: '',
    employee: ''
  };
};

const fetchTasks = async () => {
  try {
    loading.value = true;
    const data = await getAllCompanyTasks();
    tasks.value = data;
  } catch (err: any) {
    error.value = err.message || 'Error al obtener tareas';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchTasks();
});

// Opciones de filtro
const projects = computed(() => {
  const all = tasks.value.map(task => task.nombre_proyecto);
  return [...new Set(all)];
});

const employees = computed(() => {
  const employeeSet = new Set<string>();
  tasks.value.forEach(task => {
    if (task.empleado_nombre) {
      task.empleado_nombre.split(',').forEach(emp => {
        const name = emp.trim();
        if (name) employeeSet.add(name);
      });
    }
  });
  return [...employeeSet];
});

// Filtrado combinando los filtros seleccionados
const filteredTasks = computed(() => {
  return tasks.value.filter(task => {
    let valid = true;
    if (filter.value.project && task.nombre_proyecto !== filter.value.project) {
      valid = false;
    }
    if (filter.value.state && task.estado.toLowerCase() !== filter.value.state.toLowerCase()) {
      valid = false;
    }
    if (filter.value.employee) {
      const taskEmployees = task.empleado_nombre ? task.empleado_nombre.split(',').map(e => e.trim()) : [];
      if (!taskEmployees.includes(filter.value.employee)) {
        valid = false;
      }
    }
    if (filter.value.startDate && task.fecha_inicio) {
      const taskDate = new Date(task.fecha_inicio);
      const filterStart = new Date(filter.value.startDate);
      if (taskDate < filterStart) valid = false;
    }
    if (filter.value.endDate && task.fecha_fin) {
      const taskDate = new Date(task.fecha_fin);
      const filterEnd = new Date(filter.value.endDate);
      if (taskDate > filterEnd) valid = false;
    }
    return valid;
  });
});

const totalPages = computed(() => Math.ceil(filteredTasks.value.length / pageSize));

const paginatedTasks = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredTasks.value.slice(start, start + pageSize);
});

const missingRows = computed(() => {
  const missing = pageSize - paginatedTasks.value.length;
  return missing > 0 ? missing : 0;
});

const pages = computed(() => {
  const total = totalPages.value;
  const current = currentPage.value;
  if (total <= 5) {
    return Array.from({ length: total }, (_, i) => i + 1);
  } else if (current <= 3) {
    return [1, 2, 3, 4, 5];
  } else if (current >= total - 2) {
    return [total - 4, total - 3, total - 2, total - 1, total];
  } else {
    return [current - 2, current - 1, current, current + 1, current + 2];
  }
});

const prevPage = () => {
  if (currentPage.value > 1) currentPage.value--;
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) currentPage.value++;
};

const goToPage = (page: number) => {
  currentPage.value = page;
};

const openModal = () => {
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const openEditModal = (task: any) => {
  taskToEdit.value = task;
};

const closeEditModal = () => {
  taskToEdit.value = null;
};

const openDeleteModal = (task: any) => {
  taskToDelete.value = task;
};

const closeDeleteModal = () => {
  taskToDelete.value = null;
};

const deleteTaskConfirmed = async () => {
  if (!taskToDelete.value) return;
  try {
    await deleteTask(taskToDelete.value.tareas_id);
    fetchTasks();
  } catch (err: any) {
    console.error(err.message);
  } finally {
    taskToDelete.value = null;
  }
};

// Modal de vista de tarea
const openViewModal = (task: any) => {
  taskToView.value = task;
};

const closeViewModal = () => {
  taskToView.value = null;
};

// Nuevo modal para asignar empleados
const openAssignModal = (task: any) => {
  taskToAssign.value = task;
};

const closeAssignModal = () => {
  taskToAssign.value = null;
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

/* Hover para filas */
tbody tr:hover {
  background-color: #f7fafc;
}

/* Transición para el filtro */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

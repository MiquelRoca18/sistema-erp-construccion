<template>
  <!-- ProjectManagementView.vue -->
  <div class="flex flex-col justify-center items-center min-h-screen p-8 transition-colors duration-300">
    <div class="max-w-5xl mx-auto bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado y acción -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white text-center sm:text-left mb-4 sm:mb-0 transition-colors duration-300">
          Gestión de Proyectos
        </h1>
        <button
          @click="openCreateModal"
          class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors duration-300"
        >
          Nuevo Proyecto
        </button>
      </div>
  
      <!-- Filtro -->
      <div class="mb-6">
        <input
          type="text"
          v-model="searchTerm"
          placeholder="Buscar por nombre de proyecto..."
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 transition-colors duration-300"
        />
      </div>
  
      <!-- Vista Mobile: Card view -->
      <div class="block md:hidden">
        <div
          v-for="project in paginatedProjects"
          :key="project.proyectos_id"
          class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow dark:shadow-gray-900/20 mb-4 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors duration-300"
          @click="openViewModal(project)"
        >
          <div>
            <p class="text-base font-medium text-green-600 dark:text-green-400">{{ project.nombre_proyecto }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-300">Estado: {{ project.estado }}</p>
          </div>
          <!-- Botones para editar y eliminar -->
          <div class="mt-2 flex space-x-2">
            <button
              @click.stop="openEditModal(project)"
              class="px-3 py-1 bg-green-500 dark:bg-green-600 text-white rounded hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-sm"
            >
              Editar
            </button>
            <button
              @click.stop="openDeleteModal(project)"
              class="px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-sm"
            >
              Eliminar
            </button>
          </div>
        </div>
        <div v-if="paginatedProjects.length === 0" class="text-center text-gray-500 dark:text-gray-400">
          No se encontraron proyectos.
        </div>
        <!-- Divs vacíos para mantener el espacio de 5 elementos -->
        <div v-for="n in missingRows" :key="'empty-' + n" class="h-20"></div>
      </div>
  
      <!-- Vista Desktop: Tabla -->
      <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gradient-to-r from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 text-green-800 dark:text-green-200">
              <th class="px-6 py-3 text-left font-semibold">ID</th>
              <th class="px-6 py-3 text-left font-semibold">Nombre</th>
              <th class="px-6 py-3 text-left font-semibold hidden lg:table-cell">Estado</th>
              <th class="px-6 py-3 text-left font-semibold hidden xl:table-cell whitespace-nowrap">Inicio</th>
              <th class="px-6 py-3 text-left font-semibold hidden xl:table-cell whitespace-nowrap">Fin</th>
              <th class="px-6 py-3 text-left font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="project in paginatedProjects"
              :key="project.proyectos_id"
              class="bg-white dark:bg-gray-700 shadow dark:shadow-gray-900/10 rounded-lg transition-colors duration-300 cursor-pointer hover:bg-green-50 dark:hover:bg-green-900/30"
              @click="openViewModal(project)"
            >
              <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ project.proyectos_id }}</td>
              <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ project.nombre_proyecto }}</td>
              <td class="px-6 py-4 hidden lg:table-cell text-gray-800 dark:text-gray-200">{{ project.estado }}</td>
              <td class="px-6 py-4 hidden xl:table-cell whitespace-nowrap text-gray-800 dark:text-gray-200">{{ project.fecha_inicio }}</td>
              <td class="px-6 py-4 hidden xl:table-cell whitespace-nowrap text-gray-800 dark:text-gray-200">{{ project.fecha_fin }}</td>
              <td class="px-6 py-4">
                <div class="flex flex-col md:flex-row gap-1 md:gap-2" @click.stop>
                  <button
                    @click="openEditModal(project)"
                    class="px-3 py-1 bg-green-500 dark:bg-green-600 text-white rounded hover:bg-green-600 dark:hover:bg-green-700 transition-colors duration-300 text-xs md:text-sm"
                  >
                    Editar
                  </button>
                  <button
                    @click="openDeleteModal(project)"
                    class="px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-xs md:text-sm"
                  >
                    Eliminar
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="paginatedProjects.length === 0">
              <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                No se encontraron proyectos.
              </td>
            </tr>
            <!-- Filas vacías para mantener 5 filas siempre -->
            <tr
              v-for="n in missingRows"
              :key="'empty-' + n"
              class="h-20 bg-transparent"
            >
              <td class="px-6 py-4" colspan="6"></td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Paginación -->
      <div v-if="totalPages > 1" class="mt-6 flex items-center justify-center space-x-2">
        <!-- Botón Anterior -->
        <button 
          @click="prevPage" 
          :disabled="currentPage === 1"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-green-600 dark:bg-green-500 text-white hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50 transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
  
        <!-- Botones Numerados -->
        <div class="flex space-x-2">
          <button 
            v-for="page in pages" 
            :key="page" 
            @click="goToPage(page)" 
            class="w-10 h-10 rounded-full border border-green-600 dark:border-green-500 flex items-center justify-center transition-colors duration-300 font-medium"
            :class="page === currentPage 
              ? 'bg-green-600 dark:bg-green-500 text-white' 
              : 'bg-white dark:bg-gray-800 text-green-600 dark:text-green-400 hover:bg-green-600 dark:hover:bg-green-500 hover:text-white'"
          >
            <span>{{ page }}</span>
          </button>
        </div>
  
        <!-- Botón Siguiente -->
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="flex items-center justify-center w-10 h-10 rounded-full bg-green-600 dark:bg-green-500 text-white hover:bg-green-700 dark:hover:bg-green-600 disabled:opacity-50 transition-colors duration-300"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
  
      <div v-if="loading" class="text-center mt-4 text-gray-500 dark:text-gray-400">
        Cargando proyectos...
      </div>
      <div v-if="error" class="text-center mt-4 text-red-500 dark:text-red-400">
        {{ error }}
      </div>
  
      <!-- Modales -->

      <CreateProjectModal v-if="showCreateModal" @close="closeCreateModal" @created="fetchProjects" />

      <EditProjectModal v-if="projectToEdit" :project="projectToEdit" @close="closeEditModal" @updated="fetchProjects" />

      <DeleteProjectModal v-if="projectToDelete" :project="projectToDelete" @close="closeDeleteModal" @deleted="deleteProjectConfirmed" />

      <ProjectDetailsModal v-if="projectToView" :project="projectToView" @close="closeViewModal" />
    </div>
  </div>
</template>
  
<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { getProjects, deleteProject } from '@/service/projectService';
import CreateProjectModal from '@/components/adminUser/project/CreateProjectModal.vue';
import EditProjectModal from '@/components/adminUser/project/EditProjectModal.vue';
import DeleteProjectModal from '@/components/adminUser/project/DeleteProjectModal.vue';
import ProjectDetailsModal from '@/components/adminUser/project/ProjectDetailsModal.vue';

const projects = ref<any[]>([]);
const loading = ref(true);
const error = ref('');
const searchTerm = ref('');
const currentPage = ref(1);
const pageSize = 5;
const showCreateModal = ref(false);
const projectToEdit = ref(null);
const projectToDelete = ref(null);
const projectToView = ref(null);

const fetchProjects = async () => {
  try {
    loading.value = true;
    const data = await getProjects();
    projects.value = data;
  } catch (err: any) {
    error.value = err.message || 'Error al obtener proyectos.';
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchProjects();
});

// Reiniciar la página actual al cambiar el término de búsqueda
watch(searchTerm, () => {
  currentPage.value = 1;
});

const filteredProjects = computed(() => {
  if (!searchTerm.value) return projects.value;
  return projects.value.filter((project) =>
    project.nombre_proyecto.toLowerCase().includes(searchTerm.value.toLowerCase())
  );
});

const totalPages = computed(() => Math.ceil(filteredProjects.value.length / pageSize));

const paginatedProjects = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredProjects.value.slice(start, start + pageSize);
});

// Filas vacías para mantener 5 filas siempre
const missingRows = computed(() => {
  const missing = pageSize - paginatedProjects.value.length;
  return missing > 0 ? missing : 0;
});

// Paginación: Limitar a 5 números de página
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

const openCreateModal = () => {
  showCreateModal.value = true;
};

const closeCreateModal = () => {
  showCreateModal.value = false;
};

const openEditModal = (project: any) => {
  projectToEdit.value = project;
};

const closeEditModal = () => {
  projectToEdit.value = null;
};

const openDeleteModal = (project: any) => {
  projectToDelete.value = project;
};

const closeDeleteModal = () => {
  projectToDelete.value = null;
};

const deleteProjectConfirmed = async () => {
  if (!projectToDelete.value) return;
  try {
    await deleteProject(projectToDelete.value.proyectos_id);
    fetchProjects();
  } catch (err: any) {
    console.error(err.message);
  } finally {
    projectToDelete.value = null;
  }
};

const openViewModal = (project: any) => {
  projectToView.value = project;
};

const closeViewModal = () => {
  projectToView.value = null;
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
  border-radius: 0.5rem;
}
tbody tr td {
  border: none;
}
</style>
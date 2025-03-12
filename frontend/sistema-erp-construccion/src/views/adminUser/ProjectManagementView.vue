<template>
  <div class="flex flex-col justify-center items-center min-h-screen p-4 md:p-8 transition-colors duration-300">
    <div class="w-full max-w-5xl mx-auto bg-white dark:bg-gray-800 p-4 md:p-6 rounded-lg shadow-lg dark:shadow-gray-900/30 transition-colors duration-300">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row items-center justify-between mb-4 md:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-white text-center sm:text-left mb-3 sm:mb-0 transition-colors duration-300">
          Gestión de Proyectos
        </h1>
        <button
          @click="openCreateModal"
          class="px-3 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors duration-300 text-sm"
          :disabled="loading"
        >
          <span v-if="!loading">Nuevo Proyecto</span>
          <span v-else class="flex items-center space-x-1">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Procesando...</span>
          </span>
        </button>
      </div>
  
      <!-- Filtro -->
      <div class="mb-4 md:mb-6">
        <input
          type="text"
          v-model="searchTerm"
          placeholder="Buscar por nombre de proyecto..."
          class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500 transition-colors duration-300 text-sm"
          :disabled="loading"
        />
      </div>
  
      <!-- Loader principal para la carga inicial -->
      <div v-if="loading" class="flex flex-col items-center justify-center py-8">
        <div class="w-12 h-12 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-gray-600 dark:text-gray-300 text-center">Cargando proyectos...</p>
      </div>

      <!-- Tabla Responsiva -->
      <div v-else>
        <ResponsiveTable
          :items="paginatedProjects"
          :headers="tableHeaders"
          headerClass="bg-gradient-to-r from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 text-green-800 dark:text-green-200"
          :has-pagination="true"
          :current-page="currentPage"
          :total-pages="totalPages"
          :mobile-properties="['nombre_proyecto', 'estado', 'fecha_inicio', 'fecha_fin']"
          empty-message="No se encontraron proyectos."
          @edit="openEditModal"
          @delete="openDeleteModal"
          @item-click="openViewModal"
          @prev-page="prevPage"
          @next-page="nextPage"
          @go-to-page="goToPage"
        >
          <!-- Personalización de celdas -->
          <template #cell-estado="{ value }">
            <span 
              class="px-2 py-1 rounded-full text-xs font-medium" 
              :class="{
                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200': value === 'pendiente',
                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200': value === 'en progreso',
                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200': value === 'finalizado'
              }"
            >
              {{ value }}
            </span>
          </template>
          
          <!-- Personalización de elementos para móvil -->
          <template #mobile-item="{ item }">
            <div class="flex flex-col">
              <h3 class="font-bold text-gray-800 dark:text-gray-100">{{ (item as ProjectType).nombre_proyecto }}</h3>
              <div class="flex items-center mt-1">
                <span class="text-xs mr-1">Estado:</span>
                <span 
                  class="px-2 py-0.5 rounded-full text-xs" 
                  :class="{
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200': (item as ProjectType).estado === 'pendiente',
                    'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200': (item as ProjectType).estado === 'en progreso',
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200': (item as ProjectType).estado === 'finalizado'
                  }"
                >
                  {{ (item as ProjectType).estado }}
                </span>
              </div>
              <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                <p>Inicio: {{ formatDate((item as ProjectType).fecha_inicio) }}</p>
                <p v-if="(item as ProjectType).fecha_fin">Fin: {{ formatDate((item as ProjectType).fecha_fin) }}</p>
              </div>
            </div>
          </template>
        </ResponsiveTable>
      </div>
  
      <!-- Error mensaje -->
      <div v-if="error" class="mt-4 p-3 bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-center flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ error }}
      </div>
  
      <!-- Modales -->
      <CreateProjectModal 
        v-if="showCreateModal" 
        @close="closeCreateModal" 
        @created="fetchProjects" 
      />

      <EditProjectModal 
        v-if="projectToEdit" 
        :project="projectToEdit" 
        @close="closeEditModal" 
        @updated="fetchProjects" 
      />

      <DeleteProjectModal 
        v-if="projectToDelete" 
        :project="projectToDelete" 
        @close="closeDeleteModal" 
        @deleted="deleteProjectConfirmed" 
      />

      <ProjectDetailsModal 
        v-if="projectToView" 
        :project="projectToView" 
        @close="closeViewModal" 
      />

      <!-- Loader de operaciones -->
      <div 
        v-if="operationLoading" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg flex flex-col items-center">
          <div class="w-16 h-16 border-4 border-green-500 border-t-transparent rounded-full animate-spin mb-4"></div>
          <p class="text-gray-800 dark:text-gray-200">{{ operationMessage }}</p>
        </div>
      </div>
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
import ResponsiveTable from '@/components/ResponsiveTable.vue';

// Define the Project type interface
interface ProjectType {
  proyectos_id: number;
  nombre_proyecto: string;
  estado: string;
  fecha_inicio: string;
  fecha_fin: string | null;
  descripcion?: string;
  responsable_id?: number;
  responsable_nombre?: string;
}

const projects = ref<ProjectType[]>([]);
const loading = ref(true);
const operationLoading = ref(false);
const operationMessage = ref('Procesando...');
const error = ref('');
const searchTerm = ref('');
const currentPage = ref(1);
const pageSize = 5;
const showCreateModal = ref(false);
const projectToEdit = ref<ProjectType | null>(null);
const projectToDelete = ref<ProjectType | null>(null);
const projectToView = ref<ProjectType | null>(null);

// Definición de cabeceras para la tabla
const tableHeaders = [
  { key: 'proyectos_id', title: 'ID' },
  { key: 'nombre_proyecto', title: 'Nombre' },
  { key: 'estado', title: 'Estado', class: 'hidden lg:table-cell' },
  { key: 'fecha_inicio', title: 'Inicio', class: 'hidden xl:table-cell whitespace-nowrap' },
  { key: 'fecha_fin', title: 'Fin', class: 'hidden xl:table-cell whitespace-nowrap' }
];

const fetchProjects = async () => {
  try {
    loading.value = true;
    error.value = '';
    
    // Simulamos una demora mínima para mostrar el loader (al menos 500ms)
    const startTime = Date.now();
    const data = await getProjects();
    
    // Garantizamos que el loader se muestre por al menos 500ms para evitar parpadeos
    const elapsedTime = Date.now() - startTime;
    if (elapsedTime < 500) {
      await new Promise(resolve => setTimeout(resolve, 500 - elapsedTime));
    }
    
    projects.value = data as ProjectType[];
  } catch (err: any) {
    error.value = err.message || 'Error al obtener proyectos. Por favor, inténtalo de nuevo.';
    console.error('Error fetching projects:', err);
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

const openEditModal = (project: ProjectType) => {
  projectToEdit.value = project;
};

const closeEditModal = () => {
  projectToEdit.value = null;
};

const openDeleteModal = (project: ProjectType) => {
  projectToDelete.value = project;
};

const closeDeleteModal = () => {
  projectToDelete.value = null;
};

const deleteProjectConfirmed = async () => {
  if (!projectToDelete.value) return;
  
  try {
    operationLoading.value = true;
    operationMessage.value = 'Eliminando proyecto...';
    
    await deleteProject(projectToDelete.value.proyectos_id);
    
    // Pequeña pausa antes de recargar los proyectos
    await new Promise(resolve => setTimeout(resolve, 300));
    
    await fetchProjects();
  } catch (err: any) {
    error.value = `Error al eliminar proyecto: ${err.message || 'Inténtalo de nuevo'}`;
    console.error('Error deleting project:', err);
  } finally {
    operationLoading.value = false;
    projectToDelete.value = null;
  }
};

const openViewModal = (project: ProjectType) => {
  projectToView.value = project;
};

const closeViewModal = () => {
  projectToView.value = null;
};

// Formatear fechas para vista móvil
const formatDate = (dateStr: string) => {
  if (!dateStr) return 'N/A';
  const date = new Date(dateStr);
  return date.toLocaleDateString();
};
</script>
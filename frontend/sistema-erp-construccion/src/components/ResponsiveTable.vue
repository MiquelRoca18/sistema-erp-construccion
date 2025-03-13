<template>
  <div class="responsive-table-wrapper">
    <!-- Vista para móviles: tarjetas -->
    <div class="sm:hidden w-full space-y-3">
      <slot name="mobile-content">
        <!-- Contenido por defecto para móviles si no se proporciona slot -->
        <div
          v-for="(item, index) in items"
          :key="index"
          class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow dark:shadow-gray-900/20 mb-3 cursor-pointer transition-colors duration-300"
          @click="$emit('item-click', item)"
        >
          <slot name="mobile-item" :item="item">
            <div class="flex flex-col">
              <div v-for="(value, key) in getVisibleProperties(item)" :key="key" class="mb-1">
                <span class="font-semibold text-gray-600 dark:text-gray-300">{{ formatHeader(key) }}: </span>
                <span class="text-gray-800 dark:text-white">{{ value }}</span>
              </div>
            </div>
          </slot>
          
          <!-- Botones de acción -->
          <div v-if="showActions" class="mt-3 flex justify-end space-x-2" @click.stop>
            <slot name="actions" :item="item">
              <button 
                v-if="editAction"
                @click="$emit('edit', item)" 
                class="px-3 py-1 bg-blue-500 dark:bg-blue-600 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors duration-300 text-sm"
              >
                Editar
              </button>
              <button 
                v-if="deleteAction"
                @click="$emit('delete', item)" 
                class="px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-sm"
              >
                Eliminar
              </button>
            </slot>
          </div>
        </div>
      </slot>
    </div>

    <!-- Vista para escritorio: tabla -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr :class="headerClass || 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200'">
            <th 
              v-for="(header, index) in headers" 
              :key="index" 
              class="px-6 py-3 text-left font-semibold"
              :class="typeof header === 'object' && header.class ? header.class : ''"
            >
              {{ typeof header === 'object' && header.title ? header.title : header }}
            </th>
            <th v-if="showActions" class="px-6 py-3 text-left font-semibold">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in items"
            :key="index"
            class="bg-white dark:bg-gray-700 shadow dark:shadow-gray-900/10 hover:bg-gray-50 dark:hover:bg-gray-600/30 transition-colors duration-300 cursor-pointer"
            @click="$emit('item-click', item)"
          >
            <td 
              v-for="(header, headerIndex) in headers" 
              :key="headerIndex" 
              class="px-6 py-4 text-gray-800 dark:text-gray-200"
            >
              <slot :name="`cell-${getHeaderKey(header)}`" :item="item" :header="header" :value="getItemValue(item, header)">
                {{ getItemValue(item, header) }}
              </slot>
            </td>
            <td v-if="showActions" class="px-6 py-4" @click.stop>
              <div class="flex flex-wrap gap-2">
                <slot name="actions" :item="item">
                  <button 
                    v-if="editAction"
                    @click="$emit('edit', item)" 
                    class="px-3 py-1 bg-blue-500 dark:bg-blue-600 text-white rounded hover:bg-blue-600 dark:hover:bg-blue-700 transition-colors duration-300 text-xs sm:text-sm"
                  >
                    Editar
                  </button>
                  <button 
                    v-if="deleteAction"
                    @click="$emit('delete', item)" 
                    class="px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded hover:bg-red-600 dark:hover:bg-red-700 transition-colors duration-300 text-xs sm:text-sm"
                  >
                    Eliminar
                  </button>
                </slot>
              </div>
            </td>
          </tr>
          <tr v-if="items.length === 0">
            <td :colspan="showActions ? headers.length + 1 : headers.length" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
              {{ emptyMessage || 'No hay datos disponibles.' }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Paginación -->
    <div v-if="hasPagination && totalPages > 1" class="mt-6 flex items-center justify-center space-x-2">
      <button 
        @click="$emit('prev-page')" 
        :disabled="currentPage === 1"
        class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-50 transition-colors duration-300"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <div class="flex space-x-2 overflow-x-auto py-1 px-1">
        <button 
          v-for="page in visiblePages" 
          :key="page" 
          @click="$emit('go-to-page', page)" 
          class="w-10 h-10 rounded-full border border-blue-600 dark:border-blue-500 flex items-center justify-center transition-colors duration-300 font-medium"
          :class="page === currentPage 
            ? 'bg-blue-600 dark:bg-blue-500 text-white' 
            : 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 hover:bg-blue-600 dark:hover:bg-blue-500 hover:text-white'"
        >
          <span>{{ page }}</span>
        </button>
      </div>

      <button 
        @click="$emit('next-page')" 
        :disabled="currentPage === totalPages"
        class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 dark:bg-blue-500 text-white hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-50 transition-colors duration-300"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  // Datos de la tabla
  items: {
    type: Array,
    required: true
  },
  // Configuración de columnas
  headers: {
    type: Array,
    required: true
  },
  // Clases personalizadas para el encabezado
  headerClass: {
    type: String,
    default: null
  },
  // Mostrar botones de acción
  showActions: {
    type: Boolean,
    default: true
  },
  // Habilitar acción de editar
  editAction: {
    type: Boolean,
    default: true
  },
  // Habilitar acción de eliminar
  deleteAction: {
    type: Boolean,
    default: true
  },
  // Mensaje cuando no hay datos
  emptyMessage: {
    type: String,
    default: 'No hay datos disponibles.'
  },
  // Paginación
  hasPagination: {
    type: Boolean,
    default: false
  },
  currentPage: {
    type: Number,
    default: 1
  },
  totalPages: {
    type: Number,
    default: 1
  },
  // Propiedades a mostrar en la vista móvil (por defecto muestra todas)
  mobileProperties: {
    type: Array,
    default: () => []
  }
});

// Emits para eventos
const emit = defineEmits([
  'edit', 
  'delete', 
  'item-click', 
  'prev-page', 
  'next-page', 
  'go-to-page'
]);

// Obtener la clave del encabezado (para usar en slots)
const getHeaderKey = (header) => {
  if (typeof header === 'string') return header;
  return header.key || header.title || '';
};

// Obtener el valor del item según el encabezado
const getItemValue = (item, header) => {
  if (typeof header === 'string') return item[header];
  if (header.key) return item[header.key];
  if (header.formatter) return header.formatter(item);
  return '';
};

// Para la vista móvil, obtener solo las propiedades visibles
const getVisibleProperties = (item) => {
  // Si hay propiedades específicas definidas para móvil, usar esas
  if (props.mobileProperties.length > 0) {
    const mobileObj = {};
    props.mobileProperties.forEach(key => {
      if (item[key] !== undefined) {
        mobileObj[key] = item[key];
      }
    });
    return mobileObj;
  }
  
  // Si no, usar las primeras 4 propiedades o headers definidos
  const result = {};
  const keys = Object.keys(item);
  const headerKeys = props.headers.map(h => typeof h === 'string' ? h : h.key).filter(Boolean);
  
  // Priorizar las claves definidas en headers si existen
  const propertiesToShow = headerKeys.length > 0 ? headerKeys.slice(0, 4) : keys.slice(0, 4);
  
  propertiesToShow.forEach(key => {
    if (item[key] !== undefined) {
      result[key] = item[key];
    }
  });
  
  return result;
};

// Formatea el nombre del encabezado para la vista móvil
const formatHeader = (key) => {
  // Buscar si existe un título personalizado en los headers
  const customHeader = props.headers.find(h => 
    (typeof h === 'object' && h.key === key) || (typeof h === 'string' && h === key)
  );
  
  if (customHeader && typeof customHeader === 'object' && customHeader.title) {
    return customHeader.title;
  }
  
  // Si no hay título personalizado, formatear la clave
  return key
    .replace(/_/g, ' ')
    .replace(/([A-Z])/g, ' $1')
    .replace(/^./, str => str.toUpperCase());
};

// Para paginación, determinar qué páginas mostrar
const visiblePages = computed(() => {
  const total = props.totalPages;
  const current = props.currentPage;
  
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
</script>

<style scoped>
/* Estilos para la tabla y las cards en móvil */
.responsive-table-wrapper {
  width: 100%;
}

@media (max-width: 640px) {
  .responsive-table-wrapper {
    font-size: 0.9rem;
  }
}

/* Estilos para optimizar espacio en vista tablet */
@media (min-width: 641px) and (max-width: 768px) {
  .responsive-table-wrapper td, .responsive-table-wrapper th {
    padding-left: 0.75rem !important;
    padding-right: 0.75rem !important;
  }
}

/* Estilos para tabla desktop */
table {
  border-collapse: separate;
  border-spacing: 0 0.3rem;
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

/* Mejora para desplazamiento horizontal en tabla */
.overflow-x-auto {
  -webkit-overflow-scrolling: touch;
}
</style>
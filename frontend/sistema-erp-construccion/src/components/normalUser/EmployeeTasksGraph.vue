<template>
  <div class="p-2 sm:p-4 bg-white dark:bg-gray-800" style="height: 250px; sm:height: 300px;">
    <canvas ref="chartCanvas" style="width: 100%; height: 100%;"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
import { getAllTasks } from '@/service/taskService';
import Chart from 'chart.js/auto';

const props = defineProps({
  employeeId: {
    type: Number,
    required: false, 
  },
});

const chartCanvas = ref(null);
let chartInstance = null;

// Función para detectar el modo de color
const isDarkMode = () => {
  return document.documentElement.classList.contains('dark');
};

// Función para obtener los colores según el modo
const getChartColors = () => {
  const darkMode = isDarkMode();
  return {
    title: darkMode ? '#e5e7eb' : '#111827',
    legend: darkMode ? '#e5e7eb' : '#111827',
    background: darkMode ? 'rgba(255, 255, 255, 0.1)' : 'transparent'
  };
};

const fetchTasksAndRenderChart = async () => {
  if (!props.employeeId) return; // No hacer nada si employeeId es null
  try {
    const tasks = await getAllTasks(props.employeeId);
    // Calcular la cantidad de tareas por estado
    const counts = {
      pendiente: 0,
      'en progreso': 0,
      finalizado: 0,
    };

    tasks.forEach(task => {
      if (task.estado in counts) {
        counts[task.estado]++;
      }
    });

    // Destruir el gráfico previo si existe (para evitar duplicados)
    if (chartInstance) {
      chartInstance.destroy();
    }
    
    const ctx = chartCanvas.value.getContext('2d');
    const colors = getChartColors();

    // Determinar si estamos en móvil
    const isMobile = window.innerWidth < 640;

    chartInstance = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['Pendiente', 'En Progreso', 'Finalizado'],
        datasets: [{
          data: [counts.pendiente, counts['en progreso'], counts.finalizado],
          backgroundColor: ['#fbbf24', '#60a5fa', '#34d399'],
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: colors.legend,
              font: { size: isMobile ? 10 : 13 },
              boxWidth: isMobile ? 10 : 15,
              padding: isMobile ? 8 : 15
            },
            backgroundColor: colors.background,
          },
          title: {
            display: true,
            text: 'Distribución de Tareas',
            font: { size: isMobile ? 16 : 24 },
            color: colors.title,
            padding: { top: isMobile ? 8 : 10, bottom: isMobile ? 4 : 10 }
          },
          tooltip: {
            bodyFont: {
              size: isMobile ? 11 : 14
            },
            titleFont: {
              size: isMobile ? 12 : 16
            }
          }
        },
        color: colors.title,
      },
    });
  } catch (error) {
  }
};

// Función para manejar el cambio de modo
const handleModeChange = () => {
  if (chartInstance) {
    const colors = getChartColors();
    
    // Actualizar colores del título
    chartInstance.options.plugins.title.color = colors.title;
    
    // Actualizar colores de la leyenda
    chartInstance.options.plugins.legend.labels.color = colors.legend;
    chartInstance.options.plugins.legend.backgroundColor = colors.background;
    
    // Actualizar color general
    chartInstance.options.color = colors.title;
    
    // Actualizar el gráfico
    chartInstance.update();
  }
};

// Función para manejar el cambio de tamaño de pantalla
const handleResize = () => {
  const isMobile = window.innerWidth < 640;
  
  if (chartInstance && chartInstance.options && chartInstance.options.plugins) {
    // Actualizar tamaños de fuente
    if (chartInstance.options.plugins.title) {
      chartInstance.options.plugins.title.font.size = isMobile ? 16 : 24;
      chartInstance.options.plugins.title.padding = { top: isMobile ? 8 : 10, bottom: isMobile ? 4 : 10 };
    }
    
    if (chartInstance.options.plugins.legend && chartInstance.options.plugins.legend.labels) {
      chartInstance.options.plugins.legend.labels.font.size = isMobile ? 10 : 13;
      chartInstance.options.plugins.legend.labels.boxWidth = isMobile ? 10 : 15;
      chartInstance.options.plugins.legend.labels.padding = isMobile ? 8 : 15;
    }
    
    if (chartInstance.options.plugins.tooltip) {
      chartInstance.options.plugins.tooltip.bodyFont = {
        size: isMobile ? 11 : 14
      };
      chartInstance.options.plugins.tooltip.titleFont = {
        size: isMobile ? 12 : 16
      };
    }
    
    // Actualizar el gráfico
    chartInstance.update();
  }
};

// Añadir event listener para cambios de modo
const modeObserver = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.attributeName === 'class') {
      handleModeChange();
    }
  });
});

// Event listener para cambio de tamaño
const debouncedResize = () => {
  let timeout;
  return () => {
    clearTimeout(timeout);
    timeout = setTimeout(handleResize, 250);
  };
};

const handleResizeDebounced = debouncedResize();

onMounted(() => {
  fetchTasksAndRenderChart();
  
  // Observar cambios en la clase del elemento raíz para cambios de tema
  modeObserver.observe(document.documentElement, { 
    attributes: true, 
    attributeFilter: ['class'] 
  });
  
  // Observar cambios de tamaño de ventana
  window.addEventListener('resize', handleResizeDebounced);
});

// Limpiar el observer cuando el componente se desmonte
onUnmounted(() => {
  modeObserver.disconnect();
  window.removeEventListener('resize', handleResizeDebounced);
  if (chartInstance) {
    chartInstance.destroy();
  }
});

// Vigilar cambios en employeeId para volver a renderizar la gráfica
watch(() => props.employeeId, (newVal) => {
  if(newVal) {
    fetchTasksAndRenderChart();
  }
});
</script>

<style scoped>
canvas {
  display: block;
  width: 100% !important;
  height: 100% !important;
}

@media (max-width: 640px) {
  /* Ajustes adicionales para móviles si son necesarios */
  div {
    padding: 0.5rem;
  }
}
</style>
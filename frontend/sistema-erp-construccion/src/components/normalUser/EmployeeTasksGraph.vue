<template>
  <div class="p-2 sm:p-4 bg-white dark:bg-gray-800" style="height: 250px; min-height: 250px; max-height: 300px;">
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

// Función para determinar si es móvil
const isMobileView = () => {
  return window.innerWidth < 640;
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
    
    // Detector de dispositivo móvil para ajustar la configuración
    const isMobile = isMobileView();

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
            align: isMobile ? 'start' : 'center',
            labels: {
              color: colors.legend,
              font: { size: isMobile ? 10 : 13 },
              boxWidth: isMobile ? 12 : 15,
              padding: isMobile ? 8 : 10
            },
            backgroundColor: colors.background,
          },
          title: {
            display: true,
            text: 'Distribución de Tareas',
            font: { size: isMobile ? 16 : 24 },
            color: colors.title,
            padding: { top: 0, bottom: isMobile ? 5 : 10 }
          },
        },
        color: colors.title,
      },
    });
  } catch (error) {
    console.error('Error al obtener tareas para gráfico:', error);
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

// Función debounce para optimizar eventos frecuentes
function debounce(func, wait) {
  let timeout;
  return function() {
    const context = this;
    const args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

// Manejador de cambio de tamaño de ventana
const handleResize = debounce(() => {
  if (chartInstance) {
    const isMobile = isMobileView();
    
    // Actualizar la configuración basada en el tamaño de la pantalla
    chartInstance.options.plugins.legend.labels.font.size = isMobile ? 10 : 13;
    chartInstance.options.plugins.legend.labels.boxWidth = isMobile ? 12 : 15;
    chartInstance.options.plugins.legend.labels.padding = isMobile ? 8 : 10;
    chartInstance.options.plugins.legend.align = isMobile ? 'start' : 'center';
    chartInstance.options.plugins.title.font.size = isMobile ? 16 : 24;
    chartInstance.options.plugins.title.padding.bottom = isMobile ? 5 : 10;
    
    // Actualizar el gráfico
    chartInstance.update();
  }
}, 250);

// Añadir event listener para cambios de modo
const modeObserver = new MutationObserver((mutations) => {
  mutations.forEach((mutation) => {
    if (mutation.attributeName === 'class') {
      handleModeChange();
    }
  });
});

onMounted(() => {
  fetchTasksAndRenderChart();
  
  // Observar cambios en la clase del elemento raíz
  modeObserver.observe(document.documentElement, { 
    attributes: true, 
    attributeFilter: ['class'] 
  });
  
  // Añadir event listener para el resize
  window.addEventListener('resize', handleResize);
});

// Limpiar el observer cuando el componente se desmonte
onUnmounted(() => {
  modeObserver.disconnect();
  window.removeEventListener('resize', handleResize);
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
</style>
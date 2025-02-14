<!-- components/EmployeeTasksGraph.vue -->
<template>
  <div class="p-4 bg-white" style="height: 300px;">
    <canvas ref="chartCanvas" style="width: 100%; height: 100%;"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { getAllTasks } from '@/service/taskService';
import Chart from 'chart.js/auto';

const props = defineProps({
  employeeId: {
    type: Number,
    required: false, // Puede ser nulo inicialmente
  },
});

const chartCanvas = ref(null);
let chartInstance = null;

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
              color: '#111827',
              font: { size: 13 },
            },
          },
          title: {
            display: true,
            text: 'Distribución de Tareas',
            font: { size: 24 },
            color: '#111827',
          },
        },
      },
    });
  } catch (error) {
    console.error('Error al obtener tareas para gráfico:', error);
  }
};

onMounted(() => {
  fetchTasksAndRenderChart();
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

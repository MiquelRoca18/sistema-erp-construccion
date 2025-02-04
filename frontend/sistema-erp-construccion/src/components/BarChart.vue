<template>
    <div>
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script setup>
  import { Bar } from 'vue-chartjs';
  import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
  import { computed } from 'vue';
  
  ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);
  
  const props = defineProps({
    chartData: {
      type: Object,
      required: true,
    },
  });
  
  const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true,
        max: props.chartData.total, // Ajusta el máximo del eje Y al total de tareas
        ticks: {
          stepSize: Math.ceil(props.chartData.total / 5) || 1, // Asegura que la escala tenga saltos uniformes
        },
      },
    },
    plugins: {
      legend: {
        position: 'top',
      },
    },
  }));
  </script>
  
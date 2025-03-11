<template>
    <transition name="toast">
      <div 
        v-if="visible" 
        class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-sm"
        :class="[
          type === 'success' ? 'bg-green-500 dark:bg-green-600' : 
          type === 'error' ? 'bg-red-500 dark:bg-red-600' : 
          type === 'warning' ? 'bg-yellow-500 dark:bg-yellow-600' : 
          'bg-blue-500 dark:bg-blue-600'
        ]"
      >
        <div class="px-4 py-3 rounded-lg shadow-lg flex items-center justify-between">
          <!-- Icono -->
          <div class="flex items-center">
            <!-- Icono de éxito -->
            <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <!-- Icono de error -->
            <svg v-else-if="type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <!-- Icono de advertencia -->
            <svg v-else-if="type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <!-- Icono de información -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-white" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
    
            <!-- Mensaje -->
            <span class="text-sm text-white">{{ message }}</span>
          </div>
          
          <!-- Botón para cerrar -->
          <button @click="close" class="text-white hover:text-gray-200 ml-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </transition>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted, onUnmounted, defineProps, defineEmits } from 'vue';
  
  const props = defineProps({
    message: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'success',
      validator: (value: string) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    duration: {
      type: Number,
      default: 3000
    },
    autoClose: {
      type: Boolean,
      default: true
    }
  });
  
  const emit = defineEmits(['close']);
  const visible = ref(true);
  let timer: number | null = null;
  
  const close = () => {
    visible.value = false;
    if (timer) {
      clearTimeout(timer);
      timer = null;
    }
    
    // Emitir evento para que el componente padre pueda reaccionar
    setTimeout(() => {
      emit('close');
    }, 300); // Permitir que la animación termine
  };
  
  onMounted(() => {
    if (props.autoClose && props.duration > 0) {
      timer = window.setTimeout(() => {
        close();
      }, props.duration);
    }
  });
  
  onUnmounted(() => {
    if (timer) {
      clearTimeout(timer);
    }
  });
  </script>
  
  <style scoped>
  .toast-enter-active, .toast-leave-active {
    transition: all 0.3s ease-out;
  }
  .toast-enter-from {
    opacity: 0;
    transform: translate(-50%, 20px);
  }
  .toast-leave-to {
    opacity: 0;
    transform: translate(-50%, -20px);
  }
  </style>
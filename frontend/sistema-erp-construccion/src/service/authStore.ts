// authStore.ts
import { reactive, computed } from 'vue';

interface AuthState {
  token: string | null;
  user: number | null; // Asumiendo que el ID del usuario es un número
}

const state: AuthState = reactive({
  token: localStorage.getItem('token') || null,
  user: JSON.parse(localStorage.getItem('user')) || null,
});

// Estado computado para verificar si el usuario está autenticado
export const isAuthenticated = computed(() => !!state.token);

// Función para manejar el inicio de sesión
export function login(userData: { token: string; empleados_id: number }) {
  state.token = userData.token;
  state.user = userData.empleados_id;
  localStorage.setItem('token', userData.token);
  localStorage.setItem('user', JSON.stringify(userData.empleados_id));
}

// Función para manejar el cierre de sesión
export function logout() {
  state.token = null;
  state.user = null;
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}
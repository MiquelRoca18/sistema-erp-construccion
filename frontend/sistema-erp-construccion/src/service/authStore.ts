import { reactive, computed } from 'vue';

interface AuthState {
  token: string | null;
  user: number | null; 
  role: string | null;
}

const state: AuthState = reactive({
  token: localStorage.getItem('token') || null,
  user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null,
  role: localStorage.getItem('role') || null,
});

export const isAuthenticated = computed(() => !!state.token);

export const userRole = computed(() => state.role);

export function login(userData: { token: string; empleados_id: number; rol: string }) {
  state.token = userData.token;
  state.user = userData.empleados_id;
  state.role = userData.rol;
  localStorage.setItem('token', userData.token);
  localStorage.setItem('user', JSON.stringify(userData));
  localStorage.setItem('role', userData.rol);
}

export function logout() {
  state.token = null;
  state.user = null;
  state.role = null;
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  localStorage.removeItem('role');
}

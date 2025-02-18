import { reactive, computed } from 'vue';

interface AuthState {
  token: string | null;
  user: number | null; // ID del empleado
  role: string | null;
}

const state: AuthState = reactive({
  token: localStorage.getItem('token') || null,
  user: localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : null,
  role: localStorage.getItem('role') || null,
});

// Computed para saber si el usuario está autenticado
export const isAuthenticated = computed(() => !!state.token);

// Computed para obtener el rol del usuario
export const userRole = computed(() => state.role);

// Función para iniciar sesión (almacena token, id y rol)
export function login(userData: { token: string; empleados_id: number; rol: string }) {
  state.token = userData.token;
  state.user = userData.empleados_id;
  state.role = userData.rol;
  localStorage.setItem('token', userData.token);
  localStorage.setItem('user', JSON.stringify(userData.empleados_id));
  localStorage.setItem('role', userData.rol);
}

// Función para cerrar sesión
export function logout() {
  state.token = null;
  state.user = null;
  state.role = null;
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  localStorage.removeItem('role');
}

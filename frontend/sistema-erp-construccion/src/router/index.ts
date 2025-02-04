// router/index.ts
import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/DashboardView.vue';
import EmployeeProfileView from '../views/EmployeeProfileView.vue';
import TasksView from '../views/TasksView.vue';
import TaskDetailsView from '../views/TaskDetailsView.vue';
import { logout } from '@/service/authStore'; // Importar la función logout del store

const routes = [
  {
    path: '/',
    name: 'login',
    component: LoginView,
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
  },
  {
    path: '/employee/:id',
    name: 'employeeProfile',
    component: EmployeeProfileView,
  },
  {
    path: '/tasks/:id',
    name: 'tasksView',
    component: TasksView,
  },
  {
    path: '/task-details/:taskId',
    name: 'taskDetails',
    component: TaskDetailsView,
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Guard de ruta para limpiar el estado al acceder al login
router.beforeEach((to, from, next) => {
  if (to.name === 'login') {
    // Usar la función logout del store para limpiar el estado reactivamente
    logout();
  }
  next();
});

export default router;
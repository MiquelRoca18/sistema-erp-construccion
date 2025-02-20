// router/index.ts
import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/normalUser/DashboardView.vue';
import EmployeeProfileView from '../views/normalUser/EmployeeProfileView.vue';
import TasksView from '../views/normalUser/TasksView.vue';
import DashboardAdminView from '../views/adminUser/DashboardAdminView.vue';
import { logout } from '@/service/authStore';

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
  // Rutas para Admin
  {
    path: '/dashboard-admin',
    name: 'dashboardAdmin',
    component: DashboardAdminView,
  },
  {
    path: '/employees',
    name: 'employeesAdmin',
    component: { template: '<div>Placeholder: Gestión de Empleados</div>' },
  },
  {
    path: '/projects',
    name: 'projectsAdmin',
    component: { template: '<div>Placeholder: Gestión de Proyectos</div>' },
  },
  {
    path: '/tasks-admin',
    name: 'tasksAdmin',
    component: { template: '<div>Placeholder: Gestión de Tareas</div>' },
  },
  {
    path: '/budgets',
    name: 'budgetsAdmin',
    component: { template: '<div>Placeholder: Gestión de Presupuestos</div>' },
  },
  {
    path: '/roles',
    name: 'rolesAdmin',
    component: { template: '<div>Placeholder: Gestión de Roles y Usuarios</div>' },
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Guard de ruta para limpiar el estado al acceder al login
router.beforeEach((to, from, next) => {
  if (to.name === 'login') {
    logout();
  }
  next();
});

export default router;

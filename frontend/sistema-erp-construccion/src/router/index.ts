import { createRouter, createWebHistory } from 'vue-router';
import { logout, isAuthenticated } from '@/service/authStore';

// Aplicando lazy loading para todas las vistas
const LoginView = () => import('../views/LoginView.vue');
const DashboardView = () => import('../views/normalUser/DashboardView.vue');
const EmployeeProfileView = () => import('../views/normalUser/EmployeeProfileView.vue');
const TasksView = () => import('../views/normalUser/TasksView.vue');
const DashboardAdminView = () => import('../views/adminUser/DashboardAdminView.vue');
const EmployeeManagementView = () => import('../views/adminUser/EmployeeManagementView.vue');
const ProjectManagementView = () => import('../views/adminUser/ProjectManagementView.vue');
const TasksManagementView = () => import('../views/adminUser/TasksManagementView.vue');
const NotFoundView = () => import('../views/NotFoundView.vue');
const BudgetManagementView = () => import('../views/adminUser/BudgetManagementView.vue');

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
    path: '/employees-admin',
    name: 'employeesAdmin',
    component: EmployeeManagementView,
  },
  {
    path: '/projects',
    name: 'projects',
    component: ProjectManagementView,
  },
  {
    path: '/tasks-admin',
    name: 'tasksAdmin',
    component: TasksManagementView,
  },
  {
    path: '/budgets',
    name: 'budgets',
    component: BudgetManagementView,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFoundView,
    beforeEnter: (to, from, next) => {
      // Si el usuario está autenticado, redirige al dashboard, de lo contrario, al login
      if (isAuthenticated.value) {
        next({ name: 'dashboard' });
      } else {
        next({ name: 'login' });
      }
    }
  }
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
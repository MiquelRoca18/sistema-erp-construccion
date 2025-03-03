import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import DashboardView from '../views/normalUser/DashboardView.vue';
import EmployeeProfileView from '../views/normalUser/EmployeeProfileView.vue';
import TasksView from '../views/normalUser/TasksView.vue';
import DashboardAdminView from '../views/adminUser/DashboardAdminView.vue';
import EmployeeManagementView from '../views/adminUser/EmployeeManagementView.vue';
import ProjectManagementView from '../views/adminUser/ProjectManagementView.vue'; 
import TasksManagementView from '../views/adminUser/TasksManagementView.vue';
import NotFoundView from '../views/NotFoundView.vue';
import { logout, isAuthenticated } from '@/service/authStore';
import BudgetManagementView from '../views/adminUser/BudgetManagementView.vue';

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

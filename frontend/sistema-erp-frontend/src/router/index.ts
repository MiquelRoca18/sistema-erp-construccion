import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '../views/DashboardView.vue';
import ProfileView from '../views/ProfileView.vue'; 

const routes = [
  { path: '/', name: 'Dashboard', component: DashboardView },
  { path: '/profile', name: 'Profile', component: ProfileView },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

export default router;

// projectService.ts
import axios from 'axios';
const API_URL = import.meta.env.VITE_API_URL;

export const getProjects = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.get(`${API_URL}/projects`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return Array.isArray(response.data.data) ? response.data.data : [];
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al obtener proyectos');
  }
};

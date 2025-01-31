// services/taskService.ts
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

export const getPendingTasks = async (user: any) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    console.log(user)
    const response = await axios.get(`${API_URL}/employee-tasks/pending-tasks/${user}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    console.log(response.data.data);
    return response.data.data;
  } catch (error) {
    throw new Error(error.response?.data?.message || 'Error al obtener tareas pendientes');
  }
};

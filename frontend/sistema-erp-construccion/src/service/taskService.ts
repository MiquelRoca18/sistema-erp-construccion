// services/taskService.ts
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

// Obtener las tareas pendientes
export const getPendingTasks = async (employeeId: any) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }

    const response = await axios.get(`${API_URL}/employee-tasks/pending-tasks/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al obtener tareas pendientes');
  }
};

// Función para obtener todas las tareas de un empleado
export const getAllTasks = async (employeeId: any) => {
  try {
    const response = await axios.get(`http://localhost/sistema-erp-construccion/backend/public/employee-tasks/employees/${employeeId}`);
    return response.data; // Suponiendo que el formato de respuesta es un array de tareas
  } catch (error) {
    console.error('Error al obtener las tareas:', error);
    throw error;
  }
};


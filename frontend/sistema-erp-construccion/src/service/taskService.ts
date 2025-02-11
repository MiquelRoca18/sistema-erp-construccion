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
    return Array.isArray(response.data.data) ? response.data.data : [];
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al obtener tareas pendientes');
  }
};

// Obtener todas las tareas de un empleado
export const getAllTasks = async (employeeId: any) => {
  try {
    const response = await axios.get(`http://localhost/sistema-erp-construccion/backend/public/employee-tasks/employees/${employeeId}`);
    
    // Aseguramos que siempre se devuelva un array
    if (response.data && Array.isArray(response.data.data)) {
      return response.data.data;
    } else if (Array.isArray(response.data)) {
      return response.data;
    } else {
      console.warn('Formato inesperado en la respuesta de getAllTasks:', response.data);
      return [];
    }
  } catch (error) {
    console.error('Error al obtener las tareas:', error);
    return []; // Devolver un array vacío en caso de error
  }
};

export const updateTask = async (taskId: number, data: any) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.put(`${API_URL}/tasks/${taskId}`, data, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al actualizar la tarea');
  }
};

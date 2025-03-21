import axios from 'axios';
import { setLocalStorageWithExpiry, getLocalStorageWithExpiry } from '@/utils';

const API_URL = import.meta.env.VITE_API_URL;

// Obtener las tareas pendientes
export const getPendingTasks = async (employeeId: number | null) => {
  if (!employeeId) {
    throw new Error('ID de empleado no válido');
  }

  try {
    // Verificar si hay caché
    const cacheKey = `pending-tasks-${employeeId}`;
    const cachedData = getLocalStorageWithExpiry(cacheKey);
    
    // Usar caché si existe
    if (cachedData) {
      return cachedData;
    }
    
    const response = await axios.get(`${API_URL}/employee-tasks/pending-tasks/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
      },
    });
    
    const data = response.data.data;
    
    // Guardar en caché por 5 minutos
    setLocalStorageWithExpiry(cacheKey, data, 300000);
    
    return data;
  } catch (error: any) {
    console.error('Error al obtener tareas pendientes:', error);
    throw new Error(error.response?.data?.message || 'Error al obtener tareas pendientes');
  }
};

// Obtener todas las tareas de un empleado
export const getAllTasks = async (employeeId: any) => {
  if (!employeeId) {
    return [];
  }
  
  try {
    // Verificar si hay caché
    const cacheKey = `all-tasks-${employeeId}`;
    const cachedData = getLocalStorageWithExpiry(cacheKey);
    
    // Usar caché si existe
    if (cachedData) {
      return cachedData;
    }
    
    const token = localStorage.getItem('token');
    
    const response = await axios.get(`${API_URL}/employee-tasks/employees/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      }
    });
    
    let data;
    if (response.data && Array.isArray(response.data.data)) {
      data = response.data.data;
    } else if (Array.isArray(response.data)) {
      data = response.data;
    } else {
      console.warn('Formato inesperado en la respuesta de getAllTasks:', response.data);
      data = [];
    }
    
    // Guardar en caché por 5 minutos
    setLocalStorageWithExpiry(cacheKey, data, 300000);
    
    return data;
  } catch (error: any) {
    console.error('Error al obtener las tareas:', error);
    return [];
  }
};

// Actualizar una tarea
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
    
    // Invalidar cachés relacionadas con tareas
    const keysToRemove = [];
    for (let i = 0; i < localStorage.length; i++) {
      const key = localStorage.key(i);
      if (key && (
          key.startsWith('all-tasks-') || 
          key.startsWith('pending-tasks-') || 
          key.startsWith('company-tasks') || 
          key.startsWith('responsible-tasks-')
        )) {
        keysToRemove.push(key);
      }
    }
    
    // Remover las claves encontradas
    keysToRemove.forEach(key => localStorage.removeItem(key));
    
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al actualizar la tarea');
  }
};

// Obtener tareas por responsable
export const getTasksByResponsible = async (employeeId: any) => {
  if (!employeeId) {
    return [];
  }
  
  try {
    // Verificar si hay caché
    const cacheKey = `responsible-tasks-${employeeId}`;
    const cachedData = getLocalStorageWithExpiry(cacheKey);
    
    // Usar caché si existe
    if (cachedData) {
      return cachedData;
    }
    
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    
    const response = await axios.get(`${API_URL}/employee-tasks/responsible/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    const data = Array.isArray(response.data.data) ? response.data.data : [];
    
    // Guardar en caché por 5 minutos
    setLocalStorageWithExpiry(cacheKey, data, 300000);
    
    return data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al obtener tareas por responsable');
  }
};

// Actualizar la asignación de una tarea
export const updateTaskAssignment = async (taskId: number, oldEmployeeId: number, newEmployeeId: number) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.put(`${API_URL}/employee-tasks/assignment/${taskId}`, 
      { old_empleados_id: oldEmployeeId, new_empleados_id: newEmployeeId },
      {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      }
    );
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al actualizar la asignación de la tarea');
  }
};

// Obtener todas las tareas de la empresa
export const getAllCompanyTasks = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.get(`${API_URL}/tasks`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return Array.isArray(response.data.data) ? response.data.data : [];
  } catch (error) {
    throw new Error(error.response?.data?.message || 'Error al obtener tareas');
  }
};

// Crear una tarea
export const createTask = async (data: any) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.post(`${API_URL}/tasks`, data, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al crear la tarea');
  }
};

// Eliminar una tarea
export const deleteTask = async (taskId: number) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.delete(`${API_URL}/tasks/${taskId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al eliminar la tarea');
  }
};

// Añadir un empleado a la tarea
export const addEmployeeToTask = async (employeeId: number, taskId: number) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.post(`${API_URL}/employee-tasks`, {
      empleados_id: employeeId,
      tareas_id: taskId,
    }, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al agregar empleado a la tarea');
  }
};

// Remover un empleado de la tarea
export const removeEmployeeFromTask = async (employeeId: number, taskId: number) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.delete(`${API_URL}/employee-tasks`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
      data: {
        empleados_id: employeeId,
        tareas_id: taskId,
      },
    });
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al eliminar empleado de la tarea');
  }
};

// Manejar múltiples operaciones de asignación para una tarea
export const manageTaskAssignments = async (taskId: number, operations: any[]) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.post(`${API_URL}/employee-tasks/manage/${taskId}`, 
      { operations },
      {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      }
    );
    return response.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al gestionar asignaciones de la tarea');
  }
};

import axios from 'axios';
import { setLocalStorageWithExpiry, getLocalStorageWithExpiry } from '@/utils';

const API_URL = import.meta.env.VITE_API_URL;

export const getEmployees = async () => {
  try {
    // Comprobar si hay datos en caché
    const cachedData = getLocalStorageWithExpiry('employees-cache');
    
    // Usar caché si existe
    if (cachedData) {
      console.log('Usando datos de empleados desde caché');
      return cachedData;
    }
    
    // Si no hay caché, hacer la solicitud HTTP
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    
    console.log('Obteniendo datos de empleados desde API');
    const response = await axios.get(`${API_URL}/employees`, {
      headers: { 'Authorization': `Bearer ${token}` },
    });
    
    const data = Array.isArray(response.data.data) ? response.data.data : [];
    
    // Guardar en caché por 10 minutos (600000 ms)
    setLocalStorageWithExpiry('employees-cache', data, 600000);
    
    return data;
  } catch (error: any) {
    // Si ocurre un error, intentar usar la caché aunque esté obsoleta
    const cachedData = localStorage.getItem('employees-cache');
    if (cachedData) {
      try {
        console.log('Error al obtener empleados, usando caché obsoleta');
        return JSON.parse(cachedData).value;
      } catch (e) {
        // Si hay un error al parsear la caché, eliminarla
        localStorage.removeItem('employees-cache');
      }
    }
    
    throw new Error(error.response?.data?.message || 'Error al obtener empleados');
  }
};

export const createEmployee = async (employeeData: {
  nombre: string;
  rol: string;
  fecha_contratacion: string;
  telefono: string;
  correo: string;
}) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    const response = await axios.post(`${API_URL}/employees`, employeeData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });
    
    // Invalidar caché después de crear
    localStorage.removeItem('employees-cache');
    
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al crear empleado');
  }
};

export const updateEmployee = async (employeeId: number, data: any) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    const response = await axios.put(`${API_URL}/employees/${employeeId}`, data, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });
    
    // Invalidar caché después de actualizar
    localStorage.removeItem('employees-cache');
    
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al actualizar empleado');
  }
};

export const deleteEmployee = async (employeeId: number) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    const response = await axios.delete(`${API_URL}/employees/${employeeId}`, {
      headers: { 'Authorization': `Bearer ${token}` },
    });
    
    // Invalidar caché después de eliminar
    localStorage.removeItem('employees-cache');
    
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al eliminar empleado');
  }
};

export const getEmployeeData = async (employeeId: number, token: string = '') => {
  try {
    // Comprobar si hay datos en caché
    const cacheKey = `employee-data-${employeeId}`;
    const cachedData = getLocalStorageWithExpiry(cacheKey);
    
    // Usar caché si existe
    if (cachedData) {
      console.log('Usando datos específicos de empleado desde caché');
      return cachedData;
    }
    
    // Si no hay caché, hacer la solicitud HTTP
    if (!token) {
      token = localStorage.getItem('token') || '';
    }
    
    if (!token) {
      throw new Error('No se encontró el token de autenticación');
    }
    
    console.log('Obteniendo datos específicos de empleado desde API');
    const response = await axios.get(`${API_URL}/employees/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    const data = response.data.data;
    
    // Guardar en caché por 10 minutos
    setLocalStorageWithExpiry(cacheKey, data, 600000);
    
    return data;
  } catch (error: any) {
    console.error('Error al obtener datos del empleado:', error);
    throw new Error(error.response?.data?.message || 'Error al obtener datos del empleado');
  }
};
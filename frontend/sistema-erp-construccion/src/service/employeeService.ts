// employeeService.ts
import axios from 'axios';
const API_URL = import.meta.env.VITE_API_URL;

export const getEmployees = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    const response = await axios.get(`${API_URL}/employees`, {
      headers: { 'Authorization': `Bearer ${token}` },
    });
    return Array.isArray(response.data.data) ? response.data.data : [];
  } catch (error: any) {
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
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al crear empleado');
  }
};

import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

interface LoginResponse {
  token: string;
  empleados_id: number;
  rol: string;
}

export const login = async (credentials: { username: string; password_hash: string }): Promise<LoginResponse> => {
  try {
    const response = await axios.post(`${API_URL}/auth/login`, credentials, {
      headers: {
        'Content-Type': 'application/json',
      },
      withCredentials: true 
    });
    
    return response.data.data;
  } catch (error: any) {
    console.error("Error de login completo:", error);
    console.error("Error completo:", JSON.stringify(error, null, 2));
    console.error("Detalles del error:", error.response?.data);
    console.error("Status del error:", error.response?.status);
    console.error("Headers del error:", error.response?.headers);
    throw new Error(error.response?.data?.message || 'Error de autenticación');
  }
};

export const getEmployeeData = async (employeeId: number, token: string) => {
  try {
    const response = await axios.get(`${API_URL}/employees/${employeeId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    return response.data.data; 
  } catch (error: any) {
    console.error('Error al obtener datos del empleado:', error);
    throw new Error(error.response?.data?.message || 'Error al obtener datos del empleado');
  }
};

export const updateEmployee = async (employeeId: number, updatedData: any, token: string) => {
  try {
    const response = await axios.put(`${API_URL}/employees/${employeeId}`, updatedData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });
    
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al actualizar los datos del empleado');
  }
};

interface PasswordData {
  currentPassword: string;
  newPassword: string;
  confirmPassword: string;
}

export const changePassword = async (passwordData: PasswordData) => {
  const token = localStorage.getItem('token');

  if (!token) {
    throw new Error('No se encontró el token de autenticación');
  }

  try {
    const response = await axios.post(`${API_URL}/auth/change-password`, passwordData, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });

    return response.data.message;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al cambiar la contraseña');
  }
};

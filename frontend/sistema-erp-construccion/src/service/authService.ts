import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

interface LoginResponse {
  token: string;
  user: {
    id: number;
    username: string;
    email?: string;
  };
}

export const login = async (credentials: { username: string; password_hash: string }): Promise<LoginResponse> => {
  try {
    const response = await axios.post(`${API_URL}/auth/login`, credentials, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    console.log("datos de respuesta", response.data);
    console.log(response.data.data);
    return response.data.data; 
  } catch (error: any) {
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
    
    return response.data; 
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al obtener datos del empleado');
  }
};

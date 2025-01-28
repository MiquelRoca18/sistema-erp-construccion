import axios from 'axios';

const API_URL = 'http://localhost/sistema-erp-construccion/backend/public'; 

interface LoginResponse {
  token: string;
  user: {
    id: number;
    username: string;
    email?: string;
  };
}

export const login = async (credentials: { username: string; password: string }): Promise<LoginResponse> => {
  try {
    const response = await axios.post(`${API_URL}/auth/login`, credentials, {
      headers: {
        'Content-Type': 'application/json',
      },
    });
    console.log(response.data.data);
    return response.data.data; // Accedemos a los datos devueltos
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error de autenticación');
  }
};

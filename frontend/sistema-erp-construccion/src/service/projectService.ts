import axios from 'axios';

// Definir la interfaz Project aquí mismo para tenerla disponible
export interface Project {
  nombre_proyecto: string;
  estado?: string;
  fecha_inicio: string;
  fecha_fin: string | null;
  descripcion?: string;
  responsable_id?: number | string;
}

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

export const createProject = async (projectData: Partial<Project>) => {
  try {
    // Completar datos faltantes si es necesario
    const projectComplete = {
      ...projectData,
      fecha_inicio: projectData.fecha_inicio || new Date().toISOString().split('T')[0],
      fecha_fin: projectData.fecha_fin || null
    };

    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    
    const response = await axios.post(`${API_URL}/projects`, projectComplete, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al crear proyecto');
  }
};

export const updateProject = async (projectId: number, projectData: any) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    const response = await axios.put(`${API_URL}/projects/${projectId}`, projectData, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
    });
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al actualizar proyecto');
  }
};

export const deleteProject = async (projectId: number) => {
  try {
    const token = localStorage.getItem('token');
    if (!token) throw new Error('No se encontró el token.');
    const response = await axios.delete(`${API_URL}/projects/${projectId}`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al eliminar proyecto');
  }
};
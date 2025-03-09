import axios from 'axios';
import { setLocalStorageWithExpiry, getLocalStorageWithExpiry } from '@/utils';

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
    // Comprobar si hay datos en caché
    const cachedData = getLocalStorageWithExpiry('projects-cache');
    
    // Usar caché si existe
    if (cachedData) {
      console.log('Usando datos de proyectos desde caché');
      return cachedData;
    }
    
    const token = localStorage.getItem('token');
    if (!token) {
      throw new Error('No se encontró el token.');
    }
    
    console.log('Obteniendo datos de proyectos desde API');
    const response = await axios.get(`${API_URL}/projects`, {
      headers: {
        'Authorization': `Bearer ${token}`,
      },
    });
    
    const data = Array.isArray(response.data.data) ? response.data.data : [];
    
    // Guardar en caché por 10 minutos
    setLocalStorageWithExpiry('projects-cache', data, 600000);
    
    return data;
  } catch (error: any) {
    // Si ocurre un error, intentar usar la caché aunque esté obsoleta
    const cachedData = localStorage.getItem('projects-cache');
    if (cachedData) {
      try {
        console.log('Error al obtener proyectos, usando caché obsoleta');
        return JSON.parse(cachedData).value;
      } catch (e) {
        localStorage.removeItem('projects-cache');
      }
    }
    
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
    
    // Invalidar caché después de crear
    localStorage.removeItem('projects-cache');
    
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
    
    // Invalidar caché después de actualizar
    localStorage.removeItem('projects-cache');
    
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
    
    // Invalidar caché después de eliminar
    localStorage.removeItem('projects-cache');
    
    return response.data.data;
  } catch (error: any) {
    throw new Error(error.response?.data?.message || 'Error al eliminar proyecto');
  }
};
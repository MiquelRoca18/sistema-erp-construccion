export interface Project {
    nombre_proyecto: string;
    estado?: string;
    fecha_inicio: string;
    fecha_fin: string | null;
    descripcion?: string;
    responsable_id?: number | string;
  }
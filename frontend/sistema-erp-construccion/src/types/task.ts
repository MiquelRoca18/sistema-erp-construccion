export interface Task {
    tareas_id: number;
    nombre_tarea: string;
    descripcion?: string;
    estado: string;
    proyectos_id: number | string;
    nombre_proyecto: string;
    empleado_nombre?: string;
    empleados_id?: number | string;
    fecha_inicio?: string;
    fecha_fin?: string | null;
  }
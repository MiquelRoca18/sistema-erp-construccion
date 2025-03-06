<?php
namespace App\Model;

use App\Utils\BaseModel;

class Task extends BaseModel{

    public function __construct(){
        parent::__construct('tareas');
    }

    // Obtener tareas con el nombre del empleado
    public function getTasksWithEmployee() {
        // Optimización: Seleccionar solo columnas necesarias y ordenar por fecha
        $query = "SELECT 
                    t.tareas_id, 
                    t.estado, 
                    t.nombre_tarea, 
                    t.descripcion, 
                    t.proyectos_id, 
                    t.fecha_inicio, 
                    t.fecha_fin,
                    GROUP_CONCAT(e.nombre SEPARATOR ', ') AS empleado_nombre, 
                    p.nombre_proyecto 
                  FROM " . $this->table . " t
                  LEFT JOIN empleados_tareas et ON t.tareas_id = et.tareas_id
                  LEFT JOIN empleados e ON et.empleados_id = e.empleados_id
                  LEFT JOIN proyectos p ON t.proyectos_id = p.proyectos_id
                  GROUP BY t.tareas_id
                  ORDER BY t.fecha_inicio DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Nueva función para obtener tareas de un proyecto específico
    public function getTasksByProject($projectId) {
        $query = "SELECT 
                    t.tareas_id, 
                    t.estado, 
                    t.nombre_tarea, 
                    t.descripcion, 
                    t.fecha_inicio, 
                    t.fecha_fin,
                    GROUP_CONCAT(e.nombre SEPARATOR ', ') AS empleado_nombre
                  FROM " . $this->table . " t
                  LEFT JOIN empleados_tareas et ON t.tareas_id = et.tareas_id
                  LEFT JOIN empleados e ON et.empleados_id = e.empleados_id
                  WHERE t.proyectos_id = :proyectos_id
                  GROUP BY t.tareas_id
                  ORDER BY t.fecha_inicio";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':proyectos_id', $projectId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
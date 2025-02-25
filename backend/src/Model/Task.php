<?php
namespace App\Model;

use App\Utils\BaseModel;

class Task extends BaseModel{

    public function __construct(){
        parent::__construct('tareas');
    }

    public function getTasksWithEmployee() {
        $query = "SELECT t.*, 
                         GROUP_CONCAT(e.nombre SEPARATOR ', ') AS empleado_nombre, 
                         p.nombre_proyecto 
                  FROM " . $this->table . " t
                  LEFT JOIN empleados_tareas et ON t.tareas_id = et.tareas_id
                  LEFT JOIN empleados e ON et.empleados_id = e.empleados_id
                  LEFT JOIN proyectos p ON t.proyectos_id = p.proyectos_id
                  GROUP BY t.tareas_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}

?>
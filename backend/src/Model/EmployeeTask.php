<?php
namespace App\Model;
use App\Config\Database;

class EmployeeTask {
    private $db;
    private $table = 'empleados_tareas';

    public function __construct() {
         // Establece la conexión a la base de datos
        $this->db = (new Database())->getConnection();
    }

    public function exists($id, $column) {
        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE ' . $column . '_id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    

    // Verificar si la relación entre empleado y tarea ya existe
    public function relationExists($empleados_id, $tareas_id) {
        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE empleados_id = :empleados_id AND tareas_id = :tareas_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->bindParam(':tareas_id', $tareas_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Asignar tarea a un empleado
    public function addTaskToEmployee($empleados_id, $tareas_id) {
        $query = 'INSERT INTO ' . $this->table . ' (empleados_id, tareas_id) VALUES (:empleados_id, :tareas_id)';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->bindParam(':tareas_id', $tareas_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Eliminar relación entre tarea y empleado
    public function removeTaskFromEmployee($empleados_id, $tareas_id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE empleados_id = :empleados_id AND tareas_id = :tareas_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->bindParam(':tareas_id', $tareas_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtener tareas asignadas a un empleado
    public function getTasksByEmployee($empleados_id) {
        $query = 'SELECT t.* FROM tareas t 
                  INNER JOIN ' . $this->table . ' et ON t.tareas_id = et.tareas_id
                  WHERE et.empleados_id = :empleados_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Obtener empleados asignados a una tarea
    public function getEmployeesByTask($tareas_id) {
        $query = 'SELECT e.* FROM empleados e 
                  INNER JOIN ' . $this->table . ' et ON e.empleados_id = et.empleados_id
                  WHERE et.tareas_id = :tareas_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tareas_id', $tareas_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Obtener tareas asignadas a un empleado con filtro por estado (pendiente)
    public function getPendingTasksByEmployee($empleados_id, $estado = 'pendiente') {
        $query = 'SELECT t.* FROM tareas t 
                INNER JOIN ' . $this->table . ' et ON t.tareas_id = et.tareas_id
                WHERE et.empleados_id = :empleados_id AND t.estado = :estado';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
<?php
namespace App\Model;
use App\Config\Database;

class EmployeeTask {
    private $db;
    private $table = 'empleados_tareas';

    public function __construct() {
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

    // Obtener tareas asignadas a un empleado con el nombre del proyecto
    public function getTasksByEmployee($empleados_id) {
        $query = 'SELECT 
            t.tareas_id, 
            t.estado, 
            t.nombre_tarea, 
            t.descripcion, 
            t.proyectos_id, 
            t.fecha_inicio, 
            t.fecha_fin, 
            p.nombre_proyecto 
        FROM tareas t 
        INNER JOIN empleados_tareas et ON t.tareas_id = et.tareas_id
        INNER JOIN proyectos p ON t.proyectos_id = p.proyectos_id
        WHERE et.empleados_id = :empleados_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    // Obtener empleados asignados a una tarea
    public function getEmployeesByTask($tareas_id) {
        $query = 'SELECT 
                    e.empleados_id, 
                    e.nombre, 
                    e.rol
                  FROM empleados e 
                  INNER JOIN ' . $this->table . ' et ON e.empleados_id = et.empleados_id
                  WHERE et.tareas_id = :tareas_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tareas_id', $tareas_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Obtener tareas asignadas a un empleado con filtro por estado
    public function getPendingTasksByEmployee($empleados_id, $estado = 'pendiente') {
        $query = '
            SELECT 
                t.tareas_id, 
                t.estado, 
                t.nombre_tarea, 
                t.descripcion, 
                t.proyectos_id, 
                t.fecha_inicio, 
                t.fecha_fin, 
                p.nombre_proyecto
            FROM tareas t
            INNER JOIN ' . $this->table . ' et ON t.tareas_id = et.tareas_id
            INNER JOIN proyectos p ON t.proyectos_id = p.proyectos_id
            WHERE et.empleados_id = :empleados_id AND t.estado = :estado
        ';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':empleados_id', $empleados_id, \PDO::PARAM_INT);
        $stmt->bindParam(':estado', $estado, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Obtener tareas asignadas de otros empleados por empleado responsable
    public function getTasksByResponsible($employeeId) {
        $query = "
            SELECT 
                t.tareas_id, 
                t.estado, 
                t.nombre_tarea, 
                t.descripcion, 
                t.proyectos_id, 
                t.fecha_inicio, 
                t.fecha_fin, 
                p.nombre_proyecto,
                GROUP_CONCAT(e.nombre SEPARATOR ', ') AS nombre_empleado
            FROM proyectos p
            INNER JOIN tareas t ON p.proyectos_id = t.proyectos_id
            INNER JOIN empleados_tareas et ON t.tareas_id = et.tareas_id
            INNER JOIN empleados e ON et.empleados_id = e.empleados_id
            WHERE p.responsable_id = :employeeId
            GROUP BY t.tareas_id, t.estado, t.nombre_tarea, t.descripcion, t.proyectos_id, t.fecha_inicio, t.fecha_fin, p.nombre_proyecto
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':employeeId', $employeeId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }  

    // Actualizar asignación de tarea a empleado
    public function updateAssignment($taskId, $oldEmployeeId, $newEmployeeId) {
        // Convertir explícitamente los IDs a enteros por seguridad
        $taskId = (int)$taskId;
        $oldEmployeeId = (int)$oldEmployeeId;
        $newEmployeeId = (int)$newEmployeeId;
        
        try {
            $query = "UPDATE " . $this->table . " 
                      SET empleados_id = :newEmployeeId 
                      WHERE tareas_id = :taskId 
                        AND empleados_id = :oldEmployeeId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':newEmployeeId', $newEmployeeId, \PDO::PARAM_INT);
            $stmt->bindParam(':taskId', $taskId, \PDO::PARAM_INT);
            $stmt->bindParam(':oldEmployeeId', $oldEmployeeId, \PDO::PARAM_INT);
            $stmt->execute();
            
            // Verificar si realmente se actualizó alguna fila
            $rowCount = $stmt->rowCount();
            return $rowCount > 0;
        } catch (\PDOException $e) {
            error_log("Error en actualización de asignación: " . $e->getMessage());
            return false;
        }
    }

    // Nuevo método para manejar múltiples operaciones de asignación
    public function manageTaskAssignments($taskId, $operations) {
        try {
            $db = $this->db;
            $db->beginTransaction();
            
            foreach ($operations as $operation) {
                switch ($operation->type) {
                    case 'add':
                        if (!$this->relationExists($operation->empleados_id, $taskId)) {
                            $this->addTaskToEmployee($operation->empleados_id, $taskId);
                        }
                        break;
                        
                    case 'remove':
                        if ($this->relationExists($operation->empleados_id, $taskId)) {
                            $this->removeTaskFromEmployee($operation->empleados_id, $taskId);
                        }
                        break;
                        
                    case 'update':
                        if ($this->relationExists($operation->old_empleados_id, $taskId)) {
                            $this->removeTaskFromEmployee($operation->old_empleados_id, $taskId);
                            if (!$this->relationExists($operation->new_empleados_id, $taskId)) {
                                $this->addTaskToEmployee($operation->new_empleados_id, $taskId);
                            }
                        }
                        break;
                }
            }
            
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error en manageTaskAssignments: " . $e->getMessage());
            return false;
        }
    }
}
?>
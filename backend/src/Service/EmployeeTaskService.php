<?php
namespace App\Service;

use App\Model\EmployeeTask;
use App\Model\Employee;
use App\Model\Task;
use App\Utils\BaseService;

class EmployeeTaskService extends BaseService {
    private $employeeModel;
    private $taskModel;

    public function __construct() {
        $this->model = new EmployeeTask();
        $this->employeeModel = new Employee();
        $this->taskModel = new Task();
    }

    // Agregar tarea a un empleado
    public function addTaskToEmployee($data) {
        if (empty($data->empleados_id) || empty($data->tareas_id)) {
            return $this->responseError('Los campos empleados_id y tareas_id son obligatorios.');
        }

        // Verificar si el empleado existe
        if (!$this->employeeModel->exists($data->empleados_id)) {
            return $this->responseError("El empleado con ID {$data->empleados_id} no existe.");
        }

        // Verificar si la tarea existe
        if (!$this->taskModel->exists($data->tareas_id)) {
            return $this->responseError("La tarea con ID {$data->tareas_id} no existe.");
        }

        // Verificar si la relación ya existe
        if ($this->model->relationExists($data->empleados_id, $data->tareas_id)) {
            return $this->responseError('La relación entre el empleado y la tarea ya existe.');
        }

        $result = $this->model->addTaskToEmployee($data->empleados_id, $data->tareas_id);
        return $result
            ? $this->responseCreated(null, 'Tarea asignada al empleado exitosamente.')
            : $this->responseError('No se pudo asignar la tarea al empleado.');
    }

    // Eliminar tarea de un empleado
    public function removeTaskFromEmployee($data) {
        if (empty($data->empleados_id) || empty($data->tareas_id)) {
            return $this->responseError('Los campos empleados_id y tareas_id son obligatorios.');
        }

        // Verificar si la relación existe
        if (!$this->model->relationExists($data->empleados_id, $data->tareas_id)) {
            return $this->responseError('La relación no existe.');
        }

        $result = $this->model->removeTaskFromEmployee($data->empleados_id, $data->tareas_id);
        return $result
            ? $this->responseDeleted('Relación eliminada exitosamente.')
            : $this->responseError('No se pudo eliminar la relación.');
    }

    // Listar tareas asignadas a un empleado
    public function getTasksByEmployee($employeeId) {
        // Verificar si el empleado existe
        if (!$this->employeeModel->exists($employeeId)) {
            return $this->responseError("El empleado con ID {$employeeId} no existe.");
        }

        $tasks = $this->model->getTasksByEmployee($employeeId);
        return $tasks
            ? $this->responseFound($tasks, 'Tareas asignadas encontradas.')
            : $this->responseNotFound();
    }

    // Listar empleados asignados a una tarea
    public function getEmployeesByTask($taskId) {
        // Verificar si la tarea existe
        if (!$this->taskModel->exists($taskId)) {
            return $this->responseError("La tarea con ID {$taskId} no existe.");
        }

        $employees = $this->model->getEmployeesByTask($taskId);
        return $employees
            ? $this->responseFound($employees, 'Empleados asignados encontrados.')
            : $this->responseNotFound();
    }

    // Listar tareas asignadas a un empleado, con estado opcional
    public function getPendingTasksByEmployee($employeeId, $estado = 'pendiente') {
        // Verificar si el empleado existe
        if (!$this->employeeModel->exists($employeeId)) {
            return $this->responseError("El empleado con ID {$employeeId} no existe.");
        }

        $tasks = $this->model->getPendingTasksByEmployee($employeeId, $estado);
        return $tasks
            ? $this->responseFound($tasks, 'Tareas asignadas encontradas.')
            : $this->responseNotFound();
    }

    public function getTasksByResponsible($employeeId) {
        // Verificar si el empleado existe
        if (!$this->employeeModel->exists($employeeId)) {
            return $this->responseError("El empleado con ID {$employeeId} no existe.");
        }
        $tasks = $this->model->getTasksByResponsible($employeeId);
        return $tasks
            ? $this->responseFound($tasks, 'Tareas del responsable encontradas.')
            : $this->responseNotFound();
    }

    // Nuevo método para manejar múltiples operaciones de asignación
    public function manageTaskAssignments($taskId, $operations) {
        // Verificar si la tarea existe
        if (!$this->taskModel->exists($taskId)) {
            return $this->responseError("La tarea con ID {$taskId} no existe.");
        }
        
        // Convertir explícitamente el ID de tarea a entero
        $taskId = (int)$taskId;
        
        // Array para registrar resultados
        $results = [
            'success' => 0,
            'failed' => 0,
            'operations' => []
        ];
        
        // Registrar para depuración
        error_log("Iniciando manejo de múltiples asignaciones para tarea $taskId");
        
        try {
            // Iniciar transacción para garantizar que todas las operaciones se completen o se revierta todo
            $db = $this->model->getDb();
            $db->beginTransaction();
            
            foreach ($operations as $index => $operation) {
                $operationResult = [
                    'index' => $index,
                    'type' => isset($operation->type) ? $operation->type : 'unknown',
                    'success' => false,
                    'message' => ''
                ];
                
                switch ($operation->type) {
                    case 'add':
                        // Agregar un empleado a la tarea
                        if (empty($operation->empleados_id)) {
                            $operationResult['message'] = "El campo empleados_id es obligatorio para operaciones 'add'.";
                            break;
                        }
                        
                        $empleadosId = (int)$operation->empleados_id;
                        
                        // Verificar si el empleado existe
                        if (!$this->employeeModel->exists($empleadosId)) {
                            $operationResult['message'] = "El empleado con ID {$empleadosId} no existe.";
                            break;
                        }
                        
                        // Verificar si la relación ya existe
                        if ($this->model->relationExists($empleadosId, $taskId)) {
                            $operationResult['message'] = "El empleado con ID {$empleadosId} ya está asignado a esta tarea.";
                            break;
                        }
                        
                        if ($this->model->addTaskToEmployee($empleadosId, $taskId)) {
                            $operationResult['success'] = true;
                            $operationResult['message'] = "Empleado con ID {$empleadosId} asignado correctamente.";
                            $results['success']++;
                        } else {
                            $operationResult['message'] = "No se pudo asignar el empleado con ID {$empleadosId}.";
                            $results['failed']++;
                        }
                        break;
                        
                    case 'remove':
                        // Eliminar un empleado de la tarea
                        if (empty($operation->empleados_id)) {
                            $operationResult['message'] = "El campo empleados_id es obligatorio para operaciones 'remove'.";
                            break;
                        }
                        
                        $empleadosId = (int)$operation->empleados_id;
                        
                        // Verificar si la relación existe
                        if (!$this->model->relationExists($empleadosId, $taskId)) {
                            $operationResult['message'] = "El empleado con ID {$empleadosId} no está asignado a esta tarea.";
                            break;
                        }
                        
                        if ($this->model->removeTaskFromEmployee($empleadosId, $taskId)) {
                            $operationResult['success'] = true;
                            $operationResult['message'] = "Empleado con ID {$empleadosId} removido correctamente.";
                            $results['success']++;
                        } else {
                            $operationResult['message'] = "No se pudo remover el empleado con ID {$empleadosId}.";
                            $results['failed']++;
                        }
                        break;
                        
                    case 'update':
                        // Cambiar asignación de un empleado a otro
                        if (empty($operation->old_empleados_id) || empty($operation->new_empleados_id)) {
                            $operationResult['message'] = "Los campos old_empleados_id y new_empleados_id son obligatorios para operaciones 'update'.";
                            break;
                        }
                        
                        $oldEmpleadosId = (int)$operation->old_empleados_id;
                        $newEmpleadosId = (int)$operation->new_empleados_id;
                        
                        // Verificar si los empleados existen
                        if (!$this->employeeModel->exists($oldEmpleadosId)) {
                            $operationResult['message'] = "El empleado original con ID {$oldEmpleadosId} no existe.";
                            break;
                        }
                        
                        if (!$this->employeeModel->exists($newEmpleadosId)) {
                            $operationResult['message'] = "El nuevo empleado con ID {$newEmpleadosId} no existe.";
                            break;
                        }
                        
                        // Verificar si la relación original existe
                        if (!$this->model->relationExists($oldEmpleadosId, $taskId)) {
                            $operationResult['message'] = "El empleado con ID {$oldEmpleadosId} no está asignado a esta tarea.";
                            break;
                        }
                        
                        // Verificar si la nueva relación ya existe
                        if ($this->model->relationExists($newEmpleadosId, $taskId)) {
                            $operationResult['message'] = "El empleado con ID {$newEmpleadosId} ya está asignado a esta tarea.";
                            break;
                        }
                        
                        if ($this->model->updateAssignment($taskId, $oldEmpleadosId, $newEmpleadosId)) {
                            $operationResult['success'] = true;
                            $operationResult['message'] = "Asignación actualizada correctamente de empleado {$oldEmpleadosId} a {$newEmpleadosId}.";
                            $results['success']++;
                        } else {
                            $operationResult['message'] = "No se pudo actualizar la asignación.";
                            $results['failed']++;
                        }
                        break;
                        
                    default:
                        $operationResult['message'] = "Tipo de operación '{$operation->type}' no reconocido. Use 'add', 'remove' o 'update'.";
                        $results['failed']++;
                }
                
                $results['operations'][] = $operationResult;
            }
            
            // Si hubo algún fallo, revertir todo
            if ($results['failed'] > 0) {
                $db->rollBack();
                error_log("Revirtiendo todas las operaciones debido a fallos: " . json_encode($results));
                return $this->responseError("Algunas operaciones fallaron. No se realizaron cambios.", $results);
            } else {
                // Si todo fue exitoso, confirmar los cambios
                $db->commit();
                error_log("Todas las operaciones de asignación completadas con éxito: " . json_encode($results));
                return $this->responseUpdated($results, "Asignaciones gestionadas con éxito.");
            }
            
        } catch (\Exception $e) {
            // En caso de excepción, revertir cambios
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error en manejo de asignaciones: " . $e->getMessage());
            return $this->responseError("Error al gestionar asignaciones: " . $e->getMessage());
        }
    }

    // Mantener updateAssignment para compatibilidad
    public function updateAssignment($taskId, $oldEmployeeId, $newEmployeeId) {
        // Verificar que el nuevo empleado existe
        if (!$this->employeeModel->exists($newEmployeeId)) {
            return $this->responseError("El empleado con ID {$newEmployeeId} no existe.");
        }
        
        // Verificar que la relación con el empleado a cambiar exista
        if (!$this->model->relationExists($oldEmployeeId, $taskId)) {
            return $this->responseError("La asignación para el empleado con ID {$oldEmployeeId} no existe para esta tarea.");
        }
        
        // Verificar si la nueva asignación ya existe para esa tarea
        if ($this->model->relationExists($newEmployeeId, $taskId)) {
            $empData = $this->employeeModel->getById($newEmployeeId);
            $empName = $empData ? $empData['nombre'] : $newEmployeeId;
            return $this->responseError("El empleado '{$empName}' ya está asignado a esta tarea.");
        }
        
        // Registrar información para depuración
        error_log("Actualizando asignación: Tarea $taskId de empleado $oldEmployeeId a $newEmployeeId");
        
        $result = $this->model->updateAssignment($taskId, $oldEmployeeId, $newEmployeeId);
        
        if (!$result) {
            error_log("Error al actualizar asignación: Tarea $taskId de empleado $oldEmployeeId a $newEmployeeId");
        }
        
        return $result 
            ? $this->responseUpdated("Asignación actualizada exitosamente.") 
            : $this->responseError("No se pudo actualizar la asignación.");
    }
}
?>
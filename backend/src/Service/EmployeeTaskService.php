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
}
?>

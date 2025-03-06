<?php
namespace App\Controller;

use App\Service\EmployeeTaskService;
use App\Utils\BaseController;

class EmployeeTaskController extends BaseController {
    private $service;

    public function __construct() {
        $this->service = new EmployeeTaskService();
    }

    // Agregar tarea a un empleado
    public function addTaskToEmployee() {
        $data = $this->getRequestData();
        $response = $this->service->addTaskToEmployee($data);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete') && isset($data->empleados_id)) {
            apcu_delete("employee_tasks_" . $data->empleados_id);
            apcu_delete("employee_pending_tasks_" . $data->empleados_id);
            
            if (isset($data->tareas_id)) {
                apcu_delete("task_employees_" . $data->tareas_id);
            }
        }
        
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Eliminar tarea de un empleado
    public function removeTaskFromEmployee() {
        $data = $this->getRequestData();
        $response = $this->service->removeTaskFromEmployee($data);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete') && isset($data->empleados_id)) {
            apcu_delete("employee_tasks_" . $data->empleados_id);
            apcu_delete("employee_pending_tasks_" . $data->empleados_id);
            
            if (isset($data->tareas_id)) {
                apcu_delete("task_employees_" . $data->tareas_id);
            }
        }
        
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Listar tareas de un empleado
    public function getTasksByEmployee($employeeId) {
        $cacheKey = "employee_tasks_" . $employeeId;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Tareas encontradas (caché)', $cachedData);
            return;
        }
        
        $response = $this->service->getTasksByEmployee($employeeId);
        
        if (isset($response['data'])) {
            saveToCache($cacheKey, $response['data'], 300); // 5 minutos
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Listar empleados de una tarea
    public function getEmployeesByTask($taskId) {
        $cacheKey = "task_employees_" . $taskId;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Empleados encontrados (caché)', $cachedData);
            return;
        }
        
        $response = $this->service->getEmployeesByTask($taskId);
        
        if (isset($response['data'])) {
            saveToCache($cacheKey, $response['data'], 300); // 5 minutos
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Listar tareas de un empleado, con filtro por estado
    public function getPendingTasksByEmployee($employeeId) {
        $estado = isset($_GET['estado']) ? $_GET['estado'] : 'pendiente';
        $cacheKey = "employee_pending_tasks_" . $employeeId . "_" . $estado;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Tareas encontradas (caché)', $cachedData);
            return;
        }

        $response = $this->service->getPendingTasksByEmployee($employeeId, $estado);
        
        if (isset($response['data'])) {
            saveToCache($cacheKey, $response['data'], 300); // 5 minutos
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }
    
    // Listar tareas de un empleado, con filtro por responsable
    public function getTasksByResponsible($employeeId) {
        $cacheKey = "employee_responsible_tasks_" . $employeeId;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Tareas encontradas (caché)', $cachedData);
            return;
        }
        
        $response = $this->service->getTasksByResponsible($employeeId);
        
        if (isset($response['data'])) {
            saveToCache($cacheKey, $response['data'], 300); // 5 minutos
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Actualizar asignación de tarea
    public function updateAssignment($taskId) {
        $data = $this->getRequestData();
        if (empty($data->old_empleados_id) || empty($data->new_empleados_id)) {
            $this->sendResponse(400, "Los campos old_empleados_id y new_empleados_id son obligatorios.");
            return;
        }
        
        $response = $this->service->updateAssignment($taskId, $data->old_empleados_id, $data->new_empleados_id);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("employee_tasks_" . $data->old_empleados_id);
            apcu_delete("employee_tasks_" . $data->new_empleados_id);
            apcu_delete("employee_pending_tasks_" . $data->old_empleados_id . "_pendiente");
            apcu_delete("employee_pending_tasks_" . $data->new_empleados_id . "_pendiente");
            apcu_delete("task_employees_" . $taskId);
        }
        
        $this->sendResponse($response['status'], $response['message'], $response['data'] ?? null);
    }    
}
?>
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
        
        // Si la respuesta tiene 'data', la pasamos. Si no, solo pasamos el 'message'.
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
        
        // Si la respuesta tiene 'data', la pasamos. Si no, solo pasamos el 'message'.
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Listar tareas de un empleado
    public function getTasksByEmployee($employeeId) {
        $response = $this->service->getTasksByEmployee($employeeId);
        
        // Si la respuesta tiene 'data', la pasamos. Si no, solo pasamos el 'message'.
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Listar empleados de una tarea
    public function getEmployeesByTask($taskId) {
        $response = $this->service->getEmployeesByTask($taskId);
        
        // Si la respuesta tiene 'data', la pasamos. Si no, solo pasamos el 'message'.
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    // Listar tareas de un empleado, con filtro por estado
    public function getPendingTasksByEmployee($employeeId) {

        // Obtener el estado desde los parámetros de la URL (por defecto 'pendiente')
        $estado = isset($_GET['estado']) ? $_GET['estado'] : 'pendiente';

        $response = $this->service->getPendingTasksByEmployee($employeeId, $estado);
        
        // Si la respuesta tiene 'data', la pasamos. Si no, solo pasamos el 'message'.
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }
    
    public function getTasksByResponsible($employeeId) {
        $response = $this->service->getTasksByResponsible($employeeId);
        if (isset($response['data'])) {
            $this->sendResponse($response['status'], $response['message'], $response['data']);
        } else {
            $this->sendResponse($response['status'], $response['message']);
        }
    }

    public function updateAssignment($taskId) {
        $data = $this->getRequestData();
        if (empty($data->old_empleados_id) || empty($data->new_empleados_id)) {
            $this->sendResponse(400, "Los campos old_empleados_id y new_empleados_id son obligatorios.");
            return;
        }
        $response = $this->service->updateAssignment($taskId, $data->old_empleados_id, $data->new_empleados_id);
        $this->sendResponse($response['status'], $response['message'], $response['data'] ?? null);
    }    
}
?>

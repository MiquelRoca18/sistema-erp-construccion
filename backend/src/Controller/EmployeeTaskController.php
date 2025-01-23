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
}
?>

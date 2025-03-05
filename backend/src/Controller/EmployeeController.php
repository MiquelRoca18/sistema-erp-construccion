<?php
namespace App\Controller;

use App\Service\EmployeeService;
use App\Utils\BaseController;

class EmployeeController extends BaseController {
    private $employeeService;

    public function __construct() {
        $this->employeeService = new EmployeeService();
    }

    public function get() {
        $result = $this->employeeService->getEmployees();
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getEmployee($employeeId) {
        custom_error_log("Controller getEmployee - ID recibido: " . print_r($employeeId, true), 'EMPLOYEE_CONTROLLER');
        
        $result = $this->employeeService->getEmployee($employeeId);
        
        custom_error_log("Controller getEmployee - Resultado: " . print_r($result, true), 'EMPLOYEE_CONTROLLER');
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createEmployee() {
        $data = $this->getRequestData();
        $result = $this->employeeService->createEmployee($data);
        $this->sendResponse($result['status'], $result['message']);
    }

    public function updateEmployee($employeeId) {
        $data = $this->getRequestData();
        $result = $this->employeeService->updateEmployee($employeeId, $data);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function deleteEmployee($employeeId) {
        $result = $this->employeeService->deleteEmployee($employeeId);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}
?>
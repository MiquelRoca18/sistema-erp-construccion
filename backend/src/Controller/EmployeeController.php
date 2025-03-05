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
        // Validate that employeeId is a positive integer
        if (!is_numeric($employeeId) || intval($employeeId) <= 0) {
            error_log('[DEBUG] Invalid Employee ID: ' . print_r($employeeId, true));
            $this->sendResponse(400, 'Invalid employee ID', null);
            return;
        }

        error_log('[DEBUG] Controller - Employee ID: ' . $employeeId);
        $result = $this->employeeService->getEmployee($employeeId); 
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null); 
    }

    public function createEmployee() { 
        $data = $this->getRequestData(); 
        error_log('[DEBUG] Create Employee Data: ' . print_r($data, true));
        $result = $this->employeeService->createEmployee($data); 
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null); 
    }

    public function updateEmployee($employeeId) { 
        // Validate employeeId similar to getEmployee
        if (!is_numeric($employeeId) || intval($employeeId) <= 0) {
            error_log('[DEBUG] Invalid Employee ID for Update: ' . print_r($employeeId, true));
            $this->sendResponse(400, 'Invalid employee ID', null);
            return;
        }

        $data = $this->getRequestData(); 
        error_log('[DEBUG] Update Employee Data: ' . print_r($data, true));
        $result = $this->employeeService->updateEmployee($employeeId, $data); 
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null); 
    }

    public function deleteEmployee($employeeId) { 
        // Validate employeeId similar to getEmployee
        if (!is_numeric($employeeId) || intval($employeeId) <= 0) {
            error_log('[DEBUG] Invalid Employee ID for Delete: ' . print_r($employeeId, true));
            $this->sendResponse(400, 'Invalid employee ID', null);
            return;
        }

        $result = $this->employeeService->deleteEmployee($employeeId); 
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null); 
    } 
} 
?>
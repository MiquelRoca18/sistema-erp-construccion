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
        $cacheKey = "employees_all";
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Empleados encontrados (caché)', $cachedData);
            return;
        }
        
        $result = $this->employeeService->getEmployees();
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); // 5 minutos
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function getEmployee($employeeId) {
        $cacheKey = "employee_" . $employeeId;
        $cachedData = getFromCache($cacheKey);
        
        if ($cachedData !== null) {
            $this->sendResponse(200, 'Empleado encontrado (caché)', $cachedData);
            return;
        }
        
        custom_error_log("Controller getEmployee - ID recibido: " . print_r($employeeId, true), 'EMPLOYEE_CONTROLLER');
        
        $result = $this->employeeService->getEmployee($employeeId);
        
        custom_error_log("Controller getEmployee - Resultado: " . print_r($result, true), 'EMPLOYEE_CONTROLLER');
        
        if (isset($result['data'])) {
            saveToCache($cacheKey, $result['data'], 300); // 5 minutos
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function createEmployee() {
        $data = $this->getRequestData();
        $result = $this->employeeService->createEmployee($data);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("employees_all");
        }
        
        $this->sendResponse($result['status'], $result['message']);
    }

    public function updateEmployee($employeeId) {
        $data = $this->getRequestData();
        $result = $this->employeeService->updateEmployee($employeeId, $data);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("employees_all");
            apcu_delete("employee_" . $employeeId);
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function deleteEmployee($employeeId) {
        $result = $this->employeeService->deleteEmployee($employeeId);
        
        // Limpiar caché relacionada
        if (function_exists('apcu_delete')) {
            apcu_delete("employees_all");
            apcu_delete("employee_" . $employeeId);
        }
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }
}
?>
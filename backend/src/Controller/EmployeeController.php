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
        error_log('[DEBUG] Controller getEmployee - Inicio');
        error_log('[DEBUG] Controller getEmployee - ID recibido: ' . print_r($employeeId, true));
        
        $result = $this->employeeService->getEmployee($employeeId);
        
        error_log('[DEBUG] Controller getEmployee - Resultado completo: ' . print_r($result, true));
        error_log('[DEBUG] Controller getEmployee - Estatus: ' . ($result['status'] ?? 'No definido'));
        error_log('[DEBUG] Controller getEmployee - Mensaje: ' . ($result['message'] ?? 'No definido'));
        error_log('[DEBUG] Controller getEmployee - Datos: ' . print_r($result['data'] ?? 'No hay datos', true));
        
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
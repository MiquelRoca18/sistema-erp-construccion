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

    public function getEmployee($request, $response, $args) {
        $employeeId = $args['employeeId'];
        $result = $this->employeeService->getEmployee($employeeId);
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
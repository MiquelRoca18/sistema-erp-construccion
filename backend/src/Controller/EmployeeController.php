<?php
namespace App\Controller;

use App\Service\EmployeeService;

class EmployeeController {
    private $employeeService;

    public function __construct() {
        $this->employeeService = new EmployeeService();
    }

    public function get(){
        $result = $this->employeeService->getEmployees();

        http_response_code($result['status']);
            echo json_encode([
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ], JSON_UNESCAPED_UNICODE);
    }

    public function getEmployee($employeeId){
        $result = $this->employeeService->getEmployee($employeeId);

        http_response_code($result['status']);
        echo json_encode(['message' => $result['message'], 'data' => $result['data'] ?? null], JSON_UNESCAPED_UNICODE);
    }

    public function createEmployee(){
        // Obtener datos del cuerpo de la solicitud
        $data = json_decode(file_get_contents('php://input'));
        
        // Llamar al servicio para procesar la solicitud
        $result = $this->employeeService->createEmployee($data);
    
        // Establecer el código de respuesta HTTP
        http_response_code($result['status']);
    
        // Devolver la respuesta en formato JSON
        echo json_encode(['message' => $result['message']], JSON_UNESCAPED_UNICODE);
    }

    public function updateEmployee($employeeId){
        // Obtener los datos del cuerpo de la solicitud (pueden ser solo algunos campos)
        $data = json_decode(file_get_contents('php://input'));

        // Llamar al servicio para actualizar el empleado
        $result = $this->employeeService->updateEmployee($employeeId, $data);

        // Establecer el código de respuesta HTTP
        http_response_code($result['status']);

        // Devolver la respuesta en formato JSON
        echo json_encode(['message' => $result['message'], 'data' => $result['data'] ?? null], JSON_UNESCAPED_UNICODE);
    }

    public function deleteEmployee($employeeId) {
        $result = $this->employeeService->deleteEmployee($employeeId);
    
        // Establecer el código de respuesta HTTP
        http_response_code($result['status']);
    
        // Devolver la respuesta en formato JSON
        echo json_encode([
            'message' => $result['message'],
            'data' => $result['data'] ?? null 
        ], JSON_UNESCAPED_UNICODE);
    }
    
}

?>

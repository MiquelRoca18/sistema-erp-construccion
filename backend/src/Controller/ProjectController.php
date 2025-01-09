<?php
    namespace App\Controller;

    use App\Service\ProjectService;

    class ProjectController {
        private $projectService;

        public function __construct() {
            $this->projectService = new ProjectService();
        }

        public function get() {
            $result = $this->projectService->getProjects();
        
            http_response_code($result['status']);
            echo json_encode([
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ], JSON_UNESCAPED_UNICODE);
        }
        

        public function getProject($projectId) {
            $result = $this->projectService->getProject($projectId);
        
            // Configurar el código HTTP basado en el resultado
            http_response_code($result['status']);
            echo json_encode(['message' => $result['message'], 'data' => $result['data'] ?? null], JSON_UNESCAPED_UNICODE);
        }
        

        public function createProject() {
            // Obtener datos del cuerpo de la solicitud
            $data = json_decode(file_get_contents('php://input'));
        
            // Llamar al servicio para procesar la solicitud
            $result = $this->projectService->createProject($data);
        
            // Establecer el código de respuesta HTTP
            http_response_code($result['status']);
        
            // Devolver la respuesta en formato JSON
            echo json_encode(['message' => $result['message']], JSON_UNESCAPED_UNICODE);
        }
        

        public function updateProject($projectId){
            // Obtener los datos del cuerpo de la solicitud (pueden ser solo algunos campos)
            $data = json_decode(file_get_contents('php://input'));

            // Llamar al servicio para actualizar el empleado
            $result = $this->projectService->updateProject($projectId, $data);

            // Establecer el código de respuesta HTTP
            http_response_code($result['status']);

            // Devolver la respuesta en formato JSON
            echo json_encode(['message' => $result['message'], 'data' => $result['data'] ?? null], JSON_UNESCAPED_UNICODE);
        }

        public function deleteProject($projectId){
            $result = $this->projectService->deleteProject($projectId);
    
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
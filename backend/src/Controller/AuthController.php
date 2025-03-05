<?php
namespace App\Controller;
use App\Service\AuthService;
use App\Utils\BaseController;
use \Firebase\JWT\JWT;
class AuthController extends BaseController {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function login() {
        error_log("Método login() llamado");
        
        // Verificar el método de la solicitud
        error_log("Método HTTP: " . $_SERVER['REQUEST_METHOD']);
        
        // Obtener datos de la solicitud
        $data = $this->getRequestData();
        
        error_log("Datos recibidos: " . print_r($data, true));
        
        // Verificar si los datos son los esperados
        if (!isset($data->username) || !isset($data->password_hash)) {
            error_log("Datos de login incompletos");
            $this->sendResponse(400, 'Datos de login incompletos');
            return;
        }
        
        $result = $this->authService->authenticate($data);
        
        error_log("Resultado de autenticación: " . print_r($result, true));
        
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function logout() {
        $this->sendResponse(200, 'Sesión cerrada con éxito');
    }

    public function changePassword() {
        $data = $this->getRequestData();
        
        // Obtener el token JWT del encabezado de autorización
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            $this->sendResponse(401, 'Token de autenticación no proporcionado');
            return;
        }
    
        // Extraer el token eliminando "Bearer "
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        $result = $this->authService->updatePassword($token, $data);
        
        $this->sendResponse($result['status'], $result['message']);
    }
    
}
?>
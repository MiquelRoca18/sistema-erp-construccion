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
        $data = $this->getRequestData();
        $result = $this->authService->authenticate($data);
        $this->sendResponse($result['status'], $result['message'], $result['data'] ?? null);
    }

    public function logout() {
        $this->sendResponse(200, 'Sesión cerrada con éxito');
    }

    public function changePassword() {
        // Obtener el token del encabezado Authorization
        $header = $this->getAuthorizationHeader();
        if (!$header) {
            $this->sendResponse(401, 'Token de autorización faltante');
            return;
        }
    
        try {
            // Decodificar el token JWT
            $secretKey = $_ENV['SECRET_KEY'];
            $decoded = JWT::decode($header, new \Firebase\JWT\Key($secretKey, 'HS256'));
    
            // Obtener el ID del usuario del payload del token
            $userId = $decoded->sub;
    
            // Procesar el cambio de contraseña
            $data = $this->getRequestData();
    
            // Validar que se proporcionen los datos necesarios
            if (empty($data->new_password) || empty($data->confirm_password)) {
                $this->sendResponse(400, 'Faltan parámetros obligatorios');
                return;
            }
    
            // Verificar que las contraseñas coincidan
            if ($data->new_password !== $data->confirm_password) {
                $this->sendResponse(400, 'Las contraseñas no coinciden');
                return;
            }
    
            // Agregar el ID del usuario al objeto $data
            $data->id_usuario = $userId;
    
            // Delegar la lógica de cambio de contraseña al servicio
            $result = $this->authService->changePassword($data);
    
            // Enviar la respuesta al cliente
            $this->sendResponse($result['status'], $result['message']);
        } catch (\Exception $e) {
            $this->sendResponse(401, 'Token inválido o expirado');
        }
    }
}
?>
<?php
namespace App\Service;

use App\Model\AuthModel;
use App\Utils\BaseService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key; 
use Exception;

class AuthService extends BaseService {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function authenticate($data) {
        // Validar si se proporcionan username y password
        if (empty($data->username) || empty($data->password_hash)) {
            return ['status' => 400, 'message' => 'El nombre de usuario y la contraseña son obligatorios'];
        }

        // Verificar las credenciales
        $user = $this->authModel->getUserByUsername($data->username);
        
        if (!$user || !password_verify($data->password_hash, $user['password_hash'])) {
            return ['status' => 401, 'message' => 'Credenciales inválidas'];
        }

        // Crear el payload del token
        $secretKey = $_ENV['SECRET_KEY'];
        $issuedAt = time();
        $expiresAt = $issuedAt + $_ENV['JWT_EXPIRES_IN'];

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'sub' => $user['id_usuario'],
            'role' => $user['id_rol']
        ];

        // Generar el token JWT
        $token = JWT::encode($payload, $secretKey, 'HS256');

        return ['status' => 200, 'message' => 'Inicio de sesión exitoso', 'data' =>  ['empleados_id' => $user['id_usuario'], 'token' => $token]];
    }

    public function updatePassword($token, $data) {
        // Verificar que se enviaron las contraseñas correctamente
        if (empty($data->currentPassword) || empty($data->newPassword) || empty($data->confirmPassword)) {
            return ['status' => 400, 'message' => 'Todos los campos son obligatorios'];
        }
    
        // Verificar que la nueva contraseña y la confirmación coincidan
        if ($data->newPassword !== $data->confirmPassword) {
            return ['status' => 400, 'message' => 'Las contraseñas nuevas no coinciden'];
        }
    
        try {
            // Decodificar el token JWT para obtener el ID del usuario
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->sub;
    
            // Obtener el usuario actual desde la base de datos
            $user = $this->authModel->getUserById($userId);
            if (!$user) {
                return ['status' => 404, 'message' => 'Usuario no encontrado'];
            }
    
            // Verificar que la contraseña actual sea correcta
            if (!password_verify($data->currentPassword, $user['password_hash'])) {
                return ['status' => 401, 'message' => 'La contraseña actual es incorrecta'];
            }
    
            // Hashear la nueva contraseña
            $hashedPassword = password_hash($data->newPassword, PASSWORD_BCRYPT);
    
            // Actualizar la contraseña en la base de datos
            $this->authModel->updatePassword($userId, $hashedPassword);
    
            return ['status' => 200, 'message' => 'Contraseña actualizada con éxito'];
        } catch (Exception $e) {
            return ['status' => 401, 'message' => 'Token inválido o expirado'];
        }
    }
    
}
?>
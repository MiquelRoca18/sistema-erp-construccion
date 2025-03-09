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
        if (empty($data->username) || empty($data->password_hash)) {
            return ['status' => 400, 'message' => 'El nombre de usuario y la contraseña son obligatorios'];
        }

        // Verificar las credenciales
        $user = $this->authModel->getUserByUsername($data->username);
        
        if (!$user || !password_verify($data->password_hash, $user['password_hash'])) {
            return ['status' => 401, 'message' => 'Credenciales inválidas'];
        }

        // Crear el payload del token, incluyendo el rol real (admin o usuario)
        $secretKey = $_ENV['SECRET_KEY'];
        $issuedAt = time();
        $expiresAt = $issuedAt + $_ENV['JWT_EXPIRES_IN'];

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expiresAt,
            'sub' => $user['empleados_id'], 
            'role' => $user['role']
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        // Retornar además el rol en la respuesta para que el frontend lo use
        return [
            'status'  => 200,
            'message' => 'Inicio de sesión exitoso',
            'data'    => [
                'empleados_id' => $user['empleados_id'],
                'token'        => $token,
                'rol'          => $user['role']
            ]
        ];
    }

    public function updatePassword($token, $data) {
        if (empty($data->currentPassword) || empty($data->newPassword) || empty($data->confirmPassword)) {
            return ['status' => 400, 'message' => 'Todos los campos son obligatorios'];
        }
    
        if ($data->newPassword !== $data->confirmPassword) {
            return ['status' => 400, 'message' => 'Las contraseñas nuevas no coinciden'];
        }
    
        try {
            $decoded = JWT::decode($token, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $userId = $decoded->sub;
    
            $user = $this->authModel->getUserById($userId);
            if (!$user) {
                return ['status' => 404, 'message' => 'Usuario no encontrado'];
            }
    
            if (!password_verify($data->currentPassword, $user['password_hash'])) {
                return ['status' => 401, 'message' => 'La contraseña actual es incorrecta'];
            }
    
            $hashedPassword = password_hash($data->newPassword, PASSWORD_BCRYPT);
    
            $this->authModel->updatePassword($userId, $hashedPassword);
    
            return ['status' => 200, 'message' => 'Contraseña actualizada con éxito'];
        } catch (Exception $e) {
            return ['status' => 401, 'message' => 'Token inválido o expirado'];
        }
    }
}
?>

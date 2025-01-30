<?php
namespace App\Service;

use App\Model\AuthModel;
use App\Utils\BaseService;
use Firebase\JWT\JWT;

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
}
?>
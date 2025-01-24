<?php
namespace App\Controller;
use App\Service\AuthService;
use App\Utils\BaseController;
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
}
?>
<?php
namespace App\Utils;

class BaseController {
    /**
     * Envía una respuesta JSON con el código de estado proporcionado.
     *
     * @param int $status Código de estado HTTP.
     * @param string $message Mensaje de respuesta.
     * @param mixed $data Datos adicionales para incluir en la respuesta.
     */
    protected function sendResponse($status, $message, $data = null) {
        http_response_code($status);
        echo json_encode([
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtiene los datos del cuerpo de la solicitud en formato JSON.
     *
     * @return mixed|null Los datos decodificados o null si no hay cuerpo.
     */
    protected function getRequestData() {
        return json_decode(file_get_contents('php://input'));
    }
}
?>
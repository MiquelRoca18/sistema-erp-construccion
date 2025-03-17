<?php
namespace App\Utils;

class BaseController {
    // Envía una respuesta JSON con el código de estado proporcionado
    protected function sendResponse($status, $message, $data = null) {
        http_response_code($status);
        
        // Creamos la respuesta base
        $response = [
            'message' => $message
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    // Obtiene los datos del cuerpo de la solicitud en formato JSON
    protected function getRequestData() {
        return json_decode(file_get_contents('php://input'));
    }

    protected function getAuthorizationHeader() {
        // Verificar si el encabezado 'Authorization' está presente
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }
    
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            foreach ($headers as $header => $value) {
                if (strtolower($header) === 'authorization') {
                    return $value;
                }
            }
        }
    
        return null;
    }
}
?>

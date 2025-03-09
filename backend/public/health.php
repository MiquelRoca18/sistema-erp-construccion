<?php
// Configurar el registro de errores
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/health-check-errors.log'); 

// Responder con OK
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
http_response_code(200);
exit;
?>
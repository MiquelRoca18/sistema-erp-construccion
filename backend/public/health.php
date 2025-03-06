<?php
// Configurar el registro de errores
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/health-check-errors.log'); // Ajusta la ruta si es necesario

// Registrar información sobre la solicitud
error_log("==== HEALTH CHECK ACCEDIDO ====");
error_log("Fecha/Hora: " . date('Y-m-d H:i:s'));
error_log("IP: " . $_SERVER['REMOTE_ADDR']);
error_log("User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'No definido'));
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("=============================");

// Responder con OK
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
http_response_code(200);
exit;
?>
<?php
header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
http_response_code(200);
exit;
?>
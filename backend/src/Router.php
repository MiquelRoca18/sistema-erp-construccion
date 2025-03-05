<?php

namespace App;

class Router {
    private $routes = [];

    public function addRoute($method, $pattern, $handler) {
        custom_error_log("Adding route: Method = {$method}, Pattern = {$pattern}", 'ROUTER_CONFIG');
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        custom_error_log("Dispatching: URI = {$requestUri}, Method = {$requestMethod}", 'ROUTER');
        
        foreach ($this->routes as $route) {
            // Construir patrón de regex
            $pattern = '#^' . $route['pattern'] . '$#';
            
            custom_error_log("Checking route: Method = {$route['method']}, Pattern = {$pattern}", 'ROUTER');
            
            // Verificar método y patrón
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                custom_error_log("Route matched: " . $pattern, 'ROUTER');
                
                // Eliminar la coincidencia completa, dejando solo los parámetros capturados
                array_shift($matches);
                
                // Llamar al controlador con los parámetros capturados
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }
        
        // Si no se encuentra la ruta
        custom_error_log("No route found for: {$requestUri}", 'ROUTER');
        
        header("HTTP/1.0 404 Not Found");
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true, 
            'message' => 'Ruta no encontrada'
        ]);
    }
}
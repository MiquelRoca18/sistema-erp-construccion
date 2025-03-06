<?php

namespace App;

class Router {
    private $routes = [];

    public function addRoute($method, $pattern, $handler) {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch($requestUri, $requestMethod) {
        
        foreach ($this->routes as $route) {
            // Construir patrón de regex
            $pattern = '#^' . $route['pattern'] . '$#';
            
            // Verificar método y patrón
            if ($route['method'] === $requestMethod && preg_match($pattern, $requestUri, $matches)) {
                
                // Eliminar la coincidencia completa, dejando solo los parámetros capturados
                array_shift($matches);
                
                // Llamar al controlador con los parámetros capturados
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true, 
            'message' => 'Ruta no encontrada'
        ]);
    }
}
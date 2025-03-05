<?php

namespace App;

class Router {

    private $routes = [];

    public function addRoute($method, $route, $callback) {
        $this->routes[] = compact('method', 'route', 'callback');
    }

    public function dispatch($requestUri, $requestMethod) {
        // Eliminar cualquier barra inicial
        $requestUri = ltrim($requestUri, '/');
        
        error_log("Rutas registradas: " . print_r($this->routes, true));
        error_log("URI recibida (procesada): $requestUri");
        error_log("Método recibido: $requestMethod");
    
        foreach ($this->routes as $route) {
            // Eliminar barra inicial del patrón de ruta
            $routePattern = ltrim($route['route'], '/');
            
            error_log("Comparando: " . $route['method'] . " " . $routePattern);
            
            if ($requestMethod == $route['method']) {
                // Modificar el patrón de regex para ser más flexible
                if (preg_match("#^" . $routePattern . "(/.*)?$#", $requestUri, $params)) {
                    error_log("Ruta coincidente encontrada");
                    array_shift($params);
                    call_user_func_array($route['callback'], $params);
                    return;
                }
            }
        }
        
        error_log("Ninguna ruta coincidente");
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Ruta no encontrada"]);
    }
}

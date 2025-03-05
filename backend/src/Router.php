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
    
        foreach ($this->routes as $route) {
            // Eliminar barra inicial del patrón de ruta
            $routePattern = ltrim($route['route'], '/');
            
            
            if ($requestMethod == $route['method']) {
                // Modificar el patrón de regex para ser más flexible
                if (preg_match("#^" . $routePattern . "(/.*)?$#", $requestUri, $params)) {
                    array_shift($params);
                    call_user_func_array($route['callback'], $params);
                    return;
                }
            }
        }
        
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Ruta no encontrada"]);
    }
}

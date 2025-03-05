<?php

namespace App;

class Router {

    private $routes = [];

    public function addRoute($method, $route, $callback) {
        $this->routes[] = compact('method', 'route', 'callback');
    }

    public function dispatch($requestUri, $requestMethod) {
        error_log("Rutas registradas: " . print_r($this->routes, true));
        error_log("URI recibida: $requestUri");
        error_log("Método recibido: $requestMethod");
    
        foreach ($this->routes as $route) {
            error_log("Comparando: " . $route['method'] . " " . $route['route']);
            
            if ($requestMethod == $route['method']) {
                if (preg_match("#^" . $route['route'] . "$#", $requestUri, $params)) {
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

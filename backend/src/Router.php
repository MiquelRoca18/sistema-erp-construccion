<?php
// src/Router.php

namespace App;

class Router {

    private $routes = [];

    public function addRoute($method, $route, $callback) {
        $this->routes[] = compact('method', 'route', 'callback');
    }

    public function dispatch($requestUri, $requestMethod) {
        foreach ($this->routes as $route) {
            // Verifica si el método de la ruta coincide con el método de la solicitud
            if ($requestMethod == $route['method']) {
                // Asegúrate de usar delimitadores para las expresiones regulares
                if (preg_match("#^" . $route['route'] . "$#", $requestUri, $params)) {
                    // Ejecuta la función correspondiente
                    array_shift($params); // El primer valor es siempre la ruta coincidente
                    call_user_func_array($route['callback'], $params);
                    return;
                }
            }
        }
        
        // Si no se encuentra la ruta, devuelve un 404
        header("HTTP/1.0 404 Not Found");
        echo json_encode(["message" => "Ruta no encontrada"]);
    }
}

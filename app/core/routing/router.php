<?php

namespace Core\Routing;

class Router
{
    private static $routes = [];

    // Registrar uma rota
    public static function add($method, $route, $callback)
    {
        self::$routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'callback' => $callback
        ];
    }

    // Despachar a rota
    public static function dispatch()
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestUri = str_replace('/RTKSistema', '', $requestUri);  // Ajuste para subdiretório
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            $routePattern = self::convertToRegex($route['route']);
            if ($requestMethod === $route['method'] && preg_match($routePattern, $requestUri, $matches)) {
                array_shift($matches); // Remove o primeiro elemento para obter apenas parâmetros
                return call_user_func_array($route['callback'], $matches);
            }
        }

        // Rota não encontrada
        http_response_code(404);
        echo '404 - Rota não encontrada';
    }

    // Converter a rota para regex
    private static function convertToRegex($route)
    {
        $route = preg_replace('/\//', '\/', $route);
        $route = preg_replace('/\{[a-zA-Z0-9]+\}/', '([a-zA-Z0-9_-]+)', $route);  // Suporte para caracteres alfanuméricos e hífens
        return '/^' . $route . '$/';
    }
}

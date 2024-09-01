<?php
namespace Core\Routing;

class Router
{
    private static $routes = [];

    // Registrar uma rota
    public static function add($method, $route, $callback)
    {
        error_log("Registrando rota: {$route} com método: {$method}");
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

        error_log("Request URI: {$requestUri}");
        error_log("Request Method: {$requestMethod}");

        foreach (self::$routes as $route) {
            error_log("Checking route: {$route['route']}");
            $routePattern = self::convertToRegex($route['route']);
            if ($requestMethod === $route['method'] && preg_match($routePattern, $requestUri, $matches)) {
                array_shift($matches); // Remove o primeiro elemento para obter apenas parâmetros
                error_log("Match found for route: {$route['route']}");
                return call_user_func_array($route['callback'], $matches);
            }
        }

        // Rota não encontrada
        error_log("No match found. Returning 404.");
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

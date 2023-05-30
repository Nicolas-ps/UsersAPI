<?php

namespace Nicolasps\UsersAPI\Meta;

use Nicolasps\UsersAPI\Controllers\Controller;

class Router
{
    private static array $routes;

    public static function  checkRoute(string $path, string $resource, string $method, string $action): bool
    {
        if ($path == $resource) {
            if ($_SERVER['REQUEST_METHOD'] === $method) {
                return true;
            }

            return false;
        }

        // Verificação para rotas que requerem parâmetros

        if ($_SERVER['REQUEST_METHOD'] !== $method) {
            return false;
        }

        if (! preg_match_all('/\{([^\}]*)\}/', $path, $variables)) return false;

        $regex = str_replace('/', '\/', $path);

        foreach ($variables[0] as $k => $variable) {
            $replacement = '([a-zA-Z0-9\-\_\ ]+)';
            $regex = str_replace($variable, $replacement, $regex);
        }

        $regex = preg_replace('/{([a-zA-Z]+)}/', '([a-zA-Z0-9+])', $regex);

        if (! preg_match('/^' . $regex . '$/', $resource, $params)) {
            return false;
        }

        $getParams = array_combine($variables[1], array_diff_key($params, [0]));

        foreach ($getParams as $key => $param) {
            $GLOBALS['REQUEST']->set($key, $param);
        }

        return true;
    }

    public static function resolve($path): bool
    {
        $match = false;
        foreach (self::$routes as $route) {
            if (self::checkRoute($route[0], $path, $route[1], $route[3])) {
                $match = $route;
                break;
            };
        }

        if (! $match) {
            response([], HTTPResponseCodes::NotFound);
        }

        self::execute($match, $path);

        return true;
    }

    public static function execute(array $route): void
    {
        $controller = $route[2];
        $method = $route[3];

        (new $controller())->$method($GLOBALS['REQUEST']);
    }

    public static function add(string $name, string $method, $controller, string $action): void
    {
        self::$routes[] = [$name, $method, $controller, $action];
    }
}
<?php

namespace Core;

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    public function post(string $path, string $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute(string $method, string $path, string $action): void
    {
        $this->routes[] = [
            'method' => $method,
            'path'   => trim($path, '/'),
            'action' => $action,
        ];
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = trim($uri, '/');
        $routeFound = false;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || $route['path'] !== $uri) {
                continue;
            }

            $routeFound = true;
            [$controllerShort, $methodName] = explode('@', $route['action']);
            $controllerName = 'Controllers\\' . $controllerShort;

            if (!class_exists($controllerName) || !method_exists($controllerName, $methodName)) {
                http_response_code(500);
                echo 'Erro interno: controller ou ação inexistente.';
                return;
            }

            $controller = new $controllerName();
            $controller->$methodName();
            return;
        }

        http_response_code(404);
        $viewFile = dirname(__DIR__) . '/Views/errors/404.php';
        if (is_file($viewFile)) {
            $requested = htmlspecialchars($uri === '' ? '/' : '/' . $uri, ENT_QUOTES, 'UTF-8');
            require $viewFile;
            return;
        }

        echo '404 - Página não encontrada.';
        if (!$routeFound) {
            echo "<br><small>URI: '" . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . "'</small>";
        }
    }
}

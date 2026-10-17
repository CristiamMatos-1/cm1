<?php

namespace Core;

class Router
{
    private array $routes = [];

    /**
     * Registra uma rota GET
     */
    public function get(string $path, string $action): void
    {
        $this->addRoute('GET', $path, $action);
    }

    /**
     * Registra uma rota POST
     */
    public function post(string $path, string $action): void
    {
        $this->addRoute('POST', $path, $action);
    }

    /**
     * Método auxiliar para adicionar a rota na lista
     */
    private function addRoute(string $method, string $path, string $action): void
    {
        // Remove barras no início e no fim para normalizar (ex: /sobre/ -> sobre)
        $path = trim($path, '/');
        $this->routes[] = [
            'method' => $method,
            'path'   => $path,
            'action' => $action
        ];
    }

    /**
     * Despacha a requisição para o controller e método corretos
     */
    public function dispatch(string $uri, string $method): void
    {
        // Normaliza a URI (remove barras e garante que string vazia caia na raiz)
        $uri = trim($uri, '/');
        
        // Se a URI ficou vazia após o trim ou se chamou apenas index.php, forçamos para '/'
        if ($uri === '' || $uri === 'index.php') {
            $uri = ''; // Na nossa rota registramos '/' mas na addRoute cortamos as barras, então fica vazio ''
        }

        $routeFound = false;

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                $routeFound = true;
                $actionParts = explode('@', $route['action']);
                
                $controllerName = "Controllers\\" . $actionParts[0];
                $methodName = $actionParts[1];

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    
                    if (method_exists($controller, $methodName)) {
                        $controller->$methodName();
                        return;
                    }
                }
            }
        }

        if (!$routeFound) {
            http_response_code(404);
            // Log para debug caso a página não seja encontrada
            echo "404 - Página não encontrada.<br>";
            echo "<small style='color:gray'>URI Solicitada pelo cPanel: '" . htmlspecialchars($uri) . "'</small>";
        }
    }
}

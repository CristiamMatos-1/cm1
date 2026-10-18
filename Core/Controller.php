<?php

namespace Core;

use Config\App;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = App::rootPath() . "/Views/{$view}.php";

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Erro: View '{$view}' não encontrada.";
            return;
        }

        require $viewFile;
    }

    protected function redirect(string $path, array $query = []): never
    {
        header('Location: ' . App::url($path, $query));
        exit;
    }

    protected function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

<?php

namespace Core;

abstract class Controller
{
    /**
     * Método para renderizar uma view passando variáveis (dados)
     *
     * @param string $view Caminho da view (ex: 'home/index')
     * @param array $data Array associativo com os dados para a view
     */
    protected function render(string $view, array $data = []): void
    {
        // Extrai o array para variáveis locais (ex: ['title' => 'Home'] vira $title = 'Home')
        extract($data);
        
        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("Erro: View '{$view}' não encontrada no caminho {$viewFile}.");
        }
    }
}

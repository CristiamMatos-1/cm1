<?php
// ATIVA A EXIBIÇÃO E LOG DE ERROS PARA DESCOBRIR A CAUSA DA TELA BRANCA
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Força a gravação dos erros em um arquivo local caso o servidor bloqueie a exibição na tela
ini_set('log_errors', 1);
ini_set('error_log', dirname(__DIR__) . '/php-error.log');

// Corrige o erro de sessão do cPanel forçando uma pasta local do projeto
$sessionPath = dirname(__DIR__) . '/sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0755, true);
}
if (is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

session_start();

spl_autoload_register(function ($class) {
    $base_dir = dirname(__DIR__) . '/';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require_once dirname(__DIR__) . '/Core/helpers.php';

use Config\App;
use Core\Router;

$router = new Router();

// ==========================================
// ROTAS PÚBLICAS
// ==========================================
$router->get('/', 'HomeController@index');
$router->get('/materias', 'HomeController@materias');
$router->get('/artigo', 'HomeController@artigo');
$router->get('/pagina', 'HomeController@page');
$router->get('/contato', 'HomeController@contato');

// ==========================================
// ROTAS DE AUTENTICAÇÃO
// ==========================================
$router->get('/login', 'AuthController@login');
$router->post('/login/authenticate', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');

// ==========================================
// ROTAS DO PAINEL ADMINISTRATIVO (Protegidas)
// ==========================================
$router->get('/admin', 'AdminController@index');

// CRUD de Artigos
$router->get('/admin/articles', 'ArticleController@index');
$router->get('/admin/articles/create', 'ArticleController@create');
$router->post('/admin/articles/store', 'ArticleController@store');
$router->get('/admin/articles/edit', 'ArticleController@edit');
$router->post('/admin/articles/update', 'ArticleController@update');
$router->get('/admin/articles/delete', 'ArticleController@delete');

// Endpoint IA (Ajax)
$router->post('/admin/articles/generate-ai', 'ArticleController@generateAiContent');

// Configurações (WhatsApp, YouTube)
$router->get('/admin/settings', 'SettingsController@index');
$router->post('/admin/settings/store', 'SettingsController@store');

// CRUD de Usuários
$router->get('/admin/users', 'AdminUserController@index');
$router->get('/admin/users/create', 'AdminUserController@create');
$router->post('/admin/users/store', 'AdminUserController@store');
$router->get('/admin/users/edit', 'AdminUserController@edit');
$router->post('/admin/users/update', 'AdminUserController@update');
$router->get('/admin/users/delete', 'AdminUserController@delete');

// CRUD de Páginas Institucionais (Sobre, Tecnologias, etc)
$router->get('/admin/pages', 'AdminPageController@index');
$router->get('/admin/pages/create', 'AdminPageController@create');
$router->post('/admin/pages/store', 'AdminPageController@store');
$router->get('/admin/pages/edit', 'AdminPageController@edit');
$router->post('/admin/pages/update', 'AdminPageController@update');
$router->get('/admin/pages/delete', 'AdminPageController@delete');

// Captura a URI diretamente do servidor (independe do mod_rewrite) e remove
// a pasta onde o projeto está hospedado, detectada automaticamente.
$url = App::routeFromRequestUri($_SERVER['REQUEST_URI'] ?? '/');

$method = $_SERVER['REQUEST_METHOD'];

$router->dispatch($url, $method);

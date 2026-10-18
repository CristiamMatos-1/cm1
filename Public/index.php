<?php

use Config\App;
use Core\Request;
use Core\Router;
use Core\Schema;

$root = dirname(__DIR__);

require $root . '/Config/App.php';
App::boot($root);

$debug = App::isDebug();
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');

$logDir = $root . '/storage/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/php-error.log');

$sessionPath = $root . '/sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0755, true);
}

$cookiePath = App::basePath() === '' ? '/' : App::basePath() . '/';
if (is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => $cookiePath,
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';

Schema::ensure();

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/materias', 'HomeController@materias');
$router->get('/artigo', 'HomeController@artigo');
$router->get('/pagina', 'HomeController@page');
$router->get('/suporte', 'SupportController@index');
$router->get('/download', 'SupportController@download');

$router->get('/login', 'AuthController@login');
$router->post('/login/authenticate', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');

$router->get('/admin', 'AdminController@index');

$router->get('/admin/articles', 'ArticleController@index');
$router->get('/admin/articles/create', 'ArticleController@create');
$router->post('/admin/articles/store', 'ArticleController@store');
$router->get('/admin/articles/edit', 'ArticleController@edit');
$router->post('/admin/articles/update', 'ArticleController@update');
$router->post('/admin/articles/delete', 'ArticleController@delete');
$router->post('/admin/articles/generate-ai', 'ArticleController@generateAiContent');

$router->get('/admin/settings', 'SettingsController@index');
$router->post('/admin/settings/store', 'SettingsController@store');

$router->get('/admin/banners', 'AdminBannerController@index');
$router->get('/admin/banners/create', 'AdminBannerController@create');
$router->post('/admin/banners/store', 'AdminBannerController@store');
$router->get('/admin/banners/edit', 'AdminBannerController@edit');
$router->post('/admin/banners/update', 'AdminBannerController@update');
$router->post('/admin/banners/delete', 'AdminBannerController@delete');

$router->get('/admin/announcements', 'AdminAnnouncementController@index');
$router->get('/admin/announcements/create', 'AdminAnnouncementController@create');
$router->post('/admin/announcements/store', 'AdminAnnouncementController@store');
$router->get('/admin/announcements/edit', 'AdminAnnouncementController@edit');
$router->post('/admin/announcements/update', 'AdminAnnouncementController@update');
$router->post('/admin/announcements/delete', 'AdminAnnouncementController@delete');

$router->get('/admin/downloads', 'AdminDownloadController@index');
$router->get('/admin/downloads/create', 'AdminDownloadController@create');
$router->post('/admin/downloads/store', 'AdminDownloadController@store');
$router->get('/admin/downloads/edit', 'AdminDownloadController@edit');
$router->post('/admin/downloads/update', 'AdminDownloadController@update');
$router->post('/admin/downloads/status', 'AdminDownloadController@setStatus');

$router->get('/admin/users', 'AdminUserController@index');
$router->get('/admin/users/create', 'AdminUserController@create');
$router->post('/admin/users/store', 'AdminUserController@store');
$router->get('/admin/users/edit', 'AdminUserController@edit');
$router->post('/admin/users/update', 'AdminUserController@update');
$router->post('/admin/users/delete', 'AdminUserController@delete');

$router->get('/admin/pages', 'AdminPageController@index');
$router->get('/admin/pages/create', 'AdminPageController@create');
$router->post('/admin/pages/store', 'AdminPageController@store');
$router->get('/admin/pages/edit', 'AdminPageController@edit');
$router->post('/admin/pages/update', 'AdminPageController@update');
$router->post('/admin/pages/delete', 'AdminPageController@delete');

$router->dispatch(Request::path(), Request::method());

<?php
/**
 * Router do PHP built-in server:
 * php -S 0.0.0.0:8080 -t Public Public/router.php
 *
 * Aceita tanto / quanto /cm1 para simular o cPanel (devoluto.com.br/cm1).
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$fileUri = preg_replace('#^/cm1#i', '', $uri) ?: '/';

$publicFile = __DIR__ . $fileUri;
if ($fileUri !== '/' && is_file($publicFile)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/cm1/Public/index.php';
require __DIR__ . '/index.php';

<?php
/**
 * Router do PHP built-in server:
 * php -S 0.0.0.0:8080 -t Public Public/router.php
 *
 * Aceita tanto / quanto /cm1 para simular o cPanel (devoluto.com.br/cm1).
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$fileUri = preg_replace('#^/cm1#i', '', $uri) ?: '/';
$fileUri = preg_replace('#^/Public#i', '', $fileUri) ?: '/';

$base = realpath(__DIR__);
$resolved = ($fileUri !== '/' && $base !== false) ? realpath(__DIR__ . $fileUri) : false;
if ($resolved !== false && str_starts_with($resolved, $base . DIRECTORY_SEPARATOR) && is_file($resolved)) {
    if (preg_match('/\.(php|phtml|phar|htaccess)$/i', $resolved) === 1) {
        http_response_code(403);
        exit;
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($resolved) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . (string) filesize($resolved));
    header('X-Content-Type-Options: nosniff');
    readfile($resolved);
    exit;
}

$_SERVER['SCRIPT_NAME'] = '/cm1/Public/index.php';
require __DIR__ . '/index.php';

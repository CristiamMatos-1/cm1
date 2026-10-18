<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Services\FileUploadService;
use Models\Download;

App::boot($root);

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';

$failed = 0;

function assertSame(string $label, mixed $expected, mixed $actual): void
{
    global $failed;
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL {$label}: esperado " . var_export($expected, true) . ' obteve ' . var_export($actual, true) . "\n");
        $failed++;
        return;
    }
    echo "OK   {$label}\n";
}

assertSame('ext zip permitida', true, FileUploadService::isAllowedExtension('zip'));
assertSame('ext exe permitida', true, FileUploadService::isAllowedExtension('EXE'));
assertSame('ext php bloqueada', false, FileUploadService::isAllowedExtension('php'));
assertSame('ext phtml bloqueada', false, FileUploadService::isAllowedExtension('phtml'));
assertSame('ext html bloqueada', false, FileUploadService::isAllowedExtension('html'));

$svc = new FileUploadService(sys_get_temp_dir() . '/cm1-dl-test/');
assertSame('path traversal rejeitado', null, $svc->absolutePath('../etc/passwd'));
assertSame('nome inválido rejeitado', null, $svc->absolutePath('not-prefixed.zip'));
assertSame('nome vazio rejeitado', null, $svc->absolutePath(''));

assertSame('status publicado válido', true, Download::isValidStatus(Download::STATUS_PUBLISHED));
assertSame('status oculto válido', true, Download::isValidStatus(Download::STATUS_HIDDEN));
assertSame('status removido válido', true, Download::isValidStatus(Download::STATUS_REMOVED));
assertSame('status lixo inválido', false, Download::isValidStatus('deleted'));
assertSame('rótulo publicado', 'Publicado', Download::STATUS_LABELS[Download::STATUS_PUBLISHED]);

assertSame('format bytes kb', '1,0 KB', format_bytes(1024));

exit($failed === 0 ? 0 : 1);

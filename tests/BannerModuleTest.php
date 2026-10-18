<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Input;

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

assertSame('href vazio', '', Input::href(''));
assertSame('href https', 'https://coninfoms.com.br', Input::href('https://coninfoms.com.br'));
assertSame('href caminho interno', '/suporte', Input::href('/suporte'));
assertSame('href javascript bloqueado', '', Input::href('javascript:alert(1)'));
assertSame('href data bloqueado', '', Input::href('data:text/html,hi'));
assertSame('href tira tags', 'https://exemplo.com', Input::href('<b>https://exemplo.com</b>'));

exit($failed === 0 ? 0 : 1);

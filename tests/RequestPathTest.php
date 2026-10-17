<?php

require dirname(__DIR__) . '/Config/App.php';
require dirname(__DIR__) . '/Core/Request.php';

use Config\App;
use Core\Request;

App::boot(dirname(__DIR__));
putenv('APP_BASE_PATH=/cm1');
$_ENV['APP_BASE_PATH'] = '/cm1';

$cases = [
    ['/cm1', '/cm1/Public/index.php', ''],
    ['/cm1/', '/cm1/Public/index.php', ''],
    ['/cm1/index.php', '/cm1/index.php', ''],
    ['/cm1/Public/', '/cm1/Public/index.php', ''],
    ['/cm1/Public/index.php', '/cm1/Public/index.php', ''],
    ['/cm1/login', '/cm1/Public/index.php', 'login'],
    ['/cm1/admin', '/cm1/Public/index.php', 'admin'],
    ['/cm1/admin/articles', '/cm1/Public/index.php', 'admin/articles'],
    ['/cm1/materias?x=1', '/cm1/Public/index.php', 'materias'],
    ['/login', '/index.php', 'login'],
];

$failed = 0;
foreach ($cases as [$uri, $script, $expected]) {
    $actual = Request::path([
        'REQUEST_URI' => $uri,
        'SCRIPT_NAME' => $script,
        'REQUEST_METHOD' => 'GET',
    ]);
    if ($actual !== $expected) {
        fwrite(STDERR, "FAIL {$uri} => '{$actual}' (esperado '{$expected}')\n");
        $failed++;
    } else {
        echo "OK   {$uri} => '{$actual}'\n";
    }
}

exit($failed === 0 ? 0 : 1);

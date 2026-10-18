<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Models\Announcement;

App::boot($root);

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';
require $root . '/Core/Schema.php';

use Core\Schema;

Schema::ensure();

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

assertSame('status draft válido', true, Announcement::isValidStatus(Announcement::STATUS_DRAFT));
assertSame('status published válido', true, Announcement::isValidStatus(Announcement::STATUS_PUBLISHED));
assertSame('status hidden válido', true, Announcement::isValidStatus(Announcement::STATUS_HIDDEN));
assertSame('status lixo inválido', false, Announcement::isValidStatus('active'));
assertSame('rótulo publicado', 'Publicado', Announcement::STATUS_LABELS[Announcement::STATUS_PUBLISHED]);

$model = new Announcement();
$id1 = $model->create([
    'title' => 'Aviso A',
    'body' => 'Primeiro publicado',
    'status' => Announcement::STATUS_PUBLISHED,
]);
assertSame('criou id1', true, $id1 > 0);

$id2 = $model->create([
    'title' => 'Aviso B',
    'body' => 'Segundo publicado',
    'status' => Announcement::STATUS_PUBLISHED,
]);
assertSame('criou id2', true, $id2 > 0);

$first = $model->findById($id1);
$second = $model->findById($id2);
assertSame('primeiro virou oculto', Announcement::STATUS_HIDDEN, $first['status'] ?? null);
assertSame('segundo permanece publicado', Announcement::STATUS_PUBLISHED, $second['status'] ?? null);

$published = $model->getPublished();
assertSame('getPublished retorna o B', $id2, (int) ($published['id'] ?? 0));
assertSame('getPublished body', 'Segundo publicado', $published['body'] ?? null);

$model->update($id2, [
    'title' => 'Aviso B',
    'body' => 'Segundo publicado',
    'status' => Announcement::STATUS_DRAFT,
]);
assertSame('sem publicado após draft', null, $model->getPublished());

$model->delete($id1);
$model->delete($id2);

exit($failed === 0 ? 0 : 1);

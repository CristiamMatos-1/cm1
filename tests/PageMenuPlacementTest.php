<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Schema;
use Models\Page;

App::boot($root);

spl_autoload_register(function (string $class) use ($root): void {
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require $root . '/Core/helpers.php';

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

function assertTrue(string $label, bool $cond): void
{
    assertSame($label, true, $cond);
}

$labels = Page::placementLabels();
assertSame('rótulo menu', 'Menu Principal', $labels[Page::PLACE_HEADER]);
assertSame('rótulo rodapé', 'Rodapé', $labels[Page::PLACE_FOOTER]);
assertSame('rótulo novidades', 'Últimas Novidades', $labels[Page::PLACE_NEWS]);

$model = new Page();

$headerId = $model->create([
    'title' => 'Só no Header ' . uniqid(),
    'content' => '<p>Header only</p>',
    'is_published' => 1,
    'show_in_header' => 1,
    'show_in_footer' => 0,
    'show_in_news' => 0,
    'menu_order' => 10,
]);

$footerId = $model->create([
    'title' => 'Só no Rodapé ' . uniqid(),
    'content' => '<p>Footer only</p>',
    'is_published' => 1,
    'show_in_header' => 0,
    'show_in_footer' => 1,
    'show_in_news' => 0,
    'menu_order' => 20,
]);

$newsId = $model->create([
    'title' => 'Só em Novidades ' . uniqid(),
    'content' => '<p>News only</p>',
    'is_published' => 1,
    'show_in_header' => 0,
    'show_in_footer' => 0,
    'show_in_news' => 1,
    'menu_order' => 5,
]);

$draftId = $model->create([
    'title' => 'Rascunho menu ' . uniqid(),
    'content' => '<p>Draft</p>',
    'is_published' => 0,
    'show_in_header' => 1,
    'show_in_footer' => 1,
    'show_in_news' => 1,
    'menu_order' => 1,
]);

$headerIds = array_map(static fn ($r) => (int) $r['id'], $model->getPublishedFor(Page::PLACE_HEADER));
$footerIds = array_map(static fn ($r) => (int) $r['id'], $model->getPublishedFor(Page::PLACE_FOOTER));
$newsIds = array_map(static fn ($r) => (int) $r['id'], $model->getPublishedFor(Page::PLACE_NEWS));

assertTrue('header contém só-header', in_array($headerId, $headerIds, true));
assertTrue('header não contém só-rodapé', !in_array($footerId, $headerIds, true));
assertTrue('header não contém só-novidades', !in_array($newsId, $headerIds, true));
assertTrue('header não contém rascunho', !in_array($draftId, $headerIds, true));

assertTrue('rodapé contém só-rodapé', in_array($footerId, $footerIds, true));
assertTrue('rodapé não contém só-header', !in_array($headerId, $footerIds, true));

assertTrue('novidades contém só-news', in_array($newsId, $newsIds, true));
assertTrue('novidades não contém só-header', !in_array($headerId, $newsIds, true));

assertSame('placement inválido = vazio', 0, count($model->getPublishedFor('xyz')));

// cleanup
$model->delete($headerId);
$model->delete($footerId);
$model->delete($newsId);
$model->delete($draftId);

exit($failed === 0 ? 0 : 1);

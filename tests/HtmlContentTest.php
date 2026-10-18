<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\HtmlContent;

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

function assertTrue(string $label, bool $cond): void
{
    assertSame($label, true, $cond);
}

// Named entities → UTF-8
$normalized = HtmlContent::normalize('<p>Conclus&atilde;o t&eacute;cnica com &ccedil;</p>');
assertTrue('normalize decodifica named entities', str_contains($normalized, 'Conclusão técnica com ç'));
assertTrue('normalize não deixa &ccedil;', !str_contains($normalized, '&ccedil;'));

// Numeric entities
$normalizedNum = HtmlContent::normalize('<p>Op&#231;&#227;o</p>');
assertTrue('normalize decodifica entidades numéricas', str_contains($normalizedNum, 'Opção'));

// XSS basics
$xss = HtmlContent::normalize('<p>ok</p><script>alert(1)</script><img src=x onerror=alert(1)>');
assertTrue('remove script', !str_contains($xss, '<script'));
assertTrue('remove onerror', !str_contains(strtolower($xss), 'onerror'));

// Excerpt com entities (problema das listagens)
$excerpt = HtmlContent::excerpt('<p>A solu&ccedil;&atilde;o definitiva para o cliente</p>', 80);
assertSame('excerpt UTF-8 legível', 'A solução definitiva para o cliente', $excerpt);
assertTrue('excerpt sem &ccedil;', !str_contains($excerpt, '&ccedil;'));

assertSame(
    'excerpt separa parágrafos',
    'equipe. Mais detalhes',
    HtmlContent::excerpt('<p>equipe.</p><p>Mais detalhes</p>', 80)
);

$long = HtmlContent::excerpt('<p>' . str_repeat('palavra ', 50) . '</p>', 40);
assertTrue('excerpt trunca com reticências', str_ends_with($long, '…'));
assertTrue('excerpt respeita max aproximado', mb_strlen($long) <= 41);

// Helpers
assertSame('plain_excerpt helper', 'A solução definitiva para o cliente', plain_excerpt('<p>A solu&ccedil;&atilde;o definitiva para o cliente</p>', 80));

assertSame(
    'capa usa featured_image',
    'uploads/artigos/foto.jpg',
    article_cover(['featured_image' => 'uploads/artigos/foto.jpg'], 'uploads/logo.png')
);
assertSame(
    'capa fallback para logo',
    'uploads/logo.png',
    article_cover(['featured_image' => ''], 'uploads/logo.png')
);
assertSame(
    'capa vazia sem imagem nem logo',
    '',
    article_cover(['featured_image' => null], '')
);

// TinyMCE admin: entity_encoding raw
$header = file_get_contents($root . '/Views/admin/partials/header.php');
assertTrue('TinyMCE entity_encoding raw', str_contains((string) $header, "entity_encoding: 'raw'"));

exit($failed === 0 ? 0 : 1);

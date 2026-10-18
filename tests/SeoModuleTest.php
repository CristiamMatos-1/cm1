<?php

$root = dirname(__DIR__);
require $root . '/Config/App.php';

use Config\App;
use Core\Schema;
use Core\Seo;
use Models\Article;
use Models\Page;

App::boot($root);
$_SERVER['SCRIPT_NAME'] = '/cm1/Public/index.php';
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['HTTPS'] = 'off';

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

$resolved = Seo::resolve(
    ['meta_title' => 'Título SEO', 'meta_description' => 'Desc SEO', 'meta_keywords' => 'a, b'],
    'Fallback',
    'Desc fallback'
);
assertSame('usa meta_title', 'Título SEO', $resolved['title']);
assertSame('usa meta_description', 'Desc SEO', $resolved['description']);
assertSame('usa meta_keywords', 'a, b', $resolved['keywords']);

$fallback = Seo::resolve(['meta_title' => '', 'meta_description' => '', 'meta_keywords' => ''], 'Título Página', 'Resumo');
assertSame('fallback title', 'Título Página', $fallback['title']);
assertSame('fallback description', 'Resumo', $fallback['description']);

$_POST = [
    'meta_title' => '  Meta <b>Title</b>  ',
    'meta_description' => 'Descrição limpa',
    'meta_keywords' => 'seo, cms',
];
$fromPost = Seo::fromPost();
assertSame('fromPost title sem tags', 'Meta Title', $fromPost['meta_title']);
assertSame('fromPost description', 'Descrição limpa', $fromPost['meta_description']);
assertSame('fromPost keywords', 'seo, cms', $fromPost['meta_keywords']);
$_POST = [];

$pageModel = new Page();
$pageId = $pageModel->create([
    'title' => 'Página SEO ' . uniqid(),
    'content' => '<p>Conteúdo institucional de teste.</p>',
    'is_published' => 1,
    'show_in_header' => 0,
    'show_in_footer' => 0,
    'show_in_news' => 0,
    'meta_title' => 'Meta Página Teste',
    'meta_description' => 'Descrição da página de teste',
    'meta_keywords' => 'pagina, teste',
]);
$savedPage = $pageModel->findById($pageId);
assertSame('page meta_title gravado', 'Meta Página Teste', $savedPage['meta_title'] ?? null);
assertSame('page meta_description gravado', 'Descrição da página de teste', $savedPage['meta_description'] ?? null);
assertSame('page meta_keywords gravado', 'pagina, teste', $savedPage['meta_keywords'] ?? null);

$articleModel = new Article();
$articleId = $articleModel->create([
    'title' => 'Artigo SEO ' . uniqid(),
    'content' => '<p>Conteúdo do artigo.</p>',
    'is_published' => 1,
    'author_id' => 1,
    'meta_title' => 'Meta Artigo Teste',
    'meta_description' => 'Descrição do artigo',
    'meta_keywords' => 'artigo, seo',
]);
$savedArticle = $articleModel->findById($articleId);
assertSame('article meta_title gravado', 'Meta Artigo Teste', $savedArticle['meta_title'] ?? null);

// sitemap / robots via controller capture
ob_start();
(new Controllers\SeoController())->sitemap();
$sitemap = (string) ob_get_clean();
assertTrue('sitemap xml root', str_contains($sitemap, '<urlset'));
assertTrue('sitemap inclui home', str_contains($sitemap, App::absoluteUrl('')));
assertTrue('sitemap inclui matérias', str_contains($sitemap, App::absoluteUrl('materias')));
assertTrue('sitemap inclui página salva', str_contains($sitemap, (string) ($savedPage['slug'] ?? '')));

ob_start();
(new Controllers\SeoController())->robots();
$robots = (string) ob_get_clean();
assertTrue('robots Allow', str_contains($robots, 'Allow: /'));
assertTrue('robots Disallow admin', str_contains($robots, 'Disallow: ' . App::url('admin')));
assertTrue('robots Sitemap', str_contains($robots, 'Sitemap: ' . App::absoluteUrl('sitemap.xml')));

$pageModel->delete($pageId);
$articleModel->delete($articleId);

exit($failed === 0 ? 0 : 1);

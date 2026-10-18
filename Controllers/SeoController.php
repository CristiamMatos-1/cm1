<?php

namespace Controllers;

use Config\App;
use Core\Controller;
use Models\Article;
use Models\Page;
use Throwable;

/**
 * sitemap.xml e robots.txt dinâmicos.
 */
class SeoController extends Controller
{
    public function sitemap(): void
    {
        $urls = [
            [
                'loc' => App::absoluteUrl(''),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => App::absoluteUrl('materias'),
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'loc' => App::absoluteUrl('suporte'),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ],
        ];

        try {
            foreach ((new Page())->getPublished() as $page) {
                $urls[] = [
                    'loc' => App::absoluteUrl('pagina', ['slug' => $page['slug']]),
                    'lastmod' => $this->lastmod($page['updated_at'] ?? $page['created_at'] ?? null),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            }
        } catch (Throwable $e) {
        }

        try {
            foreach ((new Article())->getPublished(500) as $article) {
                $urls[] = [
                    'loc' => App::absoluteUrl('artigo', ['slug' => $article['slug']]),
                    'lastmod' => $this->lastmod($article['updated_at'] ?? $article['created_at'] ?? null),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            }
        } catch (Throwable $e) {
        }

        if (!headers_sent()) {
            header('Content-Type: application/xml; charset=UTF-8');
        }
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $entry) {
            echo "  <url>\n";
            echo '    <loc>' . $this->xml($entry['loc']) . "</loc>\n";
            if (!empty($entry['lastmod'])) {
                echo '    <lastmod>' . $this->xml($entry['lastmod']) . "</lastmod>\n";
            }
            echo '    <changefreq>' . $this->xml($entry['changefreq']) . "</changefreq>\n";
            echo '    <priority>' . $this->xml($entry['priority']) . "</priority>\n";
            echo "  </url>\n";
        }
        echo "</urlset>\n";
    }

    public function robots(): void
    {
        $sitemap = App::absoluteUrl('sitemap.xml');
        $admin = App::url('admin');
        $login = App::url('login');
        if (!headers_sent()) {
            header('Content-Type: text/plain; charset=UTF-8');
        }
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: {$admin}\n";
        echo "Disallow: {$login}\n";
        echo "\nSitemap: {$sitemap}\n";
    }

    private function lastmod(mixed $value): string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }
        $ts = strtotime($raw);
        if ($ts === false) {
            return '';
        }
        return date('Y-m-d', $ts);
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

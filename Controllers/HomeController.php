<?php

namespace Controllers;

use Config\App;
use Core\Controller;
use Core\HtmlContent;
use Core\Seo;
use Core\Services\HomeVideoService;
use Core\Services\SiteContextService;
use Models\Article;
use Models\Banner;
use Models\Page;
use Models\Setting;
use Throwable;

class HomeController extends Controller
{
    private SiteContextService $siteContext;

    public function __construct()
    {
        $this->siteContext = new SiteContextService();
    }

    public function index(): void
    {
        $settings = new Setting();
        $articles = [];
        try {
            $articles = (new Article())->getPublished(3);
        } catch (Throwable $e) {
            $articles = [];
        }

        $homeVideo = (new HomeVideoService($settings))->forHomepage();
        $banners = [];
        try {
            $banners = (new Banner())->getActive();
        } catch (Throwable $e) {
            $banners = [];
        }

        $newsPages = [];
        try {
            $newsPages = (new Page())->getPublishedFor(Page::PLACE_NEWS);
        } catch (Throwable $e) {
            $newsPages = [];
        }

        $company = $settings->get('company_name', 'CONINFOMS');
        $heroSubtitle = $settings->get('hero_subtitle', 'A CONINFOMS entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.');
        $homeTitle = $company . ' - Soluções em Tecnologia';

        $this->render('home/index', array_merge($this->siteContext->layoutData(), [
            'title' => $homeTitle,
            'seo' => [
                'title' => $homeTitle,
                'description' => $heroSubtitle,
                'keywords' => '',
            ],
            'canonicalUrl' => App::absoluteUrl(''),
            'banners' => $banners,
            'articles' => $articles,
            'newsPages' => $newsPages,
            'heroTitle' => $settings->get('hero_title', 'Inovação e Tecnologia para o seu Negócio'),
            'heroSubtitle' => $heroSubtitle,
            'heroBtn1Text' => $settings->get('hero_btn1_text', 'Conheça a Empresa'),
            'heroBtn1Link' => $settings->get('hero_btn1_link', url('pagina', ['slug' => 'sobre-a-empresa'])),
            'heroBtn2Text' => $settings->get('hero_btn2_text', 'Nossos Serviços'),
            'heroBtn2Link' => $settings->get('hero_btn2_link', '#servicos'),
            'articlesSectionTitle' => $settings->get('articles_section_title', 'Nossas Últimas Matérias'),
            'newsSectionTitle' => $settings->get('news_section_title', ''),
            'homeVideo' => $homeVideo,
        ]));
    }

    public function page(): void
    {
        $slug = trim((string) ($_GET['slug'] ?? ''));
        if ($slug === '') {
            $this->redirect('');
        }

        $page = (new Page())->findBySlug($slug);
        if (!$page) {
            $this->redirect('');
        }

        $fallbackTitle = $page['title'] . ' - CONINFOMS';
        $fallbackDesc = HtmlContent::excerpt((string) ($page['content'] ?? ''), 160);

        $this->render('home/page', array_merge($this->siteContext->layoutData(), [
            'title' => $fallbackTitle,
            'pageTitle' => $page['title'],
            'content' => $page['content'],
            'seo' => Seo::resolve($page, $fallbackTitle, $fallbackDesc),
            'canonicalUrl' => App::absoluteUrl('pagina', ['slug' => $page['slug']]),
        ]));
    }

    public function materias(): void
    {
        $articles = [];
        try {
            $articles = (new Article())->getPublished(100);
        } catch (Throwable $e) {
            $articles = [];
        }

        $this->render('home/materias', array_merge($this->siteContext->layoutData(), [
            'title' => 'Matérias - CONINFOMS',
            'seo' => [
                'title' => 'Matérias - CONINFOMS',
                'description' => 'Acompanhe artigos, tendências de tecnologia e atualizações da CONINFOMS.',
                'keywords' => 'matérias, notícias, tecnologia',
            ],
            'canonicalUrl' => App::absoluteUrl('materias'),
            'articles' => $articles,
        ]));
    }

    public function artigo(): void
    {
        $slug = trim((string) ($_GET['slug'] ?? ''));
        if ($slug === '') {
            $this->redirect('');
        }

        $article = (new Article())->findBySlug($slug);
        if (!$article) {
            $this->redirect('');
        }

        $fallbackTitle = $article['title'] . ' - CONINFOMS';
        $fallbackDesc = HtmlContent::excerpt((string) ($article['content'] ?? ''), 160);

        $this->render('home/article', array_merge($this->siteContext->layoutData(), [
            'title' => $fallbackTitle,
            'article' => $article,
            'seo' => Seo::resolve($article, $fallbackTitle, $fallbackDesc),
            'canonicalUrl' => App::absoluteUrl('artigo', ['slug' => $article['slug']]),
        ]));
    }
}

<?php

namespace Controllers;

use Core\Controller;
use Core\Services\HomeVideoService;
use Core\Services\SiteContextService;
use Models\Article;
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

        $this->render('home/index', array_merge($this->siteContext->layoutData(), [
            'title' => $settings->get('company_name', 'CONINFOMS') . ' - Soluções em Tecnologia',
            'articles' => $articles,
            'heroTitle' => $settings->get('hero_title', 'Inovação e Tecnologia para o seu Negócio'),
            'heroSubtitle' => $settings->get('hero_subtitle', 'A CONINFOMS entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.'),
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

        $this->render('home/page', array_merge($this->siteContext->layoutData(), [
            'title' => $page['title'] . ' - CONINFOMS',
            'pageTitle' => $page['title'],
            'content' => $page['content'],
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

        $this->render('home/article', array_merge($this->siteContext->layoutData(), [
            'title' => $article['title'] . ' - CONINFOMS',
            'article' => $article,
        ]));
    }
}

<?php

namespace Controllers;

use Core\Controller;
use Core\Services\YouTubeService;
use Models\Setting;
use Models\Article;

class HomeController extends Controller
{
    /**
     * Página inicial do site
     */
    public function index(): void
    {
        $ytService = new YouTubeService();
        $latestVideoId = $ytService->getLatestVideoId();

        // Instancia o model de configurações
        $settingModel = new Setting();
        
        // Pega as configurações de WhatsApp do banco de dados (ou usa padrão)
        $whatsappNumber = $settingModel->get('whatsapp_number', \Config\App::WHATSAPP_DEFAULT_NUMBER);
        $whatsappMessage = $settingModel->get('whatsapp_message', 'Olá! Gostaria de mais informações sobre os serviços da coninfoms.');
        
        // Cria a URL pronta do WhatsApp
        $whatsappUrl = \Config\App::whatsappUrl($whatsappNumber, $whatsappMessage);

        // Busca os últimos artigos publicados
        $articleModel = new Article();
        $latestArticles = $articleModel->getPublished(3); // Pega os 3 mais recentes

        // Textos Dinâmicos da Home
        $heroTitle = $settingModel->get('hero_title', 'Inovação e Tecnologia para o seu Negócio');
        $heroSubtitle = $settingModel->get('hero_subtitle', 'A coninfoms entrega soluções de ponta, desenvolvimento de sistemas e infraestrutura moderna para impulsionar o seu sucesso no mundo digital.');
        
        $heroBtn1Text = $settingModel->get('hero_btn1_text', 'Conheça a Empresa');
        $heroBtn1Link = $settingModel->get('hero_btn1_link', '/pagina?slug=sobre-a-empresa');
        $heroBtn2Text = $settingModel->get('hero_btn2_text', 'Nossos Serviços');
        $heroBtn2Link = $settingModel->get('hero_btn2_link', '#servicos');

        $articlesSectionTitle = $settingModel->get('articles_section_title', 'Nossas Últimas Matérias');
        $newsSectionTitle = $settingModel->get('news_section_title', 'Últimas Novidades');

        $youtubeSectionTitle = $settingModel->get('youtube_section_title', 'Acompanhe nosso Canal');
        $youtubeSectionSubtitle = $settingModel->get('youtube_section_subtitle', 'Fique por dentro das últimas tendências de tecnologia, tutoriais e novidades sobre a coninfoms diretamente no nosso canal do YouTube.');
        $youtubeChannelId = $settingModel->get('youtube_channel_id', '');

        // Renderiza a view 'Views/home/index.php' passando os dados
        $this->render('home/index', [
            'title' => 'coninfoms - Soluções em Tecnologia',
            'latestVideoId' => $latestVideoId,
            'whatsappUrl' => $whatsappUrl,
            'articles' => $latestArticles,
            'heroTitle' => $heroTitle,
            'heroSubtitle' => $heroSubtitle,
            'heroBtn1Text' => $heroBtn1Text,
            'heroBtn1Link' => $heroBtn1Link,
            'heroBtn2Text' => $heroBtn2Text,
            'heroBtn2Link' => $heroBtn2Link,
            'articlesSectionTitle' => $articlesSectionTitle,
            'newsSectionTitle' => $newsSectionTitle,
            'youtubeSectionTitle' => $youtubeSectionTitle,
            'youtubeSectionSubtitle' => $youtubeSectionSubtitle,
            'youtubeChannelId' => $youtubeChannelId
        ]);
    }
    
    /**
     * Página dinâmica genérica
     */
    public function page(): void
    {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            header('Location: ' . url(''));
            exit;
        }

        if (strtolower($slug) === 'contato') {
            header('Location: ' . url('contato'));
            exit;
        }

        $pageModel = new \Models\Page();
        $page = $pageModel->findBySlug($slug);

        if (!$page) {
            header('Location: ' . url(''));
            exit;
        }

        $settingModel = new Setting();
        $whatsappNumber = $settingModel->get('whatsapp_number', \Config\App::WHATSAPP_DEFAULT_NUMBER);
        $whatsappMessage = $settingModel->get('whatsapp_message', 'Olá!');
        $whatsappUrl = \Config\App::whatsappUrl($whatsappNumber, $whatsappMessage);

        $this->render('home/page', [
            'title' => $page['title'] . ' - coninfoms',
            'pageTitle' => $page['title'],
            'content' => $page['content'],
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    /**
     * Página de contato: canal único via WhatsApp
     */
    public function contato(): void
    {
        $settingModel = new Setting();
        $whatsappNumber = $settingModel->get('whatsapp_number', \Config\App::WHATSAPP_DEFAULT_NUMBER);
        $whatsappMessage = $settingModel->get('whatsapp_message', \Config\App::WHATSAPP_DEFAULT_MESSAGE);

        $this->render('home/contato', [
            'title' => 'Contato - coninfoms',
            'whatsappUrl' => \Config\App::whatsappUrl($whatsappNumber, $whatsappMessage),
            'whatsappNumber' => $whatsappNumber,
        ]);
    }

    /**
     * Página de listagem de matérias
     */
    public function materias(): void
    {
        $settingModel = new Setting();
        $whatsappNumber = $settingModel->get('whatsapp_number', \Config\App::WHATSAPP_DEFAULT_NUMBER);
        $whatsappMessage = $settingModel->get('whatsapp_message', 'Olá!');
        $whatsappUrl = \Config\App::whatsappUrl($whatsappNumber, $whatsappMessage);

        $articleModel = new Article();
        $articles = $articleModel->getPublished(100); // Traz as matérias

        $this->render('home/materias', [
            'title' => 'Matérias - coninfoms',
            'articles' => $articles,
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    /**
     * Exibe a leitura completa de um artigo
     */
    public function artigo(): void
    {
        $slug = $_GET['slug'] ?? '';

        if (empty($slug)) {
            header('Location: ' . url(''));
            exit;
        }

        $articleModel = new Article();
        $article = $articleModel->findBySlug($slug);

        if (!$article) {
            // Se o artigo não for encontrado ou não estiver publicado
            header('Location: ' . url(''));
            exit;
        }

        // WhatsApp genérico para manter o footer funcionando
        $settingModel = new Setting();
        $whatsappNumber = $settingModel->get('whatsapp_number', \Config\App::WHATSAPP_DEFAULT_NUMBER);
        $whatsappMessage = $settingModel->get('whatsapp_message', 'Olá! Gostaria de mais informações.');
        $whatsappUrl = \Config\App::whatsappUrl($whatsappNumber, $whatsappMessage);

        $this->render('home/article', [
            'title' => $article['title'] . ' - coninfoms',
            'article' => $article,
            'whatsappUrl' => $whatsappUrl,
        ]);
    }
}

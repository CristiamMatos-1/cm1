<?php

namespace Controllers;

use Config\App;
use Core\Auth;
use Core\HtmlContent;
use Core\Input;
use Core\Services\GeminiAIService;
use Core\Services\ImageUploadService;
use Core\Services\SocialMediaService;
use Models\Article;

class ArticleController extends BaseAdminController
{
    private Article $articleModel;

    public function __construct()
    {
        parent::__construct();
        $this->articleModel = new Article();
    }

    public function index(): void
    {
        $msg = $_SESSION['success_msg'] ?? null;
        unset($_SESSION['success_msg']);

        $this->render('admin/articles/index', [
            'title' => 'Gerenciar Artigos',
            'articles' => $this->articleModel->getAll(),
            'msg' => $msg,
        ]);
    }

    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/articles/create', [
            'title' => 'Novo Artigo',
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $title = Input::string($_POST['title'] ?? '');
        $content = HtmlContent::normalize((string) ($_POST['content'] ?? ''));
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if ($title === '' || $content === '') {
            $_SESSION['error_msg'] = 'Título e conteúdo são obrigatórios.';
            $this->redirect('admin/articles/create');
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'is_published' => $isPublished,
            'author_id' => Auth::id(),
        ];

        $image = $this->handleUpload('admin/articles/create');
        if ($image !== null) {
            $data['featured_image'] = $image;
        }

        $articleId = $this->articleModel->create($data);

        if ($articleId && isset($_POST['post_social']) && $isPublished) {
            $created = $this->articleModel->findById($articleId);
            $socialService = new SocialMediaService();
            $articleUrl = App::absoluteUrl('artigo', ['slug' => $created['slug'] ?? '']);
            $socialService->postToMeta($title, $articleUrl, $data['featured_image'] ?? null);
        }

        if ($articleId) {
            $_SESSION['success_msg'] = 'Artigo criado com sucesso!';
            $this->redirect('admin/articles');
        }

        $_SESSION['error_msg'] = 'Erro ao salvar no banco de dados.';
        $this->redirect('admin/articles/create');
    }

    public function edit(): void
    {
        $id = Input::int($_GET['id'] ?? null);
        if (!$id) {
            $this->redirect('admin/articles');
        }

        $article = $this->articleModel->findById($id);
        if (!$article) {
            $this->redirect('admin/articles');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/articles/edit', [
            'title' => 'Editar Artigo',
            'article' => $article,
            'error' => $error,
        ]);
    }

    public function update(): void
    {
        $this->assertCsrf();

        $id = Input::int($_POST['id'] ?? null);
        $title = Input::string($_POST['title'] ?? '');
        $content = HtmlContent::normalize((string) ($_POST['content'] ?? ''));
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if (!$id || $title === '' || $content === '') {
            $_SESSION['error_msg'] = 'Campos obrigatórios não preenchidos.';
            $this->redirect('admin/articles/edit', ['id' => (string) $id]);
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'is_published' => $isPublished,
        ];

        $image = $this->handleUpload('admin/articles/edit', ['id' => (string) $id]);
        if ($image !== null) {
            $data['featured_image'] = $image;
        }

        if ($this->articleModel->update($id, $data)) {
            $_SESSION['success_msg'] = 'Artigo atualizado com sucesso!';
            $this->redirect('admin/articles');
        }

        $_SESSION['error_msg'] = 'Erro ao atualizar no banco de dados.';
        $this->redirect('admin/articles/edit', ['id' => (string) $id]);
    }

    public function generateAiContent(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Método não permitido'], 405);
        }

        $input = json_decode((string) file_get_contents('php://input'), true);
        $topic = Input::string($input['topic'] ?? '');

        if ($topic === '') {
            $this->json(['success' => false, 'message' => 'Tema não fornecido'], 422);
        }

        $content = (new GeminiAIService())->generateArticleContent($topic);
        if ($content) {
            $this->json(['success' => true, 'content' => $content]);
        }

        $this->json(['success' => false, 'message' => 'Falha ao conectar com a IA'], 502);
    }

    public function delete(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? $_GET['id'] ?? null);

        if ($id) {
            $this->articleModel->delete($id);
            $_SESSION['success_msg'] = 'Artigo removido com sucesso!';
        }

        $this->redirect('admin/articles');
    }

    private function handleUpload(string $failPath, array $query = []): ?string
    {
        if (!isset($_FILES['featured_image']) || $_FILES['featured_image']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['featured_image']['error'] === UPLOAD_ERR_INI_SIZE) {
            $_SESSION['error_msg'] = 'A imagem excede o limite de upload do servidor.';
            $this->redirect($failPath, $query);
        }

        if ($_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error_msg'] = 'Erro no upload da imagem. Código: ' . $_FILES['featured_image']['error'];
            $this->redirect($failPath, $query);
        }

        $imagePath = (new ImageUploadService())->upload($_FILES['featured_image']);
        if (!$imagePath) {
            if (empty($_SESSION['error_msg'])) {
                $_SESSION['error_msg'] = 'Erro ao processar a imagem de destaque.';
            }
            $this->redirect($failPath, $query);
        }

        return $imagePath;
    }
}

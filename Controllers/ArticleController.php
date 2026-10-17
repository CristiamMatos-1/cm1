<?php

namespace Controllers;

use Core\Controller;
use Models\Article;
use Core\Services\ImageUploadService;
use Core\Services\GeminiAIService;
use Core\Services\SocialMediaService;

class ArticleController extends Controller
{
    private Article $articleModel;

    public function __construct()
    {
        // Protege todas as rotas deste controller
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }
        $this->articleModel = new Article();
    }

    /**
     * Lista todos os artigos no admin
     */
    public function index(): void
    {
        $articles = $this->articleModel->getAll();
        
        $msg = $_SESSION['success_msg'] ?? null;
        unset($_SESSION['success_msg']);

        $this->render('admin/articles/index', [
            'title' => 'Gerenciar Artigos',
            'articles' => $articles,
            'msg' => $msg
        ]);
    }

    /**
     * Mostra o formulário de criação
     */
    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/articles/create', [
            'title' => 'Novo Artigo',
            'error' => $error
        ]);
    }

    /**
     * Processa a criação (POST)
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/articles'));
            exit;
        }

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        // O conteúdo vem do TinyMCE (WYSIWYG), então mantemos as tags HTML, mas poderíamos usar HTMLPurifier aqui
        $content = $_POST['content'] ?? '';
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (empty($title) || empty($content)) {
            $_SESSION['error_msg'] = 'Título e conteúdo são obrigatórios.';
            header('Location: ' . url('admin/articles/create'));
            exit;
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'is_published' => $is_published,
            'author_id' => $_SESSION['user_id']
        ];

        // Processa Upload de Imagem
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['featured_image']['error'] === UPLOAD_ERR_INI_SIZE) {
                $_SESSION['error_msg'] = 'A imagem excede o limite de upload do servidor (verifique o upload_max_filesize no cPanel).';
                header('Location: ' . url('admin/articles/create'));
                exit;
            }

            if ($_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                $uploadService = new ImageUploadService();
                $imagePath = $uploadService->upload($_FILES['featured_image']);
                
                if ($imagePath) {
                $data['featured_image'] = $imagePath;
            } else {
                if (empty($_SESSION['error_msg'])) {
                    $_SESSION['error_msg'] = 'Erro ao processar a imagem de destaque.';
                }
                header('Location: ' . url('admin/articles/create'));
                exit;
            }
            } else {
                $_SESSION['error_msg'] = 'Erro desconhecido ao fazer upload da imagem. Código: ' . $_FILES['featured_image']['error'];
                header('Location: ' . url('admin/articles/create'));
                exit;
            }
        }

        $articleId = $this->articleModel->create($data);

        if ($articleId) {
            // Se o checkbox de redes sociais foi marcado, dispara o serviço
            if (isset($_POST['post_social']) && $is_published) {
                $socialService = new SocialMediaService();
                // Monta a URL completa do artigo
                $articleUrl = abs_url('artigo?slug=' . rawurlencode($data['slug'])); 
                $socialService->postToMeta($title, $articleUrl, $data['featured_image'] ?? null);
            }

            $_SESSION['success_msg'] = 'Artigo criado com sucesso!';
            header('Location: ' . url('admin/articles'));
        } else {
            $_SESSION['error_msg'] = 'Erro ao salvar no banco de dados.';
            header('Location: ' . url('admin/articles/create'));
        }
    }

    /**
     * Exibe o formulário de edição de um artigo
     */
    public function edit(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: ' . url('admin/articles'));
            exit;
        }

        $article = $this->articleModel->findById($id);
        if (!$article) {
            header('Location: ' . url('admin/articles'));
            exit;
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/articles/edit', [
            'title' => 'Editar Artigo',
            'article' => $article,
            'error' => $error
        ]);
    }

    /**
     * Atualiza o artigo no banco
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . url('admin/articles'));
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = $_POST['content'] ?? '';
        $is_published = isset($_POST['is_published']) ? 1 : 0;

        if (!$id || empty($title) || empty($content)) {
            $_SESSION['error_msg'] = 'Campos obrigatórios não preenchidos.';
            header('Location: ' . url("admin/articles/edit?id={$id}"));
            exit;
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'is_published' => $is_published
        ];

        // Processa upload se houver nova imagem
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['featured_image']['error'] === UPLOAD_ERR_INI_SIZE) {
                $_SESSION['error_msg'] = 'A imagem excede o limite de upload do servidor (verifique o upload_max_filesize no cPanel).';
                header('Location: ' . url("admin/articles/edit?id={$id}"));
                exit;
            }

            if ($_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
                $uploadService = new ImageUploadService();
                $imagePath = $uploadService->upload($_FILES['featured_image']);
                
                if ($imagePath) {
                    $data['featured_image'] = $imagePath;
                } else {
                    if (empty($_SESSION['error_msg'])) {
                        $_SESSION['error_msg'] = 'Erro ao processar a nova imagem de destaque.';
                    }
                    header('Location: ' . url("admin/articles/edit?id={$id}"));
                    exit;
                }
            } else {
                $_SESSION['error_msg'] = 'Erro desconhecido ao fazer upload da imagem. Código: ' . $_FILES['featured_image']['error'];
                header('Location: ' . url("admin/articles/edit?id={$id}"));
                exit;
            }
        }

        if ($this->articleModel->update($id, $data)) {
            $_SESSION['success_msg'] = 'Artigo atualizado com sucesso!';
            header('Location: ' . url('admin/articles'));
        } else {
            $_SESSION['error_msg'] = 'Erro ao atualizar no banco de dados.';
            header('Location: ' . url("admin/articles/edit?id={$id}"));
        }
    }

    /**
     * Endpoint API (AJAX) para gerar conteúdo com Gemini IA
     */
    public function generateAiContent(): void
    {
        // Define o header como JSON
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            exit;
        }

        // Lê o JSON enviado pelo fetch()
        $input = json_decode(file_get_contents('php://input'), true);
        $topic = $input['topic'] ?? '';

        if (empty($topic)) {
            echo json_encode(['success' => false, 'message' => 'Tema não fornecido']);
            exit;
        }

        $gemini = new GeminiAIService();
        $content = $gemini->generateArticleContent($topic);

        if ($content) {
            echo json_encode(['success' => true, 'content' => $content]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Falha ao conectar com a IA']);
        }
        exit;
    }

    /**
     * Exclui um artigo (GET simplificado para MVP)
     */
    public function delete(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if ($id) {
            // Poderíamos deletar a imagem física aqui também
            $this->articleModel->delete($id);
            $_SESSION['success_msg'] = 'Artigo removido com sucesso!';
        }
        
        header('Location: ' . url('admin/articles'));
        exit;
    }
}

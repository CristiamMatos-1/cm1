<?php

namespace Controllers;

use Core\Controller;
use Models\Page;
use Config\Database;

class AdminPageController extends Controller
{
    private Page $pageModel;

    public function __construct()
    {
        // Protege o controller: só acessa se estiver logado
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }
        
        $this->checkAndCreatePagesTable();
        $this->pageModel = new Page();
    }

    /**
     * Cria a tabela `pages` caso ela não exista ainda (UX simplificado)
     */
    private function checkAndCreatePagesTable(): void
    {
        $db = Database::getInstance()->getConnection();
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pages` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `slug` VARCHAR(255) NOT NULL UNIQUE,
                `content` TEXT NOT NULL,
                `is_published` BOOLEAN DEFAULT TRUE,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed inicial se a tabela estiver vazia
        $count = $db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
        if ($count == 0) {
            $defaultPages = [
                ['title' => 'Sobre a Empresa', 'slug' => 'sobre-a-empresa', 'content' => '<p>Conteúdo da página Sobre a Empresa.</p>'],
                ['title' => 'Tecnologias', 'slug' => 'tecnologias', 'content' => '<p>Conteúdo da página Tecnologias.</p>'],
                ['title' => 'Parceiros', 'slug' => 'parceiros', 'content' => '<p>Conteúdo da página Parceiros.</p>'],
                ['title' => 'Contato', 'slug' => 'contato', 'content' => '<p>Conteúdo da página Contato.</p>']
            ];
            $stmt = $db->prepare("INSERT INTO pages (title, slug, content, is_published) VALUES (:title, :slug, :content, 1)");
            foreach ($defaultPages as $page) {
                $stmt->execute($page);
            }
        }
    }

    /**
     * Lista as páginas disponíveis
     */
    public function index(): void
    {
        $msg = $_SESSION['page_msg'] ?? null;
        unset($_SESSION['page_msg']);

        $pages = $this->pageModel->getAll();

        $this->render('admin/pages/index', [
            'title' => 'Gerenciar Páginas - Admin',
            'pages' => $pages,
            'msg' => $msg
        ]);
    }

    /**
     * Formulário de criação de página
     */
    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/pages/create', [
            'title' => 'Nova Página - Admin',
            'error' => $error
        ]);
    }

    /**
     * Exibe o formulário de edição (TinyMCE)
     */
    public function edit(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: ' . url('admin/pages'));
            exit;
        }

        $page = $this->pageModel->findById($id);
        if (!$page) {
            header('Location: ' . url('admin/pages'));
            exit;
        }

        $this->render('admin/pages/edit', [
            'title' => 'Editar Página - Admin',
            'page' => $page
        ]);
    }

    /**
     * Salva nova página
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $content = $_POST['content'] ?? '';
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            if (empty($title) || empty($content)) {
                $_SESSION['error_msg'] = 'Título e conteúdo são obrigatórios.';
                header('Location: ' . url('admin/pages/create'));
                exit;
            }

            $this->pageModel->create([
                'title' => $title,
                'content' => $content,
                'is_published' => $is_published
            ]);

            $_SESSION['page_msg'] = 'Página criada com sucesso!';
        }

        header('Location: ' . url('admin/pages'));
        exit;
    }

    /**
     * Atualiza página existente
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $content = $_POST['content'] ?? '';
            $is_published = isset($_POST['is_published']) ? 1 : 0;
            
            if (!$id || empty($title) || empty($content)) {
                header('Location: ' . url("admin/pages/edit?id={$id}"));
                exit;
            }

            $this->pageModel->update($id, [
                'title' => $title,
                'content' => $content,
                'is_published' => $is_published
            ]);

            $_SESSION['page_msg'] = 'Página atualizada com sucesso!';
        }

        header('Location: ' . url('admin/pages'));
        exit;
    }

    /**
     * Exclui página
     */
    public function delete(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $this->pageModel->delete($id);
            $_SESSION['page_msg'] = 'Página removida com sucesso!';
        }
        
        header('Location: ' . url('admin/pages'));
        exit;
    }
}
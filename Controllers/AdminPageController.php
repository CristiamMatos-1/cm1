<?php

namespace Controllers;

use Core\HtmlContent;
use Core\Input;
use Core\Seo;
use Models\Page;

class AdminPageController extends BaseAdminController
{
    private Page $pageModel;

    public function __construct()
    {
        parent::__construct();
        $this->pageModel = new Page();
    }

    public function index(): void
    {
        $msg = $_SESSION['page_msg'] ?? null;
        unset($_SESSION['page_msg']);

        $this->render('admin/pages/index', [
            'title' => 'Gerenciar Páginas - Admin',
            'pages' => $this->pageModel->getAll(),
            'msg' => $msg,
        ]);
    }

    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/pages/create', [
            'title' => 'Nova Página - Admin',
            'error' => $error,
        ]);
    }

    public function edit(): void
    {
        $id = Input::int($_GET['id'] ?? null);
        if (!$id) {
            $this->redirect('admin/pages');
        }

        $page = $this->pageModel->findById($id);
        if (!$page) {
            $this->redirect('admin/pages');
        }

        $this->render('admin/pages/edit', [
            'title' => 'Editar Página - Admin',
            'page' => $page,
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
            $this->redirect('admin/pages/create');
        }

        $this->pageModel->create(array_merge([
            'title' => $title,
            'content' => $content,
            'is_published' => $isPublished,
        ], $this->placementFromPost(), Seo::fromPost()));

        $_SESSION['page_msg'] = 'Página criada com sucesso!';
        $this->redirect('admin/pages');
    }

    public function update(): void
    {
        $this->assertCsrf();

        $id = Input::int($_POST['id'] ?? null);
        $title = Input::string($_POST['title'] ?? '');
        $content = HtmlContent::normalize((string) ($_POST['content'] ?? ''));
        $isPublished = isset($_POST['is_published']) ? 1 : 0;

        if (!$id || $title === '' || $content === '') {
            $this->redirect('admin/pages/edit', ['id' => (string) $id]);
        }

        $this->pageModel->update($id, array_merge([
            'title' => $title,
            'content' => $content,
            'is_published' => $isPublished,
        ], $this->placementFromPost(), Seo::fromPost()));

        $_SESSION['page_msg'] = 'Página atualizada com sucesso!';
        $this->redirect('admin/pages');
    }

    public function delete(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? $_GET['id'] ?? null);
        if ($id) {
            $this->pageModel->delete($id);
            $_SESSION['page_msg'] = 'Página removida com sucesso!';
        }

        $this->redirect('admin/pages');
    }

    /**
     * @return array{show_in_header: int, show_in_footer: int, show_in_news: int, menu_order: int}
     */
    private function placementFromPost(): array
    {
        return [
            'show_in_header' => isset($_POST['show_in_header']) ? 1 : 0,
            'show_in_footer' => isset($_POST['show_in_footer']) ? 1 : 0,
            'show_in_news' => isset($_POST['show_in_news']) ? 1 : 0,
            'menu_order' => max(0, Input::int($_POST['menu_order'] ?? 0) ?? 0),
        ];
    }
}

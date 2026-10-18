<?php

namespace Controllers;

use Core\HtmlContent;
use Core\Input;
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

        $this->pageModel->create([
            'title' => $title,
            'content' => $content,
            'is_published' => $isPublished,
        ]);

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

        $this->pageModel->update($id, [
            'title' => $title,
            'content' => $content,
            'is_published' => $isPublished,
        ]);

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
}

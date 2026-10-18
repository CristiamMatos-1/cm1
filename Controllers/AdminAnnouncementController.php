<?php

namespace Controllers;

use Core\Input;
use Models\Announcement;

class AdminAnnouncementController extends BaseAdminController
{
    private Announcement $announcements;

    public function __construct()
    {
        parent::__construct();
        $this->announcements = new Announcement();
    }

    public function index(): void
    {
        $msg = $_SESSION['success_msg'] ?? null;
        unset($_SESSION['success_msg']);

        $this->render('admin/announcements/index', [
            'title' => 'Avisos importantes - Admin',
            'announcements' => $this->announcements->getAll(),
            'msg' => $msg,
        ]);
    }

    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/announcements/create', [
            'title' => 'Novo aviso - Admin',
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $title = Input::string($_POST['title'] ?? '');
        $body = $this->postedBody();
        $status = $this->postedStatus();

        if ($body === '') {
            $_SESSION['error_msg'] = 'Informe o texto do aviso.';
            $this->redirect('admin/announcements/create');
        }

        $id = $this->announcements->create([
            'title' => $title,
            'body' => $body,
            'status' => $status,
        ]);
        $this->audit('create', 'announcements', $id, $status);

        $_SESSION['success_msg'] = 'Aviso cadastrado com sucesso.';
        $this->redirect('admin/announcements');
    }

    public function edit(): void
    {
        $id = Input::int($_GET['id'] ?? null);
        if (!$id) {
            $this->redirect('admin/announcements');
        }

        $item = $this->announcements->findById($id);
        if (!$item) {
            $this->redirect('admin/announcements');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/announcements/edit', [
            'title' => 'Editar aviso - Admin',
            'item' => $item,
            'error' => $error,
        ]);
    }

    public function update(): void
    {
        $this->assertCsrf();

        $id = Input::int($_POST['id'] ?? null);
        $title = Input::string($_POST['title'] ?? '');
        $body = $this->postedBody();
        $status = $this->postedStatus();

        if (!$id || $body === '') {
            $_SESSION['error_msg'] = 'Informe o texto do aviso.';
            $this->redirect('admin/announcements/edit', ['id' => (string) $id]);
        }

        if (!$this->announcements->findById($id)) {
            $this->redirect('admin/announcements');
        }

        $this->announcements->update($id, [
            'title' => $title,
            'body' => $body,
            'status' => $status,
        ]);
        $this->audit('update', 'announcements', $id, $status);

        $_SESSION['success_msg'] = 'Aviso atualizado com sucesso.';
        $this->redirect('admin/announcements');
    }

    public function delete(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? null);
        if ($id) {
            $this->announcements->delete($id);
            $this->audit('delete', 'announcements', $id);
            $_SESSION['success_msg'] = 'Aviso excluído.';
        }
        $this->redirect('admin/announcements');
    }

    private function postedBody(): string
    {
        $body = trim(strip_tags((string) ($_POST['body'] ?? '')));
        return mb_substr($body, 0, 5000);
    }

    private function postedStatus(): string
    {
        $status = Input::string($_POST['status'] ?? Announcement::STATUS_DRAFT);
        return Announcement::isValidStatus($status) ? $status : Announcement::STATUS_DRAFT;
    }
}

<?php

namespace Controllers;

use Core\Auth;
use Core\Input;
use Core\Services\FileUploadService;
use Core\SupportPageCopy;
use Models\Download;
use Models\Setting;
use Throwable;

class AdminDownloadController extends BaseAdminController
{
    private Download $downloads;
    private FileUploadService $uploads;

    public function __construct()
    {
        parent::__construct();
        $this->downloads = new Download();
        $this->uploads = new FileUploadService();
    }

    public function index(): void
    {
        $msg = $_SESSION['success_msg'] ?? null;
        unset($_SESSION['success_msg']);

        $pageCopy = SupportPageCopy::fromMap([]);
        try {
            $pageCopy = SupportPageCopy::fromMap((new Setting())->getAll());
        } catch (Throwable $e) {
            error_log('Textos da página de downloads: ' . $e->getMessage());
        }

        $this->render('admin/downloads/index', [
            'title' => 'Suporte e Downloads - Admin',
            'downloads' => $this->downloads->getAll(),
            'msg' => $msg,
            'pageCopy' => $pageCopy,
        ]);
    }

    public function updatePage(): void
    {
        $this->assertCsrf();

        $copy = [
            SupportPageCopy::KEY_LINK => SupportPageCopy::linkLabel(Input::string($_POST['support_link_label'] ?? '')),
            SupportPageCopy::KEY_TITLE => SupportPageCopy::title(Input::string($_POST['support_page_title'] ?? '')),
            SupportPageCopy::KEY_TEXT => SupportPageCopy::text(Input::string($_POST['support_page_text'] ?? '')),
        ];

        $settings = new Setting();
        foreach ($copy as $key => $value) {
            $settings->set($key, $value);
        }

        $this->audit('update', 'settings', null, 'Textos da página de downloads');
        $_SESSION['success_msg'] = 'Textos da página de downloads atualizados.';
        $this->redirect('admin/downloads');
    }

    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/downloads/create', [
            'title' => 'Novo arquivo de suporte - Admin',
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $displayName = Input::string($_POST['display_name'] ?? '');
        $status = $this->postedStatus();
        $sortOrder = Input::int($_POST['sort_order'] ?? 0) ?? 0;

        if ($displayName === '') {
            $_SESSION['error_msg'] = 'Informe o nome de exibição do link.';
            $this->redirect('admin/downloads/create');
        }

        $uploaded = $this->uploads->upload($_FILES['support_file'] ?? []);
        if ($uploaded === false) {
            $this->redirect('admin/downloads/create');
        }

        $id = $this->downloads->create([
            'display_name' => $displayName,
            'stored_name' => $uploaded['stored_name'],
            'original_name' => $uploaded['original_name'],
            'mime_type' => $uploaded['mime_type'],
            'file_size' => $uploaded['file_size'],
            'status' => $status,
            'sort_order' => $sortOrder,
            'created_by' => Auth::id(),
        ]);
        $this->audit('create', 'downloads', $id, $displayName);

        $_SESSION['success_msg'] = 'Arquivo cadastrado com sucesso.';
        $this->redirect('admin/downloads');
    }

    public function edit(): void
    {
        $id = Input::int($_GET['id'] ?? null);
        if (!$id) {
            $this->redirect('admin/downloads');
        }

        $item = $this->downloads->findById($id);
        if (!$item) {
            $this->redirect('admin/downloads');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/downloads/edit', [
            'title' => 'Editar arquivo de suporte - Admin',
            'item' => $item,
            'error' => $error,
        ]);
    }

    public function update(): void
    {
        $this->assertCsrf();

        $id = Input::int($_POST['id'] ?? null);
        $displayName = Input::string($_POST['display_name'] ?? '');
        $status = $this->postedStatus();
        $sortOrder = Input::int($_POST['sort_order'] ?? 0) ?? 0;

        if (!$id || $displayName === '') {
            $_SESSION['error_msg'] = 'Informe o nome de exibição do link.';
            $this->redirect('admin/downloads/edit', ['id' => (string) $id]);
        }

        $current = $this->downloads->findById($id);
        if (!$current) {
            $this->redirect('admin/downloads');
        }

        $data = [
            'display_name' => $displayName,
            'status' => $status,
            'sort_order' => $sortOrder,
        ];

        $hasNewFile = isset($_FILES['support_file']) && ($_FILES['support_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        if ($hasNewFile) {
            $uploaded = $this->uploads->upload($_FILES['support_file']);
            if ($uploaded === false) {
                $this->redirect('admin/downloads/edit', ['id' => (string) $id]);
            }
            $this->uploads->delete($current['stored_name'] ?? null);
            $data = array_merge($data, $uploaded);
        }

        $this->downloads->update($id, $data);
        $this->audit('update', 'downloads', $id, $data['display_name'] ?? '');
        $_SESSION['success_msg'] = 'Arquivo atualizado com sucesso.';
        $this->redirect('admin/downloads');
    }

    public function setStatus(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? null);
        $status = $this->postedStatus(Download::STATUS_REMOVED);
        if ($id) {
            $this->downloads->setStatus($id, $status);
            $this->audit('status', 'downloads', $id, $status);
            $_SESSION['success_msg'] = 'Status atualizado.';
        }
        $this->redirect('admin/downloads');
    }

    private function postedStatus(string $default = Download::STATUS_PUBLISHED): string
    {
        $status = Input::string($_POST['status'] ?? $default);
        return Download::isValidStatus($status) ? $status : $default;
    }
}

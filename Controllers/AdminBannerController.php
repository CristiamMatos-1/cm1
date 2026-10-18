<?php

namespace Controllers;

use Core\Input;
use Core\Services\ImageUploadService;
use Models\Banner;

class AdminBannerController extends BaseAdminController
{
    private Banner $banners;
    private ImageUploadService $images;

    public function __construct()
    {
        parent::__construct();
        $this->banners = new Banner();
        $this->images = new ImageUploadService();
    }

    public function index(): void
    {
        $msg = $_SESSION['success_msg'] ?? null;
        unset($_SESSION['success_msg']);

        $this->render('admin/banners/index', [
            'title' => 'Banners da home - Admin',
            'banners' => $this->banners->getAll(),
            'msg' => $msg,
        ]);
    }

    public function create(): void
    {
        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/banners/create', [
            'title' => 'Novo banner - Admin',
            'error' => $error,
        ]);
    }

    public function store(): void
    {
        $this->assertCsrf();

        $title = Input::string($_POST['title'] ?? '');
        $linkUrl = Input::href($_POST['link_url'] ?? '');
        $sortOrder = Input::int($_POST['sort_order'] ?? 0) ?? 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $imagePath = $this->images->uploadBanner($_FILES['banner_image'] ?? []);
        if ($imagePath === false) {
            if (empty($_SESSION['error_msg'])) {
                $_SESSION['error_msg'] = 'Envie uma imagem JPG, PNG ou WEBP para o banner.';
            }
            $this->redirect('admin/banners/create');
        }

        $id = $this->banners->create([
            'title' => $title,
            'image_path' => $imagePath,
            'link_url' => $linkUrl,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
        ]);
        $this->audit('create', 'banners', $id, $title);

        $_SESSION['success_msg'] = 'Banner cadastrado com sucesso.';
        $this->redirect('admin/banners');
    }

    public function edit(): void
    {
        $id = Input::int($_GET['id'] ?? null);
        if (!$id) {
            $this->redirect('admin/banners');
        }

        $banner = $this->banners->findById($id);
        if (!$banner) {
            $this->redirect('admin/banners');
        }

        $error = $_SESSION['error_msg'] ?? null;
        unset($_SESSION['error_msg']);

        $this->render('admin/banners/edit', [
            'title' => 'Editar banner - Admin',
            'banner' => $banner,
            'error' => $error,
        ]);
    }

    public function update(): void
    {
        $this->assertCsrf();

        $id = Input::int($_POST['id'] ?? null);
        $title = Input::string($_POST['title'] ?? '');
        $linkUrl = Input::href($_POST['link_url'] ?? '');
        $sortOrder = Input::int($_POST['sort_order'] ?? 0) ?? 0;
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (!$id) {
            $this->redirect('admin/banners');
        }

        $current = $this->banners->findById($id);
        if (!$current) {
            $this->redirect('admin/banners');
        }

        $data = [
            'title' => $title,
            'link_url' => $linkUrl,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
        ];

        $hasNewFile = isset($_FILES['banner_image'])
            && ($_FILES['banner_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

        if ($hasNewFile) {
            $imagePath = $this->images->uploadBanner($_FILES['banner_image']);
            if ($imagePath === false) {
                if (empty($_SESSION['error_msg'])) {
                    $_SESSION['error_msg'] = 'Não foi possível substituir a imagem.';
                }
                $this->redirect('admin/banners/edit', ['id' => (string) $id]);
            }
            $this->images->deleteBanner($current['image_path'] ?? null);
            $data['image_path'] = $imagePath;
        }

        $this->banners->update($id, $data);
        $this->audit('update', 'banners', $id, $title . ($isActive ? ' [ativo]' : ' [inativo]'));
        $_SESSION['success_msg'] = 'Banner atualizado com sucesso.';
        $this->redirect('admin/banners');
    }

    public function delete(): void
    {
        $this->assertCsrf();
        $id = Input::int($_POST['id'] ?? null);
        if ($id) {
            $current = $this->banners->findById($id);
            if ($current) {
                $this->images->deleteBanner($current['image_path'] ?? null);
                $this->banners->delete($id);
                $this->audit('delete', 'banners', $id, (string) ($current['title'] ?? ''));
                $_SESSION['success_msg'] = 'Banner excluído.';
            }
        }
        $this->redirect('admin/banners');
    }
}
